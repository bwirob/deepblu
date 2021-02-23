<?php
namespace Cmsmart\Categoryicon\Block\Adminhtml\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Iconhover extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $path = $mediaUrl . 'Categoryicon/' . $row->getCategoryId() . "/" . $row->getIconHover();
        $img = "<img src='" . $path . "' width='30' />";
        return $img;
    }
}
