<?php

namespace Netbaseteam\Faq\Block\Adminhtml\Faqcategory\Edit\Tab;

use Netbaseteam\Faq\Model\FaqcategoryFactory;

class Faqlist extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    

   

    protected $_faqCategoryFactory;

   
    protected $registry;

    protected $_objectManager = null;

    protected $_faqFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        FaqcategoryFactory $faqCategoryFactory,
        \Netbaseteam\Faq\Model\ResourceModel\Faq\CollectionFactory $faqFactory,
        array $data = []
    ) {
        $this->_faqCategoryFactory = $faqCategoryFactory;
        $this->_faqFactory = $faqFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        
        parent::__construct($context, $backendHelper, $data);
    }

    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('faqGrid');
        $this->setDefaultSort('ordering');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('faq_category_id')) {
            $this->setDefaultFilter(array('in_faq' => 1));
        }
    }

    
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_faq') { 
            $faqIds = $this->_getSelectedFaq();
            if (empty($faqIds)) {
                $faqIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('faq_id', array('in' => $faqIds));
            } else {
                if ($faqIds) {
                    $this->getCollection()->addFieldToFilter('faq_id', array('nin' => $faqIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    
    protected function _prepareCollection()
    {
        $collection = $this->_faqFactory->create();
        $collection->addFieldToFilter('status', array('eq'=>'1'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $model = $this->_objectManager->get('\Netbaseteam\Faq\Model\Faqcategory');

        $this->addColumn(
            'in_faq',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_faq',
                'align' => 'center',
                'index' => 'faq_id',
                'values' => $this->_getSelectedFaq(),
            ]
        );

        $this->addColumn(
            'faq_id',
            [
                'header' => __('FAQ ID'),
                'type' => 'number',
                'index' => 'faq_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'faq_question',
            [
                'header' => __('Question'),
                'index' => 'question',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        $this->addColumn(
            'tag_name',
            [
                'header' => __('Tag Name'),
                'index' => 'tag',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        
        $this->addColumn(
            'statuss',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => ['1' => __('Enable'), '0' => __('Disable')],
                'class' => 'xxx',
                'width' => '50px',
                
            ]
        );
        

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        
        return $this->getUrl('*/*/faqGrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedFaq()
    {
        $faqCategory = $this->getFaqCategory();
        if($faqCategory->getFaqIds()){
            $faqIds = explode('&', $faqCategory->getFaqIds());
        }else{
            $faqIds = [];
        }
        return  $faqIds;
    }

    public function getSelectedFaq()
    {
        $faqCategory = $this->getFaqCategory();
        $faqIds = $faqCategory->getFaqIds();

        if ($faqIds) {
            $selected = explode('&',$faqCategory->getFaqIds());
        }else{
            $selected=[];
        }
        return $selected;
    }

    protected function getFaqCategory()
    {
        $faqCategoryId = $this->getRequest()->getParam('faq_category_id');       
        $faqCategory   = $this->_faqCategoryFactory->create();
        if ($faqCategoryId) {
            $faqCategory->load($faqCategoryId);
        }

        return $faqCategory;
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return true;
    }
}
