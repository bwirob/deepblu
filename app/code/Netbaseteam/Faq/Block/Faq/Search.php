<?php

namespace Netbaseteam\Faq\Block\Faq;

/**
 * FAQ content block
 */
class Search extends \Magento\Framework\View\Element\Template
{
    /**
     * FAQ collection
     *
     * @var Netbaseteam\FAQ\Model\ResourceModel\FAQ\Collection
     */
    protected $_faqCollection = null;
    
    /**
     * FAQ factory
     *
     * @var \Netbaseteam\FAQ\Model\FAQFactory
     */
    protected $_faqCollectionFactory;
    
    /** @var \Netbaseteam\FAQ\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\FAQ\Model\ResourceModel\FAQ\CollectionFactory $faqCollectionFactory
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

    public function getFormAction(){
        return $this->_dataHelper->getBaseUrls().'faq/request/index';
    }
    

}
