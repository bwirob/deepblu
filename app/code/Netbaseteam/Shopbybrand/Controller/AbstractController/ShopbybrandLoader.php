<?php

namespace Netbaseteam\Shopbybrand\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class ShopbybrandLoader implements ShopbybrandLoaderInterface
{
    /**
     * @var \Netbaseteam\Shopbybrand\Model\ShopbybrandFactory
     */
    protected $shopbybrandFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Shopbybrand\Model\ShopbybrandFactory $shopbybrandFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Shopbybrand\Model\ShopbybrandFactory $shopbybrandFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->shopbybrandFactory = $shopbybrandFactory;
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

        $shopbybrand = $this->shopbybrandFactory->create()->load($id);
        $this->registry->register('current_shopbybrand', $shopbybrand);
        return true;
    }
}
