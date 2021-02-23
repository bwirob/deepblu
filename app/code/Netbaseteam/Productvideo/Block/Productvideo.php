<?php

namespace Netbaseteam\Productvideo\Block;

/**
 * Productvideo content block
 */
class Productvideo extends \Magento\Framework\View\Element\Template
{
    /**
     * Productvideo collection
     *
     * @var Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\Collection
     */
    protected $_productvideoCollection = null;
    
    /**
     * Productvideo factory
     *
     * @var \Netbaseteam\Productvideo\Model\ProductvideoFactory
     */
    protected $_productvideoCollectionFactory;
    
    /** @var \Netbaseteam\Productvideo\Helper\Data */
    protected $_dataHelper;
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\CollectionFactory $productvideoCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\CollectionFactory $productvideoCollectionFactory,
        \Netbaseteam\Productvideo\Helper\Data $dataHelper,
		\Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    ) {
        $this->_productvideoCollectionFactory = $productvideoCollectionFactory;
        $this->_dataHelper = $dataHelper;
		$this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve productvideo collection
     *
     * @return Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_productvideoCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared productvideo collection
     *
     * @return Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\Collection
     */
    public function getCollection()
    {
        //if (is_null($this->_productvideoCollection)) {
            $this->_productvideoCollection = $this->_getCollection()
						->addFieldToFilter("status", \Netbaseteam\Productvideo\Model\Source\Status::active);
           /*  $this->_productvideoCollection->setCurPage($this->getCurrentPage());
            $this->_productvideoCollection->setPageSize($this->_dataHelper->getProductvideoPerPage());
            $this->_productvideoCollection->setOrder('published_at','asc'); */
       //}

        return $this->_productvideoCollection;
    }
    
    /**
     * Fetch the current page for the productvideo list
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
     * @param Netbaseteam\Productvideo\Model\Productvideo $productvideoItem
     * @return string
     */
    public function getItemUrl($productvideoItem)
    {
        return $this->getUrl('*/*/view', array('id' => $productvideoItem->getId()));
    }
    
    /**
     * Return URL for resized Productvideo Item image
     *
     * @param Netbaseteam\Productvideo\Model\Productvideo $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
	
	public function isCustomerLogged()
	{
		$customer = $this->_customerSession->create();
        return $customer->getCustomer()->getId();
	}
	
	public function getCustomerGroupId()
	{
		$customer = $this->_customerSession->create();
        return $customer->getCustomer()->getGroupId();
	}
}
