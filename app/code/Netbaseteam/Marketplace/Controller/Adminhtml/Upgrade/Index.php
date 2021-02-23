<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Upgrade;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	protected $_resultJsonFactory;
	protected $_helper;
	protected $_storeManager;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Action\Context $context,
		PageFactory $resultPageFactory,
		\Netbaseteam\Marketplace\Helper\Data $helper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->_storeManager = $storeManager;
		$this->resultPageFactory = $resultPageFactory;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_helper = $helper;
		parent::__construct($context);
	}

	public function execute()
    {
		$get_data = $this->getRequest()->getPost();
		$license = $get_data['license'];
		$domain_cus = $this->_storeManager->getStore()->getBaseUrl();
		$message = 'Incorrect License key';
        if($_POST) {
            $_api_url = "https://cmsmart.net/activedomain";
            $_vendor = 'netbase';
            $_sku = 'MG30';
            $_license_key = base64_encode($license);
            $_domain_name = base64_encode($domain_cus);
            /* The Url active license */
            $_link_check_active = $_api_url . '/' . $_vendor . '/' . $_sku . '/' . $license . '/' . $_domain_name;
            $return = (array)json_decode(file_get_contents($_link_check_active));
            switch ($return['code']) {
			    case "5":
			    	$return['key'] = $license;
	                $return['domain'] = $domain_cus;
	                $str = $domain_cus . $license . 'pro';
	                $token = hash_hmac("sha256", $str, $license, false);
	                $return['token'] = $token;
	                $return['salt'] = $_license_key;
	                $formattedData = json_encode($return);

	                //set the filename
	                $this->_helper->_writeContent2File($formattedData);
	                $message = 'License key can using';
			        break;
			    case "6":
				    $return['key'] = $license;
	                $return['domain'] = $domain_cus;
	                $str = $domain_cus . $license . 'pro';
	                $token = hash_hmac("sha256", $str, $license, false);
	                $return['token'] = $token;
	                $return['salt'] = $_license_key;
	                $formattedData = json_encode($return);

	                //set the filename
	                $this->_helper->_writeContent2File($formattedData);
	                $message = 'License key can using';
			        break;
			    case "3":
			    	$return['key'] = $license;
	                $return['domain'] = $domain_cus;
	                $str = $domain_cus . $license . 'pro';
	                $token = hash_hmac("sha256", $str, $license, false);
	                $return['token'] = $token;
	                $return['salt'] = $_license_key;
	                $formattedData = json_encode($return);

	                //set the filename
	                $this->_helper->_writeContent2File($formattedData);
	                $message = 'The License has expired';
			        break;
			}
        }
        $ret['mess'] = $message;
		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}
