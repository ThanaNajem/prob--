<?php
/**
 * Copyright © Thana', Inc. All rights reserved. 
 */
namespace CustomerLogin\Tracking\Model;

/**
 * Class LoginHistory.
 * get login time/date, user agent, ip address when customer logged in from browser manually, so need of interact with the database should extend the \Magento\Framework\Model\AbstractModel class.
 */
class LoginHistory extends \Magento\Framework\Model\AbstractModel {
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
    	\Magento\Framework\Model\Context $context, 
    	\Magento\Framework\Registry $registry, 
    	\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
    	\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
    	array $data = []
    ){
    	parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
    * _construct method
    *
    * Model will use the resource model to talk to the database and get/set data for it on save() and load()
	* So should call the class’s _init method with the same identifying URI you’ll be using in the
	* CustomerLogin\Tracking\Model\LoginHistory call 
    */
    public function _construct(){
        $this->_init('CustomerLogin\Tracking\Model\ResourceModel\LoginHistory');
    }
}