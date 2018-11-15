<?php
/**
 * Copyright © Thana', Inc. All rights reserved. 
 */
namespace CustomerLogin\Tracking\Block\Account;

use \Magento\Framework\View\Element\Template;
use \Magento\Customer\Model\Session; 

/**
 * Class to manage customer dashboard Login histoty pagination section
 */
class LoginHistoryPagination extends Template{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \CustomerLogin\Tracking\Model\LoginHistory
     */
    protected $_loginHistory;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_customerID;

    /**
     * @var CustomerLogin\Tracking\Model\ResourceModel\LoginHistory\Collection
     */
    protected $_collection; 

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \CustomerLogin\Tracking\Model\LoginHistory $loginHistory 
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \CustomerLogin\Tracking\Model\LoginHistory $loginHistory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_loginHistory = $loginHistory;
        $this->customerSession = $customerSession; 

    }

    /**
     * Add pagination into custom model and return the result
     * 
     * @return CustomerLogin\Tracking\Block\Account\LoginHistoryPagination
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('LoginHistory'));
        if ($this->getAllLoginTransactionsForFixedCustomer()) {
            $pager = $this->getLayout()
            ->createBlock('\Magento\Theme\Block\Html\Pager', 'test.news.pager')
            ->setAvailableLimit(array(5=>5,10=>10,15=>15))
            ->setShowPerPage(true)
            ->setCollection($this->getAllLoginTransactionsForFixedCustomer());
            $this->setChild('pager', $pager);
            $this->getAllLoginTransactionsForFixedCustomer()->load();
        }
        return $this;
    }
   
    /**
     * get values of current page, current limit and collection for pagination
     * 
     * @return CustomerLogin\Tracking\Model\ResourceModel\LoginHistory\Collection
     */
    public function getAllLoginTransactionsForFixedCustomer(){
        $this->_collection = $this->getCustomCollection(); 
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1; 
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;
        $this->_collection->setPageSize($pageSize);
        $this->_collection->setCurPage($page);
        return $this->_collection;
    
   }

    /**
     * get collection of login history for logged in customer
     * 
     * @return CustomerLogin\Tracking\Model\ResourceModel\LoginHistory\Collection
     */
    public function getCustomCollection(){  
        $this->_customerID = $this->customerSession->getCustomer()->getId(); 
        return $this->_loginHistory->getCollection()->addFieldToFilter("customer_id", array("eq" => $this->_customerID));
    }

    /**
     * get pager's child html
     * 
     * @return \Magento\Theme\Block\Html\Pager
     */
       public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}