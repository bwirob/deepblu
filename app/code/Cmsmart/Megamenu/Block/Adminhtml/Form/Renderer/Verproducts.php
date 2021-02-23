<?php
namespace Cmsmart\Megamenu\Block\Adminhtml\Form\Renderer;
 
class Verproducts extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
       \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

       protected $_template = 'Cmsmart_Megamenu::renderer/form/ver_products.phtml';
	   
       public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
       {
               $this->_element = $element;
               $html = $this->toHtml();
               return $html;
       }
}