var order = '',
    dir = '',
    currentPage = 1,
    previousPage,
    nextPage;
    w_user_role = $('#w_user_role').val();
    w_user_id = $('#w_user_id').val();

$(document).ready(function(){

    function getSearch(multinick){
        var search_value = $('#search').val(),
            role_value  = $('#role').val(),
            limit_value  = $('#nb_page').val(),
            //sort_value  = $('#tri').val(),
            html,
            pageHtml = '',
            length = search_value.length;

            if (multinick){
                search_value = 	multinick;
            }

            $.get(urlAjaxGetSearch+'?search='+search_value+'&role='+role_value+'&nb_users='+limit_value+'&order='+order+'&dir='+dir+'&page='+currentPage, function(resultJSON){	    	        
                if (resultJSON) {	
                    
                    currentPage = resultJSON['currentPage'];
                    var role;
                    modify = urlBackUsersEdit,
                    suprim = urlBackUsersDelete;
              
                    for (i= 0; i < resultJSON[0].length; i++) {

                        mod = modify.replace('userid', resultJSON[0][i]['id']);
                        del = suprim.replace('userid', resultJSON[0][i]['id']);
                        modifyUrl = '<a href="'+mod+'" class="grey-text text-darken-2"><i class="fas fa-sm fa-eye"></i> Voir</a>';
                        
                        if(["70", "99"].includes(w_user_role)){
                            modifyUrl+= '&nbsp&nbsp&nbsp<a href="'+del+'" class="red-text"><i class="fas fa-sm fa-times"></i> Supprimer</a>';
                        }
                        if (resultJSON[0][i]['role'] == '99' || resultJSON['userRole'] <= resultJSON[0][i]['role'] ){
                            modifyUrl = '<span class="font12">Aucune action disponible</span>'
                        }
                        if(resultJSON[0][i]['id'] == w_user_id){
                            modifyUrl = '<a href="'+mod+'" class="grey-text text-darken-2"><i class="fas fa-sm fa-eye"></i> Voir</a>';
                        }
                        
                        switch(resultJSON[0][i]['role']){
                            case '0':
                            role = '<span class="badge">Membre</span>'
                            break;
                            case '30':
                            role = '<span class="badge teal darken-2 white-text">Animateur radio</span>'
                            break;
                            case '50':
                            role = '<span class="badge indigo darken-2 white-text">Modérateur</span>'
                            break;
                            case '70':
                            role = '<span class="badge red white-text">Administrateur</span>'
                            break;
                            case '99':
                            role = '<span class="badge black white-text">Webmaster</span>'
                            break;
                        }

                        dateRegistered = new Date(resultJSON[0][i]['date_registered']).toLocaleString();
                        dateConnectPrev = new Date(resultJSON[0][i]['date_connect_prev']).toLocaleString();
                        dateRegistered = dateRegistered.replace('à','');
                        dateConnectPrev = dateConnectPrev.replace('à','');

                         html+= '<tr><td>'+resultJSON[0][i]['id']+'</td><td>'+resultJSON[0][i]['username']+'</td><td>'+resultJSON[0][i]['email']+'</td><td>'+role+'</td><td>'+dateRegistered+'</td><td>'+dateConnectPrev+'</td><td>'+resultJSON[0][i]['agent']['os']+' / '+resultJSON[0][i]['agent']['browser']+'</td><td>'+modifyUrl+'</td>'	 	
                    }
                    
                    if (resultJSON['totalPages'] > 1) {
                         previousPage = resultJSON['currentPage'] == '1' ? '' : resultJSON['currentPage'] - 1;
                         
                         if (parseInt(resultJSON['currentPage']) < parseInt(resultJSON['totalPages'])){
                             nextPage = parseInt(resultJSON['currentPage'])	 + 1;
                         }

                        for (i= 1; i < resultJSON['totalPages']+1; i++) {
                            if (i == resultJSON['currentPage']){
                                pageHtml += '<li id="currentPage" value="'+i+'" class="page-item active"><span class="page-link">'+i+'</span></li>'
                            } else {
                                pageHtml += '<li class="page-item"><span value="'+i+'" class="page-link page-get">'+i+'</span></li>'
                            }
                        }

                        html+= '</tr><tr><td colspan="7" class="bg-light"><ul class="pagination m-0 flex-wrap justify-content-center"><li id="prev" class="my-0 mx-3"><a href="#" role="button" class="btn btn-sm btn-outline-dark py-0 pr-0 pl-1"><i class="p-0 fas fa-arrow-alt-left "></i></a></li>'+pageHtml+'<li class="my-0 mx-3"><button id="next" href=""  class="btn btn-sm btn-outline-dark py-0 pl-0 pr-1"><i class="p-0 fas fa-arrow-alt-right "></i></button></li></ul></td></tr>';
                    }

                    $('#tableBody').html(html).fadeIn();

                } 
                else  {
                    $('#tableBody').html('<tr><td colspan="7" class="alert alert-danger text-center">Aucun membres ne correspond à votre recherche</td></tr>').fadeIn();
                    currentPage= 1;
                }

            });
    }
    getSearch();

    $('#search').keyup(function(){
        if (length >= 0) {
            getSearch();
        }
    });

    $(document).on('change','#nb_page',function(){          
        $('.multinickView').parent().css('background-color','');
        getSearch();
    });

    $(document).on('change','#role',function(){          
        $('.multinickView').parent().css('background-color','');
        getSearch();
    });

    $(document).on('change','#tri',function(){          
        $('.multinickView').parent().css('background-color','');
        getSearch();
    });

    $('#tableBody').on('click', '#next', function(){		
        if(nextPage){
            currentPage = nextPage;
            getSearch();
        }
        
    });

    $('#tableBody').on('click', '#prev', function(){		
        if(nextPage){
            currentPage = previousPage;
            getSearch();
        }
        
    });

    $('#tableBody').on('click', '.page-link', function(){		
        currentPage = $(this).text();
        getSearch();
        
    });
    $('multinickButton').click(function(){
        $('.multinickView').parent().css('background-color','');
        getSearch();
    });

    $('.multinickView').click(function(){
        multinick = $(this).prev().html();
        $('.multinickView').parent().css('background-color','');
        $(this).parent().css('background-color','#ddd').fadeIn();
        getSearch(multinick);	
    });


    //********** SORT TABLE *********
    $('#sortEmail').click(function(){
        order = 'email';
        if ($(this).hasClass('ASC')) {
            $(this).removeClass('ASC');
            dir = 'DESC';
        } else {
            dir = 'ASC';
            $(this).addClass('ASC');
        }
        getSearch();
    });
    $('#sortRole').click(function(){
        order = 'role';
        if ($(this).hasClass('ASC')) {
            $(this).removeClass('ASC');
            dir = 'DESC';
        } else {
            dir = 'ASC';
            $(this).addClass('ASC');
        }
        getSearch();
    });
    $('#sortUsername').click(function(){
        order = 'username';
        if ($(this).hasClass('ASC')) {
            $(this).removeClass('ASC');
            dir = 'DESC';
        } else {
            dir = 'ASC';
            $(this).addClass('ASC');
        }
        getSearch();
    });
    $('#sortDateRegistered').click(function(){
        order = 'date_registered';
        if ($(this).hasClass('ASC')) {
            $(this).removeClass('ASC');
            dir = 'DESC';
        } else {
            dir = 'ASC';
            $(this).addClass('ASC');
        }
        getSearch();
    });
    $('#sortDateLast').click(function(){
        order = 'date_connect_prev';
        if ($(this).hasClass('ASC')) {
            $(this).removeClass('ASC');
            dir = 'DESC';
        } else {
            dir = 'ASC';
            $(this).addClass('ASC');
        }
        getSearch();
    });
    $('#sortAgent').click(function(){
        order = 'agent';
        if ($(this).hasClass('ASC')) {
            $(this).removeClass('ASC');
            dir = 'DESC';
        } else {
            dir = 'ASC';
            $(this).addClass('ASC');
        }
        getSearch();
    });
});
