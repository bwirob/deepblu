document.observe("dom:loaded", function() {
	setControlsVer();
	setNoneDefaultVer();
	//setNoneOtherControlsLeft();
	
	function setNoneDefaultVer(){
		var prefix = "field-";
		$$('.'+prefix+'left_block_top')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_block_left')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_left_sku')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_left_sku_title')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_block_right')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_right_sku_title')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_block_right')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_right_sku')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_block_bottom')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_label_container')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_sku')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_cat_icon')[0].setStyle({display:'none'});
		
		$('megamenu_vertical_ver_top_fieldset').setStyle({display:'none'});
		$('megamenu_vertical_ver_left_fieldset').setStyle({display:'none'});
		$('megamenu_vertical_ver_right_fieldset').setStyle({display:'none'});
		$('megamenu_vertical_ver_bottom_fieldset').setStyle({display:'none'});
		$('megamenu_vertical_ver_spec_fieldset').setStyle({display:'none'});
		
		if($F('megamenu_vertical_left_content_type') == 2) {
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
		}
		
		if($F('megamenu_vertical_left_content_type') == 0) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
		}
		
		if($F('megamenu_vertical_left_content_type') == 1) {
		/* Default_Category_Listing */
			setBlokDefaultVer();
			setNoneOtherControlsLeft();
		} 
	}
	
	function setBlokDefaultVer(){
		var prefix = "field-";
		$$('.'+prefix+'left_block_top')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_block_left')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_left_sku')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_left_sku_title')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_block_right')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_right_sku_title')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_right_sku')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_block_right')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_block_bottom')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_label_container')[0].setStyle({display:'block'});
		$$('.'+prefix+'left_sku')[0].setStyle({display:'block'});
		
		$('megamenu_vertical_ver_top_fieldset').setStyle({display:'block'});
		$('megamenu_vertical_ver_left_fieldset').setStyle({display:'block'});
		$('megamenu_vertical_ver_right_fieldset').setStyle({display:'block'});
		$('megamenu_vertical_ver_bottom_fieldset').setStyle({display:'block'});
		$('megamenu_vertical_ver_spec_fieldset').setStyle({display:'block'});
	}
	
	function setNoneOtherControlsLeft(){
		var prefix = "field-";
		$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_content_block')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
		$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
	}
	
	function setControlsVer(){
		var prefix = "field-";
		if($F('megamenu_vertical_left_content_type') == 1) {
		/* Default_Category_Listing */
			setBlokDefaultVer();
			setNoneOtherControlsLeft();
		} 
		
		/* Dynamic_Category_Listing */
		if($F('megamenu_vertical_left_content_type') == 2) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'block'});
			setNoneDefaultVer();
		} 
		
		/* Static_Category_Listing */
		if($F('megamenu_vertical_left_content_type') == 3) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefaultVer();
		} 
		
		/* Product_Grid */
		if($F('megamenu_vertical_left_content_type') == 4) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefaultVer();
		} 
		
		/* Product_Listing */
		if($F('megamenu_vertical_left_content_type') == 5) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'block'});
			
			setNoneDefaultVer();
		} 
		
		/* Product_Listing_Category */
		if($F('megamenu_vertical_left_content_type') == 6) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefaultVer();
		} 
		
		/* Content */
		if($F('megamenu_vertical_left_content_type') == 7) {
			$$('.'+prefix+'left_pgrid_num_columns')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_content_block')[0].setStyle({display:'block'});
			$$('.'+prefix+'left_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'left_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefaultVer();
		} 
	}
	
	$('megamenu_vertical_left_content_type').observe('change', function(){
		setNoneDefaultVer();
		setNoneOtherControlsLeft();
		setControlsVer();
	});
	
});