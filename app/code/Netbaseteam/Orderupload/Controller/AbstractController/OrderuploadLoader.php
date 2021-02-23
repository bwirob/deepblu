<?php

namespace Netbaseteam\Orderupload\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class OrderuploadLoader implements OrderuploadLoaderInterface
{
    /**
     * @var \Netbaseteam\Orderupload\Model\OrderuploadFactory
     */
    protected $orderuploadFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Orderupload\Model\OrderuploadFactory $orderuploadFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Orderupload\Model\OrderuploadFactory $orderuploadFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->orderuploadFactory = $orderuploadFactory;
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

        $orderupload = $this->orderuploadFactory->create()->load($id);
        $this->registry->register('current_orderupload', $orderupload);
        return true;
    }
}
