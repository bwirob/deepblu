<?php

namespace Cmsmart\Megamenu\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class MegamenuLoader implements MegamenuLoaderInterface
{
    /**
     * @var \Cmsmart\Megamenu\Model\MegamenuFactory
     */
    protected $megamenuFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cmsmart\Megamenu\Model\MegamenuFactory $megamenuFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cmsmart\Megamenu\Model\MegamenuFactory $megamenuFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->megamenuFactory = $megamenuFactory;
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

        $megamenu = $this->megamenuFactory->create()->load($id);
        $this->registry->register('current_megamenu', $megamenu);
        return true;
    }
}
