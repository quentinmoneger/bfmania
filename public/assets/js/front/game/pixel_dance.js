$btn = $('.btn-wrap button'),    //button
$livescore = $('.livescore'),    //livescore
$contul = $('.control ul'),      //control ul
$bro = $('.bro > div'),          //bro
$fadet = $('.fade h2'),          //fade text
$header = $('.header'),          //header
$cont = $('.control'),           //control
$fade = $('.fade'),              //fade
$game = $('#game'),              //game
urmove = [],                     //your move
setmove = [],                    //setmove -> random 'up', 'down', 'right', 'left'
t = [[0],[0]],                   //t val -> 'hit', 'miss'
gca = [[0],[0],[0]],             //move -> id0 'good', id1 'cool', id2 'awful'
li = 0,                          //control li val
hit = 0,                         //hit val
loop = 0,                        //loop val
miss = 0,                        //miss val
temp = 0,                        //temp val
tempo = 0,                       //hit or miss
dur = 7000,                      //duration
rank = 'E',                      //rank val
space = true,                    //space key val
cont = '.control';               //control class

$(document).ready(function() {
   $btn.click(function() {
      $(this).addClass('cl');
      $(this).attr('disabled',true);

    clientId = 'faa62fb9172c1292a0a50715036b849c';
    trackId = '211054285';

    SC.initialize({
        client_id: clientId
    });
    SC.get('/tracks/'+trackId, function(track){
          audioPlayer = new Audio(track.uri + '/stream?client_id=' + clientId);
          audioPlayer.play();
    });

      setTimeout(function() {
         $game.removeClass('notready');
         $game.addClass('ready');
      }, 200);
      setTimeout(function() {
         $bro.addClass('hehe');
         $fade.fadeOut('fast');
         $livescore.fadeIn('fast');
      }, 1000);
      setTimeout(function() {
         $btn.removeClass();
         $btn.fadeOut('fast');
         $('.klist').remove();

         game();
      }, 2000);
   });
});

function over() {
   clearInterval(dance);

   setTimeout(function() {
      $bro.removeClass();
      $bro.addClass('hehe');

      $livescore.fadeOut('fast');
   }, dur+500);

   setTimeout(function() {
      $livescore.remove();

      $fade.fadeIn('fast');

      $('.finscore').fadeIn('fast');
   }, dur+750);

   setTimeout(function() {
      $('.finscore > ul > li:nth-of-type(1) span').addClass('alter');
   }, dur+1000);
   setTimeout(function() {
      $('.finscore > ul > li:nth-of-type(1) span').text(hit);
   }, dur+1225);
   setTimeout(function() {
      $('.finscore > ul > li:nth-of-type(2) span').addClass('alter');
   }, dur+2000);
   setTimeout(function() {
      $('.finscore > ul > li:nth-of-type(2) span').text(miss);
   }, dur+2225);
   setTimeout(function() {
      $('.finscore > ul > li li:nth-of-type(3) span').addClass('alter');
   }, dur+3000);
   setTimeout(function() {
      $('.finscore > ul > li li:nth-of-type(1) span').text(gca[0]);
      $('.finscore > ul > li li:nth-of-type(2) span').text(gca[1]);
      $('.finscore > ul > li li:nth-of-type(3) span').text(gca[2]);
   }, dur+3225);
   setTimeout(function() {
      if (hit >= 115) {
         rank = 'A';
      } else if (hit >= 99 && hit <= 114) {
         rank = 'B';
      } else if (hit >= 65 && hit <= 98) {
         rank = 'C';
      } else if (hit >= 33 && hit <= 64) {
         rank = 'D';
      } else if (hit <= 32) {
         rank = 'E';
      }
      $('.finscore > ul > li:nth-of-type(4) span').addClass('alter');
   }, dur+4000);
   setTimeout(function() {
      $('.finscore > ul > li:nth-of-type(4) span').text(rank);
   }, dur+4225);
}

