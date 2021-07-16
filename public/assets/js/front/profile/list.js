function listProfile(){
    var search_value = $('#search').val();			
    $.get( urlSearchProfile+search_value, function(resultJSON){		   	     	        	
           //console.log(resultJSON);
        $('#members').html(resultJSON).hide().fadeIn(500);

        $('#members').find('.cardUser').hover(
            function(){

                $(this).animate(
                    { deg: -10 },
                    {
                      duration: 600,
                      step: function(now) {
                          
                          $(this).css({ transform: 'rotate(' + now + 'deg)', height :'170px', width: '130px', position: 'relative', top:'-20px'});
                        $(this).find('img').css({height :'110px', width: '110px'});
                        $('.popover').css({margin: '-10px 0 0 0'})
                        $(this).popover('show');
                      }
                    }
                );
                                
            },
            function(){
                
                $(this).animate(
                    { deg: 0 },
                    {
                      duration: 300,
                      step: function(now) {

                          $(this).find('img').css({height :'90px', width: '90px'});
                        $(this).css({height :'160px', width: '120px'});
                          $(this).css({ transform: 'rotate(' + now + 'deg)' ,height :'160px', width: '120px', position: 'static'});
                          $(this).popover('hide');
                          $('.popover').css({margin: '0 0 0 0'});
                      }
                    }
                );		
            }
        )
    });
}

$('#search').keyup(function(){
    if (length >= 0) {
        listProfile();
    }
});

