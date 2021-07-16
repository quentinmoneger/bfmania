$(function() {
		
    var gamesCO = $('#gamesCO').val(),
        gamesCOA = $('#gamesCOA').val(),
        gamesC = $('#gamesC').val(),
        gamesCA = $('#gamesCA').val(),
        gamesM = $('#gamesM').val(),
        gamesMA = $('#gamesMA').val(),
        gamesT3 = $('#gamesT3').val(),
        gamesT4 = $('#gamesT4').val(),
        gamesT5 = $('#gamesT5').val(),
        belotePlayed = $('#belotePlayed').val(),
        beloteWon = $('#beloteWon').val(),
        tarotPlayed = $('#tarotPlayed').val(),
        tarotWon = $('#tarotWon').val(),
        scoresUser = scoreUser;

    
    
        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Games', 'Games played'],
                ['Coinche', parseInt(gamesCO)],
                ['Coinche avec annonces', parseInt(gamesCOA)],
                ['Belote', parseInt(gamesC)],
                ['Belote avec annonces', parseInt(gamesCA)],
                ['Belote Moderne', parseInt(gamesM)],
                ['Belote Moderne avec annonces', parseInt(gamesMA)],
                ['Tarot à 3', parseInt(gamesT3)],
                ['Tarot à 4', parseInt(gamesT4)],
                ['Tarot à 5', parseInt(gamesT5)]
            ]);

            var options = {
                fontName: 'Montserrat',
                slices: {
                    0:{color: '#FFB121'}, 
                    1:{color: '#FF8716'}, 
                    2:{color: '#F52C58'}, 
                    3:{color: '#DE1644'}, 
                    4:{color: '#B241C2'}, 
                    5:{color: '#8B3EC2'}, 
                    6:{color: '#1C6DC2'},  
                    7:{color: '#587ED1'},  
                    8:{color: '#0199c6'}, 
                },
                is3D: true,
                legend: {
                    position: 'labeled'
                },
                //backgroundColor: '#555',
                pieSliceText: 'none',
                chartArea: {
                    left: 5,
                    top: 5,
                    width: '90%',
                    height: '90%'
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            if(scoresUser.length != 0) {
                chart.draw(data, options);
            }
        }

        google.charts.load('current', {packages: ['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawAxisTickColors);

        function drawAxisTickColors() {
            var data = google.visualization.arrayToDataTable([
                ['Jeux', 'parties Joué', 'parties gagnées', 'parties perdus'],
                ['Belote', parseInt(belotePlayed), parseInt(beloteWon), parseInt(belotePlayed) - parseInt(beloteWon), ],
                ['Tarot', parseInt(tarotPlayed), parseInt(tarotWon), parseInt(tarotPlayed) - parseInt(tarotWon), ],
            ]);

            var options = {
                fontName: 'Montserrat',

                title: 'Parties jouées sur BFmania',
                titleTextStyle: {
                    fontSize: 15,
                    color: '#666',
                },

                chartArea: {
                    left: 60,
                    width: '50%'
                },
                //chartArea:{left:5,top:5,width:'90%',height:'90%'}
                hAxis: {
                    title: 'Nombres de parties',
                    minValue: 0,
                    textStyle: {
                        bold: true,
                        fontSize: 12,
                        color: '#666'
                    },
                    titleTextStyle: {
                        bold: true,
                        fontSize: 12,
                        color: '#666'
                    },
                },

                vAxis: {
                    title: '',
                    textStyle: {
                        fontSize: 14,
                        bold: true,
                        color: '#666'
                    },
                    titleTextStyle: {
                        fontSize: 14,
                        bold: true,
                        color: '#666'
                    }
                },

                animation: {
                    duration: 3000,
                    easing: 'out',
                    startup: true,
                },

                colors: ['#158e84', '#bfd613', '#f24404']
            };
            var chart = new google.visualization.BarChart(document.getElementById('barchart'));
            if(scoresUser.length != 0) {
                $('#detailBelote').html('<th colspan="5" class="pl-3">Détails des parties de <strong>Belote</strong></th>')
                $('#detailTarot').html('<th colspan="5" class="pl-3">Détails des parties de <strong>Tarot</strong></th>')
                chart.draw(data, options);
            }
        }

        
        $('.scoreMonth').on('click', function(e) {
            let isMonth = $(this).data('month'),
                uId = userId;

            e.preventDefault();
            $.post( urlAjaxGetScore , {'isMonth': isMonth, 'userId': uId}, function(json) {
                $('#gamesCO').val(json.games.CO);
                $('#gamesCOA').val(json.games.COA);
                $('#gamesC').val(json.games.C);
                $('#gamesCA').val(json.games.CA);
                $('#gamesM').val(json.games.M);
                $('#gamesMA').val(json.games.MA);
                $('#gamesT3').val(json.games.T3);
                $('#gamesT4').val(json.games.T4);
                $('#gamesT5').val(json.games.T5);
                $('#belotePlayed').val(json.belotePlayed);
                $('#beloteWon').val(json.beloteWon);
                $('#tarotPlayed').val(json.tarotPlayed);
                $('#tarotWon').val(json.tarotWon);
                $('.myBelotePoint').text(json.belotePoint)
                $('.myTarotPoint').text(json.tarotPoint)
                $('.Bhtml').html(json.Bhtml)
                $('.Thtml').html(json.Thtml)

                gamesT5 = json.games.T5;
                gamesT4 = json.games.T4;
                gamesT3 = json.games.T3;
                gamesCO = json.games.CO;
                gamesCOA = json.games.COA;
                gamesCA = json.games.CA;
                gamesC = json.games.C;
                gamesM = json.games.M;
                gamesMA = json.games.MA;
                belotePlayed = json.belotePlayed;
                beloteWon = json.beloteWon;
                tarotPlayed = json.tarotPlayed;
                tarotWon = json.tarotWon;
                scoresUser = json.scoresUser
                if(scoresUser.length != 0){
                    drawChart();
                    drawAxisTickColors();
                    $('#detailBelote').html('<th colspan="5" class="pl-3">Détails des parties de <strong>Belote</strong></th>');
                    $('#detailTarot').html('<th colspan="5" class="pl-3">Détails des parties de <strong>Tarot</strong></th>')
                } else {
                    $('.chartRow').html('<div class="my-3">Il n\'y a pas encore de score à cette date</div>');
                    $('#detailBelote').html('')
                    $('#detailTarot').html('')
                }
            })
        })
    
})