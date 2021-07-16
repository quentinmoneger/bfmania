window.onload = () => { 

var CKconfig = {
    format_tags: 'p;h2;h3;h4;h5;h6;pre;address',
    //colorButton_colors: '212529,C00032,850E36,F9B53B,F78E1E,666,F6F3EF',
    //colorButton_enableMore: false,
    extraPlugins: 'simplebutton',
    filebrowserBrowseUrl: urlBackMediasCkeditor ,
    filebrowserWindowWidth: '60%',
    filebrowserWindowHeight: '50%',
    toolbar: [
        // 'FontSize', 'Format',
        ['TextColor', 'BGColor', 'Bold', 'Italic', 'Blockquote', 'Underline', 'RemoveFormat'],
        ['Cut', 'Copy', 'Paste' ], // , 'PasteText', 'PasteFromWord' 
        ['NumberedList', 'BulletedList', 'Table', 'Outdent', 'Indent'],
        ['JustifyLeft', 'JustifyCenter', 'JustifyRight'], 
        ['Link', 'Unlink', 'Image', 'VideoEmbed', 'Smiley'],
    ],
    smiley_columns: 12,
};


CKEDITOR.disableAutoInline = true;
$('[contenteditable="true"]').each(function() {
    CKEDITOR.replace(this, CKconfig);
});

}