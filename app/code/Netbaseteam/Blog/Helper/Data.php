<?php

/**
 * Blog data helper
 */
namespace Netbaseteam\Blog\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of blog posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'blog/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'blog';

    /**
     * Maximum size for image in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 10485760;

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
    const MAX_HEIGHT = 10240;

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
    const MAX_WIDTH = 10240;

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


    protected $_backendUrl;
    protected $_adapterImageFactory;
    
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
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Image\AdapterFactory $adapterImageFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_backendUrl = $backendUrl;
        $this->_adapterImageFactory = $adapterImageFactory;
        parent::__construct($context);
    }
    
    /**
     * Remove Blog item image by image filename
     *
     * @param string $imageFile
     * @return bool
     */
    public function removeImage($imageFile,$scope)
    {
        $io = $this->_ioFile;
        $io->open(array('path' => $this->getBaseDir($scope)));
        if ($io->fileExists($imageFile)) {
            return $io->rm($imageFile);
        }
        return false;
    }
    
    /**
     * Return URL for resized Blog Item Image
     *
     * @param Netbaseteam\Blog\Model\Blog $item
     * @param integer $width
     * @param integer $height
     * @return bool|string
     */
    public function resize(\Netbaseteam\Blog\Model\Post $item, $width, $height = null)
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
            
            if ($uploader->save($this->getBaseDir($scope))) {
                return $uploader->getUploadedFileName();
            }
        }
        return false;
    }
    
    /**
     * Return the base media directory for Blog Item images
     *
     * @return string
     */
    public function getBaseDir($scope)
    {
        if($scope == 'thumbnail'){
            $path = $this->filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(self::MEDIA_PATH.'/thumbnail/');  
        }elseif($scope == 'image'){
            
            $path = $this->filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(self::MEDIA_PATH.'/image/'); 
        }elseif($scope == 'author_avatar'){
            $path = $this->filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(self::MEDIA_PATH.'/author_avatar/');
        }
        elseif($scope == 'category_image'){
            $path = $this->filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(self::MEDIA_PATH.'/category_image/');
        }elseif($scope == 'feature_image'){
            $path = $this->filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(self::MEDIA_PATH.'/feature_image/');
        }


        return $path;
    }
    
    /**
     * Return the Base URL for Blog Item images
     *
     * @return string
     */
    public function getBaseMediaUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . '/' . self::MEDIA_PATH;
    }
    
    /**
     * Return the number of items per page
     * @return int
     */
    public function getBlogPerPage()
    {
        return abs((int)$this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getSelectPostGridUrl(){
        return $this->_backendUrl->getUrl('blog/category/postlist', ['_current' => true]);
    }

    public function getRelatedPostUrl(){
        return $this->_backendUrl->getUrl('blog/post/relatedlist', ['_current' => true]);
    }


    public function getCommentListUrl(){
        return $this->_backendUrl->getUrl('blog/post/commentlist', ['_current' => true]);
    }


    public function getBaseFontUrl(){
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getStoreviewId(){
        return $this->_storeManager->getStore()->getId();
    }

    public function getPreBlogUrl(){
        return $this->getBaseFontUrl().'blog';
    }

    public function getPrePostThumbUrl(){
        return $this->getBaseMediaUrl().'/thumbnail/';
    }

    public function getPrePostImageUrl(){
        return $this->getBaseMediaUrl().'/image/';
    }
    public function getPreFeatureImageUrl(){
        return $this->getBaseMediaUrl().'/feature_image/';
    }
    public function getPreCategoryImageUrl(){
        return $this->getBaseMediaUrl().'/category_image/';
    }

    public function getFormatDate($date){

        $dateStr = date("F j, Y", strtotime($date));
        $m = substr($dateStr,0,3);
        $endStr = explode(" ", $dateStr);
        return $m.' '.$endStr[1].' '.$endStr[2]; 
    }

    public function getPreAuthorAvatarUrl(){
        return $this->getBaseMediaUrl().'/author_avatar/';
    }

    public function getProductsGridUrl(){
        return $this->_backendUrl->getUrl('blog/post/products', ['_current' => true]);
    }

    /*
        Get Configuration data

    */
    public function getConfigEnabled(){
        return $this->scopeConfig->getValue('blog/view/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showRecentPost(){
        return $this->scopeConfig->getValue('blog/blog_page/show_recent_post', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showRecentComment(){
        return $this->scopeConfig->getValue('blog/blog_page/show_comment_recent', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showCategoryPost(){
        return $this->scopeConfig->getValue('blog/blog_page/show_category_post', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showTagPost(){
        return $this->scopeConfig->getValue('blog/blog_page/show_tag_post', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    

    public function getMaxPostRecent(){
        return $this->scopeConfig->getValue('blog/blog_page/max_recent_post', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMaxCategoryInSidebar(){
        return $this->scopeConfig->getValue('blog/blog_page/max_category_sidebar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMaximunNumberTag(){
        return $this->scopeConfig->getValue('blog/blog_page/max_number_tag', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMaxCommentRecent(){
        return $this->scopeConfig->getValue('blog/blog_page/max_comment_recent', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    
    public function getNumPostPerPage(){
        return $this->scopeConfig->getValue('blog/blog_page/number_post_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    

    public function getListPostStyle(){
        return $this->scopeConfig->getValue('blog/blog_page/post_list_style', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getConfigShortContent(){
        return $this->scopeConfig->getValue('blog/blog_page/max_num_short_content', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEnableCommentByAccount(){
        return $this->scopeConfig->getValue('blog/blog_post_view/comment_by_acount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEnableCaptchaValidate(){
        return $this->scopeConfig->getValue('blog/blog_post_view/captcha_validate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCustomerAvatar(){
        $avatar = $this->scopeConfig->getValue('blog/blog_post_view/customer_avatar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($avatar)) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).'netbaseteam/Blog/customer_avatar/'.$avatar;
            return $url;
        }
        return false;
    }

    public function getAdminAvatar(){
        $avatar = $this->scopeConfig->getValue('blog/blog_post_view/admin_avatar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($avatar)) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).'netbaseteam/Blog/admin_avatar/'.$avatar;
            return $url;
        }
        return false;
    }

    public function resizeImg($image, $width, $height)
    {

        $absolutePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('blog/image/').$image;
      

        $imageResized = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'/').$image;

         //var_dump($imageResized);die;

        if(!empty($image)){
            $imageResize = $this->_adapterImageFactory->create();         
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(TRUE);         
            $imageResize->keepTransparency(TRUE);         
            $imageResize->keepFrame(FALSE);         
            $imageResize->keepAspectRatio(FALSE);         
            $imageResize->resize($width,$height);  
            //destination folder                
            $destination = $imageResized ;    
            //save image      
            $imageResize->save($destination);
        }         

        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'/'.$image;

        return $resizedURL;
    }
    
}
