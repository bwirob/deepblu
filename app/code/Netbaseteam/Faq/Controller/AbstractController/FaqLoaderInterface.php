<?php

namespace Netbaseteam\Faq\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface FaqLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Faq\Model\Faq
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
