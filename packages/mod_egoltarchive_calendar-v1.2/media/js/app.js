jQuery.noConflict();
window.addEvent('domready',function(){
	jQuery('.egarclmod-ttp').tooltipster({delay:'100'});
	jQuery('body').on('click', '.pnx', (function(){
		jQuery('.egcal table').replaceWith('<div class="egajaxloder" ><img src="'+egjroot+'media/mod_egoltarchive_calendar/images/loading.gif" /></div>');
		var ddt = jQuery(this).attr('id');
		jQuery.ajax ({
					type: "POST", 
					url: egjroot+"modules/mod_egoltarchive_calendar/ajax.php",
					data: "ddt="+ ddt, 
					success: function(data){ 
						jQuery('.egcal').html(data);
						jQuery('.egarclmod-ttp').tooltipster({delay:'100'});
					}
			});
    }));
});
