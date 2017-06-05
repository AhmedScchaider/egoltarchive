jQuery.noConflict();
window.addEvent('domready',function(){

    jQuery("#jform_params_assets-lbl").parent().parent().remove();	
	
	// Multi Select
	jQuery('select#jformparamscategory_inc').empty();
	jQuery('select#jformparamscategory_not_inc').empty();
	var cnd2 = jQuery('select#jform_params_content_service').val();
	jQuery.ajax ({
				type: "POST", 
				url: egjroot+"components/com_egoltarchive/ajaxcats.php",
				data: "source="+ cnd2, 
				success: function(data){ 
					jQuery('select#jformparamscategory_inc').append(jQuery(data).html());
					jQuery('select#jformparamscategory_inc').val(egnval_jformparamscategory_inc);
					jQuery('select#jformparamscategory_inc').trigger('liszt:updated');
					jQuery('select#jformparamscategory_inc').trigger('chosen:updated');
					
					jQuery('select#jformparamscategory_not_inc').append(jQuery(data).html());
					jQuery('select#jformparamscategory_not_inc').val(egnval_jformparamscategory_not_inc);
					jQuery('select#jformparamscategory_not_inc').trigger('liszt:updated');
					jQuery('select#jformparamscategory_not_inc').trigger('chosen:updated');
				}
	});

	jQuery('select#jform_params_content_service').change(function () {
		var cnd = jQuery(this).val();
		jQuery('select#jformparamscategory_inc').empty();
		jQuery('select#jformparamscategory_not_inc').empty();
		jQuery.ajax ({
					type: "POST", 
					url: egjroot+"components/com_egoltarchive/ajaxcats.php",
					data: "source="+ cnd, 
					success: function(data){ 
						jQuery('select#jformparamscategory_inc').append(jQuery(data).html());
						if(cnd2 == cnd)
							jQuery('select#jformparamscategory_inc').val(egnval_jformparamscategory_inc);
						jQuery('select#jformparamscategory_inc').trigger('liszt:updated');
						jQuery('select#jformparamscategory_inc').trigger('chosen:updated');
						
						jQuery('select#jformparamscategory_not_inc').append(jQuery(data).html());
						if(cnd2 == cnd)
							jQuery('select#jformparamscategory_not_inc').val(egnval_jformparamscategory_not_inc);
						jQuery('select#jformparamscategory_not_inc').trigger('liszt:updated');
						jQuery('select#jformparamscategory_not_inc').trigger('chosen:updated');
					}
		});
	});

	
	// archive source parameters
	// var sview = jQuery('select#jform_params_content_service').val();
	// jQuery('h3#'+sview+'_setting-options').parent().slideDown();
	// jQuery('select#jform_params_content_service').change(function () {
		// jQuery('select#jform_params_content_service option').each(function()
		// {
			// var stemp = jQuery(this).val();
			// jQuery('h3#'+stemp+'_setting-options').parent().slideUp();
		// });
		
		// var sview = jQuery(this).val();
		// jQuery('h3#'+sview+'_setting-options').parent().slideDown();
	// });

});
