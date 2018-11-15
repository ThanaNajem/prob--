<?php
/**
 *
 * Copyright © Thana', Inc. All rights reserved. 
 */
namespace CustomerLogin\Tracking\Controller\Account;

use \Magento\Customer\Model\Account\Redirect as AccountRedirect;
use \Magento\Framework\App\Action\Context;
use \Magento\Customer\Model\Session;
use \Magento\Customer\Api\AccountManagementInterface;
use \Magento\Customer\Model\Url as CustomerUrl;
use \Magento\Framework\Exception\EmailNotConfirmedException;
use \Magento\Framework\Exception\AuthenticationException;
use \Magento\Framework\Data\Form\FormKey\Validator;
use CustomerLogin\Tracking\Model\LoginHistory;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use \Magento\Framework\ObjectManager\ObjectManager;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Framework\HTTP\PhpEnvironment\Request;

/**
 * Class LoginPost to post data when submit login action
 */
class LoginPost extends \Magento\Customer\Controller\Account\LoginPost {
 
    /**
    * @var Validator
    */
    protected $formKeyValidator;

    /**
    * @var AccountRedirect
    */
    protected $resultRedirectFactory;

    /**
    * @var AccountManagementInterface
    */
    protected $customerAccountManagement;

    /**
    * @var Session
    */
    protected $session;

    /**
    * @var CustomerUrl
    */
    protected $customerUrl;

    /**
    * @var LoginHistory
    */
    protected $_loginHistory;

    /**
    * @var RemoteAddress
    */
    protected $_remoteAddress; 

    /**
    * @var DateTime
    */
    protected $_currentTimestamp;

    /**
    * @var Request
    */
    protected $_ipAddress;

    /**
    * @param Context $context
    * @param Session $session
    * @param AccountManagementInterface $customerAccountManagement
    * @param CustomerUrl $customerUrl
    * @param Validator $formKeyValidator
    * @param AccountRedirect $resultRedirectFactory
    * @param LoginHistory $_loginHistory
    * @param RemoteAddress $remoteAddress 
    * @param DateTime $currentTimestamp
    * @param Request $ipAddress
    */
    public function __construct(
        Context $context,
        Session $session, 
        AccountManagementInterface $customerAccountManagement, 
        CustomerUrl $customerUrl, 
        Validator $formKeyValidator, 
        AccountRedirect $resultRedirectFactory, 
        LoginHistory $_loginHistory, 
        RemoteAddress $remoteAddress,  
        DateTime $currentTimestamp,
        Request $ipAddress) {
            $this->formKeyValidator = $formKeyValidator;
            $this->resultRedirectFactory = $resultRedirectFactory;
            $this->customerAccountManagement = $customerAccountManagement;
            $this->session = $session;
            $this->customerUrl = $customerUrl;
            $this->_loginHistory = $_loginHistory;
            $this->_remoteAddress = $remoteAddress; 
            $this->_currentTimestamp = $currentTimestamp;
            $this->_ipAddress = $ipAddress;
            parent::__construct($context, $session, $customerAccountManagement, $customerUrl, $formKeyValidator, $resultRedirectFactory,$remoteAddress, $currentTimestamp);
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect 
     */
    public function execute() {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('home');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    $this->saveLoginHistoryIntoModel(); 
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath("customer/account/index/");  
                    return $resultRedirect; 
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                            'This account is not confirmed.' .
                            ' <a href="%1">Click here</a> to resend confirmation email.', $value
                    );
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('An error happened, so see the site programmer.'));
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
            }
        }  
    } 

    /**
     * Get user agent of logged in customer
     *
     * @return string
     */
    public function getUserAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
    } 

    /**
     * Get ip address of logged in customer 
     *
     * @return \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public function getIPAddress(){
        return $this->_ipAddress->getClientIp();
    }

    /**
     * Get current timestamp of logged in customer 
     *
     * @return \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public function getCurrentTimestamp(){
        return $this->_currentTimestamp->gmtDate();
    }

    /**
     * Save logged in customer's information as current date and time and id and user agent and ip address into custom collection
     */
    public function saveLoginHistoryIntoModel(){
        $this->_loginHistory->setData('ip_address',$this->getIPAddress());
        $this->_loginHistory->setData('user_agent',$this->getDeviceType());
        $this->_loginHistory->setData('customer_id',$this->session->getCustomer()->getId());
        $this->_loginHistory->setData('login_time',$this->getCurrentTimestamp());
        $this->_loginHistory->save();
    } 


    /**
    * Get mobile device type
    *
    */
    public function getDeviceType(){
        $deviceType = "Unknown";
        if ($this->checkIfDeviceIsAMobile()) {
            $deviceType = "Mobile";
        }
        elseif ($this->checkIfDeviceIsADesktop()) {
            $deviceType = "Desktop";
        }
        else {
            $deviceType = "Unknown";
        }
        return $deviceType;
    }

    /**
    * Check if device is mobile
    *
    * @return bool
    */
    public function checkIfDeviceIsAMobile(){
        return \Zend_Http_UserAgent_Mobile::match($this->getUserAgent(),$_SERVER);
    }

    /**
    * Check if device is desktop
    *
    * @return bool
    */
    public function checkIfDeviceIsADesktop(){
        return \Zend_Http_UserAgent_Desktop::match($this->getUserAgent(),$_SERVER);
    }
}