//dance interval!
function game() {
   dance = setInterval(function() {
      if (loop < 3) {
         if (loop == 0) {
            li = 2;
         } else if (loop < 3) {
            li = 3;
         }
         dur = 7250;
      } else if (loop < 9) {
         if (loop < 5) {
            li = 4;
         } else if (loop < 7) {
            li = 5;
         } else if (loop < 9) {
            li = 6;
         }
         dur = 7500;
      } else if (loop < 15) {
         if (loop < 12) {
            li = 7;
         } else if (loop < 15) {
            li = 8;
         }
         dur = 7750;
      } else if (loop < 19) {
         li = 9;
         dur = 8000;
      } else {
         over();
      }

      urmove = [];
      move = ['up','down','right','left']; //up, down, right, left

      //set a setmove randomly
      for (i=0; i<li; i++) {
         setmove[i] = move[Math.floor(Math.random() * move.length)];
         $contul.append('<li class="' + i + ' null fl jusc alc' + '"><i class="fa fa-arrow-' + setmove[i] + '"></i></li>');
         $cont.removeClass('out');
         $cont.addClass('in on');
         space = true; //activate space key
      }

      $(cont + ' .bar').width($contul.innerWidth());
      $(cont + ' span').css('left','0%');
      $(cont + ' span').stop().animate({
         left:'+=100%'
      }, dur-5000, "linear"); //duration

      setTimeout(function() {
         if ($(cont + ' span').position().left == $('.bar div').position().left+30 && $cont.hasClass('on')) {
            for (i=0; i<li; i++) {
               if (urmove[i] == 1) {
                  $(cont + ' .' + i).removeClass('hit null');
                  $(cont + ' .' + i).addClass('ms');
               } else if (urmove[i] == 0 || $(cont + ' .' + i).hasClass('null')) {
                  $(cont + ' .' + i).removeClass('null');
                  $(cont + ' .' + i).addClass('ms');
               }
               miss++;
            }

            $game.addClass('miss');
            $fade.fadeIn('fast');
            $fadet.text("miss!");

            setTimeout(function() {
               $('.livescore li:last-child > span').addClass('alter');
            }, 100);

            setTimeout(function() {
               $('.livescore li:last-child > span').text(miss);
            }, 325);

            setTimeout(function() {
               $fade.fadeOut('fast');
            }, 750);

            setTimeout(function() {
               $bro.removeClass();
               $bro.addClass('doh');
            }, 751);
         }

         setTimeout(function() {
            $cont.removeClass('in on');
            $cont.addClass('out');

            $game.addClass('scream');

            $(cont + ' li').remove();
            $('.livescore li > span').removeClass('alter');
         }, 750);

         if ($game.is('.aah,.err,.hmm,.miss')) {
            if ($game.hasClass('err')) {
               setTimeout(function() {
                  $bro.removeClass();
                  $bro.addClass('doh');
               }, 3000);
            }
            setTimeout(function() {
               if ($game.hasClass('err')) {
                  $bro.removeClass();
                  $bro.addClass('hehe');
               } else if ($game.is('.hmm,.miss')) {
                  $bro.removeClass();
                  $bro.addClass('hehe');
               }
               $game.addClass('noscream');
            }, 4595);
            setTimeout(function() {
               $game.removeClass();
               $fadet.text('');
            }, 4800);
         }
      }, dur-4950);

      tempo = 0;
      temp = 0;
      loop++;
   }, dur);

   //k
   $(document).keydown(function(e) {
      if (e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 39 || e.keyCode == 37 || e.keyCode == 32) {
         if (e.keyCode == 38) { //up
            key = 'up';
         }
         if (e.keyCode == 40) { //down
            key = 'down';
         }
         if (e.keyCode == 39) { //right
            key = 'right';
         }
         if (e.keyCode == 37) { //left
            key = 'left';
         }
         if (e.keyCode == 32) { //space
            if (space == true) {
               space = false;
               key = ' ';
               if (tempo <= setmove.length && $cont.hasClass('on') && $contul.has('li').length && $(cont + ' span').position().left != $('.bar div').position().left+30) {
                  $fade.fadeIn('fast');
                  if ($(cont + ' span').position().left >= $('.bar div').position().left-10 && $(cont + ' span').position().left <= $('.bar div').position().left+20) {
                     for (i=0; i<li; i++) {
                        if (urmove[i] == 1 && $(cont + ' .' + i).hasClass('ht')) {
                           temp++;
                           hit++;
                        } else if (urmove[i] == 0 || $(cont + ' .' + i).hasClass('null')) {
                           miss++;
                        }
                     }
                     arr = [["super"],["not so"]];
                     if (urmove.indexOf('1') >= 0) {
                        if (temp == li) {
                           hd = arr[0];
                           tl = '!';
                           $game.addClass('aah');
                           //super good, super cool, not so awful
                        } else if (temp < (li/2)) {
                           hd = arr[1];
                           tl = '';
                           $game.addClass('err');
                           //not so good, not so cool, super awful
                        } else {
                           hd = '';
                           tl = '!';
                           $game.addClass('hmm');
                           //good, cool, awful
                        }
                        if ($(cont + ' span').position().left >= $('.bar div').position().left-10 && $(cont + ' span').position().left < $('.bar div').position().left) {
                              $game.addClass('good');
                              $fadet.text(hd + " good" + tl);
                              gca[0]++;
                        } else if ($(cont + ' span').position().left >= $('.bar div').position().left && $(cont + ' span').position().left < $('.bar div').position().left+10) {
                              $game.addClass('cool');
                              $fadet.text(hd + " cool" + tl);
                              gca[1]++;
                        } else if ($(cont + ' span').position().left >= $('.bar div').position().left+10 && $(cont + ' span').position().left <= $('.bar div').position().left+20) {
                              if (hd == arr[0]) {
                                 hd = arr[1];
                              } else if (hd == arr[1]) {
                                 hd = arr[0];
                              }
                              $game.addClass('awful');
                              $fadet.text(hd + " awful" + tl);
                              gca[2]++;
                        }
                     } else {
                        $game.addClass('miss');
                        $fadet.text("miss!");
                     }
                  } else {
                     for (i=0; i<li; i++) {
                        miss++;
                     }
                     $game.addClass('miss');
                     $fadet.text("miss!");
                  }

                  $(cont + ' span').stop();

                  setTimeout(function() {
                     if (t[0] < hit) {
                        $('.livescore li:first-child > span').addClass('alter');
                        t[0] = hit;
                     }
                     if (t[1] < miss) {
                        $('.livescore li:last-child > span').addClass('alter');
                        t[1] = miss;
                     }
                  }, 100);

                  setTimeout(function() {
                     $('.livescore li:first-child > span').text(hit); //print hit value(s)
                     $('.livescore li:last-child > span').text(miss); //print miss value(s)
                  }, 325);

                  setTimeout(function() {
                     $fade.fadeOut('fast');

                     $cont.removeClass('in on');
                     $cont.addClass('out');

                     $('.livescore li > span').removeClass('alter');
                  }, 750);

                  setTimeout(function() {
                     if ($game.is('.good,.cool,.awful')) {
                        $bro.removeClass();
                        $bro.addClass('dance');
                     } else {
                        $bro.removeClass();
                        $bro.addClass('doh');
                     }
                  }, 751);
               }
            }
         }

         if (tempo < setmove.length && $cont.hasClass('on') && space == true) {
            urmove[tempo] = key;
            if (urmove[tempo] == ' ') {} else {
               if (urmove[tempo] == setmove[tempo]) {
                  urmove[tempo] = '1'; //hit
                  $(cont + ' .' + tempo).removeClass('null');
                  $(cont + ' .' + tempo).addClass('ht');
               } else {
                  urmove[tempo] = '0'; //miss
                  $(cont + ' .' + tempo).removeClass('null');
                  $(cont + ' .' + tempo).addClass('ms');
               }
               tempo++;
            }
         }
      }
   });
};