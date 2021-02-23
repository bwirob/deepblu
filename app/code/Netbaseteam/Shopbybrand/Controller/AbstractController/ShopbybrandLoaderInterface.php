<?php

namespace Netbaseteam\Shopbybrand\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface ShopbybrandLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Shopbybrand\Model\Shopbybrand
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
