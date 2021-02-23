<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Navigation\Plugin\Model;


class Config
{
    protected $_jsonHelper;
    protected $_moduleHelper;

    public function __construct(
        \Netbaseteam\Navigation\Helper\Data $moduleHelper
    ){
        $this->_moduleHelper = $moduleHelper;
    }

    /**
     * Aggiungo nel frontend l'opzione di ordinamento per created_at
     */
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
        $options
    ) {
        if($this->_moduleHelper->isEnabled()) {
            $options['created_at'] = __('New Product');
            $options['best_seller'] = __('Best Seller');
            $options['promo_product'] = __('Promotion Product');
            $options['rating'] = __('Rating');
            $options['review'] = __('Review');
        }
        return $options;
    }
}
