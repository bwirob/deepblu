<?php

namespace Netbaseteam\Marketplace\Ui\DataProvider\Sales;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class SaleDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{

    protected $customerSession;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderIds = $objectManager->create('Netbaseteam\Marketplace\Block\Sale\Order')->getOrders()->getAllIds();
        $data = $this->getSearchResult()->addFieldToFilter('entity_id', array('in' => $orderIds));
        return $this->searchResultToOutput($data);
    }
}

