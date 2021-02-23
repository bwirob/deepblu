<?php

namespace Netbaseteam\Blog\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class BlogLoader implements BlogLoaderInterface
{
    /**
     * @var \Netbaseteam\Blog\Model\BlogFactory
     */
    protected $postFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Blog\Model\BlogFactory $blogFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->postFactory = $postFactory;
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

        $post = $this->postFactory->create()->load($id);
        $this->registry->register('current_blog', $post);
        return true;
    }
}
