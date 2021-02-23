<?php

namespace Netbaseteam\Productvideo\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Setrate extends \Magento\Framework\App\Action\Action
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
		
		$current_vote = $load->getVoteRating();
		$current_rate = ($load->getRateVideo() + $params['rate']) / ($current_vote + 1); 
		$model
			->setRateVideo($current_rate)
			->setVoteRating($current_vote + 1)
			->setId($video_id);
		$model->save();
		
		$res["rate_video"] = $current_rate;
		
		$result = $this->_resultJsonFactory->create();
        return $result->setData($res);
    }
}
