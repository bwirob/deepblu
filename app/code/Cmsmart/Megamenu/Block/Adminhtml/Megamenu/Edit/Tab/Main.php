<?php
namespace Cmsmart\Megamenu\Block\Adminhtml\Megamenu\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $_megaHelper;
	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

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
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
		$this->_wysiwygConfig = $wysiwygConfig;
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
        $model = $this->_coreRegistry->registry('megamenu');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Cmsmart_Megamenu::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('megamenu_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Megamenu Information')]);

        if ($model->getId()) {
            $fieldset->addField('megamenu_id', 'hidden', ['name' => 'megamenu_id']);
        }
		
		/***************/	
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManagerr->get('\Cmsmart\Megamenu\Helper\Data');
		$objBlock = $objectManagerr->get('\Cmsmart\Megamenu\Block\Navigation');
		$categories = $objBlock->getSubCategories($helper->getRootCatID());
		
		$menu = $objectManagerr->get('\Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory');
		$mCollection = $menu->create();
		$old_catId = array();
		foreach($mCollection as $m){
			$old_catId[] = $m->getCategoryId();
		}

		$cate_arr = array();
		$cate_arr[''] = '-- Please select caterory --';
	  
		$id = $this->getRequest()->getParam('megamenu_id');
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
	
		$cat_url = $this->getUrl('catalog/category/index');
		
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
		
		$positionFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Position');
		$fieldset->addField(
			'position',
			'select',
			[
				'name' => 'position',
				'label' => __('Show On'),
				'title' => __('Show On'),
				'required'	=> true,
				'values' => $positionFactory->getPosition(),
			]
		);
		
		
        $this->_eventManager->dispatch('adminhtml_megamenu_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('Megamenu Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Megamenu Information');
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
