<?php
namespace Netbaseteam\Locator\Block\Adminhtml\Schedule\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Content extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_countryOption;
    protected $_workDate;


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
        \Netbaseteam\Locator\Model\Config\Source\Country $countryOption,
        \Netbaseteam\Locator\Model\Workdate $workDate,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_countryOption = $countryOption;
        $this->_workDate = $workDate;
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
        $model = $this->_coreRegistry->registry('schedule');

        
        if ($this->_isAllowedAction('Netbaseteam_Locator::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('locator_main_');

        $fieldset = $form->addFieldset('base_fieldset_mon', ['legend' => __('Monday')]);


        $fieldset->addField('work_date_ids', 'hidden', ['name' => 'work_date_ids']);
        $fieldset->addField(
            'd1_status',
            'select',
            [
                'name' => 'd1_status',
                'label' => __('Monday Status'),
                'title' => __('Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd1_open',
            'timepickers',
            [
                'name' => 'd1_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'format_time' => 'hh:mm',
                'class' =>'timer',
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd1_close',
            'timepickers',
            [
                'name' => 'd1_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );


        $fieldset = $form->addFieldset('base_fieldset_tues', ['legend' => __('Tuesday')]);

        $fieldset->addField(
            'd2_status',
            'select',
            [
                'name' => 'd2_status',
                'label' => __('Tuesday Status'),
                'title' => __('Tuesday Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd2_open',
            'timepickers',
            [
                'name' => 'd2_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd2_close',
            'timepickers',
            [
                'name' => 'd2_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset_wednes', ['legend' => __('Wednesday')]);

        $fieldset->addField(
            'd3_status',
            'select',
            [
                'name' => 'd3_status',
                'label' => __('Wednesday Status'),
                'title' => __('Wednesday Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );
         $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd3_open',
            'timepickers',
            [
                'name' => 'd3_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd3_close',
            'timepickers',
            [
                'name' => 'd3_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        

         $fieldset = $form->addFieldset('base_fieldset_thurs', ['legend' => __('Thursday')]);

        $fieldset->addField(
            'd4_status',
            'select',
            [
                'name' => 'd4_status',
                'label' => __('Thursday Status'),
                'title' => __('Thursday Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd4_open',
            'timepickers',
            [
                'name' => 'd4_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd4_close',
            'timepickers',
            [
                'name' => 'd4_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset_fri', ['legend' => __('Friday')]);

        $fieldset->addField(
            'd5_status',
            'select',
            [
                'name' => 'd5_status',
                'label' => __('Friday Status'),
                'title' => __('Friday Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd5_open',
            'timepickers',
            [
                'name' => 'd5_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd5_close',
            'timepickers',
            [
                'name' => 'd5_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

          $fieldset = $form->addFieldset('base_fieldset_satur', ['legend' => __('Saturday')]);

        $fieldset->addField(
            'd6_status',
            'select',
            [
                'name' => 'd6_status',
                'label' => __('Saturday Status'),
                'title' => __('Saturday Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd6_open',
            'timepickers',
            [
                'name' => 'd6_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd6_close',
            'timepickers',
            [
                'name' => 'd6_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset_sun', ['legend' => __('Sunday')]);

        $fieldset->addField(
            'd7_status',
            'select',
            [
                'name' => 'd7_status',
                'label' => __('Sunday Status'),
                'title' => __('Sunday Status'),
                'options' => ['1' => __('Close'), '0' => __('Open')],
                'class' =>'work-status',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'timepickers',
            '\Netbaseteam\Locator\Block\Adminhtml\Formfield\Renderer\Timepicker'
        );

        $fieldset->addField(
            'd7_open',
            'timepickers',
            [
                'name' => 'd7_open',
                'label' => __('Open Time'),
                'title' => __('Open Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'd7_close',
            'timepickers',
            [
                'name' => 'd7_close',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );


        
        
        $this->_eventManager->dispatch('adminhtml_locator_edit_tab_main_prepare_form', ['form' => $form]);

        $dataForm = $model->getData();
        
        if(!empty($dataForm)){
            $dateData = $this->getWorkDateData($dataForm['schedule_id']);

            $dataForm['work_date_ids'] = $dateData['ids'];
            $i=1;  
            foreach ($dateData['data'] as $key => $d) {
                $keyOpen  = 'd'.$i.'_open';
                $keyClose = 'd'.$i.'_close';
                $keyStatus = 'd'.$i.'_status';
                $dataForm[$keyOpen] = $d['open_time'];
                $dataForm[$keyClose] = $d['close_time'];
                $dataForm[$keyStatus] = $d['status'];
                
                $i++;
            }
            
        }else{
            $dataForm['work_date_ids'] = '';
        }
        
        $form->setValues($dataForm);
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
        return __('Schedule Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Schedule Content');
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


   

    public function getWorkDateData($scheduleId)
    {
        $collection = $this->getWorkDateCollection($scheduleId);
        $dateWorkIds = array();
        $workDateData = array();
        $dataContent = array();
        foreach ($collection as $key => $dateWork) {
            $dateWorkIds[] = $dateWork->getWorkDateId();
            $dataContent[$key]['close_time'] = $dateWork->getCloseTime();
            $dataContent[$key]['open_time'] = $dateWork->getOpenTime();
            $dataContent[$key]['status'] = $dateWork->getStatus();
        }
        $workDateData['ids'] = implode(',', $dateWorkIds);
        $workDateData['data'] = $dataContent;

        
        return $workDateData;

    }

     public function getWorkDateCollection($scheduleId)
    {
        if(empty($scheduleId)){
            return false;
        }

        $collection = $this->_workDate->getCollection()->addFieldToFilter('schedule_id', array('eq'=>$scheduleId));
       
        return $collection;
    }



   
}
