<?php

namespace Cmsmart\Categoryicon\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CategoryiconLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cmsmart\Categoryicon\Model\Categoryicon
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
