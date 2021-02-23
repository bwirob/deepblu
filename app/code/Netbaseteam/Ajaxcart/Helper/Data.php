<?php

/**
 * Ajaxcart data helper
 */
namespace Netbaseteam\Ajaxcart\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\LayoutFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of ajaxcart posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'ajaxcart/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'Ajaxcart';

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
    const MIN_HEIGHT = 50;

    /**
     * Maximum image height in pixels
     *
     * @var int
     */
    const MAX_HEIGHT = 800;

    /**
     * Manimum image width in pixels
     *
     * @var int
     */
    const MIN_WIDTH = 50;

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
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_layoutFactory;
	
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		LayoutFactory $layoutFactory,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
		$this->_layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    /**
     * Remove Ajaxcart item image by image filename
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
     * Return URL for resized Ajaxcart Item Image
     *
     * @param Netbaseteam\Ajaxcart\Model\Ajaxcart $item
     * @param integer $width
     * @param integer $height
     * @return bool|string
     */
    public function resize(\Netbaseteam\Ajaxcart\Model\Ajaxcart $item, $width, $height = null)
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
            // validate image
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
     * Return the base media directory for Ajaxcart Item images
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
     * Return the Base URL for Ajaxcart Item images
     *
     * @return string
     */
    public function getBaseUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl(     
            );
    }
    
    /**
     * Return the number of items per page
     * @return int
     */
    public function getAjaxcartPerPage()
    {
        return abs((int)$this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    /*get Config method*/

    public function getConfigEnabled(){
        return $this->scopeConfig->getValue('ajaxcart/view/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAddCartAfter(){
        return $this->scopeConfig->getValue('ajaxcart/view/add_cart_after', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getTimeMessPop(){
        return $this->scopeConfig->getValue('ajaxcart/display_popup/time_mess_pop', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
 

    public function getPopupSucessTitle(){
        return $this->scopeConfig->getValue('ajaxcart/display_popup/popsucess_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPopupBackground(){
        return $this->scopeConfig->getValue('ajaxcart/display_popup/popup_background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getTitleBackground(){
        return $this->scopeConfig->getValue('ajaxcart/display_popup/title_background', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getTextPopupColor(){
        return $this->scopeConfig->getValue('ajaxcart/display_popup/color_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getShowImageProduct(){
        return $this->scopeConfig->getValue('ajaxcart/view/show_product_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getImageFlyEffect(){
        return $this->scopeConfig->getValue('ajaxcart/view/animate_image_fly', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }





	/*get getSuccessHtml method*/
	public function getSuccessHtml($product)
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('ajaxcart_success_message');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }


     public function getPopupOptionHtml($product)
    {
        $layout = $this->_layoutFactory->create();

        $update = $layout->getUpdate();
        $update->load('ajaxcart_popup_option');

        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    public function getUpdateWishlist(){

        $layout = $this->_layoutFactory->create();
        $update = $layout->getUpdate();
        $update->load('items');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    public function checkVerson(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $versonCur = $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
        $versonRequire = ['2.0.0','2.0.1','2.0.2','2.0.3','2.0.4','2.0.5','2.0.6','2.0.7','2.0.8','2.0.9','2.0.10','2.0.11','2.0.12','2.0.13'];
        return in_array($versonCur, $versonRequire)?1:0;

    }


}
