<?php
/**
 *
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Shepherd\Controller\Adminhtml\Tour;

class Disable extends \Magento\Backend\App\Action
{
    protected $_authSession;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->_authSession = $authSession;
        parent::__construct($context);
    }

    /**
     * Disable tours action
     */
    public function execute()
    {
        $fullActionName = $this->getRequest()->getParam('full_action_name');
        $user = $this->_authSession->getUser();

        if (!$user) {
            return;
        }
        if (!$fullActionName) {
            $fullActionName = '*';
        }

        try {
            $model = $this->_objectManager->create('ShopGo\Shepherd\Model\Tour\Inactive');

            $fullActionNameFilter = $fullActionName != '*'
                ? [['eq' => '*'], ['eq' => $fullActionName]]
                : $fullActionName;

            $tour = $model->getCollection()
                ->addFieldToFilter('full_action_name', $fullActionNameFilter)
                ->addFieldToFilter('username', $user->getUsername())
                ->addFieldToFilter('area', \Magento\Framework\App\Area::AREA_ADMINHTML)
                ->getSize();

            if (!$tour) {
                $data = [
                    'full_action_name' => $fullActionName,
                    'username' => $user->getUsername(),
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML
                ];

                $model->setData($data);
                $model->save();
            }
        } catch (\Exception $e) {}
    }
}
