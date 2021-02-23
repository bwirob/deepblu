<?php

/**
 * Shopbybrand Form Image File Element Block
 *
 */
namespace Netbaseteam\Shopbybrand\Block\Adminhtml\Form\Element;

class Image extends \Magento\Framework\Data\Form\Element\Image
{ 
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }
}
