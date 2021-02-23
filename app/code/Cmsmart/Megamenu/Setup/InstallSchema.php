<?php


namespace Cmsmart\Megamenu\Setup;

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
		 * Creating table cmsmart_megamenu
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmsmart_megamenu')
		)
		->addColumn(
			'megamenu_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)
		->addColumn(
			'category_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Category Id'
		)
		->addColumn(
			'top_block_top',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Top Block Top'
		)
		->addColumn(
			'top_block_left',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Top Block Left'
		)	
		->addColumn(
			'top_block_right',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Top Block Right'
		)
		->addColumn(
			'top_block_bottom',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Top Block Bottom'
		)
		->addColumn(
			'top_label',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Label'
		)
		->addColumn(
			'top_sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Sku'
		)
		->addColumn(
			'top_label_container',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Lablel Container'
		)
		->addColumn(
			'top_label_container',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Lablel Container'
		)
		->addColumn(
			'left_block_top',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Left Block Top'
		)
		->addColumn(
			'left_block_left',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Left Block Left'
		)
		->addColumn(
			'left_block_right',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Top Block Right'
		)
		->addColumn(
			'left_block_bottom',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true,'default' => null],
			'Top Block Bottom'
		)
		->addColumn(
			'left_label',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Left Title'
		)
		->addColumn(
			'left_sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Left SKU'
		)
		->addColumn(
			'left_label_container',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Left Label Container'
		)
		->addColumn(
			'left_cat_icon',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Left Icon'
		)
		->addColumn(
			'top_left_block_sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Left Sku'
		)
		->addColumn(
			'top_right_block_sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Right Sku'
		)
		->addColumn(
			'top_pgrid_box_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Pgrid Title'
		)
		->addColumn(
			'top_pgrid_box_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Pgrid Title'
		)
		->addColumn(
			'top_pgrid_num_columns',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Top Pgrid Num_column'
		)
		->addColumn(
			'top_pgrid_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true],
			'pgrid_products'
		)
		->addColumn(
			'top_left_sku_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'left_sku_title'
		)
		->addColumn(
			'top_right_sku_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'right_sku_title'
		)
		->addColumn(
			'top_pgrid_cats',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'right_sku_title'
		)
		->addColumn(
			'top_content_block',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true],
			'content_block'
		)
		->addColumn(
			'position',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'position'
		)
		->addColumn(
			'top_hot_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'top_hot_products'
		)
		
		->addColumn(
			'top_new_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'top_new_products'
		)
		->addColumn(
			'top_sale_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'top_sale_products'
		)
		->addColumn(
			'top_content_type',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'top_content_type'
		)
		
		->addColumn(
			'left_content_type',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'ver_content_type'
		)
		->addColumn(
			'left_left_sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_left_sku'
		)
		->addColumn(
			'left_right_sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_right_sku'
		)
		->addColumn(
			'left_pgrid_box_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_pgrid_box_title'
		)
		
		->addColumn(
			'left_pgrid_num_columns',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_pgrid_num_columns'
		)
		->addColumn(
			'left_pgrid_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'ver_pgrid_products'
		)
		->addColumn(
			'left_left_sku_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_left_sku_title'
		)
		
		->addColumn(
			'left_right_sku_title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_right_sku_title'
		)
		->addColumn(
			'left_pgrid_cats',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'ver_pgrid_cats'
		)
		->addColumn(
			'left_content_block',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'2M',
			['nullable' => true],
			'ver_content_block'
		)
		
		->addColumn(
			'left_hot_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'ver_hot_products'
		)
		->addColumn(
			'left_new_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'ver_new_products'
		)
		->addColumn(
			'left_sale_products',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'1M',
			['nullable' => true],
			'ver_sale_products'
		)->setComment(
			'Megamenu item'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}