<?php


namespace Netbaseteam\Locator\Setup;

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
                $setup->getTable('cmsmart_localtor'),
                'country',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Country Code'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_localtor'),
                'state',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Address State'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_localtor'),
                'city',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Address City'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_localtor'),
                'zip_code',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Zip Code'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_localtor'),
                'identifier',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Identifier'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_localtor'),
                'url_rewrite_id',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'comment'   => 'Url Rewrite ID'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_work_date'),
                'date_w',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Date Of Week'
                )
            );
            

        }

        $setup->endSetup();
    }

   
}
