<?php

namespace Netbaseteam\Blog\Controller\Comment;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_datetime;
    protected $_resultJsonFactory;
    protected $_captchaHelper;
    protected $_captchaStringResolver;
    protected $_blogHelper;
    protected $_commentFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Captcha\Helper\Data $captchaHelper,
        \Netbaseteam\Blog\Model\CommentFactory $commentFactory,
        \Netbaseteam\Blog\Helper\Data $blogHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_datetime = $dateTime;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_captchaHelper = $captchaHelper;
        $this->_captchaStringResolver = $captchaStringResolver;
        $this->_blogHelper = $blogHelper;
        $this->_commentFactory = $commentFactory;
        parent::__construct($context);
    }

    public function execute() {
        $data = $this->getRequest()->getParams();
        if ($data) {
            if (isset($data['captcha']['blog_comment_form'])) {
                $captcha = $data['captcha']['blog_comment_form'];
                unset($data['captcha']);
            } else {
                $captcha = '';
            }

            $formId = 'blog_comment_form'; 
            $captchaModel = $this->_captchaHelper->getCaptcha($formId);
            $callBackUrl = $this->_blogHelper->getPreBlogUrl().'/'.$data['post_url'];
            if ($this->_blogHelper->getEnableCaptchaValidate()) {
                if(!$captchaModel->isCorrect($captcha, $formId)){
                    $this->messageManager->addError(__('Incorrect CAPTCHA'));
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    return $resultRedirect->setPath($callBackUrl);
                }
            } 
            
            $data['status'] = 2;
            $data['ordering'] = 0;
            $data['create_time'] = $this->_datetime->gmtDate();
           
            try {
                $model = $this->_commentFactory->create();
                $model->addData($data);
                $model->save();
                $this->messageManager->addSuccess(__('Thank For Your comment'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath($callBackUrl);

            } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError(__($e->getMessage()));
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    return $resultRedirect->setPath($callBackUrl);

            } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while send the data.'));
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    return $resultRedirect->setPath($callBackUrl);
            }
        } else {

        }
        //$this->_forward('index', 'noroute', 'cms');
        return;

    }

}