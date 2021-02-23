<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Ui\Component\Listing\Columns\Notification;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 *
 * @api
 * @since 100.0.2
 */
class Actions extends Column
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
        array $components = [],
        array $data = []
    ) {
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
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['read'] = [
                    'href' => $item['url'],
                    'label' => __('Read Details'),
                    'hidden' => false,
                ];
                if ($item['is_read'] == 0 || $item['is_read'] == '0') {
                    $item[$this->getData('name')]['markAsRead'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'marketplace/notification/markAsRead',
                            ['id' => $item['id'], 'store' => $storeId]
                        ),
                        'label' => __('Mark as Read'),
                        'hidden' => false,
                    ];
                }
                
                $item[$this->getData('name')]['remove'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'marketplace/notification/remove',
                        ['id' => $item['id'], 'store' => $storeId]
                    ),
                    'label' => __('Remove'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
