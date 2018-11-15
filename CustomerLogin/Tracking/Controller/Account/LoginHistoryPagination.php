<?php
/**
 *
 * Copyright © Thana', Inc. All rights reserved. 
 */
namespace CustomerLogin\Tracking\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use \Magento\Customer\Model\Session;

/**
 * Class LoginHistoryPagination to represent login history customer account page
 */
class LoginHistoryPagination extends \Magento\Customer\Controller\AbstractAccount
{
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
    * @var Session
    */
    protected $session;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $session
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $session 
    ) {
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Login history customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->session->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('home');
            return $resultRedirect;
        }
        return $this->resultPageFactory->create();
    }
}
