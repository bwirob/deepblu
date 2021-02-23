<?php

namespace Netbaseteam\Marketplace\Controller\Seller;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\RequestInterface;


class EditprofilePost extends Action
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
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @param Context                                         $context
     * @param Session                                         $customerSession
     * @param FormKeyValidator                                $formKeyValidator
     * @param Magento\Framework\Stdlib\DateTime\DateTime      $date
     * @param Filesystem                                      $filesystem
     * @param Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_date = $date;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
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
     * Update Seller Profile Informations.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->isPost()) {
            try {
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/editProfile',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
                list($data, $errors) = $this->validateprofiledata();

                $fields = $this->getRequest()->getParams();
                $sellerId = $this->_customerSession->getCustomerId();

                if (empty($errors)) {
                    $id = '';
                    $collection = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Sellerdata'
                    )
                    ->getCollection()
                    ->addFieldToFilter('seller_id', $sellerId);
                    foreach ($collection as $item) {
                        $id = $item->getId();
                    }
                    $fields = $this->getSellerProfileFields($fields);

                    $sellerData = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Sellerdata'
                    )->load($id);

                    $sellerData->addData($fields);
                    $sellerData->setSellerId($sellerId);

                    if(!empty($fields['main_location'])) {
                        $sellerData->setShopLocation($fields['main_location']);
                    }

                    $target = $this->_mediaDirectory->getAbsolutePath('marketplace/');
                    try {
                        /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                        $uploader = $this->_fileUploaderFactory->create(
                            ['fileId' => 'shop_banner']
                        );
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $uploader->setAllowRenameFiles(true);
                        $result = $uploader->save($target);
                        if ($result['file']) {
                            $sellerData->setShopBanner($result['file']);
                        }
                    } catch (\Exception $e) {
                    }
                    try {
                        /** @var $uploaderLogo \Magento\MediaStorage\Model\File\Uploader */
                        $uploaderLogo = $this->_fileUploaderFactory->create(
                            ['fileId' => 'shop_logo']
                        );
                        $uploaderLogo->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $uploaderLogo->setAllowRenameFiles(true);
                        $resultLogo = $uploaderLogo->save($target);
                        if ($resultLogo['file']) {
                            $sellerData->setShopLogo($resultLogo['file']);
                        }
                    } catch (\Exception $e) {
                    }

                    $sellerData->save();

                    try {
                        if (!empty($errors)) {
                            foreach ($errors as $message) {
                                $this->messageManager->addError($message);
                            }
                        } else {
                            $this->messageManager->addSuccess(
                                __('Profile information was successfully saved')
                            );
                        }

                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/editProfile',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('We can\'t save the customer.'));
                    }

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/editProfile',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                } else {
                    foreach ($errors as $message) {
                        $this->messageManager->addError($message);
                    }

                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/editProfile',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/editProfile',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/editProfile',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    protected function validateprofiledata()
    {
        $errors = [];
        $data = [];
        foreach ($this->getRequest()->getParams() as $code => $sellerData) {
            switch ($code) :
                case 'twitter_id':
                    if (trim($sellerData) != '' &&
                        preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $sellerData)
                    ) {
                        $errors[] = __('Twitterid cannot contain space and special characters');
                    } else {
                        $data[$code] = $sellerData;
                    }
                    break;
                case 'facebook_id':
                    if (trim($sellerData) != '' &&
                        preg_match('/[\'^£$%&*()}{@#~?><>, |=_+¬-]/', $sellerData)
                    ) {
                        $errors[] = __('Facebookid cannot contain space and special characters');
                    } else {
                        $data[$code] = $sellerData;
                    }
                    break;
            endswitch;
        }

        return [$data, $errors];
    }

    protected function getSellerProfileFields($fields = [])
    {
        if (!isset($fields['twitter_active'])) {
            $fields['twitter_active'] = 0;
        }
        if (!isset($fields['facebook_active'])) {
            $fields['facebook_active'] = 0;
        }
        if (!isset($fields['gplus_active'])) {
            $fields['gplus_active'] = 0;
        }
        if (!isset($fields['youtube_active'])) {
            $fields['youtube_active'] = 0;
        }
        if (!isset($fields['vimeo_active'])) {
            $fields['vimeo_active'] = 0;
        }
        if (!isset($fields['instagram_active'])) {
            $fields['instagram_active'] = 0;
        }
        if (!isset($fields['pinterest_active'])) {
            $fields['pinterest_active'] = 0;
        }
        if (!isset($fields['moleskine_active'])) {
            $fields['moleskine_active'] = 0;
        }
        return $fields;
    }
}
