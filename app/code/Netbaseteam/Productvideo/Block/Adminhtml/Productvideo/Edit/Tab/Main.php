<?php
namespace Netbaseteam\Productvideo\Block\Adminhtml\Productvideo\Edit\Tab;

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
        $model = $this->_coreRegistry->registry('productvideo');
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbaseteam_Productvideo::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('productvideo_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Information')]);

        if ($model->getId()) {
            $fieldset->addField('productvideo_id', 'hidden', ['name' => 'productvideo_id']);
        }

		$typeFactory = $objectManagerr->create('\Netbaseteam\Productvideo\Model\Source\Type');
		$fieldset->addField(
            'video_type',
            'select',
            [
                'name' => 'video_type',
                'label' => __('Video Type'),
                'title' => __('Video Type'),
                'required' => true,
                'values' => $typeFactory->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
		   'store_view',
		   'multiselect',
		   [
			 'name'     => 'store_view[]',
			 'label'    => __('Store Views'),
			 'title'    => __('Store Views'),
			 'required' => true,
			 'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
		   ]
		);
		
        $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('URL'),
                'title' => __('URL'),
                'required' => false,
				'note'		=> 'http://www.youtube.com/, http://www.dailymotion.com/, http://www.twitch.tv/(chanel), https://vimeo.com',
                'disabled' => $isElementDisabled
            ]
        );
		
		if ($model->getVideoType() == \Netbaseteam\Productvideo\Model\Source\Type::local
			&& $model->getTitle() != "") {
			$fieldset->addField(
				'video_name',
				'note',
				[
					'name' => 'video_name',
					'label' => __('Filed Uploaded'),
					'title' => __('Filed Uploaded'),
					'required'  => false,
					'note'		=> "<p style='margin-top: -17px; font-size: 14px; font-weight: bold;'>".$model->getTitle()."</p>",
					'disabled' => $isElementDisabled
				]
			);
		}
		
		$fieldset->addField(
            'local_video',
            'image',
            [
                'name' => 'local_video',
                'label' => __('Local Video'),
                'title' => __('Local Video'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		$field = $fieldset->addField(
            'video_html',
            'text',
            [
                'name' => 'video_html',
                'label' => __('Products'),
                'title' => __('Products'),
            ]
        );
		$renderer = $this->getLayout()->createBlock(
			   'Netbaseteam\Productvideo\Block\Adminhtml\Renderer\Videohtml'
		);
		$field->setRenderer($renderer);
		
		$fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title_tmp',
                'label' => __('Video Title'),
                'title' => __('Video Title'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'thumb',
            'image',
            [
                'name' => 'thumb',
                'label' => __('Thumb Video'),
                'title' => __('Thumb Video'),
                'required'  => false,
				'note'		=> 'After you enter video url, thumb will get. If the thumb has not get, please choose image file: .jpg or .png. 
								Good image size: 120 x 90 (px)',
                'disabled' => $isElementDisabled
            ]
        );
        
		$statusFactory = $objectManagerr->create('\Netbaseteam\Productvideo\Model\Source\Status');
		$fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => false,
				'values' => $statusFactory->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
		
        /* $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField('published_at', 'date', [
            'name'     => 'published_at',
            'date_format' => $dateFormat,
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'value' => $model->getPublishedAt(),
            'label'    => __('Publishing Date'),
            'title'    => __('Publishing Date'),
            'required' => true
        ]); */
        
		/* show image in form */
		if ($model->getThumb()) {
			$path = 'Productvideo/'.$model->getThumb();
			if (strpos($model->getThumb(), 'http') !== false) {
				$path = $model->getThumb();
			}
			$model->setData('thumb', $path);
		}
		
        $this->_eventManager->dispatch('adminhtml_productvideo_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
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
