<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget;

/**
 * @method string getButtonKey()
 * @method string getRegion()
 * @method string getName()
 * @method int getLevel()
 * @method int getSortOrder()
 * @method string getTitle()
 */
class Item extends \Magento\Framework\DataObject
{
    /**
     * Object delete flag
     *
     * @var bool
     */
    protected $_isDeleted = false;

    /**
     * Set _isDeleted flag value (if $isDeleted parameter is defined) and return current flag value
     *
     * @param boolean $isDeleted
     * @return bool
     */
    public function isDeleted($isDeleted = null)
    {
        $result = $this->_isDeleted;
        if ($isDeleted !== null) {
            $this->_isDeleted = $isDeleted;
        }
        return $result;
    }
}
