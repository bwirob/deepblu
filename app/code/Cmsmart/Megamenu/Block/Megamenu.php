<?php

namespace Cmsmart\Megamenu\Block;

/**
 * Megamenu content block
 */
class Megamenu extends \Magento\Framework\View\Element\Template
{
    /**
     * Megamenu collection
     *
     * @var Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    protected $_megamenuCollection = null;
    
    /**
     * Megamenu factory
     *
     * @var \Cmsmart\Megamenu\Model\MegamenuFactory
     */
    protected $_megamenuCollectionFactory;
    
    /** @var \Cmsmart\Megamenu\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory,
        \Cmsmart\Megamenu\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_megamenuCollectionFactory = $megamenuCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve megamenu collection
     *
     * @return Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_megamenuCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared megamenu collection
     *
     * @return Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_megamenuCollection)) {
            $this->_megamenuCollection = $this->_getCollection();
            $this->_megamenuCollection->setCurPage($this->getCurrentPage());
            $this->_megamenuCollection->setPageSize($this->_dataHelper->getMegamenuPerPage());
            $this->_megamenuCollection->setOrder('published_at','asc');
        }

        return $this->_megamenuCollection;
    }
    
    /**
     * Fetch the current page for the megamenu list
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
     * @param Cmsmart\Megamenu\Model\Megamenu $megamenuItem
     * @return string
     */
    public function getItemUrl($megamenuItem)
    {
        return $this->getUrl('*/*/view', array('id' => $megamenuItem->getId()));
    }
    
    /**
     * Return URL for resized Megamenu Item image
     *
     * @param Cmsmart\Megamenu\Model\Megamenu $item
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
        $pager = $this->getChildBlock('megamenu_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $megamenuPerPage = $this->_dataHelper->getMegamenuPerPage();

            $pager->setAvailableLimit([$megamenuPerPage => $megamenuPerPage]);
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
