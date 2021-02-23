<?php

namespace Netbaseteam\Locator\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface LocaltorLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Localtor\Model\Localtor
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
