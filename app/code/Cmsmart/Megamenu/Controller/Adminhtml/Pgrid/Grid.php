<?php

namespace Cmsmart\Megamenu\Controller\Adminhtml\Pgrid;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer grid action
     *
     * @return void
     */
    public function execute()
    {
		$this->_view->loadLayout();
        $this->getResponse()->setBody(
              $this->_view->getLayout()->createBlock('Cmsmart\Megamenu\Block\Adminhtml\Pgrid\Grid')->toHtml()
        );
    }
}
