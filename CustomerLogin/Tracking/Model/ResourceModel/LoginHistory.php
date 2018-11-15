<?php
/**
 * Copyright © Thana', Inc. All rights reserved.
 */
namespace CustomerLogin\Tracking\Model\ResourceModel;

/**
 * Customer Login History Resource Model
 *
 * @author      Thana' Najem <thana.najem13@gmail.com>
 */
class LoginHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
     * method will call _init() function to define the table/model name and primary key for that table
     *
     * Initialize connection and define main table - tells  LoginHistory.php in the resource
     * which table in the Database to connect to. 
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('customer_login_history', 'id');
    }
}