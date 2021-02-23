<?php

namespace Netbaseteam\Marketplace\Controller\Vacation\Product;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Framework\UrlInterface;
use Netbaseteam\Marketplace\Block\Catalog\Product\View as Vacation;
use Netbaseteam\Marketplace\Helper\Data;

/**
 * Cminds MultiUserAccounts checkout cart index controller plugin.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotrek.pierzak@gmail.com>
 */
class View
{

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var CoreRegistry
     */
    protected $coreRegistry;

    /**
     * Object initialization.
     *
     * @param   CustomerSession $customerSession
     * @param   ManagerInterface $messageManager
     * @param   ModuleConfig $moduleConfig
     * @param   ViewHelper $viewHelper
     * @param   ResponseInterface $response
     * @param   UrlInterface $urlBuilder
     * @param   CoreRegistry $coreRegistry
     */
    public function __construct(
        ManagerInterface $messageManager,
        ResponseInterface $response,
        UrlInterface $urlBuilder,
        CoreRegistry $coreRegistry,
        Vacation $vacation,
        Data $mpHelper
    )
    {
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $coreRegistry;
        $this->_vacation = $vacation;
        $this->mpHelper = $mpHelper;
    }

    /**
     * Check if subaccount have permission to view this page.
     *
     * @param   ActionInterface $subject
     * @param   RequestInterface $request
     * @return  array
     */
    public function afterDispatch(
        ActionInterface $subject, $actions
    )
    {
        if ($this->mpHelper->isVacation() === false) {
            return $actions;
        }

        $vacation = $this->_vacation->getVacation();
        if ($vacation) {
            $disableType = $vacation['disable_type'];
            if ($disableType == 'product_disable') {
                $subject->getActionFlag()->set('', 'no-dispatch', true);

                $this->messageManager->addWarning(
                    __('Products has been disabled until shop open again')
                );

                $this->response->setRedirect(
                    $this->urlBuilder->getUrl()
                );
            }
        }

        return $actions;
    }
}