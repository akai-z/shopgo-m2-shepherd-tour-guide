<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Shepherd\Model;

/**
 * Config model
 */
class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var Config\System
     */
    protected $_systemConfig;

    /**
     * @var Config\File
     */
    protected $_fileConfig;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Config\System $systemConfig
     * @param \ShopGo\Shepherd\Model\Config\File $fileConfig
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        Config\System $systemConfig,
        \ShopGo\Shepherd\Model\Config\File $fileConfig,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->_systemConfig  = $systemConfig;
        $this->_fileConfig    = $fileConfig;
        $this->_cacheTypeList = $cacheTypeList;
    }

    /**
     * Get system config model
     *
     * @return Config\System
     */
    public function getSystemConfigModel()
    {
        return $this->_systemConfig;
    }

    /**
     * Get file config model
     *
     * @return \ShopGo\AmazonSns\Model\Config\File
     */
    public function getFileConfigModel()
    {
        return $this->_fileConfig;
    }

    /**
     * Get config data value
     *
     * @param string $path
     * @return string
     */
    public function getConfigData($path)
    {
        $config = $this->_fileConfig->getConfigElementValue($path);
        return !$config ? $this->_systemConfig->getConfigData($path) : $config;
    }

    /**
     * Set config data
     *
     * @param string $path
     * @param string $value
     * @return bool
     */
    public function setConfigData($path, $value)
    {
        $result = false;

        try {
            $path = explode('/', $path);

            $group = [
                $path[1] => [
                    'fields' => [
                        $path[2] => [
                            'value' => $value
                        ]
                    ]
                ]
            ];

            $configData = [
                'section' => $path[0],
                'website' => null,
                'store'   => null,
                'groups'  => $group
            ];

            $this->_systemConfig->setConfigData($configData);
            $this->_cacheTypeList->cleanType('config');

            $result = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        } catch (\Exception $e) {}

        return $result;
    }
}
