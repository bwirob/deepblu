<?php

namespace Netbaseteam\Productvideo\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class ProductvideoLoader implements ProductvideoLoaderInterface
{
    /**
     * @var \Netbaseteam\Productvideo\Model\ProductvideoFactory
     */
    protected $productvideoFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Productvideo\Model\ProductvideoFactory $productvideoFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Productvideo\Model\ProductvideoFactory $productvideoFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->productvideoFactory = $productvideoFactory;
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

        $productvideo = $this->productvideoFactory->create()->load($id);
        $this->registry->register('current_productvideo', $productvideo);
        return true;
    }
}
