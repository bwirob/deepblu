<?php

namespace Netbaseteam\Productvideo\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface ProductvideoLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Productvideo\Model\Productvideo
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
