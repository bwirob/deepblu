<?php

namespace Netbaseteam\Blog\Block\Adminhtml\Post\Edit\Tab;

use Netbaseteam\Blog\Model\PostFactory;

class Relatedlist extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $_postFactory;

    protected $registry;

    protected $_objectManager = null;

    protected $_postFactoryCollection;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        PostFactory $postFactory,
        \Netbaseteam\Blog\Model\ResourceModel\Post\CollectionFactory $postFactoryCollection,
        array $data = []
    ) {
        $this->_postFactoryCollection = $postFactoryCollection;
        $this->_postFactory = $postFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        
        parent::__construct($context, $backendHelper, $data);
    }

    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('relatedGrid');
        $this->setDefaultSort('ordering');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('post_id')) {
            $this->setDefaultFilter(array('in_related' => 1));
        }
    }

    
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_related') { 
            $relatedIds = $this->_getSelected();
            if (empty($relatedIds)) {
                $relatedIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('post_id', array('in' => $relatedIds));
            } else {
                if ($relatedIds) {
                    $this->getCollection()->addFieldToFilter('post_id', array('nin' => $relatedIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    
    protected function _prepareCollection()
    {
        $current_id =  $this->getRequest()->getParam('post_id');
        $collection = $this->_postFactoryCollection->create();
        $collection->addFieldToFilter('status', array('eq'=>'1'))
                ->addFieldToFilter('post_id', array('neq'=>$current_id));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $model = $this->_objectManager->get('\Netbaseteam\Blog\Model\Post');

        $this->addColumn(
            'in_related',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_related',
                'align' => 'center',
                'index' => 'post_id',
                'values' => $this->_getSelected(),
            ]
        );

        $this->addColumn(
            'related_id',
            [
                'header' => __('Post ID'),
                'type' => 'number',
                'index' => 'post_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'related_title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        $this->addColumn(
            'related_tag',
            [
                'header' => __('Tag Name'),
                'index' => 'tag',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        
        $this->addColumn(
            'related_status',
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
        
        return $this->getUrl('*/*/relatedGrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelected()
    {
        $post = $this->getPost();
        if($post->getRelatedPost()){
            $relatedIds = explode('&', $post->getRelatedPost());
        }else{
            $relatedIds = [];
        }
        return  $relatedIds;
    }

    public function getSelected()
    {
        $post = $this->getPost();
        $relatedIds = $post->getRelatedPost();

        if ($relatedIds) {
            $selected = explode('&',$post->getRelatedPost());
        }else{
            $selected=[];
        }
        return $selected;
    }

    protected function getPost()
    {
        $postId = $this->getRequest()->getParam('post_id');       
        $post   = $this->_postFactory->create();
        if ($postId) {
            $post->load($postId);
        }

        return $post;
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
