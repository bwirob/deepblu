<?php
namespace Netbase\Sun\Block\Config;

class Image extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
		
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery"], function ($) {
                $(document).ready(function () {
					
					$el = $("#' . $element->getHtmlId() . '");
					$el.change(function(event) {
					  var img = $el.val();
					  var data = img.split(".");
					  var defineImg = last(data);
					  if(defineImg == "svg"){
						  alert("That format file not support");
						  $el.val("");
					  }; 
					});
					
					
					var last =  function(array, n) {
					  if (array == null) 
						return void 0;
					  if (n == null) 
						 return array[array.length - 1];
					  return array.slice(Math.max(array.length - n, 0));  
					};
					
					
					
                });
            });
            </script>';
        return $html;
    }

}