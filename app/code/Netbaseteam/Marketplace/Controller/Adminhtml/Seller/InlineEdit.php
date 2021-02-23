<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_seller';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        if ($this->_helper->releaseLimit()) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            $postItems = $this->getRequest()->getParam('items', []);

            if (!($this->getRequest()->getParam('isAjax'))) {
                return $resultJson->setData([
                    'messages' => [__('Please correct the data sent.')],
                    'error' => true,
                ]);
            }

            if ($postItems) {

                $id = 0;
                foreach ($postItems as $item) {
                    $id = $item['id'];
                    $status = $item['status'];
                }

                if ($id) {
                    $model = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Seller')->load($id);

                    $model->setStatus($status);
                    $model->save();

                }

                return $resultJson->setData([
                    'messages' => [__('Seller status has been updated.')],
                    'error' => false
                ]);
            }
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
