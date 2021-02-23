<?php

namespace Netbaseteam\Marketplace\Ui\Component\Listing\Columns\Seller;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Commission
 */
class CommissionActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $store = $this->storeManager->getStore(
                    $this->context->getFilterParam('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                );
                $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());

                $item[$fieldName.'_html'] = "<button class='button'><span>Edit</span></button>";
                $item[$fieldName.'_title'] = __('Edit Seller Commission');
                $item[$fieldName.'_submitlabel'] = __('Save');
                $item[$fieldName.'_cancellabel'] = __('Reset');
                $item[$fieldName.'_sellerid'] = $item['id'];
                $commissionAmount = $item['commission_amount'];
                $commissionAmount = trim($commissionAmount, $currency->getSymbol());
                $commissionAmount = trim($commissionAmount, '%');
                $item[$fieldName.'_commissionAmount'] = $commissionAmount;

                $item[$fieldName.'_commissionOption'] = $item['fixed_or_percentage'];
                $item[$fieldName.'_commissionType'] = $item['commission_type'];

                $item[$fieldName.'_formaction'] = $this->urlBuilder->getUrl('marketplace/seller/edit');
            }
        }

        return $dataSource;
    }
}
