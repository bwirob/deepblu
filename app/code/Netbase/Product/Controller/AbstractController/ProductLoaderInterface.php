<?php

namespace Netbase\Product\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface ProductLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbase\Product\Model\Product
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
