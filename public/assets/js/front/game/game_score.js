$(document).ready(function() {
    loadScore(game_name)
});

/**
*   Fonction pour enregistrer le score en base de données
*   @param score
*       Le score réalisé sur la partie.
*/
 function gameScore(score) {
    $.post( urlScore , {
        game_name: game_name,
        score: score  
        }
    );
}

/**
*   Fonction de rechargement de score
*   @param loadscore
*       Le score réalisé sur la partie.
*/
function loadScore(game_name) {

    $.post( urlLoadScore , {
        game_name: game_name, 
        } , function(resultJSON){                   
            if (resultJSON) {
                $('#result1').html(resultJSON['gameResult']['result1']),
                $('#result2').html(resultJSON['gameResult']['result2']),
                $('#result3').html(resultJSON['gameResult']['result3']),
                $('#result4').html(resultJSON['gameResult']['result4']),
                $('#result5').html(resultJSON['gameResult']['result5']),
                $('#bestresult').html(resultJSON['gameResult']['bestresult']);
                $('#bestPlayers').html('')
                for(var i in resultJSON['bestPlayers']){
                    $('#bestPlayers').append('<tr><td class="p-1">'+resultJSON['bestPlayers'][i]+'</td></tr>')
                }
            }
        }
    );
}

