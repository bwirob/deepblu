<?php
namespace Cmsmart\Megamenu\Model;

class Contenttype
{
	const Default_Category_Listing		= 1;
    const Dynamic_Category_Listing		= 2;
    const Static_Category_Listing		= 3;
    const Product_Grid					= 4;
    const Product_Listing				= 5;
    const Product_Listing_Category	    = 6;
    const Contact	   					= 7;
	
    public function getTypes()
    {	
      	$dur = array();
      	$dur[] = array('value' => "", 'label'=> __('-- Please select type --'));
      	$dur[] = array('value' => self::Default_Category_Listing, 'label'=> __('Default Category Listing'));
      	$dur[] = array('value' => self::Dynamic_Category_Listing, 'label'=> __('Dynamic Category Listing'));
      	$dur[] = array('value' => self::Static_Category_Listing, 'label'=> __('Static Category Listing'));
		$dur[] = array('value' => self::Product_Grid, 'label'=> __('Product Grid'));
		$dur[] = array('value' => self::Product_Listing, 'label'=> __('Product Listing'));
		$dur[] = array('value' => self::Product_Listing_Category, 'label'=> __('Dynamic Product Listing by Category'));
		$dur[] = array('value' => self::Contact, 'label'=> __('Content'));
        return $dur;
    }
}