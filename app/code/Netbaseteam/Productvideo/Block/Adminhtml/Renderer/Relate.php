<?php
namespace Netbaseteam\Productvideo\Block\Adminhtml\Renderer;
 
class Relate extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
       \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

       protected $_template = 'Netbaseteam_Productvideo::renderer/form/ver_relate.phtml';
	   
       public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
       {
               $this->_element = $element;
               $html = $this->toHtml();
               return $html;
       }
}