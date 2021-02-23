<?php


namespace Netbaseteam\Blog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_comment'),
                'create_time',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable'  => true,
                    'comment'   => 'Created Comment Time'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_comment'),
                'post_title',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Post Title of comment'
                )
            );


            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_comment'),
                'reply_author',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Reply Author'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_comment'),
                'reply_createtime',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable'  => false,
                    'comment'   => 'Reply Create Time'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_comment'),
                'reply_content',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Reply Content'
                )
            );


            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_comment'),
                'post_url',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Post Url of comment'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_category'),
                'url_rewrite_id',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'comment'   => 'Url Rewrite ID'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_post'),
                'url_rewrite_id',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'comment'   => 'Url Rewrite ID'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_post'),
                'author_avatar',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Author Avatar'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_post'),
                'feature_image',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Feature Image'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_post'),
                'author_description',
                
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    '2M',
                    'comment'   => 'Author Avatar'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_post'),
                'related_products',
                
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Related Products'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_blog_category'),
                'category_image',
                
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Category Image'
                )
            );


        }

        $setup->endSetup();
    }

   
}
