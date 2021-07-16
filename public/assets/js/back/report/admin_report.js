$(document).ready(function() {
    
    // Moment
    moment.locale('fr');
    $('.class-report').each(function(index, value) {
        date_report = $(this).attr("data-date");
        id = $(this).attr("data-id");
        date_report = moment(date_report).fromNow();
        date_report = `<span>Signalé ${date_report}</span>`;
        let date_report_insert_id = `#date-report-insert-${id}`;
        $(date_report_insert_id).html(date_report);

    })


	// Modal Suppression
	$('.modal-delete').click(
		function(e) {
			id = $(this).attr("data-id");
			$('.btn-delete').val(id);
		}
	);

	// Modal Message
	$('.modal-message').click(
		function(e) {
			type = $(this).attr("data-type");
			console.log(type)
			if (type == 'messagerie' ){
				$('#modal-type').html('Titre du message :');
			} else if (type == 'chat' ){
				$('#modal-type').html('Salon :');
			}
			message = $(this).attr("data-message");
			username = $(this).attr("data-username");
			title = $(this).attr("data-title");
			$('#modal-message').html(message);
			$('#modal-visual-username').html(username);
			$('#modal-title').html(title);
		}
	);

	// Modal signalement
	$('.modal-close').click(
		function(e) {
			id = $(this).attr("data-id");
			username = $(this).attr("data-username");
			$('.btn-close').val(id);
			$('#modal-close-username').html(username);
		}
	);

});