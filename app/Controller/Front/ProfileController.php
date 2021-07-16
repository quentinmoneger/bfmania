<?php

namespace Controller\Front;

use Model\UsersModel;
use Model\MessagesModel;
use Model\ForumPostsModel;
use \Model\ScoresModel;
use \Respect\Validation\Validator as v;

class ProfileController extends MasterFrontController
{

	const PATH_VIEWS = 'front/profile';
	
	protected $scoresDb;

	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
		$this->scoresDb = new ScoresModel();
		$this->pointsWon = 10;
		$this->pointsLost = 5;
		$this->usersDb = new UsersModel();
		$this->messagesDb = new MessagesModel();
	}

	/**
	 * Affiche le profil public d'un utilisateur 
	 * @param string $username On cherche un utilisateur par son utilisateur
	 */
	public function showProfile($username)
	{	
		// Je note le mois qu'il est pour afficher les scores correspondants
		$currentMonth  = mktime(0, 0, 0, date("m"), 1, date("Y"));
		
		if(!empty($_GET)){
		
			foreach ($_GET as $key => $value) {
				$get[$key] = trim(strip_tags($value));
			}
			
			if(isset($get['currentMonth'])){
				
				if (!v::notEmpty()->date()->validate($get['currentMonth'])){
					$currentMonth = $get['currentMonth'];
				}
				else {
					if(!empty($get['currentMonth'])){
						die('Error: invalid currentMonth param');
					}
				}
			}
		}

		// On récupère les infos de l'utilisateur
		$user = (new \Model\UsersModel)->findBy('username', $username);
		if(empty($user)){
			$this->showNotFound();
		}
		
		// On récupère les posts de l'utilisateur via son id
		$userPosts = (new ForumPostsModel)->findAllBy('id_author', $user['id']);
		
		// On compte le nombre d'élément dans notre tableau
		$postNbr = count($userPosts);
		
		// On récupère les scores de l'utilisateur grâce à son id et a la date (mois)
		$scoresUser = $this->scoresDb->listScoresByUser($user['id'], $currentMonth);


		// Je calcule les scores tarots/belotes
		$belotePlayed = 0;
		$tarotPlayed = 0;
		$tarotWon = 0;
		$beloteWon = 0;
		$belotePoint= 0;
		$tarotPoint= 0;
		$won = [];

		$games =[ 'CO'=>0, 'COA'=>0, 'C'=>0, 'CA'=> 0, 'M'=>0, 'MA'=>0, 'T3'=>0, 'T4'=>0, 'T5'=>0];
		foreach ($scoresUser as  $score) {	
			$games[$score['type']] += 1;
			if (in_array($score['type'], ['T3', 'T4', 'T5'])) {
				$tarotPlayed += 1 ;
				$scoreUser = 0;
				$count = 0;
				for ($i=1; $i < 6; $i++) { 
					if ($user['id'] ==  $score['player_'.$i]) {
						$scoreUser = $score['score_'.$i];
					} 		
					if($scoreUser > $score['score_'.$i]) {
						$count += 1;
					}
				}

				if($count == 4){
					$tarotWon += 1;
					$tarotPoint += $this->pointsWon;
					$won[]= $score['id'];
				} else {
					$tarotPoint -= $this->pointsLost;
				}	
				
			} else {
				$belotePlayed += 1 ;
				$count = 0;
				$scoreUser = 0;
				for ($i=1; $i < 5; $i++) { 
					if ($user['id'] ==  $score['player_'.$i]) {
						$scoreUser = $score['score_'.$i];
					} 		
					if($scoreUser > $score['score_'.$i]) {
						$count += 1;
					}	
				}
				if($count == 2){
					$beloteWon += 1;
					$belotePoint += $this->pointsWon;
					$won[]= $score['id'];
				}else {
					$belotePoint -= $this->pointsLost;
				}
			}
		}


		$this->render(self::PATH_VIEWS . '/show', [
			'user' 			=>	$user,
			'postNbr'		=>	$postNbr ?? '',
			'scoresUser' 	=>	$scoresUser ?? '',
			'currentMonth' 	=>	$currentMonth,
			'belotePlayed' 	=>	$belotePlayed ?? '',
			'tarotPlayed' 	=>	$tarotPlayed ?? '',
			'tarotWon' 		=>	$tarotWon ?? '',
			'beloteWon' 	=>	$beloteWon ?? '',
			'belotePoint' 	=>	$belotePoint ?? '',
			'tarotPoint' 	=>	$tarotPoint ?? '',
			'games'			=>	$games ?? '',
			'won' 			=>	$won ?? '',
		]);
	}

	/**
	 * Affichage page recherche des membres
	 */
	public function searchViewProfile()
	{
		$user = $this->getUser();
		if(empty($user)){
			$this->showForbidden();
		}
		$this->render(self::PATH_VIEWS . '/list');

	}

	
		/**
	 * Liste des membres public
	 */
	public function searchProfile()
	{	
		$html= '';
		$usersDb = new UsersModel;
		$listUsers = $usersDb->findAllBy('role','0');
		$user = $this->getUser();
		if(!empty($_GET)){
			foreach ($_GET as $key => $value) {
				$get[$key] = trim(strip_tags($value));
			}

			if(isset($get['search']) && !empty($get['search'])){
				$listUsers = $usersDb->search(['username' => $get['search']]);
			}
		}

		foreach ($listUsers as $listUser) {
			$link = $this->generateUrl('profile_show',['username' => $listUser['username']]);
			$date_registered= \Tools\Utils::dateFr($listUser['date_registered'], 'd/m/y à H:i');
			$date_connect_prev= \Tools\Utils::timeAgo($listUser['date_connect_now']);
			$data_content ='<div><small>Inscription:</small> '.$date_registered.'</div><div><small>Dernière connexion:</small> '.$date_connect_prev.'</div><div><small>Forum:</small> Todo Msgs</div>';
			if(in_array($user['role'], [50, 70, 90]) && $user['role'] > $listUser['role']) {
				$data_content.= '<div><small>Email:</small> '.$listUser['email'].'<div>';
			}
			$avatar = (!empty($listUser['avatar'])) ? $listUser['avatar'] : $this->assetUrl('/img/nophoto.jpg');
			$html.= '<a href="'.$link.'"><div class="cardUser rounded border border-secondary py-1 m-1 d-inline-block align-middle " style="width: 120px; height: 160px;" data-toggle="popover" data-html="true" data-placement="bottom" data-content="'.$data_content.'"
					<figure class="avatar">
						<img id="imgAvatar" src="'.$avatar.'" class="mt-3 rounded-circle border border-secondary" alt="Votre avatar" style="width: 90px; height: 90px;"><br><strong class="font18">'.$listUser['username'].'</strong>
					</figure>
					
			</div></a>';
		}
		$this->showJson($html);
	}

}
