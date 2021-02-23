<?php

namespace Netbase\Product\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class ProductLoader implements ProductLoaderInterface
{
    /**
     * @var \Netbase\Product\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbase\Product\Model\ProductFactory $productFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbase\Product\Model\ProductFactory $productFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->productFactory = $productFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $product = $this->productFactory->create()->load($id);
        $this->registry->register('current_product', $product);
        return true;
    }
}
