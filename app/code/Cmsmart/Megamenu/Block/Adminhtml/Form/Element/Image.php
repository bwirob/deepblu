<?php

/**
 * Megamenu Form Image File Element Block
 *
 */
namespace Cmsmart\Megamenu\Block\Adminhtml\Form\Element;

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
