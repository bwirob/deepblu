<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Filter;

class Text extends \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Filter\AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $html = '<input type="text" name="' .
            $this->_getHtmlName() .
            '" id="' .
            $this->_getHtmlId() .
            '" value="' .
            $this->getEscapedValue() .
            '" class="input-text netbaseteam__control-text no-changes"' .
            $this->getUiId(
                'filter',
                $this->_getHtmlName()
            ) . ' />';
        return $html;
    }
}
