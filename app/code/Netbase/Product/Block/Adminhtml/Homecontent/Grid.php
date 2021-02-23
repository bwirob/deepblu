<?php
namespace Netbase\Product\Block\Adminhtml\Homecontent;

/**
 * Adminhtml Categoryicon grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cmsmart\Categoryicon\Model\Categoryicon $categoryiconPage
     * @param \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
		$this->setTemplate('Netbase_Product::process.phtml');
    }
}
