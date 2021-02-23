<?php


namespace Netbaseteam\FAQ\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table cmsmart_faq
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_faq')
		)->addColumn(
			'faq_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'question',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => false],
			'FAQ Question'
		)->addColumn(
			'answer',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => false],
			'FAQ Answer'
		)->addColumn(
			'created_time',
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => true],
			'Created Time'
		)->addColumn(
			'update_time',
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => true,'default' => null],
			'Update Time'
		)->addColumn(
			'ordering',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['nullable' => true,'default' => null],
			'Sort View FAQ'
		)->addColumn(
			'most_frequently',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => true,'default' => null],
			'For Select Most Frequently Question'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Status Of FAQ item'
		)->addColumn(
			'tag',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Tag Name Attachment'
		)->addColumn(
			'faq_category_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['nullable' => false],
			'Select FAQ Category'
		)->addColumn(
			'pro_category',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Select Product Category'
		)->setComment(
			'FAQ item'
		);
		$installer->getConnection()->createTable($table);
		
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_faq_category')
		)->addColumn(
			'faq_category_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'FAQ Category Name'
		)->addColumn(
			'description',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'FAQ Category Description'
		)->addColumn(
			'ordering',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['nullable' => true,'default' => null],
			'Sort View FAQ Category'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Status Of FAQ Category'
		)->setComment(
			'Cmsmart FAQ Category Table'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}