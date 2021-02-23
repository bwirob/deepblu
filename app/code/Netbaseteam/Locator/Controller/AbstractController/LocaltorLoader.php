<?php

namespace Netbaseteam\Locator\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class LocaltorLoader implements LocaltorLoaderInterface
{
    /**
     * @var \Netbaseteam\Localtor\Model\LocaltorFactory
     */
    protected $localtorFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Localtor\Model\LocaltorFactory $localtorFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Locator\Model\LocatorFactory $localtorFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->localtorFactory = $localtorFactory;
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

        $localtor = $this->localtorFactory->create()->load($id);
        $this->registry->register('current_localtor', $localtor);
        return true;
    }
}
