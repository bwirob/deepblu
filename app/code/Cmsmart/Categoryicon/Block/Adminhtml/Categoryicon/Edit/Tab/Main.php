<?php
namespace Cmsmart\Categoryicon\Block\Adminhtml\Categoryicon\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('categoryicon');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Cmsmart_Categoryicon::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('categoryicon_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Categoryicon Information')]);

        if ($model->getId()) {
            $fieldset->addField('categoryicon_id', 'hidden', ['name' => 'categoryicon_id']);
        }

	   /***************/	
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$objBlock = $objectManagerr->get('\Cmsmart\Categoryicon\Block\Categoryicon');
		$categories = $objBlock->getSubCategories();
		$cat_url = $this->getUrl('catalog/category/index');
		
		$menu = $objectManagerr->get('\Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory');
		$mCollection = $menu->create();
		$old_catId = array();
		foreach($mCollection as $m){
			$old_catId[] = $m->getCategoryId();
		}

		$cate_arr = array();
		$cate_arr[''] = '-- Please select caterory --';
	  
		$id = $this->getRequest()->getParam('categoryicon_id');
		if($id){
			foreach ($categories as $cat):    
				if($cat->getId() == $model->getCategoryId()){
					$cate_arr[$cat->getId()] = $cat->getName();
				}
			endforeach;
		}  else {
			foreach ($categories as $cat):    
				if(!in_array($cat->getId(), $old_catId)){
					$cate_arr[$cat->getId()] = $cat->getName();
				}
			endforeach;
		}
		
		$fieldset->addField(
			'category_id',
			'select',
			[
				'name' => 'category_id',
				'label' => __('Select Category'),
				'title' => __('Select Category'),
				'values' => $cate_arr,
				'required'	=> true,
				'note'	 => 'You can create new category by click <a href="'.$cat_url.'" target="_blank">Manage Categories</a><br/>
							After create new cagegories, please refresh this page.'
			]
		);
		
       $fieldset->addField(
            'icon_init',
            'image',
            [
                'name' => 'icon_init',
                'label' => __('Icon category'),
                'title' => __('Icon category'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'icon_hover',
            'image',
            [
                'name' => 'icon_hover',
                'label' => __('Icon category when hover'),
                'title' => __('Icon category when hover'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		if ($model->getIconInit()) {
			$path = 'Categoryicon/'.$model->getCategoryId()."/".$model->getIconInit();
			$model->setData('icon_init', $path);
		}
		
		if ($model->getIconHover()) {
			$path = 'Categoryicon/'.$model->getCategoryId()."/".$model->getIconHover();
			$model->setData('icon_hover', $path);
		}
        
        $this->_eventManager->dispatch('adminhtml_categoryicon_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Categoryicon Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Categoryicon Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
