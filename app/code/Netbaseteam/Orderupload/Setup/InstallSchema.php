<?php

namespace Netbaseteam\Orderupload\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements InstallSchemaInterface
{

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface    $context)
	{
		$installer = $setup;
		$installer->startSetup();
		
		$eavTable = $installer->getTable('quote_item');
		$columns = [
			'session_file' => [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => false,
				'comment' => 'session_file',
			],
		];

		$connection = $installer->getConnection();
		foreach ($columns as $name => $definition) {
			$connection->addColumn($eavTable, $name, $definition);
		}
		
		/****************************************************/
		
		$eavTable1 = $installer->getTable('sales_order_item');
		$columns1 = [
			'session_file' => [
				'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				'nullable' => false,
				'comment' => 'session_file',
			],
		];

		foreach ($columns1 as $name => $definition) {
			$connection->addColumn($eavTable1, $name, $definition);
		}

		$installer->endSetup();
	}
}