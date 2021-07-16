var CKconfig = {
	format_tags: 'p;h2;h3;h4;h5;h6;pre;address',
//	colorButton_colors: '212529,C00032,850E36,F9B53B,F78E1E,666,F6F3EF',
//	colorButton_enableMore: false,
	extraPlugins: 'simplebutton',
	filebrowserBrowseUrl: urlBackMediasCkeditor ,
	filebrowserWindowWidth: '60%',
	filebrowserWindowHeight: '50%',
	toolbar: [
		[ 'FontSize', 'Format', 'TextColor'],
		[ 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat' ],
		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
		'/',
		[ 'Undo', 'Redo', '-', 'simplebutton', 'Link', 'Unlink', 'Anchor', '-', 'Image', 'HorizontalRule' ],
		[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'NumberedList', 'BulletedList',  'Table', '-', 'Outdent', 'Indent' ],
		[ 'Smiley', 'Source' ],
	],
};

CKEDITOR.disableAutoInline = true;
$('[contenteditable="true"]').each(function(){
	CKEDITOR.inline(this, CKconfig);
});


$(function(){
	$('.awmedias').awMedias({
		url_get: urlAjaxMedialist, 
		url_save: urlAjaxMediaUpload, 
	});
});