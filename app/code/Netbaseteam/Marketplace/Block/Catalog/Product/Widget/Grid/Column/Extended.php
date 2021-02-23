<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Column;

class Extended extends \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Column
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\Code\NameBuilder $nameBuilder
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Math\Random $mathRandom,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\Code\NameBuilder $nameBuilder,
		array $data = []
	)
    {
        $this->_rendererTypes['checkbox'] = 'Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Renderer\Checkboxes\Extended';

        parent::__construct($context, $mathRandom, $formKey, $nameBuilder, $data);
    }
}
