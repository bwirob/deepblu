<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Opc\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const ENABLE_OPC = 'cmsmart_opc/general/enable_in_frontend';
    const META_TITLE = 'cmsmart_opc/general/opc_title';
    const FONT_COLOR = 'cmsmart_opc/general/font_color';
    const HOVER_COLOR = 'cmsmart_opc/general/hover_color';
    const GIFT_WRAP_TYPE = 'cmsmart_opc/gift_wrap/type';
    const GIFT_WRAP_AMOUNT = 'cmsmart_opc/gift_wrap/amount';

    public function getEnable(){
        return (bool)$this->scopeConfig->getValue(self::ENABLE_OPC);
    }

    public function getMetaTitle(){
        return $this->scopeConfig->getValue(self::META_TITLE);
    }

    public function getFontColor() {
        return $this->scopeConfig->getValue(self::FONT_COLOR);
    }

    public function getHoverColor() {
        return $this->scopeConfig->getValue(self::HOVER_COLOR);
    }

}