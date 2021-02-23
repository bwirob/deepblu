<?php

namespace Netbaseteam\Faq\Controller\Product;


class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $_datetime;
    protected $_timezone;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone 
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_datetime = $dateTime;
        $this->_timezone = $timezone;

        parent::__construct($context);
    }

    public function execute() {
        $data = $this->getRequest()->getParams();

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
                $this->messageManager->addSuccess(__('Your question has sent successfully, we will answer this question as soon as possible'));
                $this->_redirect($data['reindex_url']);
                
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while send the data.'));
            }

        }
        $this->_redirect($data['reindex_url']);
    }

}