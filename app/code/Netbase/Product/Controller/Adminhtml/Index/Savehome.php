<?php

namespace Netbase\Product\Controller\Adminhtml\Index;

use Magento\Framework\View\Result\PageFactory;

class Savehome extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_configWriter;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_configWriter = $configWriter;
        parent::__construct($context);
    }
	
    /**
     * Default Productvideo Index page
     *
     * @return void
     */
    public function execute()
    {
		$ret = array(); 
		$section = 0; $alias_id = 0; $typevalue_id = 0;
		$params = $this->getRequest()->getParams();
		$input = trim($params['input_string'], ";");
		$identifier = trim($params['identifier']);
		$title = trim($params['title']);
		$page_layout 	=  trim($params['page_layout']);
		
		if($input == "") {
			die("Please set data");
		}
		$input_arr_bound = explode(";", $input);
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('netbase_product_homecontent');
		$str_content = "";
		
		for($i=0; $i<count($input_arr_bound); $i++) {
			$input_arr = explode("/", $input_arr_bound[$i]);
			$section = trim($input_arr[0]);
			$alias_id = trim($input_arr[1]);
			$typevalue_id = trim($input_arr[2]);
			
			$view = "SELECT * FROM " . $tableName . " WHERE section = '".$section."'";
			$result = $connection->fetchAll($view); 
			if(count($result)) {
				$update = "UPDATE " . $tableName . " SET alias_id = '".$alias_id."', typevalue_id = '".$typevalue_id."', identifier = '".$identifier."', mtitle = '".$title."', page_layout = '".$page_layout."' WHERE section = '" . $section . "'";
				$connection->query($update);
			} else {
				$insert = "INSERT INTO " . $tableName . " (id, alias_id, typevalue_id, section, identifier, mtitle, page_layout) VALUES ('', '".$alias_id."', '".$typevalue_id."', '".$section."', '".$identifier."', '".$title."', '".$page_layout."')";
				$connection->query($insert);
			}
			
			$model = $objectManager->create('\Netbase\Product\Model\Typevalue');
			$loadValue = $model->load($typevalue_id);
			
			if($alias_id == \Netbase\Product\Model\Homepagetype::custom_static_block){
				$vContent = '{{block class="Magento\Cms\Block\Block" block_id="'.$typevalue_id.'"}}';
			} else {
				$vContent = $loadValue->getContent();
			}
			
			$str_content .= $vContent;
		}
		
		$data_page["identifier"] 	= $identifier;
		$data_page["title"] 		= $title;
		//$data_page["page_layout"] 	= "1column";
		$data_page["content"] 		= $str_content;
		$data_page["page_layout"] 		= $page_layout;
		
		$model = $objectManager->create('Magento\Cms\Model\Page');
		$collection = $objectManager->get('\Magento\Cms\Model\ResourceModel\Page\CollectionFactory')->create();
		
		$collection ->addFieldToFilter('identifier' , $data_page["identifier"])
					->addFieldToFilter('is_active' , \Magento\Cms\Model\Page::STATUS_ENABLED);

		$cmdPageId = 0;
		if(sizeof($collection)) {
			foreach($collection as $cmsPage){
				$cmdPageId = $cmsPage->getId();
				break;
			}
			$model	->setData($data_page)
					->setData('stores',array("0"=>0))
					->setId($cmdPageId);
			$model->save();
		} else {
			$model	->setData($data_page)
					->setData('stores',array("0"=>0));
			$model->save();
		}
		
		$this->_configWriter->save('web/default/cms_home_page', 
			$identifier, 
			$scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 
			$scopeId = 0);
		
		$ret["message"] = "The data has been saved successfully";					
		echo json_encode($ret);	
		exit();
    }
}