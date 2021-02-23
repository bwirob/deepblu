<?php

namespace Netbaseteam\Shopbybrand\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    protected $_resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Shopbybrand Index page
     *
     * @return void
     * 
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id_att');
        $model = $this->_objectManager->create('Netbaseteam\Shopbybrand\Model\Shopbybrand');
        if ($id)
        {
            $model->load($id);
        }
        try {
          
            $model->save();
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while download item.'));
        }

        $ret = array();

        $download_count = $model->load($id)->getCount();
        $ret["download_count"] = $download_count;

        $result = $this->_resultJsonFactory->create();
        return $result->setData($ret);
    }
}
