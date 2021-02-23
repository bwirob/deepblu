<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassStatus extends \Magento\Framework\App\Action\Action
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Action\Context $context
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Ui\Component\MassAction\Filter $filter
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->formkey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formkey->getFormKey());
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Validate batch of products before theirs status will be set
     *
     * @param array $productIds
     * @param int $status
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        if ($status == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
            if (!$this->_objectManager->create(\Magento\Catalog\Model\Product::class)->isProductsHasSku($productIds)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please make sure to define SKU values for all processed products.')
                );
            }
        }
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {

            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productIds = $collection->getAllIds();
            $requestStoreId = $storeId = $this->getRequest()->getParam('store', null);
            $filterRequest = $this->getRequest()->getParam('filters', null);
            $status = (int) $this->getRequest()->getParam('status');

            if (null === $storeId && null !== $filterRequest) {
                $storeId = (isset($filterRequest['store_id'])) ? (int) $filterRequest['store_id'] : 0;
            }

            try {
                $this->_validateMassStatus($productIds, $status);
                $this->_objectManager->get(\Magento\Catalog\Model\Product\Action::class)
                    ->updateAttributes($productIds, ['status' => $status], (int) $storeId);
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been updated.', count($productIds))
                );
                $this->_productPriceIndexerProcessor->reindexList($productIds);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while updating the product(s) status.')
                );
            }
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
