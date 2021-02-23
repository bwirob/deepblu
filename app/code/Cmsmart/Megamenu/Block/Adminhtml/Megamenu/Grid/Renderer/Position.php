<?php
namespace Cmsmart\Megamenu\Block\Adminhtml\Megamenu\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Position extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $categoryFactory->create()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter("entity_id", $row->getCategoryId());
        $cat_name = "";
        foreach ($categories as $cat):
            $cat_name = $cat->getName();
        break;
        endforeach;

        return $cat_name;
    }
}
