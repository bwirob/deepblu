<?php

namespace Netbaseteam\Faq\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCaptchaFormObserver implements ObserverInterface {

    protected $_helper;
    protected $_actionFlag;
    protected $messageManager;
    protected $_session;
    protected $_urlManager;
    protected $captchaStringResolver;
    protected $redirect;
    protected $_faqHelper;

    public function __construct(\Magento\Captcha\Helper\Data $helper, 
            \Magento\Framework\App\ActionFlag $actionFlag, 
            \Magento\Framework\Message\ManagerInterface $messageManager, 
            \Magento\Framework\Session\SessionManagerInterface $session, 
            \Magento\Framework\UrlInterface $urlManager, 
            \Magento\Framework\App\Response\RedirectInterface $redirect, 
            \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
            \Netbaseteam\Faq\Helper\Data $faqHelper

    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->_session = $session;
        $this->_urlManager = $urlManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->_faqHelper = $faqHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        if ($this->_faqHelper->getEnableCaptchaValidate()) {
            $formId = 'product_faq_form'; 
            $captchaModel = $this->_helper->getCaptcha($formId);
            $controller = $observer->getControllerAction();
            $datForm = $controller->getRequest()->getPostValue();
            if (!$captchaModel->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $this->messageManager->addError(__('Incorrect CAPTCHA'));
                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->_session->setCustomerFormData($controller->getRequest()->getPostValue());
                $url = $this->_urlManager->getUrl($datForm['reindex_url'], ['_nosecret' => true]);
                $controller->getResponse()->setRedirect($this->redirect->error($url));
            }
        }

        return $this;
    }

}