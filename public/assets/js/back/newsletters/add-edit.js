
window.onload = () => { 

$('button[name="opendiv"]').on('click', function(e){
	e.preventDefault();
	target = $(this).attr('id');

	$('div[data-target]').not('[data-target="'+target+'"]').hide();
	$('div[data-target="'+target+'"]').show();
});

var CKupload = urlBackUploadCkeditor;
var CKConfig = {
	allowedContent: true,
	extraPlugins: 'simplebutton',
	uploadUrl: CKupload,
	imageUploadUrl: CKupload + '?Type=Image',
	filebrowserImageUploadUrl: CKupload + '?Type=Image',  
	toolbar: [
		[ 'FontSize', 'TextColor', '-', 'simplebutton', 'Link', 'Unlink', 'Image', '-', 'Undo', 'Redo' ],
		'/',
		[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
		[ 'NumberedList', 'BulletedList' ],
		[ 'Bold', 'Italic', 'Underline', 'Strike' ],
		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord' ],
	],		
};

CKEDITOR.disableAutoInline = true;
CKEDITOR.inline('editor', CKConfig);

}