<?php
namespace Netbase\Product\Block\Adminhtml\Typevalue\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
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
        $this->_systemStore = $systemStore;
		$this->_wysiwygConfig = $wysiwygConfig;
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
        $model = $this->_coreRegistry->registry('product');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbase_Product::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('product_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Content Type Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
		
		/***************/	
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$type = $objectManagerr->get('\Netbase\Product\Model\ResourceModel\Product\CollectionFactory');
		$mCollection = $type->create();

		$cate_arr = array();
		$cate_arr[''] = __('-- Please select section type --');
	  
		if(sizeof($mCollection)) {
			foreach ($mCollection as $c):    
				$cate_arr[$c->getId()] = $c->getTitle();
			endforeach;
		}
		
		$fieldset->addField(
			'alias',
			'select',
			[
				'name' => 'alias',
				'label' => __('Section Type'),
				'title' => __('Section Type'),
				'values' => $cate_arr,
				'required'	=> true,
			]
		);
		
		$fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

		$wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $contentField = $fieldset->addField(
            'content',
            'editor',
            [
				'label' => __('Content'),
                'title' => __('Content'),
                'name' => 'content',
                'style' => 'height:10em;',
                'required' => true,
                'disabled' => $isElementDisabled,
                //'config' => $wysiwygConfig
            ]
        );
		
		$fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		/* show image in form */
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManagerr->get('Netbase\Product\Helper\Data');

		if ($model->getImage()) {
			$path = $helper->getBaseUrl()."/".$model->getImage();
			$model->setData('image', $path);

			$html = '<img width="100%" height="100%" src = "'.$path.'"/>';
			$fieldset->addField(
				'mynote',
				'note',
				[
					'name' => 'mynote',
					'label' => __(''),
					'title' => __(''),
					'note' => $html,
					'disabled' => $isElementDisabled
				]
			);
		}

        $this->_eventManager->dispatch('adminhtml_product_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('Type Content Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Type Content Information');
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
