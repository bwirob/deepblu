<?php

/**
 * FAQ data helper
 */
namespace Netbaseteam\Faq\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\LayoutFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of faq posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'faq/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'Faq';

    /**
     * Maximum size for image in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 1048576;

    /**
     * Manimum image height in pixels
     *
     * @var int
     */
    const MIN_HEIGHT = 10;

    /**
     * Maximum image height in pixels
     *
     * @var int
     */
    const MAX_HEIGHT = 1024;

    /**
     * Manimum image width in pixels
     *
     * @var int
     */
    const MIN_WIDTH = 10;

    /**
     * Maximum image width in pixels
     *
     * @var int
     */
    const MAX_WIDTH = 1024;


    /**
     * Array of image size limitation
     *
     * @var array
     */
    protected $_imageSize   = array(
        'minheight'     => self::MIN_HEIGHT,
        'minwidth'      => self::MIN_WIDTH,
        'maxheight'     => self::MAX_HEIGHT,
        'maxwidth'      => self::MAX_WIDTH,
    );
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\HTTP\Adapter\FileTransferFactory
     */
    protected $httpFactory;
    
    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
    
    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Io\File
     */
    protected $_ioFile;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */

    protected $_backendUrl;

    protected $_layoutFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        LayoutFactory $layoutFactory

    ) {
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_backendUrl = $backendUrl;
        $this->_layoutFactory = $layoutFactory;
        parent::__construct($context);
    }
    
    /**
     * Remove FAQ item image by image filename
     *
     * @param string $imageFile
     * @return bool
     */
    public function removeImage($imageFile)
    {
        $io = $this->_ioFile;
        $io->open(array('path' => $this->getBaseDir()));
        if ($io->fileExists($imageFile)) {
            return $io->rm($imageFile);
        }
        return false;
    }
    
    /**
     * Return URL for resized FAQ Item Image
     *
     * @param Netbaseteam\FAQ\Model\FAQ $item
     * @param integer $width
     * @param integer $height
     * @return bool|string
     */
    public function resize(\Netbaseteam\Faq\Model\Faq $item, $width, $height = null)
    {
        if (!$item->getImage()) {
            return false;
        }

        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            return false;
        }
        $width = (int)$width;

        if (!is_null($height)) {
            if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
                return false;
            }
            $height = (int)$height;
        }

        $imageFile = $item->getImage();
        $cacheDir  = $this->getBaseDir() . '/' . 'cache' . '/' . $width;
        $cacheUrl  = $this->getBaseUrl() . '/' . 'cache' . '/' . $width . '/';

        $io = $this->_ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(array('path' => $cacheDir));
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }

        try {
            $image = $this->_imageFactory->create($this->getBaseDir() . '/' . $imageFile);
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Upload image and return uploaded image file name or false
     *
     * @throws Mage_Core_Exception
     * @param string $scope the request key for file
     * @return bool|string
     */
    public function uploadImage($scope)
    {
        $adapter = $this->httpFactory->create();
        $adapter->addValidator(new \Zend_Validate_File_ImageSize($this->_imageSize));
        $adapter->addValidator(
            new \Zend_Validate_File_FilesSize(['max' => self::MAX_FILE_SIZE])
        );
        
        if ($adapter->isUploaded($scope)) {
            if (!$adapter->isValid($scope)) {
                throw new \Magento\Framework\Model\Exception(__('Uploaded image is not valid.'));
            }
            
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $scope]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            
            if ($uploader->save($this->getBaseDir())) {
                return $uploader->getUploadedFileName();
            }
        }
        return false;
    }
    
    /**
     * Return the base media directory for FAQ Item images
     *
     * @return string
     */
    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::MEDIA_PATH);
        return $path;
    }
    
    /**
     * Return the Base URL for FAQ Item images
     *
     * @return string
     */
    public function getBaseUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . '/' . self::MEDIA_PATH;
    }
    
    /**
     * Return the number of items per page
     * @return int
     */
    public function getFAQPerPage()
    {
        return abs((int)$this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getFAQGridUrl(){
        return $this->_backendUrl->getUrl('faq/category/faqlist', ['_current' => true]);
    }

    public function getProductGridUrl(){
        return $this->_backendUrl->getUrl('faq/index/productlist', ['_current' => true]);
    }

    public function getCategoryAction(){
        return $this->_storeManager->getStore()->getBaseUrl().'faq/index/request';
    }

    public function getBaseUrls(){
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getStoreviewId(){
        return $this->_storeManager->getStore()->getId();
    }

    public function getLisfFaqByTagHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('faq_request_tag');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    public function getLisfFaqBySearchHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('faq_request_search');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    public function getLisfFaqByCategoryHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('faq_request_category');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    /*Get Configuration Setting*/
    public function getConfigEnabled(){
        return $this->scopeConfig->getValue('faq/view/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEnableAttachInCategory(){
        return $this->scopeConfig->getValue('faq/view/catalog_sidebar_faq', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getEnableFaqInProductPage(){
        return $this->scopeConfig->getValue('faq/view/product_view_faq', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getNumberFaqInSidebar(){
        return $this->scopeConfig->getValue('faq/faq_page/number_sidebar_question', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getMaximunNumberTag(){
        return $this->scopeConfig->getValue('faq/faq_page/faq_number_tag', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getMaximunMostFAQ(){
        return $this->scopeConfig->getValue('faq/faq_page/faq_number_most_faq', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getShowFaqAthor(){
        return $this->scopeConfig->getValue('faq/faq_page/show_faq_author', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getShowFaqCreatedTime(){
        return $this->scopeConfig->getValue('faq/faq_page/show_faq_created_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getMaxFaqPerProductPage(){
        return $this->scopeConfig->getValue('faq/product_view_faq/number_related_faq', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

    public function getEnableCaptchaValidate(){
        return $this->scopeConfig->getValue('faq/product_view_faq/captcha_contact_form', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
    }

}
