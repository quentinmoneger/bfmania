/* version axx */
$('.upsideDown').click(function() {

    var target = $(this).find('.upDown');

    $('.upDown').not(target).removeClass('fa-sort-up').addClass('fa-sort-down');

    if (target.hasClass('fa-sort-up')) {
        target.removeClass('fa-sort-up').addClass('fa-sort-down');
    } else if (target.hasClass('fa-sort-down')) {
        target.removeClass('fa-sort-down').addClass('fa-sort-up');
    }
});