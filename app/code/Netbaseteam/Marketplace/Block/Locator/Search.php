<?php

namespace Netbaseteam\Marketplace\Block\Locator;

class Search extends \Netbaseteam\Marketplace\Block\Locator
{
    public function getSearchAction(){
        return $this->getUrl("*/*/", ['_secure' => $this->getRequest()->isSecure()]);
    }
}