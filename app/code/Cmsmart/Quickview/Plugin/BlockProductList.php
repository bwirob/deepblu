<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Cmsmart\Quickview\Plugin;

class BlockProductList
{
    const XML_PATH_QUICKVIEW_ENABLED = 'cmsmart_quickview/general/enabled';
    const XML_PATH_QUICKVIEW_BUTTONTEXT = 'cmsmart_quickview/quickview_style/button_text';
    const XML_PATH_QUICKVIEW_BUTTONCOLOR = 'cmsmart_quickview/quickview_style/button_color';
    const XML_PATH_QUICKVIEW_BACKGROUNDCOLOR = 'cmsmart_quickview/quickview_style/background_color';


    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    )
    {
        $result = $proceed($product);
        $isEnabled = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            $buttonText = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_BUTTONTEXT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $buttonColor = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_BUTTONCOLOR, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $backgroundColor = $this->scopeConfig->getValue(self::XML_PATH_QUICKVIEW_BACKGROUNDCOLOR, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $productUrl = $this->urlInterface->getUrl('quickview/catalog_product/view', array('id' => $product->getId()));
            if (!$buttonColor) {
                $buttonColor = '#373737';
            }

            if (strlen($buttonText) > 15) {
                $out = explode("\n", wordwrap($buttonText, 15, "\n"));
                $out = $out[0] . '...';
                $buttonText = $out;
            }

            $result = '<a class="cmsmart-quickview cmsmart_quickview_button" data-quickview-url=' . $productUrl . ' href="javascript:void(0);" style="color:' . $buttonColor . '; background:' . $backgroundColor . '"><span>' . __("$buttonText") . '</span>
                <style>
                    .cmsmart-quickview.cmsmart_quickview_button::before {
                        color:'.$buttonColor.' !important;
                    }
                </style>
            </a>';

            return $result;
        }

        return $result;
    }
}
