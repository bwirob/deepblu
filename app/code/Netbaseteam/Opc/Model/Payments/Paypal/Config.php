<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Opc\Model\Payments\Paypal;

use \Magento\Paypal\Model\Config as paypalConfig;

class Config extends paypalConfig{

    public function getBuildNotationCode()
    {
        return 'Netbaseteam_SI_MagentoCE_WPS';

    }
}