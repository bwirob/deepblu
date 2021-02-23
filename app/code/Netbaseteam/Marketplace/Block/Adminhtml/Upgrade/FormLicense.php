<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml\Upgrade;

class FormLicense extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Netbaseteam_Marketplace::field/formlicense.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
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
        return $this->getUrl('marketplace/upgrade/getlicense', ['status' => true, '_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')]);
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'btn_getkey',
                'label' => __('Get Key'),
            ]
        );

        return $button->toHtml();
    }
}