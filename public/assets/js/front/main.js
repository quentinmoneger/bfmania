$(function() {

  
  if( $('#alertUnsubscribe').val() == 'valid'){
    swal("Vous avez été désinscrit de la newsletters", "", "success")
  };

  if( $('#alertUnsubscribe').val() == 'error'){
    swal("Une erreur c'est produite dans la désinscription", "", "error")
  };

  // smooth scroll
  $(".smooth-scroll").click(function(e) {
    e.preventDefault();
    var anchor = $(this.hash);
    $("html, body").animate(
      {
        scrollTop: anchor.offset().top
      },
      1250
    );
  });

  // link-anchor-offset
  var path = window.location.hash;
  headerHeight = $(".navbar").height() + 5;
  if(path != ""){
    $('html,body').animate({
      scrollTop:   $(path).offset().top - headerHeight
    }, 500);
  }

  $("[data-scroll]").click(function(e) {
    e.preventDefault();
    var anchor = $(this).data("scroll");
    $("html, body").animate(
      {
        scrollTop: $(anchor).offset().top - 150
      },
      1250
    );
  });

  // tooltip
  $('[data-toggle="tooltip"]').tooltip();

  // ligthbox
  $(document).on("click", '[data-toggle="lightbox"]', function(e) {
    e.preventDefault();
    $(this).ekkoLightbox();
  });
  // Autoselect
  $(".autoselect").each(function() {
    $(this)
      .find("option:selected")
      .removeAttr("selected");
    select = $(this).find('option[value="' + $(this).data("select") + '"]');
    select.attr("selected", "selected");
  });

});
