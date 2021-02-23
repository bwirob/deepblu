<?php
namespace Netbase\Product\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Image extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_typevalueFactory = $objectManager->create('Netbase\Product\Model\Typevalue');

        $model =  $_typevalueFactory->load($row->getId());
        $html = "<strong>No Image</strong>";
        $src = $model->getImage();
        $helper = $objectManager->get('Netbase\Product\Helper\Data');

        $html = "
		<script type='text/javascript'>
		function showhide(e) {
			require([
				'jquery'
			], function($){
				'use strict';
				var element = jQuery(e);
				if (element.next().css('display') == 'none') {
					element.next().css('display', 'block');
				} else {
					element.next().css('display', 'none');
				}
				
			});
		}
		</script>
		";

        if ($src) {
            $path = $helper->getBaseUrl() . "/" . $src;
            $html .= '<p onClick="showhide(this); return false;">Show/Hide</p><img class="img-preview" style="display: none;" width="100%" height="100%" src = "' . $path . '"/>';
        }

        return $html;
    }
}
