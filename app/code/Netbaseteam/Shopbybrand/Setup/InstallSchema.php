<?php
namespace Netbaseteam\Shopbybrand\Setup;

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
		 * Creating table cmsmart_shopbybrand
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_shopbybrand')
		)->addColumn(
			'brand_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'brand_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Brand Name '
		)->addColumn(
			'meta_keyworlds',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Meta Keywords'
		)->addColumn(
			'meta_description',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Meta Description'
		)->addColumn(
			'description',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			99999,
			['nullable' => true,'default' => null],
			'Description'
		)->addColumn(
			'urlkey',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Urlkey'
		)->addColumn(
			'store_ids',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Store IDS '
		)->addColumn(
			'featured',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			'2M',
			['nullable' => true,'default' => null],
			'Featured'
		)->addColumn(
			'logo',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Logo'
		)->addColumn(
			'product_ids',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => false],
			'Product Ids'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Status'
		)->addColumn(
			'banner',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Banner'
		)->setComment(
			'Shopbybrand item'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}