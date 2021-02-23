<?php

namespace Netbaseteam\Shopbybrand\Controller\Index;

use Netbaseteam\Shopbybrand\Controller\ShopbybrandInterface;

class Search extends \Netbaseteam\Shopbybrand\Controller\AbstractController\View implements ShopbybrandInterface
{
    public function execute()
    {
        
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
        $breadcrumbs->addCrumb('shopbybrand',
            [
                'label' => __('Shop By Brand'),
                'title' => __('Shop By Brand'),
                'link' => $this->_url->getUrl('shopbybrand')
           ]
         );
        $breadcrumbs->addCrumb('search',
            [
                'label' => __('Search'),
                'title' => __('Search')
           ]
         );
        $this->_view->renderLayout();
    }
}
