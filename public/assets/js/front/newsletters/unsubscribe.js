$('#unsubscribe-button').click(function() {
    $.post($('#form-newsletter-unsubscribe').attr('action') , {
      email: $('#form-newsletter-unsubscribe').find('input[name=email]').val()
    },
    function(resultJson) {
      if (resultJson['success'] == true) {
        swal(resultJson['alert'], "", "success"),
        $('#form-newsletter-unsubscribe').find('input[name=email]').val('')
        $('#unsubscribe').modal('toggle');
       }
       if (resultJson['success'] == false) {
        swal(resultJson['alert'], "", "error");
       }
   });
});

if($('#alertUnsubscribe').val() == 'error'){
  swal('Vous n\'êtes pas abonné a la newsletters !', "", "error")
}
if($('#alertUnsubscribe').val() == 'valid'){
swal('Vous avez bien été désabonné a la newsletters', "", "success")
}  

