<?php

namespace Netbaseteam\Blog\Block\Sidebar;

class Search extends \Magento\Framework\View\Element\Template
{ 
    protected $_dataHelper;
    protected $_coreRegistry;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $context,
            $data
        );
    }

    public function getFormAction(){
        return $this->_dataHelper->getBaseFontUrl().'blog/search/index';
    }

    public function getSearchString(){
        $request = $this->_coreRegistry->registry('request');
        if(!empty($request['type'])){
            if ($request['type']=='search') {
                return $request['q'];
            }
        }
        return false;
    }


    public function getListStyle()
    {   
        return $this->_coreRegistry->registry('style');
    }

    

}
