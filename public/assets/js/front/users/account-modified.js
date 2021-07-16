$('#register').click(function(){
    $('#hiddenbtn').click();
})

$(document).ready(function() {

    $image_crop = $('#image_demo').croppie({
        enableExif: true,
        viewport: {
            width: 200,
            height: 200,
            type: 'circle'
        },
        boundary: {
            width: 300,
            height: 300
        }
    });

    $('#upload_image').on('change', function() {
        var reader = new FileReader();
        reader.onload = function(event) {
            $image_crop.croppie('bind', {
                url: event.target.result
            }).then(function() {
                console.log('jQuery bind complete');
            });
        }
        reader.readAsDataURL(this.files[0]);
        $('#image_demo').show();
        $('#buttonupload').show();
        $('#hidetitle').hide();
        $('#imgpresent').hide();
    });
   
    $('.crop_image').click(function(event) {
        $image_crop.croppie('result', {
            type: 'canvas',
            size: 'viewport',
        }).then(function(response) {
            $.ajax({
                type: "POST",
                data: {"image": response},
                url: urlUsersAccountImage,
                success: function(data) {
                    console.log('ajaxsucess');
                    $('#uploaded_image').html(data);
                    document.location.reload();               
                }

            });
        })
    });
});