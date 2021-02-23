<?php

namespace Netbaseteam\Marketplace\Controller\Product\Bundle;

class Grid extends \Netbaseteam\Marketplace\Controller\Product\Account
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $index = $this->getRequest()->getParam('index');
        if (!preg_match('/^[a-z0-9_.]*$/i', $index)) {
            throw new \InvalidArgumentException('Invalid parameter "index"');
        }

        return $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Netbaseteam\Marketplace\Block\Catalog\Product\Bundle\Search\Grid',
                'marketplace_product_bundle_option_search_grid'
            )->setIndex($index)->toHtml()
        );
    }
}
