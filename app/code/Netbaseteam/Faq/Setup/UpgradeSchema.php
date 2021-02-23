<?php


namespace Netbaseteam\Faq\Setup;

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
                $setup->getTable('cmsmart_faq_category'),
                'faq_ids',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Selected FAQ'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'store_ids',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Selected Store View'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'icon',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'FAQ Category Icon'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'fontsize',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Font Size'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'text_color',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Text Color'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'background_color',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Background Color'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'border_color',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Border Color'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'border_width',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Border Width'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'active_color',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Active Color'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'active_background',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Active Background'
                )
            );

             $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'category_fontsize',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Category FontSize'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq_category'),
                'category_color',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Category Color'
                )
            );

            

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq'),
                'store_ids',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Selected Store View'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq'),
                'product_ids',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'comment'   => 'Selected Products'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq'),
                'sidebar_faq',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'comment'   => 'Selected FAQ to attach in sidebar'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq'),
                'author_name',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Athor Name'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('cmsmart_faq'),
                'author_email',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'comment'   => 'Athor Email'
                )
            );



        }

        $setup->endSetup();
    }

   
}
