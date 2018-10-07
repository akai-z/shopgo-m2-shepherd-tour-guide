<?php

namespace ShopGo\Shepherd\Block\Adminhtml;

class Head extends \Magento\Framework\View\Element\Template
{
    protected $_config;
    protected $_assetRepository;
    protected $_authSession;
    protected $_fileConfig;
    protected $_tour;
    protected $_InactiveTour;
    protected $_translatableTourStepConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Backend\Model\Auth\Session $authSession,
        \ShopGo\Shepherd\Model\Config $config,
        \ShopGo\Shepherd\Model\Tour\Inactive $inactiveTour,
        array $data = []
    ) {
        $this->_assetRepository = $assetRepository;
        $this->_authSession = $authSession;
        $this->_fileConfig = $config->getFileConfigModel();
        $this->_InactiveTour = $inactiveTour;
        return parent::__construct($context, $data);
    }

    public function getFullActionName()
    {
        return $this->getRequest()->getFullActionName();
    }

    protected function _getAdminUsername()
    {
        $user = $this->_authSession->getUser();

        return $user ? $user->getUsername() : '';
    }

    /**
     * Check whether shepherd is enabled for current admin user
     *
     * @return boolean
     */
    protected function _isActiveForAdminUser()
    {
        $user = $this->_getAdminUsername();

        if (!$user) {
            return false;
        }

        $element = $this->_fileConfig->getConfigElementValue([
            'users' => [],
            'user'  => ['attributes' => ['name' => '*']]
        ]);

        if ($element) {
            $excludedUsers = $this->_fileConfig->getConfigElement([
                'users' => [],
                'user'  => ['attributes' => ['name' => '*']],
                'exclude' => []
            ]);

            if ($excludedUsers !== null) {
                $_excludedUsers = [];
                foreach ($excludedUsers->childNodes as $excUser) {
                    $_excludedUsers[$excUser->nodeValue] = '';
                }

                if (isset($_excludedUsers[$user])) {
                    return false;
                }
            }
        } else {
            $element = $this->_fileConfig->getConfigElementValue([
                'users' => [],
                'user'  => ['attributes' => ['name' => $user]]
            ]);
        }

        return $element ? true : false;
    }

    protected function _setTranslatableTourStepConfig()
    {
        $translatables = $this->_fileConfig->getConfigElementAttribute(['tours' => []], 'translate');
        $this->_translatableTourStepConfig = explode(' ', $translatables);
    }

    public function getTour()
    {
        if (!$this->_tour && !$this->_isInactiveTour()) {
            $tour = $this->_fileConfig->getConfigElement([
                'tours' => [],
                'tour' => ['attributes' => ['full_action_name' => $this->getFullActionName()]]
            ]);

            if ($tour) {
                if ($tour->hasAttribute('disabled') && $tour->getAttribute('disabled') == '1') {
                    // Do nothing
                } else {
                    $this->_tour = $tour;
                    $this->_setTranslatableTourStepConfig();
                }
            }
        }

        return $this->_tour;
    }

    protected function _isInactiveTour()
    {
        $adminUsername = $this->_getAdminUsername();

        if (!$adminUsername) {
            return false;
        }

        $tour = $this->_InactiveTour->getCollection()
            ->addFieldToFilter('full_action_name', [
                ['eq' => '*'],
                ['eq' => $this->getFullActionName()]
            ])
            ->addFieldToFilter('username', $adminUsername)
            ->addFieldToFilter('area', \Magento\Framework\App\Area::AREA_ADMINHTML)
            ->getSize();

        return $tour ? true : false;
    }

    protected function _translateTourStepConfig($configName, $configValue)
    {
        $translatableConfig = array_flip($this->_translatableTourStepConfig);

        if (isset($translatableConfig[$configName])) {
            $configValue = __($configValue);
        }

        return $configValue;
    }

    protected function _getTourStepConfig($element)
    {
        $config = [];

        foreach ($element->childNodes as $elementConfig) {
            $configName = $elementConfig->getAttribute('name');

            if (
                $elementConfig->hasChildNodes()
                && !$elementConfig->childNodes->item(0) instanceof \DOMCharacterData
            ) {
                $_config = $this->_getTourStepConfig($elementConfig);
            } else {
                $_config = $this->_translateTourStepConfig($configName, $elementConfig->nodeValue);
            }

            if ($configName == '') {
                $config[] = $_config;
            } else {
                $config[$configName] = $_config;
            }
        }

        return $config;
    }

    public function getTourSteps($json = false)
    {
        $steps = [];
        $tour  = $this->getTour();

        foreach ($tour->childNodes as $step) {
            $stepName = $step->getAttribute('name');
            $steps[$stepName] = $this->_getTourStepConfig($step);
        }

        return $json ? json_encode($steps) : $steps;
    }

    public function createAsset($fileId, array $params = [])
    {
        return $this->_assetRepository->createAsset($fileId, $params);
    }

    public function getAssetUrl($fileId, array $params = [])
    {
        $asset = $this->createAsset($fileId, $params);

        return $asset->getUrl();
    }

    public function isActive()
    {
        $result = $this->_isActiveForAdminUser();

        if ($result) {
            $result = $this->getTour();
        }

        return $result;
    }

    public function getDisableTourUrl()
    {
        return $this->getUrl('shopgo_shepherd/tour/disable');
    }

    public function getDisableTourMessage($type)
    {
        $message = '';

        switch ($type) {
            case 'current':
                $message = __('Would you like to disable this page tour?');
                break;
            case 'all':
                $message = __('Would you like to disable tours on all pages?');
                break;
        }

        return addslashes($message);
    }

    public function getThemeCssHref()
    {
        return $this->getAssetUrl('ShopGo_Shepherd::css/shepherd-theme-arrows.css');
    }
}
