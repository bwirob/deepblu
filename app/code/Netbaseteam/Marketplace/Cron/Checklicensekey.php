<?php

namespace Netbaseteam\Marketplace\Cron;

class Checklicensekey
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Checklicensekey constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $url = $baseUrl . "license.json";
        $data = file_get_contents($url);
        $characters = json_decode($data);
        $_api_url = "https://cmsmart.net/checkactive";
        $_license_key = $characters->key;
        $_domain_name = base64_encode($baseUrl);
        $_vendor = 'netbase';
        $_sku = 'MG30';
        /* The Url active license */
        $_link_check_status = $_api_url . '/' . $_vendor . '/' . $_sku . '/' . $_license_key . '/' . $_domain_name;
        $return = json_decode(file_get_contents($_link_check_status));

        return $this;
    }
}
