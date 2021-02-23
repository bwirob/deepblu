<?php
namespace Cmsmart\Brandcategory\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Vercheckbox extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $catalogSession = $objectManager->create('Magento\Catalog\Model\Session');
        $result = '';
        $chk = "";
        $pid = $row->getEntityId();
        $pids = explode(",", $catalogSession->getProducts());
        if (in_array($pid, $pids)) {
            $chk = ' checked="true"';
        }
        $result = '<input class="checkbox" type="checkbox" value="' . $pid . '" ' . $chk . ' id = "select_' . $pid . ' name="select_' . $pid . '" onClick="myVerFunctionSelect(this)">';
        return $result;
    }
}
