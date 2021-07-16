<?php

namespace Controller\Front;

use \Model\GameModel;
use DateTime;

class GameController extends MasterFrontController
{

    public $gameid = [
        'snake' => [1,'points'],
        'space_invaders' => [2,'points'],
        'pong' => [3,'points'],
        'tetris' => [4,'points'],
        'pacman' => [5,'points'],
        'radius_raid' => [6,'points'],
        'doodle_jump' => [7,'points'],
        'pop_lock' => [8,'points'],
        'memory_game' => [9,'points'],
        'flappy_bird' => [10,'points'],
        'something_in_space' => [11,'points'],
        'rocket' => [12,'points'],
        'air_balloon' => [13,'points'],
        'stick_hero' => [14,'points'],
        'tilt' => [15,'points'],
        'memory_game' => [16,'time']
    ];

	const PATH_VIEWS = 'front/game';

	public function __construct()
	{
		parent::__construct();

        $this->gameDb = new GameModel();
	}

    /**
     * vue d'un topic
     * @param STRING $game_name nom du jeu
     */
    public function view($game_name)
    {
        $this->redirectNotConnect();

        $this->render(self::PATH_VIEWS . '/layout_game', [ 'game_name' => $game_name]);
    }

    /**
     * Enregistrement du score
     * @param STRING $game_name nom du jeu
     * @param INT $score score de la partie
     */
    public function score()
    {

        if (!empty($_POST)) {
    
            $safe = array_map('trim', array_map('strip_tags', $_POST));

            $user = $this->getUser();

            if ($this->gameid[$safe['game_name']]){

                $id_game = $this->gameid[$safe['game_name']][0];

                // Si il existe déjà une ligne joueur/jeu
                if($this->gameDb->findGame($id_game, $user['id'])){
    
                    $gameScore = $this->gameDb->findGame($id_game, $user['id']);
    
                    if($this->gameid[$safe['game_name']][1] == 'points' ){
                        if($safe['score'] > $gameScore['bestresult'] ){
                            $gameScore['bestresult'] = $safe['score'];
                        };

                        
                    }else{
                        if($safe['score'] < $gameScore['bestresult'] ){
                            $gameScore['bestresult'] = $safe['score'];
                        };
                    }
    
                    $data = [
                        "result1" => $safe['score'],
                        "date_result1" => date('Y-m-d H:i:s'),
                        "result2" => $gameScore['result1'] ?? null,
                        "date_result2" => $gameScore['date_result1'] ?? null,
                        "result3" => $gameScore['result2'] ?? null,
                        "date_result3" => $gameScore['date_result2'] ?? null,
                        "result4" => $gameScore['result3'] ?? null,
                        "date_result4" => $gameScore['date_result3'] ?? null,
                        "result5" => $gameScore['result4'] ?? null,
                        "date_result5" => $gameScore['date_result4'] ?? null,
                        "bestresult" => $gameScore['bestresult'],
                        "date_bestresult" => date('Y-m-d H:i:s')
                    ];
    
                    $this->gameDb->update($data, $gameScore['id']);
    
                }else{
    
                    $data = [                    
                        "id_game" => $id_game,
                        "id_user" => $user['id'],
                        "result1" => $safe['score'],
                        "date_result1" => date('Y-m-d H:i:s'),
                        "bestresult" => $safe['score'],
                        "date_bestresult" => date('Y-m-d H:i:s')
                    ];
                    $this->gameDb->insert($data);
                }
            };
        }
    }

    /**
     * Rechargement des scores 
     * @param STRING $game_name nom du jeu
     */
    public function loadScore()
    {

        if (!empty($_POST)) {
    
            $safe = array_map('trim', array_map('strip_tags', $_POST));

            if ($this->gameid[$safe['game_name']]){

                $id_game = $this->gameid[$safe['game_name']][0];

                $user = $this->getUser();

                if($this->gameDb->findGame($id_game, $user['id'])){

                    $gameScore = $this->gameDb->findGame($id_game, $user['id']);

                    if($this->gameid[$safe['game_name']][1] == 'points' ){

                        $gameResult = [
                            'result1' => '<b>'.$gameScore['result1'].' points </b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result1'], null).'</small>',
                            'result2' => $gameScore['date_result2'] ? '<b>'.$gameScore['result2'].' points </b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result2'], null).'</small>' : null,
                            'result3' => $gameScore['date_result3'] ? '<b>'.$gameScore['result3'].' points </b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result3'], null).'</small>' : null,
                            'result4' => $gameScore['date_result4'] ? '<b>'.$gameScore['result4'].' points </b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result4'], null).'</small>' : null,
                            'result5' => $gameScore['date_result5'] ? '<b>'.$gameScore['result5'].' points </b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result5'], null).'</small>' : null,
                            'bestresult' => '<b>'.$gameScore['bestresult'].' points </b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_bestresult'], null).'</small>'
                        ];
                        
                    }else{

                        $gameResult = [
                            'result1' => '<b>'.\Tools\Utils::countdownFormat($gameScore['result1']).'</b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result1'], null).'</small>',
                            'result2' => $gameScore['date_result2'] ? '<b>'.\Tools\Utils::countdownFormat($gameScore['result2']).'</b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result2'], null).'</small>' : null,
                            'result3' => $gameScore['date_result3'] ? '<b>'.\Tools\Utils::countdownFormat($gameScore['result3']).'</b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result3'], null).'</small>' : null,
                            'result4' => $gameScore['date_result4'] ? '<b>'.\Tools\Utils::countdownFormat($gameScore['result4']).'</b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result4'], null).'</small>' : null,
                            'result5' => $gameScore['date_result5'] ? '<b>'.\Tools\Utils::countdownFormat($gameScore['result5']).'</b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_result5'], null).'</small>' : null,
                            'bestresult' => '<b>'.\Tools\Utils::countdownFormat($gameScore['bestresult']).'</b><br><small>'.\Tools\Utils::timeAgo($gameScore['date_bestresult'], null).'</small>'
                        ];


                    }

                }
            
                $bestScoreRankWithUsername = $this->gameDb->bestScoreRankWithUsername($id_game, 10);

                $bestPlayers = [];

                foreach($bestScoreRankWithUsername as $key => $player){
                    $medal = '&#127941';

                    if($key == 0){
                        $medal = '&#129351';
                    };
                    if($key == 1){
                        $medal = '&#129352';
                    };
                    if($key == 2){
                        $medal = '&#129353';
                    };

                    if($this->gameid[$safe['game_name']][1] == 'points' ){

                        $bestPlayers[] = ucfirst($player['username']).$medal.' <br><b>'.$player['bestresult'].' points </b><br><small>'.\Tools\Utils::timeAgo($player['date_bestresult'], null).'</small>';
                   
                    }else{
                        $bestPlayers[] = ucfirst($player['username']).$medal.' <br><b>'.\Tools\Utils::countdownFormat($player['bestresult']).'</b><br><small>'.\Tools\Utils::timeAgo($player['date_bestresult'], null).'</small>';
                    }
                
                }
            }

            $json = [ 'gameResult' => $gameResult, 'bestPlayers' => $bestPlayers];

        }

        $this->showJson($json);
    }
}