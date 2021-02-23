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
		
		$cate_arr = array();
		$cate_arr[0] = '-- Please select caterory --';
	  
		foreach ($categories as $cat):    
			$cate_arr[$cat->getId()] = $cat->getName();
			/*  \Zend_Debug::dump($cat->getData());  */
		endforeach;
	
		$fieldset->addField(
			'category_id',
			'select',
			[
				'name' => 'category_id',
				'label' => __('Select Category'),
				'title' => __('Select Category'),
				'values' => $cate_arr,
			]
		);
		
		$positionFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Position');
		$fieldset->addField(
			'position',
			'select',
			[
				'name' => 'position',
				'label' => __('Position'),
				'title' => __('Position'),
				'values' => $positionFactory->getPosition(),
			]
		);

		/* Horizontal Menu */
		$fieldset = $form->addFieldset('horizontal_fieldset', ['legend' => __('Horizontal Menu Configurations')]);
		
		$labelFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Label');
		$fieldset->addField(
			'top_label',
			'select',
			[
				'name' => 'top_label',
				'label' => __('Category Label Type'),
				'title' => __('Category Label Type'),
				'values' => $labelFactory->getLabel(),
			]
		);
		
		$typeFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Contenttype');
		$fieldset->addField(
			'top_content_type',
			'select',
			[
				'name' => 'top_content_type',
				'label' => __('Menu Content Type'),
				'title' => __('Menu Content Type'),
				'values' => $typeFactory->getTypes(),
			]
		);
		
		$wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $contentField = $fieldset->addField(
            'top_block_top',
            'editor',
            [
                'name' => 'top_block_top',
				'label' => __('Top Block'),
				'title' => __('Top Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
		
		$contentField = $fieldset->addField(
            'top_block_left',
            'editor',
            [
                'name' => 'top_block_left',
				'label' => __('Left Block Or SKU'),
				'title' => __('Left Block Or SKU'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]',
                'config' => $wysiwygConfig
            ]
        );
		
		if ($model->getTopLeftSkuTitle() || $model->getTopLeftBlockSku()) {
			$fieldset->addField(
				'feature_1',
				'note',
				[
					'name' => 'feature_1',
					'label' => __('Left Block SKU'),
					'title' => __('Left Block SKU'),
					'text'	=> __('Title: ').$model->getTopLeftSkuTitle() . __('. SKU: ') . $model->getTopLeftBlockSku(),
					'disabled' => $isElementDisabled
				]
			);
		}
		
		$contentField = $fieldset->addField(
            'top_block_right',
            'editor',
            [
                'name' => 'top_block_right',
				'label' => __('Right Block Or SKU'),
				'title' => __('Right Block Or SKU'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]',
                'config' => $wysiwygConfig
            ]
        );
		
		if ($model->getTopRightSkuTitle() || $model->getTopRightBlockSku()) {
			$fieldset->addField(
				'feature_2',
				'note',
				[
					'name' => 'feature_2',
					'label' => __('Right Block SKU'),
					'title' => __('Right Block SKU'),
					'text'	=> __('Title: ').$model->getTopRightSkuTitle() . __('. SKU: ') . $model->getTopRightBlockSku(),
					'disabled' => $isElementDisabled
				]
			);
		}
		
		$contentField = $fieldset->addField(
            'top_block_bottom',
            'editor',
            [
                'name' => 'top_block_bottom',
				'label' => __('Bottom Block'),
				'title' => __('Bottom Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
		
		$fieldset->addField(
            'top_label_container',
            'text',
            [
                'name' => 'top_label_container',
                'label' => __('Title SKU Specical'),
                'title' => __('Title SKU Specical'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'top_sku',
            'text',
            [
                'name' => 'top_sku',
                'label' => __('SKU Specical'),
                'title' => __('SKU Specical'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'top_pgrid_num_columns',
            'text',
            [
                'name' => 'top_pgrid_num_columns',
                'label' => __('Number of Columns'),
                'title' => __('Number of Columns'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'left_pgrid_cats',
            'text',
            [
                'name' => 'left_pgrid_cats',
                'label' => __('Categories'),
                'title' => __('Categories'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$contentField = $fieldset->addField(
            'left_content_block',
            'editor',
            [
                'name' => 'left_content_block',
				'label' => __('Static Block Content'),
				'title' => __('Static Block Content'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]',
                'config' => $wysiwygConfig
            ]
        );
		
		$fieldset->addField(
            'left_pgrid_box_title',
            'text',
            [
                'name' => 'left_pgrid_box_title',
                'label' => __('Products Box Title'),
                'title' => __('Products Box Title'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$field = $fieldset->addField(
            'left_pgrid_products',
            'text',
            [
                'name' => 'left_pgrid_products',
                'label' => __('Products'),
                'title' => __('Products'),
				 'value'     => '120', 
            ]
        );
		$renderer = $this->getLayout()->createBlock(
			   'Cmsmart\Megamenu\Block\Adminhtml\Form\Renderer\Customfield'
		);
		 
		$field->setRenderer($renderer);
		
		$fieldset->addField(
            'left_hot_products',
            'text',
            [
                'name' => 'left_hot_products',
                'label' => __('+ Hot Products IDs (separate by comma): '),
                'title' => __('+ Hot Products IDs (separate by comma): '),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'left_new_products',
            'text',
            [
                'name' => 'left_new_products',
                'label' => __('+ New Products IDs (separate by comma):'),
                'title' => __('+ New Products IDs (separate by comma):'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'left_sale_products',
            'text',
            [
                'name' => 'left_sale_products',
                'label' => __('+ Sale Products IDs (separate by comma):'),
                'title' => __('+ Sale Products IDs (separate by comma):'),
                'disabled' => $isElementDisabled
            ]
        );
		
		/*********** Vertical Menu **************/
		
		$fieldset = $form->addFieldset('vertical_fieldset', ['legend' => __('Vertical Menu Configurations')]);
		
		$labelFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Label');
		$fieldset->addField(
			'left_label',
			'select',
			[
				'name' => 'left_label',
				'label' => __('Category Label Type'),
				'title' => __('Category Label Type'),
				'values' => $labelFactory->getLabel(),
			]
		);
		
		$contentField = $fieldset->addField(
            'left_block_top',
            'editor',
            [
                'name' => 'left_block_top',
				'label' => __('Top Block'),
				'title' => __('Top Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
		
		$contentField = $fieldset->addField(
            'left_block_left',
            'editor',
            [
                'name' => 'left_block_left',
				'label' => __('Left Block Or SKU'),
				'title' => __('Left Block Or SKU'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]',
                'config' => $wysiwygConfig
            ]
        );
		
		/* if ($model->getLeftLeftSkuTitle() || $model->getLeftLeftSku()) {
			$fieldset->addField(
				'feature_3',
				'note',
				[
					'name' => 'feature_3',
					'label' => __('Left Block SKU'),
					'title' => __('Left Block SKU'),
					'text'	=> __('Title: ').$model->getLeftLeftSkuTitle() . __('. SKU: ') . $model->getLeftLeftSku(),
					'disabled' => $isElementDisabled
				]
			);
		} */
		
		$field = $fieldset->addField(
            'left_left_sku_title',
            'text',
            [
                'name' => 'left_left_sku_title',
                'label' => __('Left SKU Title'),
                'title' => __('Left SKU Title'),
				 'value'     => '120', 
            ]
        );
		
		$field = $fieldset->addField(
            'left_left_sku',
            'text',
            [
                'name' => 'left_left_sku',
                'label' => __('Left SKU'),
                'title' => __('Left SKU'),
				 'value'     => '120', 
            ]
        );
		
		$contentField = $fieldset->addField(
            'left_block_right',
            'editor',
            [
                'name' => 'left_block_right',
				'label' => __('Right Block Or SKU'),
				'title' => __('Right Block Or SKU'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]',
                'config' => $wysiwygConfig
            ]
        );
		
		if ($model->getLeftRightSkuTitle() || $model->getLeftRightSku()) {
			$fieldset->addField(
				'feature_2',
				'note',
				[
					'name' => 'feature_2',
					'label' => __('Right Block SKU'),
					'title' => __('Right Block SKU'),
					'text'	=> __('Title: ').$model->getLeftRightSkuTitle() . __('. SKU: ') . $model->getLeftRightSku(),
					'disabled' => $isElementDisabled
				]
			);
		}
		
		$contentField = $fieldset->addField(
            'left_block_bottom',
            'editor',
            [
                'name' => 'left_block_bottom',
				'label' => __('Bottom Block'),
				'title' => __('Bottom Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
		
		$fieldset->addField(
            'left_label_container',
            'text',
            [
                'name' => 'left_label_container',
                'label' => __('Title SKU Specical'),
                'title' => __('Title SKU Specical'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'left_sku',
            'text',
            [
                'name' => 'left_sku',
                'label' => __('SKU Specical'),
                'title' => __('SKU Specical'),
                'disabled' => $isElementDisabled
            ]
        );

		/* $layoutFieldset = $form->addFieldset(
            'image_fieldset',
            ['legend' => __('Icon category'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        ); */

       /*  $fieldset->addField(
            'left_cat_icon',
            'image',
            [
                'name' => 'left_cat_icon',
                'label' => __('Icon category'),
                'title' => __('Icon category'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        ); */
        
		/* show image in form */

		/* if ($model->getLeftCatIcon()) {
			$path = (string)'Megamenu/'.(string)$model->getLeftCatIcon();
			$model->setData('left_cat_icon', $path);
		} */
		
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
