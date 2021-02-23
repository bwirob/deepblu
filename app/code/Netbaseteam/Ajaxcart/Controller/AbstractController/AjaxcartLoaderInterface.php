<?php

namespace Netbaseteam\Ajaxcart\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface AjaxcartLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Ajaxcart\Model\Ajaxcart
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
