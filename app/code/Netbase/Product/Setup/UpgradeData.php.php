<?php
namespace Netbase\Product\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{

	public function upgrade( ModuleDataSetupInterface $setup, ModuleContextInterface $context ) {
		$setup->startSetup();
		
		if (version_compare($context->getVersion(), '1.0.4', '<')) {
			$setup->getConnection()->query("
				INSERT INTO `".$setup->getTable('netbase_product_sectiontype')."` 
						(`title`, `alias`) VALUES
						('Slider', 'slider'),
						('Service', 'service'),
						('Banner', 'banner'),
						('Bestseller Product', 'bestseller'),
						('New Product', 'new_product'),
						('Deal Product', 'deal_product'),
						('Featured Product', 'featured_product'),
						('Brand', 'brand'),
						('Custom Static Block', 'custom_static_block')
				");
				
			$setup->getConnection()->query("
				INSERT INTO `".$setup->getTable('netbase_product_typevalue')."` 
						(`title`, `content`, `alias`, `image`) VALUES	
						INSERT INTO `netbase_product_typevalue` VALUES ('Slider Layout 01', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_slider_01\"}}', '1', 'slider-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Slider Layout 02', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_slider_02\"}}', '1', 'slider-02.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Slider Layout 03', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_slider_03\"}}', '1', 'slider-03.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Slider Layout 04', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_slider_04\"}}', '1', 'slider-04.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Slider Layout 05', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_slider_05\"}}', '1', 'slider-05.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Service Layout 01', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_service_01\"}}', '2', 'policy-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Service Layout 02', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_service_02\"}}', '2', 'policy-02.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 01', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_01\"}}', '3', 'banner-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 02', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_02\"}}', '3', 'banner-02.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 03', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_03\"}}', '3', 'banner-03.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 04', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_04\"}}', '3', 'banner-04.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 05', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_05\"}}', '3', 'banner-05.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 06', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_06\"}}', '3', 'banner-06.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 07', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_07\"}}', '3', 'banner-07.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 08', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_08\"}}', '3', 'banner-08.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Banner Layout 09', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_banner_09\"}}', '3', 'banner-09.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Bestseller Product Layout 01', '{{block class=\"Netbase\\Product\\Block\\Bestseller\" template=\"Netbase_Product::Bestseller.phtml\"}}', '4', 'bestseller-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Bestseller Product Layout 02', '<div class=\" bestseller bestseller-home-3\">{{block class=\"Netbase\\Product\\Block\\Bestseller\" template=\"Netbase_Product::Bestseller-no-img.phtml\"}}</div>', '4', 'bestseller-02.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Bestseller Product Layout 03', '<div class=\"bestseller bestseller-6\">{{block class=\"Netbase\\Product\\Block\\Bestseller\" template=\"Netbase_Product::Bestseller-all-pro.phtml\"}}</div>', '4', 'bestseller-03.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('New Product Layout 01', '{{block class=\"Netbase\\Product\\Block\\NewProduct\" template=\"Netbase_Product::New.phtml\"}}', 'new-product-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('New Product Layout 02', '<div class=\"new-6 container\">{{block class=\"Netbase\\Product\\Block\\NewProduct\" template=\"Netbase_Product::New-no-img.phtml\"}}</div>', 'new-product-02.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('New Product Layout 03', '<div class=\"new-home-4 container\">{{block class=\"Netbase\\Product\\Block\\NewProduct\" template=\"Netbase_Product::New-all-pro.phtml\"}}</div>', 'new-product-03.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Deal Product Layout 01', '{{block class=\"Netbase\\Product\\Block\\Deals\" template=\"Netbase_Product::Deals.phtml\"}}', '6', 'deal-product-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Featured Product Layout 01', '{{block class=\"Netbase\\Product\\Block\\ProductByAttribute\" category_id=\"21\"&nbsp;attribute_id=\"nb_featured\" template=\"Netbase_Product::Featured.phtml\"}}', '7', 'featured-product-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Featured Product Layout 02', '<div class=\"featured-6 container\">\r\n    {{block class=\"Netbase\\Product\\Block\\ProductByAttribute\" category_id=\"21\"&nbsp;attribute_id=\"nb_featured\" template=\"Netbase_Product::Featured-no-img.phtml\"}} \r\n</div>', '7', 'featured-product-02.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Featured Product Layout 03', '<div class=\"featured-home-4 container\">{{block class=\"Netbase\\Product\\Block\\ProductByAttribute\" category_id=\"21\"&nbsp;attribute_id=\"nb_featured\" template=\"Netbase_Product::Featured-all-pro.phtml\"}}</div>', '7', 'featured-product-03.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Brand Layout 01', '{{block class=\"Cmsmart\\Brandcategory\\Block\\Brandcategory\" template=\"Cmsmart_Brandcategory::brand_view.phtml\"}}', '8', 'brand-01.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('Slider Layout 06', '{{block class=\"Magento\\Cms\\Block\\Block\" block_id=\"home_slider_06\"}}', '1', 'slider-06.jpg');
						INSERT INTO `netbase_product_typevalue` VALUES ('slider 07', 'slider 07', '1', null)
			");
		}
		
        $setup->endSetup();
	}
}