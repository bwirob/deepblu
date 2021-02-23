<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Block\Catalog\Product\Edit\Tab\Downloadable;

/**
 * Adminhtml catalog product downloadable items tab links section
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Samples extends \Magento\Framework\View\Element\Template
{
    /**
     * Block config data
     *
     * @var \Magento\Framework\DataObject
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit/downloadable/samples.phtml';

    /**
     * Downloadable file
     *
     * @var \Magento\Downloadable\Helper\File
     */
    protected $_downloadableFile = null;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Downloadable\Model\Sample
     */
    protected $_sampleModel;

    /**
     * @var \Magento\Backend\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Downloadable\Helper\File $downloadableFile
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Downloadable\Model\Sample $sampleModel
     * @param \Magento\Backend\Model\UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Downloadable\Model\Sample $sampleModel,
//        \Magento\Backend\Model\UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreFileStorageDb = $coreFileStorageDatabase;
        $this->_downloadableFile = $downloadableFile;
        $this->_coreRegistry = $coreRegistry;
        $this->_sampleModel = $sampleModel;
//        $this->_urlFactory = $urlFactory;

        parent::__construct($context, $data);
    }

    /**
     * Get model of the product that is being edited
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product_edit');
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Retrieve samples array
     *
     * @return array
     */
    public function getSampleData()
    {
        $samplesArr = [];
        if ($this->getProduct()->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return $samplesArr;
        }
        $samples = $this->getProduct()->getTypeInstance()->getSamples($this->getProduct());
        $fileHelper = $this->_downloadableFile;
        foreach ($samples as $item) {
            $tmpSampleItem = [
                'sample_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];

            $sampleFile = $item->getSampleFile();
            if ($sampleFile) {
                $file = $fileHelper->getFilePath($this->_sampleModel->getBasePath(), $sampleFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'marketplace/product_downloadable_product_edit/sample',
                        ['id' => $item->getId(), '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $sampleFile
                    ) . '</a>';
                    $tmpSampleItem['file_save'] = [
                        [
                            'file' => $sampleFile,
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            if ($this->getProduct() && $item->getStoreTitle()) {
                $tmpSampleItem['store_title'] = $item->getStoreTitle();
            }
            $samplesArr[] = new \Magento\Framework\DataObject($tmpSampleItem);
        }

        return $samplesArr;
    }


    /**
     * Retrieve config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        $url = $this->getUrl('marketplace/product_downloadable_file/upload',
            ['type' => 'samples', '_secure' => true]);
        $this->getConfig()->setUrl($url);
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField('samples');
        $this->getConfig()->setFilters(['all' => ['label' => __('All Files'), 'files' => ['*.*']]]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return $this->_jsonEncoder->encode($this->getConfig()->getData());
    }

    /**
     * Retrieve config object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        if ($this->_config === null) {
            $this->_config = new \Magento\Framework\DataObject();
        }

        return $this->_config;
    }
}
