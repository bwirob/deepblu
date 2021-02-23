<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Vacation;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;


class Save extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Model\VacationFactory $vacationFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_date = $date;
        $this->_registry = $registry;
        $this->vacationFactory = $vacationFactory;
        parent::__construct(
            $context
        );
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get(
            'Magento\Customer\Model\Url'
        )->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Save Seller Vacation Informations.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isVacation = $helper->isVacation();

        if ($isVacation == 1) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPostValue();

                try {
                    if (!$this->_formKeyValidator->validate($this->getRequest())) {
                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/index',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    }

                    $sellerId = $this->_customerSession->getCustomerId();

                    $vacation = $this->vacationFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

                    $id = '';
                    $vacationData = $vacation->getData();
                    if (!empty($vacationData)) {
                        $id = $vacationData[0]['id'];
                    }

                    $vacationModel = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Vacation'
                    );

                    if ($id) {
                        $vacationModel->load($id);
                        $vacationModel->setUpdatedAt($this->_date->gmtDate());
                    } else {
                        $vacationModel->setCreatedAt($this->_date->gmtDate());
                    }

                    $dateFrom = date_create($data['date_from']);
                    $dateTo = date_create($data['date_to']);

                    $diff = (int)date_diff($dateFrom, $dateTo)->format("%R%h%i%s%a");

                    if ($diff <= 0) {
                        $this->messageManager->addError(
                            __('The Date To must be greater than the Date From!')
                        );

                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/index',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    }
                    try {
                        $vacationModel->setSellerId($sellerId);
                        $vacationModel->setVacationMessage($data['vacation_message']);
                        $vacationModel->setDateFrom($data['date_from']);
                        $vacationModel->setDateTo($data['date_to']);
                        $vacationModel->setDisableType($data['disable_type']);
                        $vacationModel->setAddToCartLabel($data['add_to_cart_label']);
                        $vacationModel->setVacationStatus($data['vacation_status']);
                        $vacationModel->save();

                        $this->messageManager->addSuccess(
                            __('Vacation was successfully saved!')
                        );

                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/index',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('We can\'t save the vacation.'));
                    }

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/index',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );

                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/index',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/index',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('customer/account/', ['_secure' => $this->getRequest()->isSecure()]);
        }
    }
}
