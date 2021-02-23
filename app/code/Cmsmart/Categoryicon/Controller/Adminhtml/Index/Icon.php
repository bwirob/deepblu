<?php

namespace Cmsmart\Categoryicon\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Icon extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;
    protected $_collectionFactory;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
		Action\Context $context, 
		\Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory $collectionFactory,
		PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        $this->_collectionFactory = $collectionFactory->create();
        parent::__construct($context);
    }

    
    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {    
        $result = array();
		
		$category_id = $this->getRequest()->getParam('id_cate');
		
		$icon_type = $this->getRequest()->getParam('icon_type');
		$collections  = $this->_collectionFactory->addFieldToFilter('category_id', $category_id);
						
		if($icon_type == "save") { /* save icon */		
			$data = array();
			 $model = $this->_objectManager->create('Cmsmart\Categoryicon\Model\Categoryicon');
			 
			$cat_class = $this->getRequest()->getParam('cat_class');
			
			$data['category_id'] = $category_id;
			$data['class_name'] = $cat_class;
			
			$id = "";
			/* \zend_debug::dump($save_icon);
			die; */
			foreach($collections as $col){
				$id = $col->getId();
				break;
			}
			if ($id != ""){
				//edit
				$model->setData($data)
					->setId($id);
				$model->save();
			} else {
				//add new
				$model->setData($data);
				$model->save();
			}
		} 
		
		if($icon_type == "load") /* load icon */ {
			foreach($collections as $col){
				$result["class_name"] = "icon-".$col->getClassName();
				break;
			}
		}
		echo json_encode($result);
		die();
    }
}
