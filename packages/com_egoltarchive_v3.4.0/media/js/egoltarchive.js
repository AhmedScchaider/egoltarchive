jQuery.noConflict();
var EgoltArchiveResponse = function (){
		if(jQuery('.egoltarchive').width()<660)
		{
			jQuery('.egoltarchive .ti-body #ti-middle').addClass('tablet');			
		}
		else
		{
			jQuery('.egoltarchive .ti-body #ti-middle').removeClass('tablet');					
		}
		
		if(jQuery('.egoltarchive').width()<500)
		{
			jQuery('.egoltarchive .to-body #to-info').addClass('tablet');			
		}
		else
		{
			jQuery('.egoltarchive .to-body #to-info').removeClass('tablet');					
		}
		
		if(jQuery('.egoltarchive').width()<490)
		{
			jQuery('.egoltarchive .flabel').addClass('tablet');
			jQuery('.egoltarchive .categorys').addClass('tablet');
			jQuery('.egoltarchive .authors').addClass('tablet');
			jQuery('.egoltarchive .sort').addClass('tablet');
			jQuery('.egoltarchive .like').addClass('tablet');
			jQuery('.egoltarchive .notlike').addClass('tablet');
		}
		else {
			jQuery('.egoltarchive .flabel').removeClass('tablet');
			jQuery('.egoltarchive .categorys').removeClass('tablet');
			jQuery('.egoltarchive .authors').removeClass('tablet');
			jQuery('.egoltarchive .sort').removeClass('tablet');
			jQuery('.egoltarchive .like').removeClass('tablet');
			jQuery('.egoltarchive .notlike').removeClass('tablet');		
		}
		
		if(jQuery('.egoltarchive').width()<415)
		{
			jQuery('.egoltarchive .dselect').addClass('phone');
		}
		else
		{
			jQuery('.egoltarchive .dselect').removeClass('phone');		
		}
		
		if(jQuery('.egoltarchive').width()<402)
		{
			jQuery('.egoltarchive .aainput').addClass('phone');
			jQuery('.egoltarchive .betdatelabel').addClass('phone');
			jQuery('.egoltarchive .checkexact').addClass('phone');
			jQuery('.egoltarchive .checkexactlabel').addClass('phone');
			jQuery('.egoltarchive .pagecounter').addClass('phone');
		}
		else
		{
			jQuery('.egoltarchive .aainput').removeClass('phone');
			jQuery('.egoltarchive .betdatelabel').removeClass('phone');
			jQuery('.egoltarchive .checkexact').removeClass('phone');
			jQuery('.egoltarchive .checkexactlabel').removeClass('phone');		
			jQuery('.egoltarchive .pagecounter').removeClass('phone');		
		}
		
		if(jQuery('.egoltarchive').width()<300)
		{
			jQuery('.egoltarchive #ti-img').addClass('phone');			
		}
		else
		{
			jQuery('.egoltarchive #ti-img').removeClass('phone');					
		}
}
window.addEvent('domready',function(){
	jQuery('.dsblock-'+egdatetype).slideDown(200);
	if(eghidesearch)
	{
		jQuery('.search-body').hide();
		jQuery('.searchbox-top').prepend('<a class="nodecor showsearch" ><div class="showsbutton" >'+egchangesearchtxt+'</div></a>');	
	}
	jQuery('a.showsearch').click(function(){
		jQuery(this).slideUp(200);		
		jQuery('.search-body').slideDown(400);		
	});
	jQuery('.dsheader').click(function(){
		var sdtype = jQuery(this).attr('id');
		jQuery('.egrbtab').remove();
		jQuery('form#egoltarchivecp').prepend('<input type="hidden" name="rbtab" class="egrbtab" value="'+sdtype+'" />');
		jQuery('.dsblock').slideUp(50);
		jQuery(this).parent().find('.dsblock').slideDown(200);
	});
});