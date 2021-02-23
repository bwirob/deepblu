<?php

namespace Netbaseteam\Blog\Block\Adminhtml\Post\Edit\Tab;

use Netbaseteam\Blog\Model\PostFactory;

class Commentlist extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $_postFactory;
   
    protected $registry;

    protected $_objectManager = null;

    protected $_commentFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        PostFactory $postFactory,
        \Netbaseteam\Blog\Model\ResourceModel\Comment\CollectionFactory $commentFactory,
        array $data = []
    ) {
        $this->_postFactory = $postFactory;
        $this->_commentFactory = $commentFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        
        parent::__construct($context, $backendHelper, $data);
    }

    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('commentGrid');
        $this->setDefaultSort('ordering');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('post_id')) {
            $this->setDefaultFilter(array('in_comment' => 1));
        }
    }

    
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_comment') { 
            $commentIds = $this->_getSelectedComment();
            if (empty($commentIds)) {
                $commentIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('blog_comment_id', array('in' => $commentIds));
            } else {
                if ($commentIds) {
                    $this->getCollection()->addFieldToFilter('blog_comment_id', array('nin' => $commentIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    
    protected function _prepareCollection()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $collection = $this->_commentFactory->create();
        $collection->addFieldToFilter('post_id', array('eq'=>$postId));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $model = $this->_objectManager->get('\Netbaseteam\Blog\Model\Post');

        $this->addColumn(
            'blog_comment_id',
            [
                'header' => __('Comment ID'),
                'type' => 'number',
                'index' => 'blog_comment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'comment_author_name',
            [
                'header' => __('Author Name'),
                'index' => 'author_name',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        $this->addColumn(
            'comment_author_email',
            [
                'header' => __('Author Email'),
                'index' => 'author_email',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );

        $this->addColumn(
            'comment_content',
            [
                'header' => __('Content'),
                'index' => 'content',
                'class' => 'xxx',
                'width' => '200px',
            ]
        );

        
        $this->addColumn(
            'comment_status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => ['2' => __('Pending'),'1' => __('Enable'), '0' => __('Disable')],
                'class' => 'xxx',
                'width' => '50px',
                
            ]
        );

        
        

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        
        return $this->getUrl('*/*/commentGrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedComment()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $collection = $this->_commentFactory->create();
        $collection->addFieldToFilter('post_id', array('eq'=>$postId));
        $commentIds = array();
        foreach ($collection as $comment) {
            $commentIds[] = $comment->getBlogCommentId();
        }
        return  $commentIds;
    }

    public function getSelectedComment()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $collection = $this->_commentFactory->create();
        $collection->addFieldToFilter('post_id', array('eq'=>$postId));
        $commentIds = array();
        foreach ($collection as $comment) {
            $commentIds[] = $comment->getBlogCommentId();
        }
        return  $commentIds;
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
