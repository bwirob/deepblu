<?php

namespace Netbaseteam\Blog\Block;


class Sidebar extends \Magento\Framework\View\Element\Template
{
   
    protected $_dataHelper;
    protected $_coreRegistry;
   
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        array $data = []
    ) {
        
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $context,
            $data
        );
    }
    
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    
    public function getItemUrl($blogItem)
    {
        return $this->getUrl('*/*/view', array('id' => $blogItem->getId()));
    }
    
    
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    
    public function getPager()
    {
        $pager = $this->getChildBlock('blog_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $blogPerPage = $this->_dataHelper->getBlogPerPage();

            $pager->setAvailableLimit([$blogPerPage => $blogPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }


    public function getListStyle()
    {   
       
        return $this->_dataHelper->getListPostStyle();
    }


    
    

}
