<?php

namespace Cmsmart\Categoryicon\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class Observer implements ObserverInterface
{
    protected $_layout;
    protected $_logger;

    public function __construct(
        \Magento\Framework\View\Element\Context $context
    )
    {
        $this->_layout = $context->getLayout();
        $this->_logger = $context->getLogger();
    }

    public function execute( EventObserver $observer )
    {
        $tabs = $observer->getEvent()->getData('tabs');
        $tabs->addTab('icon_tab', array(
            'label'     => __('Icons Category'),
            'content'   => 'Icons Category'
        ));
        return $this;
    }
}
