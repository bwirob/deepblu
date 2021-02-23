<?php


namespace Netbase\Product\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();

		/* create new table */

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$table = $setup->getConnection()->newTable(
				$setup->getTable('netbase_product_sectiontype')
			)->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
				'Entity Id'
			)->addColumn(
				'title',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Title'
			)->addColumn(
				'alias',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'alias'
			)->setComment(
				'product section type'
			);
			$setup->getConnection()->createTable($table);
		}

		
		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$table = $setup->getConnection()->newTable(
				$setup->getTable('netbase_product_typevalue')
			)->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
				'Entity Id'
			)->addColumn(
				'title',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true],
				'Title'
			)->addColumn(
				'content',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'2M',
				['nullable' => true,'default' => null],
				'Content'
			)->addColumn(
				'alias',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'alias'
			)->addColumn(
				'image',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				['nullable' => true,'default' => null],
				'Product image media path'
			)->setComment(
				'product section type value'
			);
			$setup->getConnection()->createTable($table);
		}
		
		if (version_compare($context->getVersion(), '1.0.3', '<')) {
			$table = $setup->getConnection()->newTable(
				$setup->getTable('netbase_product_homecontent')
			)->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
				'Entity Id'
			)->addColumn(
				'alias_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'alias'
			)->addColumn(
				'typevalue_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'alias'
			)->addColumn(
				'section',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'section'
			)->addColumn(
				'identifier',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'identifier'
			)->addColumn(
				'mtitle',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'mtitle'
			)->addColumn(
				'page_layout',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => true,'default' => null],
				'page_layout'
			)->setComment(
				'product section type value'
			);
			$setup->getConnection()->createTable($table);
		}
		
        $setup->endSetup();
    }
}