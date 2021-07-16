

var list_id_to = {};

window.onload = () => {

// Collapse pour la zone de messagerie
function emptyBox() {
    if (!$('.send-box').html()) {
        $('#get-box').append('<div class="text-center send-box collapse empty"><i class="fad fa-mail-bulk fa-6x"></i><br><span class="font20 mx-3">Vous n\'avez aucun message envoyés</span></div>')
    }
    if (!$('.get-box').html()) {
        $('#get-box').append('<div class="text-center get-box collapse empty show"><i class="fad fa-mail-bulk fa-6x"></i><br><span class="font20 mx-3">Vous n\'avez aucun message reçus</span></div>')
    }
}

emptyBox();
//----------------


function markread($id_post, $readUnread) {
    $.get( urlReadUnread + '?mark=' + $readUnread + '&id_post=' + $id_post, function(resultJSON) {
        var newMsg = $('.newMessage').html();
        if (resultJSON >= 1 && newMsg) {
            $('.newMessage').html(resultJSON);
            $('.notifMessage').html(resultJSON);
            $('.toggleMessage').html(resultJSON);
        } else if (resultJSON >= 1) {
            $('#navMsg').append('<span class="newMessage rounded-circle">' + resultJSON + '</span>');
            $('#navbarDropdownMenuLinkUser').append('<span class="notifMessage rounded-circle">' + resultJSON + '</span>');
            $('#toggleMsg').append('<span class="toggleMessage rounded-circle">' + resultJSON + '</span>');
        } else if (newMsg) {
            $('.newMessage').remove();
            $('.notifMessage').remove();
            $('.toggleMessage').remove();
        }
    });
}

// Lu / non-lu
$('.envelope').click(
    function(e) {
        e.preventDefault();
        var id_post = $(this).next().val(),
            readUnread = 0;

        if($(this).data("open") == 1 ){
            $(this).data("open", 0)
            readUnread = 0;
        }else{
            $(this).data("open", 1)
            readUnread = 1;
        }

        markread(id_post, readUnread);

        if ($(this).data("open") == 1) {
            $(this).removeClass('fa-envelope text-dark').addClass('fa-envelope-open');
            $(this).parents('.mpline').removeClass('bg-white');          
        } else {
            $(this).removeClass('fa-envelope-open').addClass('fa-envelope text-dark');
            $(this).parents('.mpline').addClass('bg-white');
        }
    }
);


$('.read-mail ').click(
    function(e) {
        e.preventDefault();
        var id_post = $(this).parents('.mpline').find('.message-id').val();

        if ($(this).parents('.mpline').find('.envelope').hasClass('fa-envelope')) {
            $(this).parents('.mpline').find('.envelope').removeClass('fa-envelope').addClass('fa-envelope-open');
            $(this).parents('.mpline').removeClass('bg-white');
            markread(id_post, 1);
        }

    }
);

$('.fa-chevron-right').click(
    function(e) {
        e.preventDefault();
        var id_post = $(this).parents('.mpline').find('.message-id').val();

        if ($(this).parents('.mpline').find('.envelope').hasClass('fa-envelope')) {
            $(this).parents('.mpline').find('.envelope').removeClass('fa-envelope').addClass('fa-envelope-open');
            $(this).parents('.mpline').removeClass('bg-white');
            markread(id_post, 1);
        }

    }
);

//---------------

//Suppression Message
$('.button-delete').click(
    function(e) {
        $(this).parents('.modal').modal('hide');
        deletePost($(this).val());
    }
);

function deletePost($id_message) {
    $.get( urlDeleteMessage + '?id_message=' + $id_message, function(resultJSON) {
        if (resultJSON) {
            if ($('.get-box').hasClass('show')) {
                $('#btnReceive').click();
            } else {
                $('#btnSend').click();
            }
            $('#list-' + $id_message).remove();
            emptyBox();
        }
    });
}
//----------------

// Envoi du message
$('#write-send, #write-send-mobil').click(
    function(e) {
        e.preventDefault();
        var id_to = $('#to').val(),
            title = $('.write-message').find('input[name=title]').val(),
            message = $('.write-message').find('[name=message').val();
        writePost(id_to, title, message);
    }
);

function writePost($id_to, $title, $message, $id_parent = '') {

    $.post( urlWriteMessage, {
        to: $id_to,
        title: $title,
        message: $message,
        id_parent: $id_parent
    }, function(resultJSON) {
        if (resultJSON) {
            if (resultJSON['success'] == true) {
                // Vide le champ des querySelectors
                $('.write-message').find('textarea').val('');
                $('.write-message').find('input[name=to]').val('');
                $('.write-message').find('input[name=title]').val('');
                $('#get-box').prepend(resultJSON['mpline']);
                $('#btnReceive').click();
                $('.write-post-line').hover(
                    function() {
                        $(this).find('.mail-date').hide();
                        $(this).find('.action-date').fadeIn();
                    },

                    function() {
                        $(this).find('.action-date').hide();
                        $(this).find('.mail-date').fadeIn();
                    }
                );
                if(window.matchMedia("(max-width: 1025px)").matches ){
                    $('.mpline-show').addClass('show');
                }
            }

            if (resultJSON['success'] == false && resultJSON['reply'] == false) {
                $('.write-message').prepend(resultJSON['alert']);
            } else {
                $('#get-box').prepend(resultJSON['alert']);
                emptyBox();
            }
        }
    });
}
//----------------

// Réponse a tous
$('.reply-send-all').click(
    function(e) {
        e.preventDefault();
        var id_to = $(this).parents('.mail').find('input[name=id_from]').val(),
            id_from = $(this).parents('.mail').find('input[name=id_to]').val(),
            title = $(this).parents('.mail').find('input[name=title-all]').val(),
            message = $(this).parents('.mail').find('textarea[name=message-reply-all]').val(),
            id_parent = $(this).parents('.mail').find('input[name=id_parent]').val();
        replyPostAll(id_from, id_to, title, message, id_parent);
    }
);

function replyPostAll($id_from, $id_to, $title, $message, $id_parent = '') {

    $.post( urlReplyAllMessage, {
        from: $id_from,
        to: $id_to,
        title: $title,
        message: $message,
        id_parent: $id_parent
    }, function(resultJSON) {
        if (resultJSON) {
            if (resultJSON['success'] == true) {
                $('.write-message').find('textarea').val('');
                $('.write-message').find('input[name=to]').val('');
                $('.write-message').find('input[name=title]').val('');
                $('#usernames-to').html('');
                $('.mail').find('textarea').val('');
                $('.mail').find('input[name=to]').val('');
                $('#get-box').prepend(resultJSON['mpline']);
                $('#btnReceive').click();
                $('.write-post-line').hover(
                    function() {
                        $(this).find('.mail-date').hide();
                        $(this).find('.action-date').fadeIn();
                    },

                    function() {
                        $(this).find('.action-date').hide();
                        $(this).find('.mail-date').fadeIn();
                    }
                );
                if(window.matchMedia("(max-width: 1025px)").matches ){
                    $('.mpline-show').addClass('show');
                }
            }
            if (resultJSON['success'] == false && resultJSON['reply'] == false) {
                $('.write-message').prepend(resultJSON['alert']);
            } else {
                $('#get-box').prepend(resultJSON['alert']);
                emptyBox();
            }
        }
    });
}
//----------------

// Repondre
$('.reply-send').click(
    function(e) {
        e.preventDefault();
        var id_to = $(this).parents('.mail').find('input[name=id_from]').val(),
            title = $(this).parents('.mail').find('input[name=title]').val(),
            message = $(this).parents('.mail').find('textarea[name=message-reply]').val(),
            id_parent = $(this).parents('.mail').find('input[name=id_parent]').val();
        replyPost(id_to, title, message, id_parent);
    }
);

function replyPost($id_to, $title, $message, $id_parent = '') {
    $.post( urlReplyMessage, {
        to: $id_to,
        title: $title,
        message: $message,
        id_parent: $id_parent
    }, function(resultJSON) {
        if (resultJSON) {
            if (resultJSON['success'] == true) {
                $('.write-message').find('textarea').val('');
                $('.write-message').find('input[name=to]').val('');
                $('.write-message').find('input[name=title]').val('');
                $('#usernames-to').html('');
                $('.mail').find('textarea').val('');
                $('.mail').find('input[name=to]').val('');
                $('#get-box').prepend(resultJSON['mpline']);
                $('#btnReceive').click();
                $('.write-post-line').hover(
                    function() {
                        $(this).find('.mail-date').hide();
                        $(this).find('.action-date').fadeIn();
                    },

                    function() {
                        $(this).find('.action-date').hide();
                        $(this).find('.mail-date').fadeIn();
                    }
                );
                if(window.matchMedia("(max-width: 1025px)").matches ){
                    $('.mpline-show').addClass('show');
                }
            }
            if (resultJSON['success'] == false && resultJSON['reply'] == false) {
                $('.write-message').prepend(resultJSON['alert']);
            } else {
                $('#get-box').prepend(resultJSON['alert']);
                emptyBox();
            }
        }
    });
}
//------

// Nouvelle fonction recherche destinataires
$('.autocomplete').select2({
    
    dropdownParent: $('.write-message'),

    placeholder: " Ajout du destinataire",
    language: {
        errorLoading: function() {
            return "Aucun Resultat"
        },
        searching: function() {
            return "Recherche..."
        }
    },
    ajax: {
        url: urlAutoCompleteMessage ,
        delay: 250,
        dataType: 'json',
        minimumInputLenght: 2,
        data: function(data) {
            return {
                search: data.term
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
        cache: true,
    },
});

//------ CSS-liste-message ----

$('.mp-date .fa-trash').hover(
    function() {
        $(this).tooltip({
            container: '.listMessages',
            title: 'Supprimer'
        });
        $(this).tooltip('show');
    }
);

$('.mp-date .fa-reply-all').hover(
    function() {
        $(this).tooltip({
            container: '.listMessages',
            title: 'Répondre a tous'
        });
        $(this).tooltip('show');
    }
);

$('.mp-date .fa-arrow-alt-right').hover(
    function() {
        $(this).tooltip({
            container: '.listMessages',
            title: 'Transférer'
        });
        $(this).tooltip('show');
    }
);

$('.mp-date .fa-reply').hover(
    function() {
        $(this).tooltip({
            container: '.listMessages',
            title: 'Répondre'
        });
        $(this).tooltip('show');
    }
);

$('.mp-date .fa-chevron-right').hover(
    function() {
        $(this).tooltip({
            container: '.listMessages',
            title: 'Lire'
        });
        $(this).tooltip('show');
    }
);

$('.refresh').hover(
    function() {
        $(this).tooltip({
            title: 'Rafraîchir les messages'
        });
        $(this).tooltip('show');
    }
);
//--------------------



// Navigation Messagerie
$('#btnReceive, #btnReceiveMobile ').click(
    function() {
        $('.write-message').hide();
        $('.send-box, .mail').collapse('hide');
        $('.get-box').collapse('show');
        $('#get-box').hide().fadeIn();

    }
);

$('#btnSend, #btnSendMobile').click(
    function() {
        $('.write-message').hide();
        $('.get-box, .mail').collapse('hide');
        $('.send-box').collapse('show');
        $('#get-box').hide().fadeIn();
    }
);

$('#btnNew, #btnNewMobile').click(
    function() {
        $('#get-box').hide();
        $('.write-message').hide().fadeIn();
        $('.get-box, .send-box').collapse('hide');
        $('.mail').collapse('show');
        $('.autocomplete').empty();
        $('#title').val('');
        $('#message').val('');
    }
);

$('.fa-arrow-alt-right').click(
    function() {
        $('#get-box').hide();
        $('.write-message').hide().fadeIn();
        $('.get-box, .send-box').collapse('hide');
        $('.mail').collapse('show');
        $('#title').val(($(this).attr("data-title")) ?? '' );
        $('#message').val(($(this).attr("data-message")) ?? '' );
        $('#to').html(null);       
    }
);
//------------------

// Retour Json
$('.mpline').hover(
    function() {
        $(this).find('.mail-date').hide();
        $(this).find('.action-date').fadeIn();
    },

    function() {
        $(this).find('.action-date').hide();
        $(this).find('.mail-date').fadeIn();
    }
);
//-------------

// Show mpline 
if(window.matchMedia("(max-width: 1025px)").matches ){
    console.log('okey')
    $('.mpline-show').addClass('show');
}
}