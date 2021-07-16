$(document).ready(function() {

    $('#report').on('show.bs.modal', function (event) { 
            var button = $(event.relatedTarget) 
            var modal = $(this)
            modal.find('.button-report').val((button.data('id')) ?? '' );
            modal.find('#modal-username-from').html((button.data("username-from")) ?? '' );;
            modal.find('#modal-report-type').val((button.data("report-type")) ?? '' );         
        }
    );


    //Signalement Message
    $('.button-report').click(
        function(e) {
            $(this).parents('.modal').modal('hide');
        var id_message = $(".button-report").val()
            type = $("#modal-report-type").val()
            report_message = $("#report-message").val();
            inform_report = $('input[name=inform_report]:checked').val();
            reportMessage(id_message, type, report_message, inform_report);
        }
    );


    function reportMessage( id_message, type, report_message, inform_report ) {
        $.post(urlReport, {
            id_message: id_message,
            type: type,
            report_message: report_message,
            inform_report:  inform_report
        },function(resultJSON) {
            if (resultJSON) {
                if (resultJSON['success'] == false ) {
                    swal(resultJSON['alert'], "", "error");
                } else {
                    swal(resultJSON['alert'], "", "success");
                    $('#report-message').val('');
                }
            }
        });
    }

    $('.fa-comment-alt-exclamation').hover(
        function() {
            $(this).tooltip({
                title: 'Signaler Message'
            });
            $(this).tooltip('show');
        }
    );
    //----------------

});