<?php

/**
 * Productvideo data helper
 */
namespace Netbaseteam\Productvideo\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to store config where count of productvideo posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE     = 'productvideo/view/items_per_page';
    
    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH    = 'Productvideo';

    /**
     * Maximum size for image in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 1048576000;

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
     * Remove Productvideo item image by image filename
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
     * Return URL for resized Productvideo Item Image
     *
     * @param Netbaseteam\Productvideo\Model\Productvideo $item
     * @param integer $width
     * @param integer $height
     * @return bool|string
     */
    public function resize(\Netbaseteam\Productvideo\Model\Productvideo $item, $width, $height = null)
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
            /* if (!$adapter->isValid($scope)) {
                throw new \Magento\Framework\Model\Exception(__('Uploaded image is not valid.'));
            } */
            
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $scope]);
			if($scope == "local_video") {
				$uploader->setAllowedExtensions(['mp3', 'mp4']);
			} else {
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            }
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
     * Return the base media directory for Productvideo Item images
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
     * Return the Base URL for Productvideo Item images
     *
     * @return string
     */
    public function getBaseUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . '/' . self::MEDIA_PATH;
    }
	
	public function getCurrentStoreId()
    { 
		return $this->_storeManager->getStore()->getStoreId(); 
    }
    
    /**
     * Return the number of items per page
     * @return int
     */
    public function getProductvideoPerPage()
    {
        return abs((int)$this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function getMaxVideoSize()
    {
        return abs($this->scopeConfig->getValue(
		"productvideo/view/max_file_size"
		, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function showRatingVideo()
    {
        return abs((int)$this->scopeConfig->getValue(
		"productvideo/view/show_rate_video"
		, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function showUploadVideo()
    {
        return abs((int)$this->scopeConfig->getValue(
		"productvideo/view/show_upload_video"
		, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function getAllowCustomerGroup()
    {
        return abs((int)$this->scopeConfig->getValue(
		"productvideo/view/allowed_groups"
		, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }
	
	public function _createProductvideoFolder(){
		$base_dir = $this->getBaseDir();
		if (!file_exists($base_dir)){
			mkdir($base_dir, 0777);
		}
	}
	


    public function videoType($url) {
      if (strpos($url, 'youtube') !== false) {
        return 'youtube';
      } elseif (strpos($url, 'vimeo') !== false) {
        return 'vimeo';
      } elseif (strpos($url, 'dailymotion') !== false) {
        return 'dailymotion';
      } elseif (strpos($url, 'twitch') !== false) {
        return 'twitch';
      } else {
        return 'unknown';
      }
     }
	
	public function getVideoInforFromURL($url){
		$resultData = array(); $videoName = ""; $thumbnailUrl = ""; $videoId = 0;
		if(strpos($url, 'youtu')!==FALSE){
			$videoData['provider'] = 'youtube';
			$youtubeId  = '';
			
			/* GET YOUTUBE VIDEO ID */
			if(strpos($url,'youtube.com')!==FALSE){
			
				/* http://www.youtube.com/embed/VIDEOID
				http://www.youtube.com/embed/VIDEOID?modestbranding=1&amp;rel=0
				http://www.youtube.com/v/VIDEO-ID?fs=1&amp;hl=en_US */
				$videoIdRegex = '/youtube.com\/(?:embed|v){1}\/([a-zA-Z0-9_\-]+)\??/i';
				if(!preg_match($videoIdRegex, $url, $results)){
					/* http://www.youtube.com/watch?v=VIDEOID */
					$videoIdRegex = '/youtube.com\/(?:watch\?v=){1}([a-zA-Z0-9_\-]+)\??/i';
				}
				
			} elseif(strpos($url,'youtu.be') !== FALSE){
				/* http://youtu.be/VIDEOID */
				$videoIdRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
			}
			
			preg_match($videoIdRegex, $url, $results);
			$youtubeId = $results[1];

			$content = file_get_contents("http://youtube.com/get_video_info?video_id=".$youtubeId);
			parse_str($content, $youtube);
			if($youtubeId!='' && $youtube['title']){
				$videoId = $youtubeId;				
				$videoName = $youtube['title'];							
				$thumbnailUrl = $youtube['thumbnail_url'];
			}else{
				$resultData['error'] = __('Video URL invalid');
			}
		}elseif(strpos($url, 'vimeo')!==FALSE){
			$videoData['provider'] = 'vimeo';
			$vimeoId = '';
			
			/* GET VIMEO VIDEO ID */
			if(strpos($url, 'player.vimeo.com')!==FALSE){
				/* http://player.vimeo.com/video/VIDEOID?title=0&amp;byline=0&amp;portrait=0 */
				$videoIdRegex = '/player.vimeo.com\/video\/([0-9]+)\??/i';
			} else {
				/* http://vimeo.com/VIDEOID */
				$videoIdRegex = '/vimeo.com\/([0-9]+)\??/i';
			}
			
			preg_match($videoIdRegex, $url, $results);
			$vimeoId = $results[1];
			$vimeoData = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.$vimeoId.'.php'));
			$vimeoData = $vimeoData[0];
			
			if($vimeoId!='' && $vimeoData['title']){
				$videoId = $vimeoId;
				$videoName = $vimeoData['title'];
				$thumbnailUrl = $vimeoData['thumbnail_large'];	
			}else{
				$resultData['error'] = __('Video URL invalid');
			}
		} elseif(strpos($url, 'dailymotion')!==FALSE){
			$videoData['provider'] = 'dailymotion';
			$videoIdRegex = '/video\/([a-zA-Z0-9]+)/i';
			preg_match($videoIdRegex, $url, $results);
			$dailymotionId = $results[1];
			$dailymotionData = file_get_contents('https://api.dailymotion.com/video/'. $dailymotionId.'?fields=id,title,description,thumbnail_medium_url');
			$get_data = json_decode($dailymotionData, TRUE);
			
			if($dailymotionId!='' && $get_data['title']){
				$videoId = $dailymotionId;
				$videoName = $get_data['title'];
				$thumbnailUrl = $get_data['thumbnail_medium_url'];
				
			}else{
				$resultData['error'] = $this->__('Video URL invalid');
			}
		} elseif(strpos($url, 'twitch')!==FALSE){
			$videoData['provider'] = 'twitch';
			$videoIdRegex = '/twitch.tv\/([\w]+)/i';
			preg_match($videoIdRegex, $url, $results);
			
			$twitchId = $results[1];
			
			/* $dataArray = json_decode(@file_get_contents('https://api.twitch.tv/kraken/streams?channel=' . $twitchId), true);
		
			foreach($dataArray['streams'] as $mydata){
				 if($mydata['_id'] != null){
					$title      = $mydata['channel']['display_name'];
					$video_banner   = $mydata['channel']['video_banner'];
				}
			} */
			
			$videoId = $twitchId;
			$title = "";
			if($twitchId!='' && $title){
				$videoId = $twitchId;
				$videoName = $title;
				$thumbnailUrl = $video_banner;
				
			}else{
				$resultData['error'] = __('Video URL invalid');
			}
		}
		
		$resultData['video_id'] = $videoId;
		$resultData['video_title'] = $videoName;
		$resultData['video_thumb'] = $thumbnailUrl;
		
		return $resultData;
	}
	
	public function getExtensionFile($file_name) {
        $filetype = explode('.', $file_name);
        $file_exten = $filetype[count($filetype) - 1];
        return $file_exten;
    }
	
	public function get_user_browser(){
		$browser = "other";
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/(?i)msie|trident|edge/",$_SERVER['HTTP_USER_AGENT'])) {
			$browser = "ie";
		}
		
		return $browser;
	}
}
