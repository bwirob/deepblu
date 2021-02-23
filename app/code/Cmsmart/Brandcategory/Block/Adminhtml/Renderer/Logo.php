<?php
namespace Cmsmart\Brandcategory\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Logo extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManagerr->get('\Cmsmart\Brandcategory\Helper\Data');
        $img_path = $row->getLogo();
        $path = "";
        if ($img_path) {
            $path = '<img  width="80" src="' . $helper->getBaseUrl() . "/" . $img_path . '" >';
        }
        return $path;
    }
}
