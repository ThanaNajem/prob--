<?php
/**
 * Copyright © Thana', Inc. All rights reserved. 
 */
namespace CustomerLogin\Tracking\Model\ResourceModel\LoginHistory;
/**
 * Login History Collection
 *
 *@author      Thana' Najem <thana.najem13@gmail.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('CustomerLogin\Tracking\Model\LoginHistory', 'CustomerLogin\Tracking\Model\ResourceModel\LoginHistory');
    }
}