<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Cmsmart\Quickview\Helper;

use Magento\Framework\View\LayoutFactory;

/**
 * Catalog data helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LayoutFactory $layoutFactory
    )
    {
        $this->urlInterface = $context->getUrlBuilder();
        parent::__construct($context);
    }


    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getProductUrl($pid = 0)
    {
        return $this->urlInterface->getUrl('quickview/catalog_product/view', array('id' => $pid));
    }

    public function isEnable()
    {
        return $this->getConfig('cmsmart_quickview/general/enabled');
    }

    public function getButtonColor()
    {
        return $this->getConfig('cmsmart_quickview/quickview_style/button_color');
    }

    public function getButtonText()
    {
        return $this->getConfig('cmsmart_quickview/quickview_style/button_text');
    }

    public function getBackgroundColor()
    {
        return $this->getConfig('cmsmart_quickview/quickview_style/background_color');
    }

    public function getQvTitle()
    {
        $buttonText = $this->getConfig('cmsmart_quickview/quickview_style/label_title');

        if (strlen($buttonText) > 100) {
            $out = explode("\n", wordwrap($buttonText, 70, "\n"));
            $out = $out[0] . '...';
        } else {
            $out = $buttonText;
        }
        return $out;
    }

    public function showAddtocart()
    {
        return $this->getConfig('cmsmart_quickview/add_to_cart_style/show_addtocart');
    }

    public function showAddto()
    {
        return $this->getConfig('cmsmart_quickview/product_add_form/show_addto');
    }

    public function showEmailto()
    {
        return $this->getConfig('cmsmart_quickview/product_add_form/show_mailto');
    }

    public function showDetail()
    {
        return $this->getConfig('cmsmart_quickview/product_info_detailed/show_productdetail');
    }

    public function showMoreInfo()
    {
        return $this->getConfig('cmsmart_quickview/product_info_detailed/show_productattributes');
    }

    public function showReview()
    {
        return $this->getConfig('cmsmart_quickview/product_info_detailed/show_productreview');
    }

    public function showProductRelated()
    {
        return $this->getConfig('cmsmart_quickview/product_related_upsell/show_productrelated');
    }

    public function showProductUpsell()
    {
        return $this->getConfig('cmsmart_quickview/product_related_upsell/show_productupsell');
    }
}
