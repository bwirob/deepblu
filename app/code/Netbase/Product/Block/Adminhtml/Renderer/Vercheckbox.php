<?php
namespace Cmsmart\Megamenu\Block\Adminhtml\Megamenu\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Vercheckbox extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $catalogSession = $objectManager->create('Magento\Catalog\Model\Session');
        /* \zend_debug::dump($model->getTopPgridProducts()); */
        $result = '';
        $chk = "";
        $pid = $row->getEntityId();
        $pids = explode(",", $catalogSession->getVerSelectedProducts());
        if (in_array($pid, $pids)) {
            $chk = ' checked="true"';
        }
        $result = '<input class="checkbox" type="checkbox" value="' . $pid . '" ' . $chk . ' id = "select_' . $pid . ' name="select_' . $pid . '" onClick="myVerFunctionSelect(this)">';
        /* $result = '<input class="checkbox" type="checkbox" value="'.$id.'" '.$chk.' id = "select_'.$id.' name="select_'.$id.'" onClick="myFunctionSelect(this)">'; */
        return $result;
    }
}
