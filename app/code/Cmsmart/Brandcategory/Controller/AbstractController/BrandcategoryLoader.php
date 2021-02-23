<?php

namespace Cmsmart\Brandcategory\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class BrandcategoryLoader implements BrandcategoryLoaderInterface
{
    /**
     * @var \Cmsmart\Brandcategory\Model\BrandcategoryFactory
     */
    protected $brandcategoryFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cmsmart\Brandcategory\Model\BrandcategoryFactory $brandcategoryFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cmsmart\Brandcategory\Model\BrandcategoryFactory $brandcategoryFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->brandcategoryFactory = $brandcategoryFactory;
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

        $brandcategory = $this->brandcategoryFactory->create()->load($id);
        $this->registry->register('current_brandcategory', $brandcategory);
        return true;
    }
}
