<?php

namespace Netbaseteam\Marketplace\Controller\Locator;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Default Localtor Index page
     *
     * @return void
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isLocator = $helper->isLocatorEnable();

        if ($isLocator) {
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $resultPage = $this->resultPageFactory->create();
            $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home',
                [
                    'label' => __('Home'),
                    'title' => __('Home'),
                    'link' => $this->_url->getUrl('')
                ]
            );
            $breadcrumbs->addCrumb('locator',
                [
                    'label' => __('Seller Store Locator'),
                    'title' => __('Seller Store Locator')
                ]
            );

            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('', ['_secure' => $this->getRequest()->isSecure()]);
        }
    }
}
