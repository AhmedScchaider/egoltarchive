window.addEvent('domready',function(){
	EgoltArchiveResponse();
	jQuery(window).resize(function(){	
		EgoltArchiveResponse();
	});
});