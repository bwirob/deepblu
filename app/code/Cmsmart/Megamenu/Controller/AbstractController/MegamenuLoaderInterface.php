<?php

namespace Cmsmart\Megamenu\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface MegamenuLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cmsmart\Megamenu\Model\Megamenu
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
