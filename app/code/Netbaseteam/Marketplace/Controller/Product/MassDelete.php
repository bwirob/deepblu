<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassDelete extends \Magento\Framework\App\Action\Action
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
        \Netbaseteam\Marketplace\Model\ProductFactory $productFactory,
        \Netbaseteam\Marketplace\Model\ResourceModel\Product\CollectionFactory $mkProductCollection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Ui\Component\MassAction\Filter $filter
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->mkProductCollection = $mkProductCollection;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->formkey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formkey->getFormKey());
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->filter = $filter;
        parent::__construct($context);
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
            $productDeleted = 0;
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($collection->getItems() as $product) {
                $this->productRepository->delete($product);
                $mkId = $this->mkProductCollection->create()
                                ->addFieldToFilter('product_id', array('eq' => $product->getId()))->getFirstItem()->getId();
                $this->productFactory->create()->load($mkId)->delete();
                $productDeleted++;
            }

            if ($productDeleted) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', $productDeleted)
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
