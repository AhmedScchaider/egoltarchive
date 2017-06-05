// jQuery.noConflict();
window.addEvent('domready',function(){

	var parent = 'li:first';
	if($(".row-fluid").length){
		parent = 'div.control-group:first';
	}
    $("#jform_params_assets-lbl").parents(parent).remove();
	
    $("h3#ti_setting-options").parent().hide();	
    $("h3#to_setting-options").parent().hide();	
    $("h3#fd_setting-options").parent().hide();	
    $("h3#ig_setting-options").parent().hide();	
    $("h3#paging-options").parent().hide();	
    $("h3#searchbox-options").parent().hide();	

	// view type parameters
	var sview = $('select#jform_params_view_type').val();
	$('h3#'+sview+'_setting-options').parent().slideDown();
	if(sview != 'fd')
	{
		$("h3#searchbox-options").parent().slideDown();			
		$("h3#paging-options").parent().slideDown();			
	}	
	$('select#jform_params_view_type').change(function () {
		$('select#jform_params_view_type option').each(function()
		{
			var stemp = $(this).val();
			$('h3#'+stemp+'_setting-options').parent().slideUp();
		});
		$("h3#searchbox-options").parent().slideUp();	
		$("h3#paging-options").parent().slideUp();	
		
		var sview = $(this).val();
		$('h3#'+sview+'_setting-options').parent().slideDown();
		if(sview != 'fd')
		{
			$("h3#searchbox-options").parent().slideDown();			
			$("h3#paging-options").parent().slideDown();			
		}
	});	
	
	// archive source parameters
	var sview = $('select#jform_params_content_service').val();
	$('h3#'+sview+'_setting-options').parent().slideDown();
	$('select#jform_params_content_service').change(function () {
		$('select#jform_params_content_service option').each(function()
		{
			var stemp = $(this).val();
			$('h3#'+stemp+'_setting-options').parent().slideUp();
		});
		
		var sview = $(this).val();
		$('h3#'+sview+'_setting-options').parent().slideDown();
	});
	
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

});
