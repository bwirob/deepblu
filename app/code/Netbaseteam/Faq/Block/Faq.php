<?php

namespace Netbaseteam\Faq\Block;

/**
 * Faq content block
 */
class Faq extends \Magento\Framework\View\Element\Template
{
    /**
     * Faq collection
     *
     * @var Netbaseteam\Faq\Model\ResourceModel\Faq\Collection
     */
    protected $_faqCollection = null;
    
    /**
     * Faq factory
     *
     * @var \Netbaseteam\Faq\Model\FaqFactory
     */
    protected $_faqCollectionFactory;
    
    /** @var \Netbaseteam\Faq\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        \Netbaseteam\Faq\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_faqCollectionFactory = $faqCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve faq collection
     *
     * @return Cmsmart\Faq\Model\ResourceModel\Faq\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_faqCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared faq collection
     *
     * @return Cmsmart\Faq\Model\ResourceModel\Faq\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_faqCollection)) {
            $this->_faqCollection = $this->_getCollection();
            $this->_faqCollection->setCurPage($this->getCurrentPage());
            $this->_faqCollection->setPageSize($this->_dataHelper->getFaqPerPage());
            $this->_faqCollection->setOrder('published_at','asc');
        }

        return $this->_faqCollection;
    }
    
    /**
     * Fetch the current page for the faq list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Cmsmart\Faq\Model\Faq $faqItem
     * @return string
     */
    public function getItemUrl($faqItem)
    {
        return $this->getUrl('*/*/view', array('id' => $faqItem->getId()));
    }
    
    /**
     * Return URL for resized Faq Item image
     *
     * @param Cmsmart\Faq\Model\Faq $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('faq_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $faqPerPage = $this->_dataHelper->getFaqPerPage();

            $pager->setAvailableLimit([$faqPerPage => $faqPerPage]);
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
}
