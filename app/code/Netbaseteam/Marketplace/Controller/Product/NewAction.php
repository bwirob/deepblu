<?php

/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

class NewAction extends Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        FormKeyValidator $formKeyValidator,
        PageFactory $resultPageFactory
    )
    {
        $this->productRepository = $productRepository;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Remove item
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {

            try {
                $set = $this->getRequest()->getParam('set');
                $type = $this->getRequest()->getParam('type');

                if (isset($set) && isset($type)) {
                    return $this->resultRedirectFactory->create()
                        ->setPath(
                            '*/*/add',
                            [
                                'set' => $set,
                                'type' => $type,
                                '_secure' => $this->getRequest()->isSecure(),
                            ]
                        );
                } else {
                    $this->messageManager->addError(
                        __('Please select attribute set and product type.')
                    );

                    return $this->resultRedirectFactory->create()
                        ->setPath(
                            '*/*/',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $this->resultRedirectFactory->create()
                    ->setPath(
                        '*/*/',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );

                $this->messageManager->addError(__($e->getMessage()));

            }
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }

    }

}
