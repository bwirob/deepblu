<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml\Upgrade;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Netbaseteam_Marketplace::field/button.phtml';
    protected $_storeManager;
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->directory_list = $directory_list;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxUrl()
    {
        return $this->getUrl('marketplace/upgrade/index', ['status' => true, '_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')]);
    }

    public function getAjaxUrlRemove()
    {
        return $this->getUrl('marketplace/upgrade/removelicense', ['status' => true, '_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')]);
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'btn_active',
                'label' => __('Active'),
            ]
        );

        return $button->toHtml();
    }

   public function getTypeLicense()
    {
        // $baseUrl = $this->getBaseUrl();
        $baseUrl = $this->directory_list->getRoot();
        $data = file_get_contents($baseUrl.'/var/config/licenseX.json');
        if (!empty($data)) {
            $characters = json_decode($data);
            $type = $characters->type;

            return $type;
        } else {
            return "";
        }
    }

}