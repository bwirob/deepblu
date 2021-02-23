<?php
namespace Netbaseteam\Ajaxsearch\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * Path to store config where count of ajaxsearch posts per page is stored
	 *
	 * @var string
	 */
	const XML_PATH_ITEMS_PER_PAGE = 'ajaxsearch/view/items_per_page';

	/**
	 * Media path to extension images
	 *
	 * @var string
	 */
	const MEDIA_PATH = 'Ajaxsearch';

	const EXT_NAME = 'ajaxsearch';

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
	protected $_imageSize = array(
		'minheight' => self::MIN_HEIGHT,
		'minwidth' => self::MIN_WIDTH,
		'maxheight' => self::MAX_HEIGHT,
		'maxwidth' => self::MAX_WIDTH,
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

	protected $_objectManager;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\File\Size $fileSize,
		\Magento\Framework\ObjectManagerInterface $objectManager,
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
		$this->_objectManager = $objectManager;
		parent::__construct($context);
	}

	/**
	 * Remove Ajaxsearch item image by image filename
	 *
	 * @param string $imageFile
	 * @return bool
	 */
	public function removeImage($imageFile) {
		$io = $this->_ioFile;
		$io->open(array('path' => $this->getBaseDir()));
		if ($io->fileExists($imageFile)) {
			return $io->rm($imageFile);
		}
		return false;
	}

	/**
	 * Return URL for resized Ajaxsearch Item Image
	 *
	 * @param Netbaseteam\Ajaxsearch\Model\Ajaxsearch $item
	 * @param integer $width
	 * @param integer $height
	 * @return bool|string
	 */
	public function resize(\Netbaseteam\Ajaxsearch\Model\Ajaxsearch $item, $width, $height = null) {
		if (!$item->getImage()) {
			return false;
		}

		if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
			return false;
		}
		$width = (int) $width;

		if (!is_null($height)) {
			if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
				return false;
			}
			$height = (int) $height;
		}

		$imageFile = $item->getImage();
		$cacheDir = $this->getBaseDir() . '/' . 'cache' . '/' . $width;
		$cacheUrl = $this->getBaseUrl() . '/' . 'cache' . '/' . $width . '/';

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
	public function uploadImage($scope) {
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
	 * Return the base media directory for Ajaxsearch Item images
	 *
	 * @return string
	 */
	public function getBaseDir() {
		$path = $this->filesystem->getDirectoryRead(
			DirectoryList::MEDIA
		)->getAbsolutePath(self::MEDIA_PATH);
		return $path;
	}

	/**
	 * Return the Base URL for Ajaxsearch Item images
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_storeManager->getStore()->getBaseUrl();
	}

	/**
	 * Return the number of items per page
	 * @return int
	 */
	public function getAjaxsearchPerPage() {
		return abs((int) $this->scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
	}

	/*
		        Helper for custom ajax search extention
	*/
	public function getEnableConfig() {
		return abs((int) $this->scopeConfig->getValue('ajaxsearch/setting/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
	}

	public function getWidthPopup() {

		return abs((int) $this->scopeConfig->getValue('ajaxsearch/setting/width_popup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
	}

	public function getRequestTime() {

		return abs((int) $this->scopeConfig->getValue('ajaxsearch/setting/request_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
	}

	public function getMinchar() {

		return abs((int) $this->scopeConfig->getValue('ajaxsearch/setting/minchar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
	}

	/* public function getNoResultText(){
		        return $this->scopeConfig->getValue('ajaxsearch/setting/no_result_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	*/

	public function getNumProductShow() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/number_product_show', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getShowImage() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/show_product_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getShowDescription() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/show_description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getShowRating() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/show_rating', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getShowReview() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/show_review', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getSearchByCate() {
		return $this->scopeConfig->getValue('ajaxsearch/search_by_cate/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getShowPrice() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/show_price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getViewAll() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/view_all', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getNumDescription() {
		return $this->scopeConfig->getValue('ajaxsearch/product_preview/num_char_description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getEnableSearchCate() {
		return $this->scopeConfig->getValue('ajaxsearch/cate_preview/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getHightlightColor() {
		return $this->scopeConfig->getValue('ajaxsearch/setting/hightlight_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getProductSearch() {

		$q = $this->getRequest()->getParam('q');
		$prodCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

		$productCollection = $prodCollection->create()
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', array('eq' => '1'))
			->addAttributeToFilter('visibility', array('neq' => '1'))
			->addAttributeToFilter(
				array(
					array('attribute' => 'name', 'like' => '%' . $q . '%'),
					array('attribute' => 'description', 'like' => '%' . $q . '%'),
					array('attribute' => 'sku', 'eq' => $q),
				)

			);

		if (!empty($this->getRequest()->getParam('nbsearch'))) {
			$cat_ids_arr = explode(",", $this->getRequest()->getParam('cat'));
			$productCollection->addCategoriesFilter(['in' => $cat_ids_arr]);
		}

		return $productCollection;
	}

	public function getBaseMediaUrl() {
		return $this->_storeManager->getStore()->getBaseUrl(
			\Magento\Framework\UrlInterface::URL_TYPE_MEDIA
		);
	}

	public function top_get_categories() {
		$category = $this->_objectManager->create('Magento\Catalog\Model\Category');
		$tree = $category->getTreeModel();
		$tree->load();
		$ids = $tree->getCollection()->addFieldToFilter('is_active', array('eq' => '1'))->getAllIds();
		$arr = array();
		$arrayCategories = array();
		if ($ids) {
			foreach ($ids as $id) {
				$cat = $this->_objectManager->create('Magento\Catalog\Model\Category');
				$cat->load($id);

				$arrayCategories[$id] =
				array("parent_id" => $cat->getParentId(),
					"name" => $cat->getName(),
					"cat_id" => $cat->getId(),
					"cat_level" => $cat->getLevel(),
					"cat_url" => $cat->getUrl(),
				);

			} // for each ends
			return $arrayCategories;
		}
	}

	public function top_createTree($array, $currentParent, $currLevel = 0, $prevLevel = -1) {
		$str = '';
		$catalogSession = $this->_objectManager->create('Magento\Catalog\Model\Session');
		$selected_arr = explode(",", $catalogSession->getTopCatIds());
		foreach ($array as $categoryId => $category) {
			if ($currentParent == $category['parent_id']) {
				if ($currLevel > $prevLevel) {
					$str .= "<ul id='ul_" . $categoryId . "'>";
				}

				if ($currLevel == $prevLevel) {
					$str .= " </li> ";
				}

				$chk = "";
				if (in_array($categoryId, $selected_arr)) {
					$chk = ' checked="true"';
				}

				$str .= '
                <li id="li_' . $categoryId . '">
                    ';
				if ($categoryId > 1) {
					$str .= '<input type="checkbox" class="mytree-l-tcb" id="ext-gen' . $categoryId . '" ' . $chk . ' value="' . $categoryId . '" onClick="myCatSelect(this)">';
				}

				$str .= '<a id="a_' . $categoryId . '" target="_blank" >';
				$str .= '<span id="span_' . $categoryId . '">' . $category['name'] . '</span>';
				$str .= '</a>';
				if ($currLevel > $prevLevel) {
					$prevLevel = $currLevel;
				}
				$currLevel++;
				$this->top_createTree($array, $categoryId, $currLevel, $prevLevel);
				$currLevel--;
			}
		}
		if ($currLevel == $prevLevel) {
			$str .= " </li></ul> ";
		}
		return $str;
	}

}
