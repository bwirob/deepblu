<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Upgrade;

use Magento\Framework\View\Result\PageFactory;

class Getlicense extends \Magento\Framework\App\Action\Action {
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

    public function execute() {
        $get_data = $this->getRequest()->getPost();
        $get_license_email = $get_data['get_license_email'];
        $get_license_user = $get_data['get_license_user'];
        $domain_cus = $this->_storeManager->getStore()->getBaseUrl();
        $email = $get_license_email;
        $username = preg_replace('/\s+/', '%20', $get_license_user);
        $_api_url = "https://cmsmart.net/subcrible";
        $_sku = 'MG30';
        $_email = base64_encode($email);
        $_domain_name = base64_encode($domain_cus);
        $server_ip = $this->get_client_ip();
        $_ip = base64_encode($server_ip);

        /* The Url active license */
        $_link_license_key_free = $_api_url . '/' . $_sku . '/' . $_email . '/' . $_domain_name . '/' . $username . '/' . $_ip;

        try {
            $return = file_get_contents($_link_license_key_free);
            $this->messageManager->addSuccess(__('The Data has been Send.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError(__('error!'));
        }
    }

    public function get_client_ip()
    {
        if ($_POST) {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if (isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if (isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if (isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }
    }
}