<?php

namespace Netbaseteam\Faq\Controller\Index;


class Contact extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_datetime;
    protected $_timezone;
    protected $_resultJsonFactory;
    protected $_captchaHelper;
    protected $_captchaStringResolver;
    protected $_faqHelper;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Captcha\Helper\Data $captchaHelper,
        \Netbaseteam\Faq\Helper\Data $faqHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_datetime = $dateTime;
        $this->_timezone = $timezone;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_captchaHelper = $captchaHelper;
        $this->_captchaStringResolver = $captchaStringResolver;
        $this->_faqHelper = $faqHelper;
        parent::__construct($context);
    }

    public function execute() {
        $data = $this->getRequest()->getParams();
        
        $formId = 'product_faq_form'; 
        $captchaModel = $this->_captchaHelper->getCaptcha($formId);
        if (isset($data['captcha']) && !$captchaModel->isCorrect($data['captcha']['product_faq_form'], $formId)&&$this->_faqHelper->getEnableCaptchaValidate()) {
            $result = $this->_resultJsonFactory->create();
            $errorData = array(
                        'error'=>'1',
                        'message'=>'Incorrect CAPTCHA'
                    );
            return $result->setData($errorData);
        }
        if ($data) {
            $model = $this->_objectManager->create('Netbaseteam\Faq\Model\Faq');
            $data['status'] = 2;
            $data['sidebar_faq'] = 0;
            $data['most_frequently'] = 0;
            $data['ordering'] = 0;
            $data['created_time'] = $this->_datetime->gmtDate();
            $model->addData($data);

            try {
                $model->save();
                $result = $this->_resultJsonFactory->create();
                $successData = array(
                        'succes'=>'1',
                        'message'=>'Your question has sent successfully, we will answer this question as soon as possible'
                    );
                return $result->setData($successData);

            } catch (\Magento\Framework\Model\Exception $e) {
                $result = $this->_resultJsonFactory->create();
                $errorData = array(
                        'error'=>'1',
                        'message'=>$e->getMessage()
                    );
                return $result->setData($errorData);

            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
                $result = $this->_resultJsonFactory->create();
                $errorData = array(
                        'error'=>'1',
                        'message'=>$e->getMessage()
                    );
                return $result->setData($errorData);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while send the data.'));
                $result = $this->_resultJsonFactory->create();
                $errorData = array(
                        'error'=>'1',
                        'message'=>$e->getMessage()
                    );
                return $result->setData($errorData);
            }

        }
        isset($data['reindex_url']) ? $this->_redirect($data['reindex_url']) : '';
    }

    function checkValidCaptcha(){

    }

}