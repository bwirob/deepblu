<?php
namespace Cmsmart\Brandcategory\Block\Adminhtml\Renderer;

class Products extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
       \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'Cmsmart_Brandcategory::renderer/form/ver_products.phtml';

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        $html = $this->toHtml();
        return $html;
    }
}
