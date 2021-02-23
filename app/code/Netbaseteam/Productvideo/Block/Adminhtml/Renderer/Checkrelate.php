<?php
namespace Netbaseteam\Productvideo\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Object;

class Checkrelate extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalogSession = $objectManager->create('Magento\Catalog\Model\Session');
		$result = ''; $chk = "";
    	$pid = $row->getProductvideoId();
		$pids = explode(",", $catalogSession->getVrelate());
		if(in_array($pid, $pids)){
			$chk = ' checked="true"';
		}
		$result = '<input class="checkbox" type="checkbox" value="'.$pid.'" '.$chk.' id = "relate_select_'.$pid.' name="relate_select_'.$pid.'" onClick="myRelateFunctionSelect(this)">'; 
    	return $result;
    }
}
