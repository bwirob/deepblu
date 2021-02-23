<?php

namespace Cmsmart\Brandcategory\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface BrandcategoryLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cmsmart\Brandcategory\Model\Brandcategory
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
