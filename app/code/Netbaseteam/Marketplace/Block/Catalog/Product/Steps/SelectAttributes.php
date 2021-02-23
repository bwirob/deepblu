<?php

/**
 * Marketplace block for fieldset of configurable product.
 */

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Steps;

class SelectAttributes extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->_registry = $registry;
        parent::__construct($context);
    }

    public function getCaption()
    {
        return __('Select Attributes');
    }
}
