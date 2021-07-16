loadMediasList();

$('#dropbtn').click( function(){
	$('#dropzone').slideToggle('slow', function(){
		display = $(this).css('display');

		if(display == 'block'){
			$('#dropbtn').text('Masquer').blur();
		}
		else if(display == 'none'){
			$('#dropbtn').text('Ajouter').blur();
		}
	});
});
$('button[name="open_file"]').click(function(e){
	e.preventDefault();
	$('input#file').click();
	$('#dropfile').css('border', '3px dashed green');
	$(this).blur();
});

$("#file").one('change', function(e){
	var file = document.getElementById('file').files[0];

	if(file.size < MaxSize){

		var fd = new FormData($(this).closest('form')[0]);
		uploadData(fd);
	}
	else {
		alert('Le poids du fichier dépasse la taille maximum autorisée');
	}

});

$(document).on('dragenter', '#dropfile', function() {
	$(this).css('border', '3px dashed red');
	
	return false;
}).on('dragover', '#dropfile', function(e){
	e.preventDefault();
	e.stopPropagation();
	
	$(this).css('border', '3px dashed red');
	
	return false;
}).on('dragleave', '#dropfile', function(e) {
	e.preventDefault();
	e.stopPropagation();
	
	$(this).css('border', '3px dashed #bbb');
	
	return false;

}).on('drop', '#dropfile', function(e) {
	if(e.originalEvent.dataTransfer){
		if(e.originalEvent.dataTransfer.files.length) {
			e.preventDefault();
			e.stopPropagation();
			$(this).css('border', '3px dashed green');
			
			var file = e.originalEvent.dataTransfer.files;
			if(file[0].size < MaxSize){
				var fd = new FormData();
				fd.append('file', file[0]);

				uploadData(fd);
			}
			else {
				alert('Le poids du fichier dépasse la taille maximum autorisée');
			}
		}
	}
	else {
		$(this).css('border', '3px dashed #bbb');
	}

	return false;
});

function uploadData(formdata){

	$.ajax({
		url: urlAjaxMediaUpload , 
		type: 'post',
		data: formdata,
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function(res){
		},
		xhr: function(){
			var xhr = $.ajaxSettings.xhr() ;

			xhr.upload.onprogress = function(evt){ 
				total = evt.loaded/evt.total*100;
				$('[role="progressbar"]').css('width', parseInt(total) + '%').attr('aria-valuenow', parseInt(total)).text(parseInt(total) + ' %');
			};
			xhr.upload.onload = function(){ 
				$('#dropfile').css('border', '3px dashed #bbb');
				//loadMediasList();
			};
			return xhr;
		}
	}).done(function(data){
		loadMediasList();
		setTimeout(function(){
			$('[role="progressbar"]:not(".disabled")').css('width', '0%').attr('aria-valuenow', 0).text(' ');
		}, 1000);
	});
}

function loadMediasList() {
	$.get( urlAjaxMedialist, {action: Action}, function(html){
		$('#media-list').html(html);
		var mediaBox = $('#media-list').width();
		var figure = countColItem(mediaBox);
		var size = figure - 16;

		$('figure.is-media').css({
			'height': size + 'px',
			'width': size + 'px',
		});

		$('.selectable-editor').click(function(e){
			myFile = $(this);
			console.log();

			function getUrlParam(paramName) {
				var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' );
				var match = window.location.search.match(reParam);

				return (match && match.length > 1) ? match[1] : null;
			}

			function returnFileUrl(fileUrl) {
				var funcNum = getUrlParam('CKEditorFuncNum');
				window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
				window.close();
			}

			returnFileUrl(myFile.data('awmediaTmpName'))

		});
	});
}

function countColItem(widthBox)
{
	var figure = widthBox / 10;
	if(figure < 100){
		nb_items = Math.round(widthBox / 100) - 1;
		figure = widthBox / nb_items;
	}
	return figure;
}