<?php

/**
 * Megamenu data helper
 */
namespace Cmsmart\Megamenu\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of megamenu posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'megamenu/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'Megamenu';

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
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        parent::__construct($context);
    }
    
    /**
     * Remove Megamenu item image by image filename
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
     * Return URL for resized Megamenu Item Image
     *
     * @param Cmsmart\Megamenu\Model\Megamenu $item
     * @param integer $width
     * @param integer $height
     * @return bool|string
     */
    public function resize(\Cmsmart\Megamenu\Model\Megamenu $item, $width, $height = null)
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
     * Return the base media directory for Megamenu Item images
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
     * Return the Base URL for Megamenu Item images
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
    public function getMegamenuPerPage()
    {
        return abs((int)$this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function getRootCatID()
    {
        return abs((int)$this->scopeConfig->getValue('megamenu/mainmenu/root_cat_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function isEnable()
    {
        return abs((int)$this->scopeConfig->getValue('megamenu/mainmenu/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function getTitleSkuFromPattern($sku)
    {
        /* $pattern = '/^[sku]\W+[sku]$/'; */
		$sku1 = explode("[sku]", $sku);
		$sku2 = explode("[sku]", $sku1[1]);

		/* $pattern = [title][your_product_sku] */
		$sku3 = explode("][", $sku2[0]);
		$title = trim($sku3[0], '[');
		$sku = trim($sku3[1], ']');
		
		$data = array();
		$data['tilte'] = $title;
		$data['sku'] = $sku;
		
		return $data;
    }
	
	public function limit_text($text, $limit) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '...';
      }
      return $text;
    }
	
	public function findText($mystring, $findme){
		$pos = strpos($mystring, $findme);
		if ($pos === false) {
			return false;
		}
		return true;
	}
	
	public function showPrice($price){
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		return $objectManagerr->get('\Magento\Framework\Pricing\Helper\Data')->currency($price, true, false);
	}
	
	public function getBcolorHover() {
        return $this->scopeConfig->getValue('megamenu/color_setting/bcolor_hover', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getTextColor() {
        return $this->scopeConfig->getValue('megamenu/color_setting/text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getTextColorHover() {
        return $this->scopeConfig->getValue('megamenu/color_setting/text_color_hover', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getTextProductColor() {
        return $this->scopeConfig->getValue('megamenu/color_setting/text_product_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getPriceColor() {
        return $this->scopeConfig->getValue('megamenu/color_setting/price_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function showHomeLink(){
		return $this->scopeConfig->getValue('megamenu/mainmenu/home', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getPosition(){
		return $this->scopeConfig->getValue('megamenu/mainmenu/menu_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getHeaderType() {
        return $this->scopeConfig->getValue('sun_settings/header/header_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
