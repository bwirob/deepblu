<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Renderer\Checkboxes;

class Extended extends \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Renderer\Checkbox
{
    /**
     * Prepare data for renderer
     *
     * @return array
     */
    public function _getValues()
    {
        return $this->getColumn()->getValues();
    }
}
