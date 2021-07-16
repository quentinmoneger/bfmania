var CKconfig = {
    format_tags: 'p;h2;h3;h4;h5;h6;pre;address',
    //	colorButton_colors: '212529,C00032,850E36,F9B53B,F78E1E,666,F6F3EF',
    //	colorButton_enableMore: false,
    extraPlugins: 'simplebutton',
    filebrowserBrowseUrl: urlBackMediasCkeditor,
    filebrowserWindowWidth: '60%',
    filebrowserWindowHeight: '50%',
    toolbar: [
        ['TextColor', 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
        ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
        ['Undo', 'Redo', '-', 'simplebutton', 'Link', 'Unlink', 'Anchor', '-', 'Image', 'HorizontalRule'],
    ],
};

CKEDITOR.disableAutoInline = true;
$('[contenteditable="true"]').each(function() {
    CKEDITOR.replace(this, CKconfig);
});


$(function() {
    $('.awmedias').awMedias({
        url_get: urlAjaxMedialist,
        url_save: urlAjaxMediaUpload,
    });
});