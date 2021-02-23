<?php


namespace Netbaseteam\Locator\Setup;

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
		 * Creating table cmsmart_localtor
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_localtor')
		)->addColumn(
			'localtor_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Store Location Id'
		)->addColumn(
			'store_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Store Name'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Store Status'
		)->addColumn(
			'description',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Store Description'
		)->addColumn(
			'store_link',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Store Website link'
		)->addColumn(
			'ordering',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => true,'default' => null],
			'Sort Order In List Store'
		)->addColumn(
			'phone_number',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Phone Number Contact'
		)->addColumn(
			'email',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Email Contact'
		)->addColumn(
			'fax_number',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Fax Number Contact'
		)->addColumn(
			'address',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Address Name of Store'
		)->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Identifier Url'
        )->addColumn(
			'schedule_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => true],
			'Schedule Id'
		)->addColumn(
			'latitude',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Latitude Of location'
		)->addColumn(
			'longitude',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'Longitude Of location'
		)->addColumn(
			'zoom_level',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Zoom Level'
		)->addColumn(
			'marker_icon',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Marker Icon'
		)->addColumn(
			'store_image',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Store Image'
		)->addColumn(
			'store_tag',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Store Tag'
		)->setComment(
			'Store Location Info'
		);
		$installer->getConnection()->createTable($table);


		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_schedule')
		)->addColumn(
			'schedule_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Schedule Id'
		)->addColumn(
            'schedule_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Schedule Name'
        )->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Status Schedule '
		)->setComment(
			'Cmsmart Shedule List'
		);
		$installer->getConnection()->createTable($table);
		
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_work_date')
		)->addColumn(
			'work_date_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			11,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Work Date Id'
		)->addColumn(
            'schedule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            ['nullable' => false],
            'Schedule Id'
        )->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			6,
			['nullable' => false],
			'Status Schedule '
		)->addColumn(
            'open_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Open Time'
        )->addColumn(
            'open_break_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Open Break Time'
        )->addColumn(
            'close_break_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Close Break Time'
        )->addColumn(
            'close_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Close Time'
        )->setComment(
			'Cmsmart Shedule Days'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}