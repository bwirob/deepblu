<?php

/**
 * Orderupload data helper
 */
namespace Netbaseteam\Orderupload\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of orderupload posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'orderupload/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'Orderupload';

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
    protected $_productFactory;
    protected $layoutFactory;
    protected $requestInterface;
    
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
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_productFactory = $productFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }
    
    /**
     * Remove Orderupload item image by image filename
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
     * Return URL for resized Orderupload Item Image
     *
     * @param Netbaseteam\Orderupload\Model\Orderupload $item
     * @param integer $width
     * @param integer $height
     * @return bool|string
     */
    public function resize(\Netbaseteam\Orderupload\Model\Orderupload $item, $width, $height = null)
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
     * Return the base media directory for Orderupload Item images
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
     * Return the Base URL for Orderupload Item images
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
    public function getOrderuploadPerPage()
    {
        return abs((int)$this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
    
    public function _getProductData($sku){
        $product = $this->_productFactory->create();
        return $product->loadByAttribute('sku', $sku);
    }
    
    public function _LoadProductById($id){
        $product = $this->_productFactory->create();
        return $product->load($id);
    }
    
    public function getMaxFileSize() {
        return $this->scopeConfig->getValue('orderupload/view/max_file_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function isShowDownload() {
        return $this->scopeConfig->getValue('orderupload/view/show_download', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function isShowComment() {
        return $this->scopeConfig->getValue('orderupload/view/show_comment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function isShowUploadCheckout() {
        return $this->scopeConfig->getValue('orderupload/view/show_on_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function isShowUploadViewOrderFrontend() {
        return $this->scopeConfig->getValue('orderupload/view/show_on_viewoder_frontend', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function allowMultiFile() {
        return $this->scopeConfig->getValue('orderupload/view/allow_multi_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function allowFileTypes() {
        $file_types = $this->scopeConfig->getValue('orderupload/view/file_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $result = str_replace(" ", "", $file_types);
        return $result;
    }
    
    public function isEnable() {
        return $this->scopeConfig->getValue('orderupload/view/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function showOrderUploadOnCheckoutPage($_item){
        $show_on_checkout = $this->isShowUploadCheckout();
        if($show_on_checkout){
            return $this->layoutFactory->create()->createBlock('Magento\Framework\View\Element\Template')
                ->setData('block_data', $_item->getProductId()."|".$_item->getSku()."|".$_item->getSessionFile())
                ->setTemplate('Netbaseteam_Orderupload::checkout.phtml')->toHtml(); 
        } else {
            return "";
        }
    }
    
    public function showOrderUploadOnSaleViewPageFrontend($_item){
        $show_on_view_order = $this->isShowUploadViewOrderFrontend();       
        if($show_on_view_order){
            return $this->layoutFactory->create()->createBlock('Magento\Framework\View\Element\Template')
                        ->setData('block_data', $_item->getProductId()."|".$_item->getSku()."|".$_item->getSessionFile())
                        ->setTemplate('Netbaseteam_Orderupload::checkout.phtml')->toHtml();  
        } else {
            return "";
        }
    }
    
    public function showOrderUploadOnSaleViewPageBackend($_item){
        return $this->layoutFactory->create()->createBlock('Magento\Framework\View\Element\Template')
                    ->setData('block_data', $_item->getProductId()."|".$_item->getSku()."|".$_item->getSessionFile())
                    ->setTemplate('Netbaseteam_Orderupload::checkout.phtml')->toHtml();
    }
    
    public function showOrderUploadDetailPage(){
        return $this->layoutFactory->create()->createBlock('Magento\Framework\View\Element\Template')
                    ->setTemplate('Netbaseteam_Orderupload::orderupload.phtml')->toHtml(); 
    }
    
    public function getMediaUrl(){
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
    
    public function getPathPage(){
        $request = $this->_getRequest();
        $routeName      = $request->getRouteName();
        $moduleName     = $request->getModuleName(); 
        $controllerName = $request->getControllerName(); 
        $actionName     = $request->getActionName();
        $path = $moduleName."/".$controllerName."/".$actionName;
        return $path;
    }
    
    public function text_limit($str,$limit=5)
    {
        if(stripos($str," ")!==false){
            $ex_str = explode(" ",$str);
            if(count($ex_str)>$limit){
                for($i=0;$i<$limit;$i++){
                    $str_s.=$ex_str[$i]." ";
                }
                return $str_s;
            }else{
                return $str;
            }
        }else{
            return $str;
        }
    }
}
