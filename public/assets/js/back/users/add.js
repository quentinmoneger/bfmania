$(document).ready(function() {

    bsCustomFileInput.init();

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#imgAvatar').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    var uploadCrop = $('#upload-img').croppie({
        viewport: { width: 200, height: 200, type: 'circle'},
        boundary: { width: 250, height: 250 },            
    });

   function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                //$('#upload-img').addClass('ready');
                uploadCrop.croppie('bind', {
                    url: e.target.result,
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
        else {
            swal("Sorry - you're browser doesn't support the FileReader API");
        }
    }



    $('#avatar').on('change', function () { 
        $('#upload-img').show().fadeIn();
        readFile(this);

        $('#upload-img').find('.cr-slider').change(function (){
            uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport',
                format: 'png',
                //circle: false,
            })
            .then(function(respImage) {
                $('#resultCrop').val(respImage);
                $('#imgAvatar').attr('src', respImage);
            });   
        });
           $('#upload-img').find('.cr-boundary').mouseout(function (){
            uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport',
                format: 'png',
                //circle: false,
            })
            .then(function(respImage) {
                $('#resultCrop').val(respImage);
                $('#imgAvatar').attr('src', respImage);
            });   
        });
    });

});