<?php


namespace Netbaseteam\Blog\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_blog_post')
		)->addColumn(
			'post_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Post ID'
		)->addColumn(
			'title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => false],
			'Post Title'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Blog Status'
		)->addColumn(
			'store_ids',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Store View Ids'
		)->addColumn(
			'category_ids',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Post Category Ids'
		)->addColumn(
			'enable_comment',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Enable Comment By Customer'
		)->addColumn(
            'meta_keyword',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Post Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Post Meta Description'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Post String Identifier'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '4M',
            ['nullable' => false],
            'Post Content'
        )->addColumn(
            'short_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Short Content'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Post Creation Time'
        )->addColumn(
            'publish_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Post Publish Time'
        )->addColumn(
			'tag',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Tag Name Attachment'
		)->addColumn(
			'author_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Author Name'
		)->addColumn(
			'author_email',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Author Email'
		)->addColumn(
			'related_post',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Related Post'
		)->addColumn(
			'thumbnail',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Blog Thumnail media path'
		)->addColumn(
			'image',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Blog image media path'
		)->addColumn(
			'ordering',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['nullable' => true,'default' => null],
			'Sort Order Blog'
		)->setComment(
			'Cmsmart Blog Post: Manage Post Item'
		);
		$installer->getConnection()->createTable($table);

		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_blog_category')
		)->addColumn(
			'blog_category_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Blog Category Id'
		)->addColumn(
			'name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Blog Category Name'
		)->addColumn(
			'description',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'FAQ Category Description'
		)->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Post Category String Identifier'
        )->addColumn(
            'parent_category',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Parent Post Category'
        )->addColumn(
            'design_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Design Category Type'
        )->addColumn(
            'order_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Order Post Item By'
        )->addColumn(
			'ordering',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['nullable' => true,'default' => null],
			'Sort View Blog Category'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Status Of FAQ Category'
		)->addColumn(
			'store_ids',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Store View Ids'
		)->addColumn(
            'post_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Post Id items'
        )->setComment(
			'Cmsmart Block Category Table'
		);


		$installer->getConnection()->createTable($table);

		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_blog_comment')
		)->addColumn(
			'blog_comment_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Blog Category Id'
		)->addColumn(
			'author_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Author Comment Name'
		)->addColumn(
			'author_email',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Author Comment Email'
		)->addColumn(
			'content',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Comment Content'
		)->addColumn(
			'ordering',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['nullable' => true,'default' => null],
			'Sort Order Comment'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Comment Status'
		)->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            6,
            ['nullable' => false],
            'Post Id'
        )->addColumn(
            'post_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Post Title'
        )->addColumn(
			'store_ids',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Store View Ids'
		)->setComment(
			'Cmsmart Blog Comment Table'
		);

		$installer->getConnection()->createTable($table);

		$installer->endSetup();
	}
}