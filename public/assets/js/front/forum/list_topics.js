window.onload = () => {

$('.pin').hover(
    function() {
        $(this).addClass('sticky');
        $(this).find('i').css('cursor', 'pointer');
    },
    function() {
        $(this).removeClass('sticky');
    }
);

$('.pin').click(
    function() {
        $(this).find('form').submit();
    }
)

$('.falseLink').click(function(e){
e.preventDefault();
var target = $(this);
swal({
    title: 'Êtes-vous sûr ?',
    type: "warning",
    confirmButtonText: "Supprimer",
    cancelButtonText: "Annuler",
    showCancelButton: true,

}, function(){
    window.location.href = target.attr('href');
    
});
})

}