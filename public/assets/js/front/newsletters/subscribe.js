$('#subscribe-button').click(function() {
    $.post($('#form-newsletter-subscribe').attr('action') , {
      email: $('#form-newsletter-subscribe').find('input[name=email]').val()
    },
    function(resultJson) {
      if (resultJson['success'] == true) {
        swal(resultJson['alert'], "", "success"),
        $('#form-newsletter-subscribe').find('input[name=email]').val('')
       }
       if (resultJson['success'] == false) {
        swal(resultJson['alert'], "", "error");
       }
   });
});