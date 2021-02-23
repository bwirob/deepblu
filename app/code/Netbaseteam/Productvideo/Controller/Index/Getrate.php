<?php

namespace Netbaseteam\Productvideo\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Getrate extends \Magento\Framework\App\Action\Action
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
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Productvideo Index page
     *
     * @return void
     */
    public function execute()
    {
		$res = array(); $data = array();
		$params = $this->getRequest()->getParams();
		$video_id = $params['id_video'];
		$model = $this->_objectManager->create('Netbaseteam\Productvideo\Model\Productvideo');
		$load = $model->load($video_id);
		$res["rate_video"] = $load->getRateVideo();

		$catalogSession = $this->_objectManager->create('Magento\Catalog\Model\Session');
		$catalogSession->setVidRelate($load->getVidRelate());
		
		$resultPage = $this->resultPageFactory->create();
		
		$relate_html = $resultPage->getLayout()->createBlock('Netbaseteam\Productvideo\Block\Productvideo')
						->setData('relate_data', $load->getVidRelate())
						->setTemplate('Netbaseteam_Productvideo::relatevideos.phtml')->toHtml();
		$res["relate_html"] = $relate_html;


		$result = $this->_resultJsonFactory->create();
        return $result->setData($res);							
    }
}
