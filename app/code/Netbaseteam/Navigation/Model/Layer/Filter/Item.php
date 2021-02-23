<?php
namespace Netbaseteam\Navigation\Model\Layer\Filter;


class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{
    protected $_request;
    protected $_moduleHelper;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\App\RequestInterface $request,
        \Netbaseteam\Navigation\Helper\Data $moduleHelper
    ) {
        parent::__construct($url,$htmlPagerBlock);
        $this->_request = $request;
        $this->_moduleHelper = $moduleHelper;

    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $value = array();
        $requestVar = $this->getFilter()->getRequestVar();
        if($requestValue = $this->_request->getParam($requestVar)){
            $value = explode(',', $requestValue);
        }

        if(in_array($this->getValue(), $value)){
            $value = array_diff($value, array($this->getValue()));
        }

        if($requestVar == 'price'){
            $value = [];
        }

        $query = [$requestVar => count($value) ? implode(',', $value) : $this->getFilter()->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $value = array();
        $requestVar = $this->getFilter()->getRequestVar();
        if($requestValue = $this->_request->getParam($requestVar)){
            $value = explode(',', $requestValue);
        }
        $value[] = $this->getValue();

        if($requestVar == 'price'){
            $value = ["{price_start}-{price_end}"];
        }

        $query = [
            $this->getFilter()->getRequestVar() => implode(',', $value),
            // exclude current page from urls
            $this->_htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}
