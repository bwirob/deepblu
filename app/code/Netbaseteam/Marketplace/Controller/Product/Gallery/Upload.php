<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Product\Gallery;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Marketplace Product Image Upload controller.
 */
class Upload extends Action
{
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
     * @param \Magento\Framework\App\Action\Context $context
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            try {
                $target = $this->_mediaDirectory->getAbsolutePath(
                    $this->_objectManager->get(
                        'Magento\Catalog\Model\Product\Media\Config'
                    )->getBaseTmpMediaPath()
                );
                $fileUploader = $this->_fileUploaderFactory->create(
                    ['fileId' => 'image']
                );
                $fileUploader->setAllowedExtensions(
                    ['gif', 'jpg', 'png', 'jpeg']
                );
                $fileUploader->setFilesDispersion(true);
                $fileUploader->setAllowRenameFiles(true);
                $resultData = $fileUploader->save($target);
                unset($resultData['tmp_name']);
                unset($resultData['path']);
                $resultData['url'] = $this->_objectManager->get(
                    'Magento\Catalog\Model\Product\Media\Config'
                )->getTmpMediaUrl($resultData['file']);
                $resultData['file'] = $resultData['file'].'.tmp';
                $this->getResponse()->representJson(
                    $this->_objectManager->get(
                        'Magento\Framework\Json\Helper\Data'
                    )->jsonEncode($resultData)
                );
            } catch (\Exception $e) {
                $this->getResponse()->representJson(
                    $this->_objectManager->get(
                        'Magento\Framework\Json\Helper\Data'
                    )->jsonEncode(
                        [
                            'error' => $e->getMessage(),
                            'errorcode' => $e->getCode(),
                        ]
                    )
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
