<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\Shepherd\Model\Tour;

/**
 * Inactive Tour model
 */
class Inactive extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('ShopGo\Shepherd\Model\Tour\ResourceModel\Inactive');
    }
}
