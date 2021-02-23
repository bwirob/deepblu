<?php


namespace Cmsmart\Categoryicon\Setup;

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
		 * Creating table cmsmart_categoryicon
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_categoryicon')
		)->addColumn(
			'categoryicon_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'category_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Category Id'
		)->addColumn(
			'class_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'class_name'
		)
		->setComment(
			'Categoryicon item'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}