document.observe("dom:loaded", function() {
	setControls();
	setTopLeftTab();
	setNoneDefault();
	//setNoneOtherControls();
	
	function setNoneDefault(){
		var prefix = "field-";
		$$('.'+prefix+'top_block_top')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_block_left')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_left_block_sku')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_left_sku_title')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_block_right')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_right_sku_title')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_right_block_sku')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_block_bottom')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_label_container')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_sku')[0].setStyle({display:'none'});	
		
		$('megamenu_horizontal_top_fieldset').setStyle({display:'none'});
		$('megamenu_horizontal_left_fieldset').setStyle({display:'none'});
		$('megamenu_horizontal_right_fieldset').setStyle({display:'none'});
		$('megamenu_horizontal_bottom_fieldset').setStyle({display:'none'});
		$('megamenu_horizontal_spec_fieldset').setStyle({display:'none'});
		
		
		if($F('megamenu_horizontal_top_content_type') == 2) {
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
		}
		
		if($F('megamenu_horizontal_top_content_type') == 0) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
		}
		
		/* Default_Category_Listing */
		if($F('megamenu_horizontal_top_content_type') == 1) {
			setBlokDefault();
			setNoneOtherControls();
		} 
	}
	
	function setBlokDefault(){
		var prefix = "field-";
		$$('.'+prefix+'top_block_top')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_block_left')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_left_block_sku')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_left_sku_title')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_block_right')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_right_sku_title')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_right_block_sku')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_block_bottom')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_label_container')[0].setStyle({display:'block'});
		$$('.'+prefix+'top_sku')[0].setStyle({display:'block'});
		
		$('megamenu_horizontal_top_fieldset').setStyle({display:'block'});
		$('megamenu_horizontal_left_fieldset').setStyle({display:'block'});
		$('megamenu_horizontal_right_fieldset').setStyle({display:'block'});
		$('megamenu_horizontal_bottom_fieldset').setStyle({display:'block'});
		$('megamenu_horizontal_spec_fieldset').setStyle({display:'block'});
	}
	
	function setNoneOtherControls(){
		var prefix = "field-";
		$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_content_block')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
		$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
	}
	
	function setControls(){
		var prefix = "field-";
		/* Default_Category_Listing */
		if($F('megamenu_horizontal_top_content_type') == 1) {
			setBlokDefault();
			setNoneOtherControls();
		} 
		
		/* Dynamic_Category_Listing */
		if($F('megamenu_horizontal_top_content_type') == 2) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'block'});
			setNoneDefault();
		} 
		
		/* Static_Category_Listing */
		if($F('megamenu_horizontal_top_content_type') == 3) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefault();
		} 
		
		/* Product_Grid */
		if($F('megamenu_horizontal_top_content_type') == 4) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefault();
		} 
		
		/* Product_Listing */
		if($F('megamenu_horizontal_top_content_type') == 5) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'block'});
			
			setNoneDefault();
		} 
		
		/* Product_Listing_Category */
		if($F('megamenu_horizontal_top_content_type') == 6) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefault();
		} 
		
		/* Content */
		if($F('megamenu_horizontal_top_content_type') == 7) {
			$$('.'+prefix+'top_pgrid_num_columns')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_cats')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_content_block')[0].setStyle({display:'block'});
			$$('.'+prefix+'top_pgrid_box_title')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_pgrid_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_hot_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_new_products')[0].setStyle({display:'none'});
			$$('.'+prefix+'top_sale_products')[0].setStyle({display:'none'});
			
			setNoneDefault();
		} 
	}
	
	function setTopLeftTab(){
		if($F('megamenu_main_position') == "top") {
			$('page_tabs_horizontal_section').setStyle({display:'block'});
			$('page_tabs_vertical_section').setStyle({display:'none'});
		}
		
		if($F('megamenu_main_position') == "left") {
			$('page_tabs_horizontal_section').setStyle({display:'none'});
			$('page_tabs_vertical_section').setStyle({display:'block'});
		}
		
		if($F('megamenu_main_position') == "both") {
			$('page_tabs_horizontal_section').setStyle({display:'block'});
			$('page_tabs_vertical_section').setStyle({display:'block'});
		}
		
		if($F('megamenu_main_position') == "") {
			$('page_tabs_horizontal_section').setStyle({display:'none'});
			$('page_tabs_vertical_section').setStyle({display:'none'});
		}
	}
	
	$('megamenu_horizontal_top_content_type').observe('change', function(){
		setNoneDefault();
		setNoneOtherControls();
		setControls();
	});
	
	$('megamenu_main_position').observe('change', function(){
		setTopLeftTab();
	});
});