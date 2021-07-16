$(function(){

    $motifs = { 
        1 : 'Quitte les parties en cours de jeu sans motif valable.',
        2 : 'Provocations envers un ou plusieurs utilisateurs du site.', 
        3 : 'Insultes envers un ou plusieurs utilisateurs du site.',
        4 : 'Le flood (messages répétitifs) n\'est pas toléré',
        5 : 'Publicité pouvant porter atteinte au site, ou aux utilisateurs.' };


        $('#select').change(function(){

            $('#text').val($motifs[$('#select').val()]);

        });
    });


$('#valid').click(function(e){




});