<?php

namespace Netbaseteam\Marketplace\Ui\Component\Listing\Columns\Order;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Payseller extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
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
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
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
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_flag'] = 0;

                if ($item['paid_status'] == 'paid') {
                    $item[$fieldName . '_html'] = "<span>Paid</span>";
                } else {
                    if ($item['status'] == 'Complete') {
                        $item[$fieldName . '_html'] = "<button class='button'><span>Pay</span></button>";
                        $item[$fieldName . '_flag'] = 1;
                    } elseif ($item['status'] == 'Pending') {
                        $item[$fieldName . '_html'] = "<span>Item Pending</span>";
                    } elseif ($item['status'] == 'Refunded') {
                        $item[$fieldName . '_html'] = "<span>Item Refunded</span>";
                    }elseif ($item['status'] == 'Closed') {
                        $item[$fieldName . '_html'] = "<span>Item Refunded</span>";
                    }elseif ($item['status'] == 'Canceled') {
                        $item[$fieldName . '_html'] = "<span>Item Refunded</span>";
                    } else {
                        $item[$fieldName . '_html'] = "<span>Item Pending</span>";
                    }
                }


                $item[$fieldName . '_title'] = __('Please enter a message that you want to send to Seller');
                $item[$fieldName . '_submitlabel'] = __('Send');
                $item[$fieldName . '_cancellabel'] = __('Reset');
                $item[$fieldName . '_id'] = $item['id'];
                $item[$fieldName . '_seller_amount'] = $item['seller_amount'];
                $item[$fieldName . '_commission'] = $item['commission'];

                $item[$fieldName . '_formaction'] = $this->urlBuilder->getUrl('marketplace/order/payseller');
            }
        }

        return $dataSource;
    }
}