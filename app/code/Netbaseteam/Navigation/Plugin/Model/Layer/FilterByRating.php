<?php
/**
 * Copyright Â© 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Netbaseteam\Navigation\Plugin\Model\Layer;


class FilterByRating
{
    const CONFIG_ENABLED_XML_PATH = 'cmsmart_navigation/stock_filter/enabled';

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Netbaseteam\Navigation\Helper\Data $moduleHelper
    )
    {
        $this->request = $request;
        $this->objectManager = $objectManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_moduleHelper = $moduleHelper;
    }

    public function aroundGetFilters(\Magento\Catalog\Model\Layer\FilterList $subject, \Closure $proceed, \Magento\Catalog\Model\Layer $layer)
    {
        $result = $proceed($layer);
        if ($this->_moduleHelper->isEnabled() && $this->isActived()) {
            $result[] = $this->objectManager->create('Netbaseteam\Navigation\Model\Layer\Filter\Rating', ['layer' => $layer]);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isActived()
    {
        $extensionEnabled = $this->_scopeConfig->isSetFlag(
            self::CONFIG_ENABLED_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $extensionEnabled;
    }
}
