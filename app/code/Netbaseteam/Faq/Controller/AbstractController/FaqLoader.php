<?php

namespace Netbaseteam\Faq\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class FaqLoader implements FaqLoaderInterface
{
    /**
     * @var \Netbaseteam\Faq\Model\FaqFactory
     */
    protected $faqFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Faq\Model\FaqFactory $faqFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Faq\Model\FaqFactory $faqFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->faqFactory = $faqFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $faq = $this->faqFactory->create()->load($id);
        $this->registry->register('current_faq', $faq);
        return true;
    }
}
