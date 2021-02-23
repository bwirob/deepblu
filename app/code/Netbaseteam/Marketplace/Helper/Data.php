<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ATTRIBUTE_SET_ID = 'netbaseteammp/product_settings/attributesetid';
    const ALLOWED_PRODUCT_TYPE = 'netbaseteammp/product_settings/allow_product_type';
    const ALLOWED_SKU_TYPE = 'netbaseteammp/product_settings/sku_type';
    const ALLOWED_SKU_PREFIX = 'netbaseteammp/product_settings/sku_prefix';
    const TERM_AND_CONDITION = 'netbaseteammp/account/term_and_condition';
    const PRODUCT_APPROVAL = 'netbaseteammp/product_settings/product_approval';
    const ORDER_APPROVAL = 'netbaseteammp/order_settings/order_approval';
    const ADMIN_EMAIL_ID = 'netbaseteammp/store_settings/adminemail';
    const PRODUCT_EMAIL_TEMPLATE = 'netbaseteammp/email_setting/product_email_template';
    const ACCOUNT_EMAIL_TEMPLATE = 'netbaseteammp/email_setting/account_email_template';
    const CONTACT_EMAIL_TEMPLATE = 'netbaseteammp/email_setting/contact_email_template';
    const SELLER_EMAIL_TEMPLATE = 'netbaseteammp/email_setting/seller_email_template';
    const ADMIN_PRODUCT_EMAIL_TEMPLATE = 'netbaseteammp/email_setting/admin_product_email_template';
    const PAY_SELLER_EMAIL_TEMPLATE = 'netbaseteammp/email_setting/pay_seller_email_template';
    const ADMIN_LANDING_PAGE = 'netbaseteammp/landing_page/layout_type';
    const ADMIN_LANDINGPAGE_HOTSELLER = 'netbaseteammp/landing_page/seller_id';
    const ADMIN_COMMISSION_AMOUNT = 'netbaseteammp/commission/amount';
    const ADMIN_COMMISSION_OPTION = 'netbaseteammp/commission/fixed_or_percentage';
    const ADMIN_COMMISSION_TYPE = 'netbaseteammp/commission/type';
    const LOCATOR_ENABLE = 'netbaseteammp/locator/enable';
    const LOCATOR_DISTANCE_UNIT = 'netbaseteammp/locator/distance_unit';
    const LOCATOR_GOOGLE_API_KEY = 'netbaseteammp/locator/google_api';
    const VACATION_ENABLE = 'netbaseteammp/vacation/enable';

    protected $_storeManager;

    protected $_template;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    protected $_customerSession;

    protected $_assetRepo;

    protected $_directory_list;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;


    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_localeCurrency = $localeCurrency;
        $this->_assetRepo = $assetRepo;
        $this->_scopeConfig = $scopeConfig;
        $this->_directory_list = $directory_list;
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context);
        $this->_messageManager = $messageManager;
        $this->_url = $url;
    }

    public function _deleteContent2File()
    {
        $varDir = $this->_directory_list->getPath('var') . '/config/licenseX.json';
        unlink($varDir);
    }

    public function _writeContent2File($content)
    {
        $varDir = $this->_directory_list->getPath('var') . '/config';
        $filePath = $varDir . '/licenseX.json';
        if (!is_dir($varDir)) {
            mkdir($varDir, 0777, true);
        }
        $myFile = fopen($filePath, 'w+');
        realpath($filePath);

        fwrite($myFile, $content);
        fclose($myFile);
    }

    public function _readFile($pathFile)
    {
        if (!is_dir($this->_directory_list->getPath('var') . '/config')) {
            mkdir($this->_directory_list->getPath('var') . '/config', 0777, true);
            fopen($pathFile, 'w+');
            realpath($pathFile);
        }
        $fp = @fopen($pathFile, "r");
        if (!$fp) {
            fopen($pathFile, 'w+');
            realpath($pathFile);
        }
        return filesize($pathFile) ? fread($fp, filesize($pathFile)) : '';
    }

    public function _getUrlFile($path_file)
    {
        return $this->_assetRepo->getUrl($path_file);
    }

    public function isLoggedIn(){
        return $this->_customerSession->isLoggedIn();
    }

    public function storeImageExtension(){
        return $this->scopeConfig->getValue(self::UPLOAD_IMAGE_TYPE);
    }

    public function getAllowedAttributesetIds(){
        $current_store = $this->_storeManager->getStore();
        return $this->scopeConfig->getValue(self::ATTRIBUTE_SET_ID);
    }

    public function getCurrencySymbol()
    {
        return $this->_localeCurrency->getCurrency(
            $this->getBaseCurrencyCode()
        )->getSymbol();
    }

    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    public function getAllowedProductTypes(){
        return $this->scopeConfig->getValue(self::ALLOWED_PRODUCT_TYPE);
    }

    public function getAllowedSkuTypes(){
        return $this->scopeConfig->getValue(self::ALLOWED_SKU_TYPE);
    }

    public function getSkuPrefix(){
        return $this->scopeConfig->getValue(self::ALLOWED_SKU_PREFIX);
    }

    public function getCommissionAmount(){
        return $this->scopeConfig->getValue(self::ADMIN_COMMISSION_AMOUNT);
    }

    public function getCommissionOption(){
        return $this->scopeConfig->getValue(self::ADMIN_COMMISSION_OPTION);
    }

    public function getCommissionType(){
        return $this->scopeConfig->getValue(self::ADMIN_COMMISSION_TYPE);
    }

    public function getLandingPage(){
        return $this->scopeConfig->getValue(self::ADMIN_LANDING_PAGE);
    }

    public function getHotsellers(){
        return $this->scopeConfig->getValue(self::ADMIN_LANDINGPAGE_HOTSELLER);
    }

    public function getSellerId(){
        $customerId = $this->_customerSession->getCustomerId();

        $model = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Seller');

        $seller = $model->getCollection()
            ->addFieldToFilter('seller_id',$customerId)
            ->addFieldToFilter('status',1);
        if ($seller->getData()) {
            return $seller->getData()[0]['seller_id'];
        } else {
            return '';
        }
    }

    public function getIsProductApproval(){
        return $this->scopeConfig->getValue(self::PRODUCT_APPROVAL);
    }

    public function getIsOrderApproval(){
        return $this->scopeConfig->getValue(self::ORDER_APPROVAL);
    }

    /**
     * Return the authorize seller status.
     *
     * @return bool|0|1
     */
    public function isCorrectSeller($productId)
    {
        $data = 0;
        if ($productId) {
            $model = $this->_objectManager->create(
                'Netbaseteam\Marketplace\Model\Product'
            )
                ->getCollection()
                ->addFieldToFilter(
                    'product_id',
                    $productId
                )->addFieldToFilter(
                    'seller_id',
                    $this->_customerSession->getCustomerId()
                );

            if ($model->getData()) {
                $data = 1;
            }
        }

        return $data;
    }

    /**
     * Retrieve YouTube API key
     *
     * @return string
     */
    public function getYouTubeApiKey()
    {
        return $this->scopeConfig->getValue(
            'catalog/product_video/youtube_api_key'
        );
    }

    public function getWebsiteId()
    {
        return $this->_storeManager->getStore(true)->getWebsite()->getId();
    }

    public function getSingleStoreStatus()
    {
        return $this->_storeManager->hasSingleStore();
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    public function getTermandCondition() {
        return $this->scopeConfig->getValue(self::TERM_AND_CONDITION);
    }

    public function getAdminEmailId()
    {
        return $this->scopeConfig->getValue(self::ADMIN_EMAIL_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultTransEmailId()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     */
    public function sendProductMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        try {
            $this->_template = $this->getTemplateId(self::PRODUCT_EMAIL_TEMPLATE);

            $this->_inlineTranslation->suspend();
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {
            $this->_messageManager->addException($e, __($e->getMessage()));
        }
    }

    public function sendAccountMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        try {
            $this->_template = $this->getTemplateId(self::ACCOUNT_EMAIL_TEMPLATE);

            $this->_inlineTranslation->suspend();
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {
            $this->_messageManager->addException($e, __($e->getMessage()));
        }
    }

    public function sendContactMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        try {
            $this->_template = $this->getTemplateId(self::CONTACT_EMAIL_TEMPLATE);

            $this->_inlineTranslation->suspend();
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {
            $this->_messageManager->addException($e, __($e->getMessage()));
        }
    }

    public function sendSellerMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        try {
            $this->_template = $this->getTemplateId(self::SELLER_EMAIL_TEMPLATE);

            $this->_inlineTranslation->suspend();
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {
            $this->_messageManager->addException($e, __($e->getMessage()));
        }
    }

    public function sendAdminProductMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        try {
            $this->_template = $this->getTemplateId(self::ADMIN_PRODUCT_EMAIL_TEMPLATE);

            $this->_inlineTranslation->suspend();
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {
            $this->_messageManager->addException($e, __($e->getMessage()));
        }
    }

    public function sendPaysellerMail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        try {
            $this->_template = $this->getTemplateId(self::PAY_SELLER_EMAIL_TEMPLATE);

            $this->_inlineTranslation->suspend();
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch(\Exception $e) {
            $this->_messageManager->addException($e, __($e->getMessage()));
        }
    }

    /**
     * Return template id.
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->scopeConfig->getValue($xmlPath);
    }

    /**
     * [generateTemplate description].
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template = $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    public function isLocatorEnable() {
        return $this->scopeConfig->getValue(self::LOCATOR_ENABLE);
    }

    public function getDistanceUnit() {
        return $this->scopeConfig->getValue(self::LOCATOR_DISTANCE_UNIT);
    }

    public function getGoogleApiKey() {
        if ($this->scopeConfig->getValue(self::LOCATOR_GOOGLE_API_KEY)) {
            return $this->scopeConfig->getValue(self::LOCATOR_GOOGLE_API_KEY);
        } else {
            return "AIzaSyA2R7NEXim1UxTR1O3wbVaI8ma9ad2ziFs";
        }
    }

    public function isVacation()
    {
        return $this->scopeConfig->getValue(self::VACATION_ENABLE);
    }

    /**
     * @return bool
     * limit function if license incorrect
     */
    public function releaseLimit()
    {
        $content_license = $this->_readFile($this->_directory_list->getPath('var') . '/config/licenseX.json');
        if (isset($content_license) && $content_license != "") {
            $arr_license = json_decode($content_license, true);
            if (isset($arr_license['key'])) {
                if ($arr_license['key'] != '') {
                    $resource = $this->_resourceConnection;
                    $connection = $resource->getConnection();
                    $tableName = $resource->getTableName('core_config_data');
                    $sql = "Select * FROM " . $tableName . " where `path`='netbaseteam_license/license/license_code'";
                    $result = $connection->fetchAll($sql);
                    if (count($result) > 0) {
                        $salt = $result[0]['value'];
                        if (!empty($salt)) {
                            if ($salt == $arr_license['key']) {
                                if ($arr_license['type'] == 'free') {
                                    return 'free';
                                }
                                if ($arr_license['type'] == 'pro') {
                                    if ($arr_license['code'] == '3') {
                                        return 'expired';
                                    }
                                    return 'pro';
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * check if data email is null, limit function Marketplace
     */
    public function checkDataEmailToLimitFunction()
    {
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('core_config_data');
        $sql = "Select * FROM " . $tableName . " where `path`='netbaseteam_license/get_license/get_license_code_email'";
        $result = $connection->fetchAll($sql);
        if ($this->releaseLimit()) {
            return true;
        } elseif (count($result) > 0 && isset($result[0]['value']) && $result[0]['value']) {
            if ($this->releaseLimit()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Magento\Framework\Message\ManagerInterface|string
     */
    public function getMessageLicense()
    {
        $version = $this->releaseLimit();
        $error = '';
        if (!$version) {
            $systemUrl = $this->_url->getUrl('adminhtml/system_config/edit/section/netbaseteam_license');
            $error1 = 'WW91ciBsaWNlbnNlIGlzIGluY29ycmVjdC4gUGxlYXNlIGNsaWNr';
            $error2 = "<a target='_blank' href='$systemUrl'> here</a>";
            $error3 = "IHRvIGFjdGl2ZSBFeHRlbnNpb24u";
            $error = base64_decode($error1) . $error2 . base64_decode($error3);
            return $error ? $this->_messageManager->addError($error) : '';
        } elseif ($version == 'free') {
            $error = "WW91IGFyZSB1c2luZyB0aGUgZnJlZSB2ZXJzaW9uLiBUbyB1cGdyYWRlIHRvIHRoZSBwcmVtaXVtIHZlcnNpb24sIHBsZWFzZSBjbGljayA8YSB0YXJnZXQ9J2JsYW5rJyBocmVmPSdodHRwczovL2Ntc21hcnQubmV0L21hZ2VudG8tMi1leHRlbnNpb25zL21hZ2VudG8tbXVsdGktdmVuZG9yJz5oZXJlPC9hPi4=";
            return $error ? $this->_messageManager->addError(base64_decode($error)) : '';
        }
    }

    public function getMessageLicenseFrontend()
    {
        $version = $this->releaseLimit();
        $error = '';
        if ($version == 'free') {
            $error = "WW91IGFyZSB1c2luZyB0aGUgZnJlZSB2ZXJzaW9uLiBUbyB1cGdyYWRlIHRvIHRoZSBwcmVtaXVtIHZlcnNpb24sIHBsZWFzZSBjbGljayA8YSB0YXJnZXQ9J2JsYW5rJyBocmVmPSdodHRwczovL2Ntc21hcnQubmV0L21hZ2VudG8tMi1leHRlbnNpb25zL21hZ2VudG8tbXVsdGktdmVuZG9yJz5oZXJlPC9hPi4=";
        }
        return $error ? $this->_messageManager->addError(base64_decode($error)) : '';
    }

    public function nbadmintheme_get_ip()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
    }

    /**
     * Get google fonts list
     *
     * @return array
     */
    public function getFontsList()
    {
        return array(
            'ABeeZee',
            'Abel',
            'Abril Fatface',
            'Aclonica',
            'Acme',
            'Actor',
            'Adamina',
            'Advent Pro',
            'Aguafina Script',
            'Akronim',
            'Aladin',
            'Aldrich',
            'Alef',
            'Alegreya',
            'Alegreya SC',
            'Alegreya Sans',
            'Alegreya Sans SC',
            'Alex Brush',
            'Alfa Slab One',
            'Alice',
            'Alike',
            'Alike Angular',
            'Allan',
            'Allerta',
            'Allerta Stencil',
            'Allura',
            'Almendra',
            'Almendra Display',
            'Almendra SC',
            'Amarante',
            'Amaranth',
            'Amatic SC',
            'Amethysta',
            'Anaheim',
            'Andada',
            'Andika',
            'Angkor',
            'Annie Use Your Telescope',
            'Anonymous Pro',
            'Antic',
            'Antic Didone',
            'Antic Slab',
            'Anton',
            'Arapey',
            'Arbutus',
            'Arbutus Slab',
            'Architects Daughter',
            'Archivo Black',
            'Archivo Narrow',
            'Arimo',
            'Arizonia',
            'Armata',
            'Artifika',
            'Arvo',
            'Asap',
            'Asset',
            'Astloch',
            'Asul',
            'Atomic Age',
            'Aubrey',
            'Audiowide',
            'Autour One',
            'Average',
            'Average Sans',
            'Averia Gruesa Libre',
            'Averia Libre',
            'Averia Sans Libre',
            'Averia Serif Libre',
            'Bad Script',
            'Balthazar',
            'Bangers',
            'Basic',
            'Battambang',
            'Baumans',
            'Bayon',
            'Belgrano',
            'Belleza',
            'BenchNine',
            'Bentham',
            'Berkshire Swash',
            'Bevan',
            'Bigelow Rules, cursive',
            'Bigshot One',
            'Bilbo',
            'Bilbo Swash Caps',
            'Bitter',
            'Black Ops One',
            'Bokor',
            'Bonbon',
            'Boogaloo',
            'Bowlby One',
            'Bowlby One SC',
            'Brawler',
            'Bree Serif',
            'Bubblegum Sans',
            'Bubbler One',
            'Buda',
            'Buenard',
            'Butcherman',
            'Butterfly Kids',
            'Cabin',
            'Cabin Condensed',
            'Cabin Sketch',
            'Caesar Dressing',
            'Cagliostro',
            'Calligraffitti',
            'Cambo',
            'Candal',
            'Cantarell',
            'Cantata One',
            'Cantora One',
            'Capriola',
            'Cardo',
            'Carme',
            'Carrois Gothic',
            'Carrois Gothic SC',
            'Carter One',
            'Caudex',
            'Cedarville Cursive',
            'Ceviche One',
            'Changa One',
            'Chango',
            'Chau Philomene One',
            'Chela One',
            'Chelsea Market',
            'Chenla',
            'Cherry Cream Soda',
            'Cherry Swash',
            'Chewy',
            'Chicle',
            'Chivo',
            'Cinzel',
            'Cinzel Decorative',
            'Clicker Script',
            'Coda',
            'Coda Caption',
            'Codystar',
            'Combo',
            'Comfortaa',
            'Coming Soon',
            'Concert One',
            'Condiment',
            'Content',
            'Contrail One',
            'Convergence',
            'Cookie',
            'Copse',
            'Corben',
            'Courgette',
            'Cousine',
            'Coustard',
            'Covered By Your Grace',
            'Crafty Girls',
            'Creepster',
            'Crete Round',
            'Crimson Text',
            'Croissant One',
            'Crushed',
            'Cuprum',
            'Cutive',
            'Cutive Mono, monospace',
            'Damion',
            'Dancing Script',
            'Dangrek',
            'Dawning of a New Day',
            'Days One',
            'Delius',
            'Delius Swash Caps',
            'Delius Unicase',
            'Della Respira',
            'Denk One',
            'Devonshire',
            'Didact Gothic',
            'Diplomata',
            'Diplomata SC',
            'Domine',
            'Donegal One',
            'Doppio One',
            'Dorsa',
            'Dosis',
            'Dr Sugiyama',
            'Droid Sans',
            'Droid Sans Mono',
            'Droid Serif',
            'Duru Sans',
            'Dynalight',
            'EB Garamond',
            'Eagle Lake',
            'Eater',
            'Economica',
            'Ek Mukta',
            'Electrolize',
            'Elsie',
            'Elsie Swash Caps',
            'Emblema One',
            'Emilys Candy, cursive',
            'Engagement',
            'Englebert',
            'Enriqueta',
            'Erica One',
            'Esteban',
            'Euphoria Script',
            'Ewert',
            'Exo',
            'Exo 2',
            'Expletus Sans',
            'Fanwood Text',
            'Fascinate',
            'Fascinate Inline',
            'Faster One',
            'Fasthand',
            'Fauna One',
            'Federant, cursive',
            'Federo',
            'Felipa',
            'Fenix',
            'Finger Paint',
            'Fira Mono',
            'Fira Sans',
            'Fjalla One',
            'Fjord One',
            'Flamenco',
            'Flavors',
            'Fondamento',
            'Fontdiner Swanky',
            'Forum',
            'Francois One',
            'Freckle Face',
            'Fredericka the Great',
            'Fredoka One',
            'Freehand',
            'Fresca',
            'Frijole',
            'Fruktur',
            'Fugaz One',
            'GFS Didot',
            'GFS Neohellenic',
            'Gabriela',
            'Gafata',
            'Galdeano',
            'Galindo',
            'Gentium Basic',
            'Gentium Book Basic',
            'Geo',
            'Geostar',
            'Geostar Fill',
            'Germania One',
            'Gilda Display',
            'Give You Glory',
            'Glass Antiqua',
            'Glegoo',
            'Gloria Hallelujah',
            'Goblin One',
            'Gochi Hand',
            'Gorditas',
            'Goudy Bookletter 1911',
            'Graduate',
            'Grand Hotel',
            'Gravitas One',
            'Great Vibes',
            'Griffy',
            'Gruppo',
            'Gudea',
            'Habibi',
            'Halant',
            'Hammersmith One',
            'Hanalei',
            'Hanalei Fill',
            'Handlee',
            'Hanuman',
            'Happy Monkey',
            'Headland One',
            'Henny Penny',
            'Herr Von Muellerhoff',
            'Hind',
            'Holtwood One SC',
            'Homemade Apple',
            'Homenaje',
            'IM Fell DW Pica',
            'IM Fell DW Pica SC',
            'IM Fell Double Pica',
            'IM Fell Double Pica SC',
            'IM Fell English',
            'IM Fell English SC',
            'IM Fell French Canon',
            'IM Fell French Canon SC',
            'IM Fell Great Primer',
            'IM Fell Great Primer SC',
            'Iceberg',
            'Iceland',
            'Imprima',
            'Inconsolata',
            'Inder',
            'Indie Flower',
            'Inika',
            'Irish Grover',
            'Istok Web',
            'Italiana',
            'Italianno',
            'Jacques Francois',
            'Jacques Francois Shadow',
            'Jim Nightshade',
            'Jockey One',
            'Jolly Lodger',
            'Josefin Sans',
            'Josefin Slab',
            'Joti One',
            'Judson',
            'Julee',
            'Julius Sans One',
            'Junge',
            'Jura',
            'Just Another Hand',
            'Just Me Again Down Here',
            'Kalam',
            'Kameron',
            'Kantumruy',
            'Karla',
            'Karma',
            'Kaushan Script',
            'Kavoon',
            'Kdam Thmor',
            'Keania One',
            'Kelly Slab',
            'Kenia',
            'Khand',
            'Khmer',
            'Kite One',
            'Knewave',
            'Kotta One',
            'Koulen',
            'Kranky',
            'Kreon',
            'Kristi',
            'Krona One',
            'La Belle Aurore',
            'Laila',
            'Lancelot',
            'Lato',
            'League Script',
            'Leckerli One',
            'Ledger',
            'Lekton',
            'Lemon',
            'Libre Baskerville',
            'Life Savers',
            'Lilita One',
            'Lily Script One',
            'Limelight',
            'Linden Hill',
            'Lobster',
            'Lobster Two',
            'Londrina Outline',
            'Londrina Shadow',
            'Londrina Sketch',
            'Londrina Solid',
            'Lora',
            'Love Ya Like A Sister',
            'Loved by the King',
            'Lovers Quarrel',
            'Luckiest Guy',
            'Lusitana',
            'Lustria',
            'Macondo',
            'Macondo Swash Caps',
            'Magra',
            'Maiden Orange',
            'Mako',
            'Marcellus',
            'Marcellus SC',
            'Marck Script',
            'Margarine',
            'Marko One',
            'Marmelad',
            'Marvel',
            'Mate',
            'Mate SC',
            'Maven Pro',
            'McLaren',
            'Meddon',
            'MedievalSharp',
            'Medula One',
            'Megrim',
            'Meie Script',
            'Merienda',
            'Merienda One',
            'Merriweather',
            'Merriweather Sans',
            'Metal',
            'Metal Mania',
            'Metamorphous',
            'Metrophobic',
            'Michroma',
            'Milonga',
            'Miltonian',
            'Miltonian Tattoo',
            'Miniver',
            'Miss Fajardose',
            'Modern Antiqua',
            'Molengo',
            'Molle',
            'Monda',
            'Monofett',
            'Monoton',
            'Monsieur La Doulaise',
            'Montaga',
            'Montez',
            'Montserrat',
            'Montserrat Alternates',
            'Montserrat Subrayada',
            'Moul',
            'Moulpali',
            'Mountains of Christmas',
            'Mouse Memoirs',
            'Mr Bedfort',
            'Mr Dafoe',
            'Mr De Haviland',
            'Mrs Saint Delafield',
            'Mrs Sheppards',
            'Muli',
            'Mystery Quest',
            'Neucha',
            'Neuton',
            'New Rocker',
            'News Cycle',
            'Niconne',
            'Nixie One',
            'Nobile',
            'Nokora',
            'Norican',
            'Nosifer',
            'Nothing You Could Do',
            'Noticia Text',
            'Noto Sans',
            'Noto Serif',
            'Nova Cut',
            'Nova Flat',
            'Nova Mono',
            'Nova Oval',
            'Nova Round',
            'Nova Script',
            'Nova Slim',
            'Nova Square',
            'Numans',
            'Nunito',
            'Odor Mean Chey',
            'Offside',
            'Old Standard TT',
            'Oldenburg',
            'Oleo Script',
            'Oleo Script Swash Caps',
            'Open Sans',
            'Open Sans Condensed',
            'Oranienbaum',
            'Orbitron',
            'Oregano',
            'Orienta',
            'Original Surfer',
            'Oswald',
            'Over the Rainbow',
            'Overlock',
            'Overlock SC',
            'Ovo',
            'Oxygen',
            'Oxygen Mono',
            'PT Mono',
            'PT Sans',
            'PT Sans Caption',
            'PT Sans Narrow',
            'PT Serif',
            'PT Serif Caption',
            'Pacifico',
            'Paprika',
            'Parisienne',
            'Passero One',
            'Passion One',
            'Pathway Gothic One',
            'Patrick Hand',
            'Patrick Hand SC',
            'Patua One',
            'Paytone One',
            'Peralta',
            'Permanent Marker',
            'Petit Formal Script',
            'Petrona',
            'Philosopher',
            'Piedra',
            'Pinyon Script',
            'Pirata One',
            'Plaster',
            'Play',
            'Playball',
            'Playfair Display',
            'Playfair Display SC',
            'Podkova',
            'Poiret One',
            'Poller One',
            'Poly',
            'Pompiere',
            'Pontano Sans',
            'Poppins',
            'Port Lligat Sans',
            'Port Lligat Slab',
            'Prata',
            'Preahvihear',
            'Press Start 2P',
            'Princess Sofia',
            'Prociono',
            'Prosto One',
            'Puritan',
            'Purple Purse',
            'Quando',
            'Quantico',
            'Quattrocento',
            'Quattrocento Sans',
            'Questrial',
            'Quicksand',
            'Quintessential',
            'Qwigley',
            'Racing Sans One',
            'Radley',
            'Rajdhani',
            'Raleway',
            'Raleway Dots',
            'Rambla',
            'Rammetto One',
            'Ranchers',
            'Rancho',
            'Rationale',
            'Redressed',
            'Reenie Beanie',
            'Revalia',
            'Ribeye',
            'Ribeye Marrow',
            'Righteous',
            'Risque',
            'Roboto',
            'Roboto Condensed',
            'Roboto Slab',
            'Rochester',
            'Rock Salt',
            'Rokkitt',
            'Romanesco',
            'Ropa Sans',
            'Rosario',
            'Rosarivo',
            'Rouge Script',
            'Rozha One',
            'Rubik Mono One',
            'Rubik One',
            'Ruda',
            'Rufina',
            'Ruge Boogie',
            'Ruluko',
            'Rum Raisin',
            'Ruslan Display',
            'Russo One',
            'Ruthie',
            'Rye',
            'Sacramento',
            'Sail',
            'Salsa',
            'Sanchez',
            'Sancreek',
            'Sansita One',
            'Sarina',
            'Sarpanch',
            'Satisfy',
            'Scada',
            'Schoolbell',
            'Seaweed Script',
            'Sevillana',
            'Seymour One',
            'Shadows Into Light',
            'Shadows Into Light Two',
            'Shanti',
            'Share',
            'Share Tech',
            'Share Tech Mono',
            'Shojumaru',
            'Short Stack',
            'Siemreap',
            'Sigmar One',
            'Signika',
            'Signika Negative',
            'Simonetta',
            'Sintony',
            'Sirin Stencil',
            'Six Caps',
            'Skranji',
            'Slabo 13px',
            'Slabo 27px',
            'Slackey',
            'Smokum',
            'Smythe',
            'Sniglet',
            'Snippet',
            'Snowburst One',
            'Sofadi One',
            'Sofia',
            'Sonsie One',
            'Sorts Mill Goudy',
            'Source Code Pro',
            'Source Sans Pro',
            'Source Serif Pro',
            'Special Elite',
            'Spicy Rice',
            'Spinnaker',
            'Spirax',
            'Squada One',
            'Stalemate',
            'Stalinist One',
            'Stardos Stencil',
            'Stint Ultra Condensed',
            'Stint Ultra Expanded',
            'Stoke',
            'Strait',
            'Sue Ellen Francisco',
            'Sunshiney',
            'Supermercado One',
            'Suwannaphum',
            'Swanky and Moo Moo',
            'Syncopate',
            'Tangerine',
            'Taprom',
            'Tauri',
            'Teko',
            'Telex',
            'Tenor Sans',
            'Text Me One',
            'The Girl Next Door',
            'Tienne',
            'Tinos',
            'Titan One',
            'Titillium Web',
            'Trade Winds',
            'Trocchi',
            'Trochut',
            'Trykker',
            'Tulpen One',
            'Ubuntu',
            'Ubuntu Condensed',
            'Ubuntu Mono',
            'Ultra',
            'Uncial Antiqua',
            'Underdog',
            'Unica One',
            'UnifrakturCook',
            'UnifrakturMaguntia',
            'Unkempt',
            'Unlock',
            'Unna',
            'VT323',
            'Vampiro One',
            'Varela',
            'Varela Round',
            'Vast Shadow',
            'Vesper Libre',
            'Vibur',
            'Vidaloka',
            'Viga',
            'Voces',
            'Volkhov',
            'Vollkorn',
            'Voltaire',
            'Waiting for the Sunrise',
            'Wallpoet',
            'Walter Turncoat',
            'Warnes',
            'Wellfleet',
            'Wendy One',
            'Wire One',
            'Yanone Kaffeesatz',
            'Yellowtail',
            'Yeseva One',
            'Yesteryear',
            'Zeyada',
        );
    }
}
