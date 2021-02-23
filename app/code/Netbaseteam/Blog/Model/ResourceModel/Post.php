<?php

namespace Netbaseteam\Blog\Model\ResourceModel;

/**
 * FAQ Resource Model
 */
class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_blog_post', 'post_id');
    }


    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {

        $postId = $object->getId();
        $comments = $this->_lookupCommentsData($postId);

        $nextPost = $this->_lookupNextPost($postId);
        $prevPost = $this->_lookupPrevPost($postId);

        $object->setData('comments', $comments);
        $object->setData('next_post', $nextPost);
        $object->setData('prev_post', $prevPost);
        return parent::_afterLoad($object);
    }

    protected function _lookupCommentsData($postId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getTable('cmsmart_blog_comment')
        )->where(
            'post_id = ?',
            (int)$postId
        )->order('create_time DESC');


        return $adapter->fetchAll($select);
    }
    protected function _lookupNextPost($postId){
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getMainTable()
        );
        for ($i=(int)$postId+1; $i < 1000 ; $i++) { 
            $nextId = (int)$i;
            $select->where(
                'post_id = ?',
                $nextId
            );
            if (count($select)>0) {
                return $adapter->fetchAll($select);
            } 
        }

        return [];
    }

    protected function _lookupPrevPost($postId){
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getMainTable()
        );
        for ($i=(int)$postId-1; $i >0; $i--) { 
            $prevId = (int)$i;
            $select->where(
                'post_id = ?',
                $prevId
            );
            if (count($select)>0) {
                return $adapter->fetchAll($select);
            } 
        }

        return [];
    }
}
