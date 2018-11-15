<?php
/**
 * Copyright © Thana', Inc. All rights reserved. 
 */
namespace CustomerLogin\Tracking\Block\Account;

use \Magento\Framework\View\Element\Template;
use CustomerLogin\Tracking\Block\Account\LoginHistoryPagination;

/**
 * Class to manage customer dashboard previous login data for the current login section
 */
class PreviousLoginData extends Template {
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \CustomerLogin\Tracking\Model\LoginHistory
     */
    protected $_loginHistory;

    /**
     * @var int
     */
    protected $_customerID;
    
    /**
    * @var LoginHistoryPagination
    */
    protected $_loginHistoryPagination;

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
        LoginHistoryPagination $LoginHistoryPagination, 
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_loginHistory = $loginHistory;
        $this->customerSession = $customerSession; 
        $this->_loginHistoryPagination = $LoginHistoryPagination ;
        $this->_customerID = $this->customerSession->getCustomer()->getId(); 

    }

    /**
     * Get previous login data for the current login if logged in is not the first time else null
     *  
     * @return \CustomerLogin\Tracking\Model\LoginHistory|string
     */
    public function getLastLoginTransactionInformationBeforeCurrentLogin(){
        $customerLoggedInStatus = false ;
		if ($this->checkIfThisIsFirstLogin()) {
			$customerLoggedInStatus = true;
		}
        return $customerLoggedInStatus;
    }
    
    /**
     * Check if customer is logged in
     *  
     * @return bool
     */
    public function checkIfCustomerLoggedIn(){
    	return $this->customerSession->isLoggedIn();
    }

    /**
     * Check if the first customer's logged from browser manually
     *  
     * @return bool
     */
    public function checkIfThisIsFirstLogin(){ 
 	    return $this->getCountOfCustomerLogin()==1 ;
 	}

    /**
     * Times Of Customer logged in from browser manually that may be zero or more.
     *  
     * @return int 
     */
    public function getCountOfCustomerLogin(){
        return count($this->getCustomCollection());
    } 
    
    /**
     * Get previous login data for the current customer logged in from browser manually
     *  
     * @return CustomerLogin\Tracking\Model\ResourceModel\LoginHistory\Collection|null
     */
    public function getFirstItemOfPreviouslyCurrentLoginHistoryCollectionSortedByLoginTime(){
        $this->_customerID = $this->customerSession->getCustomer()->getId(); 
        $collection = $this->getCustomCollection()
                           ->addFieldToSelect("*")
                           ->addFieldToFilter("id", array("neq" => $this->getLastLoginId()))
                           ->setOrder("login_time","DESC");
        return $collection->getFirstItem();
    }

    /**
     * Get previous login id for the current customer logged in from browser manually
     *  
     * @return int
     */ 
    public function getPreviouslyOfCurrentLoginId(){
        return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollectionSortedByLoginTime()
                    ->getId();
    }

    /**
     * Get previous login IP Address for the current customer logged in from browser manually
     *  
     * @return string
     */ 
    public function getPreviouslyOfCurrentLoginIpAddress(){
        return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollectionSortedByLoginTime()
                    ->getIpAddress();
    }

    /**
     * Get previous login user agent for the current customer logged in from browser manually
     *  
     * @return string
     */ 
    public function getPreviouslyOfCurrentLoginUserAgent(){
        return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollectionSortedByLoginTime()
                    ->getUserAgent();
    }

    /**
     * Get previous login timestamp for the current customer logged in from browser manually
     *  
     * @return string
     */ 
    public function getPreviouslyOfCurrentLoginTime(){
        return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollectionSortedByLoginTime()
                    ->getLoginTime();
    }

    /**
     * Get last login id for the current customer logged in from browser manually
     *  
     * @return int
     */ 
    public function getLastLoginId(){ 
        return $this->getCustomCollection()
                    ->addFieldToSelect("*")
                    ->getLastItem()
                    ->getId();   
    }
    
    /**
     * Get all login data history for the current customer logged in from browser manually
     *  
     * don't check if customer logged in or not because it was done in related controller
     * @return CustomerLogin\Tracking\Model\ResourceModel\LoginHistory\Collection|null
     */
    public function getCustomCollection(){  
        return $this->_loginHistoryPagination->getCustomCollection();
    }

    /**
     * Get error message into dashboard section when display previous of current login data and customer is not loggedIn
     *  
     * @return string
     */
 	public function getMessageForFirstLogin(){
 		return 'This is your first login in website, so there are no previous logins';
 	}
}
