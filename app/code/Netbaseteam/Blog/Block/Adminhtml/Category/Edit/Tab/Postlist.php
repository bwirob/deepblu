<?php

namespace Netbaseteam\Blog\Block\Adminhtml\Category\Edit\Tab;

use Netbaseteam\Blog\Model\CategoryFactory;

class Postlist extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_categoryFactory;
   
    protected $registry;

    protected $_objectManager = null;

    protected $_postFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Model\ResourceModel\Post\CollectionFactory $postFactory,
        array $data = []
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        
        parent::__construct($context, $backendHelper, $data);
    }

    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('ordering');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('blog_category_id')) {
            $this->setDefaultFilter(array('in_post' => 1));
        }
    }

    
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_post') { 
            $postIds = $this->_getSelectedPost();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('post_id', array('in' => $postIds));
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('post_id', array('nin' => $postIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    
    protected function _prepareCollection()
    {
        $collection = $this->_postFactory->create();
        $collection->addFieldToFilter('status', array('eq'=>'1'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $model = $this->_objectManager->get('\Netbaseteam\Blog\Model\Category');

        $this->addColumn(
            'in_post',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_post',
                'align' => 'center',
                'index' => 'post_id',
                'values' => $this->_getSelectedPost(),
            ]
        );

        $this->addColumn(
            'post_id',
            [
                'header' => __('Post ID'),
                'type' => 'number',
                'index' => 'post_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        $this->addColumn(
            'post_tag',
            [
                'header' => __('Tag Name'),
                'index' => 'tag',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        
        $this->addColumn(
            'post_status',
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
        
        return $this->getUrl('*/*/postGrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedPost()
    {
        $category = $this->getCategory();
        if($category->getPostIds()){
            $postIds = explode('&', $category->getPostIds());
        }else{
            $postIds = [];
        }
        return  $postIds;
    }

    public function getSelectedPost()
    {
        $category = $this->getCategory();
        $postIds = $category->getPostIds();

        if ($postIds) {
            $selected = explode('&',$category->getPostIds());
        }else{
            $selected=[];
        }
        return $selected;
    }

    protected function getCategory()
    {
        $categoryId = $this->getRequest()->getParam('blog_category_id');       
        $category   = $this->_categoryFactory->create();
        if ($categoryId) {
            $category->load($categoryId);
        }

        return $category;
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
