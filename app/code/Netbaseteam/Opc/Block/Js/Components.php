<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Opc\Block\Js;

use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template;

class Components extends \Magento\Framework\View\Element\Template
{

    const DISCOUNTS_ENABLE = 'cmsmart_opc/general/opc_discount_enable';

    public function getDiscountsEnable(){
        return $this->_scopeConfig->getValue(self::DISCOUNTS_ENABLE);
    }
}