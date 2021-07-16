$(function(){

	// tooltip 
	$('[data-toggle="tooltip"]').tooltip();
	
	// ligthbox
	$(document).on('click', '[data-toggle="lightbox"]', function(e) {
		e.preventDefault();
		$(this).ekkoLightbox();
	});

	// désactive le submit sur la touche entrée
	$('.no-submit-press-enter').keypress(function(e){
		if(e.which == 13 || e.keyCode == 13){
			e.preventDefault();
			console.log('Enter submit disabled for this input')
		}			
	});

	// Autoselect
	$('.autoselect').each(function(){
		$(this).find('option:selected').removeAttr('selected');
		select = $(this).find('option[value="'+ $(this).data('select') + '"]');
		select.attr('selected', 'selected');
	});

	//Dashboard
	$('#reso').text(screen.width+'px * '+screen.height+'px');

});
