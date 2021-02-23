<?php

namespace Netbaseteam\Ajaxsearch\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface AjaxsearchLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Ajaxsearch\Model\Ajaxsearch
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
