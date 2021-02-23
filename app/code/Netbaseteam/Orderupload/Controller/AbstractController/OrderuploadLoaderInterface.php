<?php

namespace Netbaseteam\Orderupload\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface OrderuploadLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Orderupload\Model\Orderupload
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
