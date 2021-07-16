$(document).ready(function(){

    bsCustomFileInput.init();

  function addZero(i) {
    if (i < 10) {
      i = "0" + i;
    }
    return i;
  }

  function listNotes(){
      $.post( urlAjaxNotesList , {id_user: postId }, function(resultJSON){
          $('#nbNote').html('[ <strong>'+resultJSON['notes']+'</strong> ]')
          $('#tableNotes').html(resultJSON['html']);
      })
  }
  listNotes();

  $('#confirmNoteAdd').click(function(){
          
          
      $.post( urlAjaxNoteAdd , { id_user: postId , note:$('#noteText').val()}, function(resultJSON){
          if (resultJSON['status']==true){
              listNotes();
              $('#noteText').val('')
          }
          else{

              $('#alertNote').html(resultJSON['errors']+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>×</span></button>').show()
          }
      })
  });

  function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function(e) {
              $('#imgAvatar').attr('src', e.target.result);
          }

          reader.readAsDataURL(input.files[0]);
      }
  }

  $("#avatar").change(function() {
      //readURL(this);
      readFile(this);
  });

  $(".btn-edit-user").click(function() {
      $(this).toggleClass('active')
  });


  $motifs = { 
      1 : 'Quitte les parties en cours de jeu sans motif valable.',
      2 : 'Provocations envers un ou plusieurs utilisateurs du site.', 
      3 : 'Insultes envers un ou plusieurs utilisateurs du site.',
      4 : 'Le flood (messages répétitifs) n\'est pas toléré',
      5 : 'Publicité pouvant porter atteinte au site, ou aux utilisateurs.' 
  };


  $('#banishMotif').change(function(){
      $('#textBanish').val($motifs[$('#banishMotif').val()]);
      $('#textBanish').popover('hide');	
  });

  $('#warningMotif').change(function(){
      $('#textWarning').val($motifs[$('#warningMotif').val()]);
      $('#textWarning').popover('hide');	
  });

  $('#banishSubmit').click(function(){
      if($('#date_expire').val()){
          d = new Date()
          time = addZero(d.getHours())+':'+addZero(d.getMinutes());
          date_expire = new Date($('#date_expire').val()).toLocaleDateString();
          $('#confirmBanishDate').html(date_expire+' à '+time);
          $('#hiddenDateExpire').val($('#date_expire').val()+' '+time);
      }

      if($('#textBanish').val()){
          $('#banishComfirm').modal('show');
          $('#comfirmMotif').html($('#textBanish').val());
          $('#hiddenBanishMsg').html($('#textBanish').val());

      } else {
          $('#text').popover('enable');
          $('#text').popover('show');
      }

  });

  $('#warningSubmit').click(function(){
      if($('#textWarning').val().trim()){
          $('#warningComfirm').modal('show');
          $('#comfirmWarningMotif').html($('#textWarning').val().trim());
          $('#hiddenWarningMsg').html($('#textWarning').val().trim());

      } else {
          $('#text').popover('enable');
          $('#text').popover('show');
      }			
  });

  $('#textBanish').keyup(function(){
      $('#text').popover('hide');
      $('#text').popover('disable');	
  });

  $('#textWarning').keyup(function(){
      $('#text').popover('hide');
      $('#text').popover('disable');	
  });


  $('#tableNotes').on('click', '.deleteNote',function(){
  
      console.log($(this).data('idNote'));

      _targetId = $(this).data('idNote');

      swal({
          title: "Confirmer la suppresion ?",
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Non",
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Oui !",
          closeOnConfirm: true
      },
      function(isConfirm){
          if(isConfirm){ // Confirmation
              $.post(urlAjaxNotesDelete, {id_note: _targetId}, function(resultJSON){
                  console.log(resultJSON);
                  if(resultJSON.status == true){

                      listNotes()
                  }
              })
          }
      })

  });

  //CROP IMAGE

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