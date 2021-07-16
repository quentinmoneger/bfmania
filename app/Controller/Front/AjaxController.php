<?php

namespace Controller\Front;

use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;
use DateTime;
use \Intervention\Image\ImageManagerStatic as Image;
use \Model\UsersModel;
use \Model\TopicModel;
use \Model\MessagesModel;
use Model\ScoresModel;
use W\Security\AuthentificationModel;

class AjaxController extends MasterFrontController
{

	const PATH_VIEWS = 'front/ajax';

	public function __construct()
	{
		parent::__construct();
		$this->usersDb = new UsersModel();
		$this->messagesDb = new MessagesModel();
	}

	/**
	 * Récupère les scores en fonction du mois
	 */
	public function getScore()
	{
		if(!empty($_POST)){
			$post = array_map('trim', array_map('strip_tags', $_POST));
			$Bhtml = '';
			$Thtml = '';

			$errors = [
				(!v::notEmpty()->intVal()->validate($post['userId'])) ? 'L\'id utilisateur indiqué n\'est pas valide' : null,
				(!v::notEmpty()->intVal()->validate($post['isMonth'])) ? 'Le mois indiqué n\'est pas valide' : null,
			];

			$errors = array_filter($errors);

			if(count($errors) === 0){
				$scoreMonth  = mktime(0, 0, 0, $post['isMonth'], 1, date("Y"));				
				$scoresUser = (new ScoresModel)->listScoresByUser($post['userId'], $scoreMonth) ?? '';
				$pointsWon = 10;
				$pointsLost = 5;
				$belotePlayed = 0;
				$tarotPlayed = 0;
				$tarotWon = 0;
				$beloteWon = 0;
				$belotePoint= 0;
				$tarotPoint= 0;
				$won = [];				
				
				// On calcule le score en fonction du nombre de point remportés lors de la partie
				$games =[ 'CO'=>0, 'COA'=>0, 'C'=>0, 'CA'=> 0, 'M'=>0, 'MA'=>0, 'T3'=>0, 'T4'=>0, 'T5'=>0];
				foreach ($scoresUser as  $score) {	
					$games[$score['type']] += 1;
					
					// Pour le Tarot
					if (in_array($score['type'], ['T3', 'T4', 'T5'])) {
						$tarotPlayed += 1 ;
						$scoreUser = 0;
						$count = 0;
						for ($i=1; $i < 6; $i++) { 
							if ($post['userId'] ==  $score['player_'.$i]) {
								$scoreUser = $score['score_'.$i];
							} 		
							if($scoreUser > $score['score_'.$i]) {
								$count += 1;
							}
						}
		
						if($count == 4){
							$tarotWon += 1;
							$tarotPoint += $pointsWon;
							$won[]= $score['id'];
						} else {
							$tarotPoint -= $pointsLost;
						}

						$profilScore4 = ($score['score_4']) ? '<a class="text-dark" href="'.$this->generateUrl('profile_show', ['username' => $score['username_4']]).'">'.$score['username_4'].'</a><small class="badge badge-light border border-secondary text-secondary ml-2">'.$score['score_4'].' <sup>pts</sup></small>' : '';

						$profilScore5 = ($score['score_5'])? '<a class="text-dark" href="'.$this->generateUrl('profile_show', ['username' => $score['username_4']]).'">'.$score['username_5'].'</a><small class="badge badge-light border border-secondary text-secondary ml-2">'.$score['score_5'].' <sup>pts</sup></small>' : '';

						$LoW = (in_array($score['id'], $won))? 'bg-won' : 'bg-loose';
						
						$Thtml .= '<tr>
						<td class="px-3 '. $score['type'] .' d-flex justify-content-between  align-items-center">
							'. \Tools\Utils::getHumanGame($score['type']) .' <small>'. \Tools\Utils::dateFr($score['date_create'], 'd/m/Y - H:i') .'</small>
						</td>
						<td class="px-1">
							<a class="text-dark" href="'.  $this->generateUrl('profile_show', ['username' => $score['username_1']])  .'">'. $score['username_1'] .'</a><small class="badge badge-light border border-secondary text-secondary ml-2">'. $score['score_1'] .' <sup>pts</sup></small>
						</td>
						<td class="px-1">
							<a class="text-dark" href="'.  $this->generateUrl('profile_show', ['username' => $score['username_2']])  .'">'. $score['username_2'] .'</a><small class="badge badge-light border border-secondary text-secondary ml-2">'. $score['score_2'] .' <sup>pts</sup></small>
						</td>
						<td class="px-1">
							<a class="text-dark" href="'.  $this->generateUrl('profile_show', ['username' => $score['username_3']])  .'">'. $score['username_3'] .'</a><small class="badge badge-light border border-secondary text-secondary ml-2">'. $score['score_3'] .' <sup>pts</sup></small>
						</td>
						<td class="px-1">
							'. $profilScore4 .'
						</td>
						<td class="px-1">
							'. $profilScore5 .'
						</td>
						<td class="'. $LoW .'" style="width: 15px;">
							&nbsp;
						</td>
					</tr>';

					// Pour la Belote	
					} else {
						$belotePlayed += 1 ;
						$count = 0;
						$scoreUser = 0;
						for ($i=1; $i < 5; $i++) { 
							if ($post['userId'] ==  $score['player_'.$i]) {
								$scoreUser = $score['score_'.$i];
							} 		
							if($scoreUser > $score['score_'.$i]) {
								$count += 1;
							}	
						}
						if($count == 2){
							$beloteWon += 1;
							$belotePoint += $pointsWon;
							$won[]= $score['id'];
						}else {
							$belotePoint -= $pointsLost;
						}

						$WorL = (in_array($score["id"], $won))? "bg-won" : "bg-loose";

						$Bhtml .= '<tr class="">
							<td class="px-3 '. $score["type"] .' d-flex justify-content-between align-items-center">
								'. \Tools\Utils::getHumanGame($score["type"]) .'
								<small>'. \Tools\Utils::dateFr($score["date_create"], "d/m/Y - H:i") .'</small>
							</td>
							<td class="px-2">
								<a href="'. $this->generateUrl("profile_show", ["username" => $score["username_1"]]) .'" class="text-dark">'. $score["username_1"] .'</a> & 
								<a href="'. $this->generateUrl("profile_show", ["username" => $score["username_2"]]) .'" class="text-dark">'. $score["username_2"] .'</a> : 
								<small class="badge badge-light border border-secondary text-secondary ml-2">'. $score["score_1"] .' <sup>pts</sup></small> 
							</td>
							<td class="px-2">
								<a href="'. $this->generateUrl("profile_show", ["username" => $score["username_3"]]) .'" class="text-dark">'. $score["username_3"] .'</a> & <a href="'. $this->generateUrl("profile_show", ["username" => $score["username_4"]]) .'" class="text-dark">'. $score["username_4"] .'</a> : <small class="badge badge-light border border-secondary text-secondary ml-2">'. $score["score_3"] .' <sup>pts</sup></small>
							</td>
							<td class="'. $WorL .'" style="width: 15px;">
								&nbsp;
							</td>
						</tr>';
					}
				}
			} else {
				$this->flash(implode('<br>', $errors), 'danger');
			}

			return $this->json([
				'scoresUser' 	=>	$scoresUser,						
				'belotePlayed' 	=>	$belotePlayed,
				'tarotPlayed' 	=>	$tarotPlayed,
				'tarotWon' 		=>	$tarotWon,
				'beloteWon' 	=>	$beloteWon,
				'belotePoint' 	=>	$belotePoint,
				'tarotPoint' 	=>	$tarotPoint,
				'games'			=>	$games,
				'Bhtml'			=>	$Bhtml,
				'Thtml'			=>	$Thtml,
				'won' 			=>	$won,
			]);
		}
	}
}

