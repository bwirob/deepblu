<?php

namespace Netbaseteam\Blog\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface BlogLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Blog\Model\Blog
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
