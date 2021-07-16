<?php

namespace Controller\Front;

use DateInterval;
use DateTime;
use \Model\Model;
use \Model\UsersModel;
use \Model\TokensModel;
use \Model\BanishModel;
use \W\Security\AuthentificationModel;
use \Respect\Validation\Validator as v;
use \Intervention\Image\ImageManagerStatic as Image;
use \google\ReCaptcha;
use \Tools\Utils;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Rules\Uuid as RulesUuid;
use \Jenssegers\Agent\Agent;
use Model\NewsletterUsersModel;

class UsersController extends MasterFrontController
{
	const PATH_VIEWS = 'front/users';

	protected $usersDb; // contient le model

	public function __construct()
	{
		parent::__construct();
		$this->usersDb = new UsersModel();
		$this->authDb = new AuthentificationModel();
		$this->banishDb = new BanishModel();
		$this->newslettersUsersDb = new NewsletterUsersModel();
	}

	/**
	 * Page d'inscription
	 */
	public function signup()
	{
		$this->redirectIsConnect();
		$uuid = Uuid::uuid4();


		$post = $errors = [];

		if (!empty($_POST)) {
			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errors = [
				(!v::notEmpty()->email()->validate($post['email'])) ? 'Votre adresse email est invalide' : null,
				(!v::notEmpty()->noWhitespace()->alnum('-', '_')->length(3, 20)->validate($post['username'])) ? 'Votre nom d\'utilisateur doit comporter entre 3 et 20 caractères' : null,
				(!v::notEmpty()->length(8, null)->validate($post['password'])) ? 'Votre mot de passe doit comporter au moins 8 caractères' : null,
				(!v::notEmpty()->identical($post['password'])->validate($post['password_confirm'])) ? 'La confirmation de mot de passe ne correspond pas' : null,
				($this->usersDb->emailExists($post['email'])) ? 'Cette adresse email est déjà enregistrée' : null,
				($this->usersDb->usernameExists($post['username'])) ? 'Ce nom d\'utilisateur est déjà existant' : null,
				($this->usersDb->uuidExist($uuid)) ? 'Une erreur est survenue, merci de renvoyer le formulaire' : null,
			];

			$recaptcha = new \ReCaptcha\ReCaptcha('6Ldss7oUAAAAAKJVh7el7B85UOha722ghcJu3MyP');
			$resp = $recaptcha->verify($_POST['g-recaptcha-response']);
			if (!$resp->isSuccess()) {
				$errors[] = 'Erreur de Captcha';
			}

			$errors = array_filter($errors);

			// User-Agent
			$agent = new Agent;
	
			if(count($errors) === 0) {

				if($post['newsletter']){
					$newsletterUser = [
						'email' => $post['email'],
						'date_create' => date('Y-m-d H:i:s'),
						'active' => 1
					];

					$this->newslettersUsersDb->insert($newsletterUser);
				}

				$data = [
					'username'			=> $post['username'],
					'email' 			=> mb_strtolower($post['email'], 'UTF-8'),
					'password'			=> $this->authDb->hashPassword($post['password']),
					'avatar'			=> null,
					'role'				=> 0,
					'date_registered' 	=> date('Y-m-d H:i:s'),
					'date_connect_prev' => date('Y-m-d H:i:s'),
					'date_connect_now' 	=> date('Y-m-d H:i:s'),
					'facebook_id' 		=> null,
					'ip_address'		=> \Tools\Utils::getIpAddress(),
					'uuid'				=> $uuid,
					'agent'             => $agent->setUserAgent()
				];

				$new_user = $this->usersDb->insert($data);

				if ($new_user) {
					$this->flash('Votre inscription sur BFmania est réussie. Vous pouvez désormais vous connecter à votre compte !', 'success');
					$this->redirectToRoute('users_login');
				}
			}
		}

		$this->render(self::PATH_VIEWS . '/signup', [
			'errors' 	=> $errors,
			'success' 	=> $success ?? false,
			'post'		=> $post ?? [],
		]);
	}

	/**
	 * Connexion
	 */
	public function login()
	{
		$this->redirectIsConnect();

		$post = $errors = [];

		if (!empty($_POST)) {
			$post = array_map('trim', array_map('strip_tags', $_POST));

			if (empty($post['login']) && empty($post['password'])) {
				$errors[] = 'Vous devez compléter votre identifiant et mot de passe pour vous connecter.';
			} else {
				$id_user = $this->authDb->isValidLoginInfo($post['login'], $post['password']);

				if ($id_user) {
					$user = $this->usersDb->find($id_user);

					$banish = $this->banishDb->findBy('id_user', $id_user);

					$banish_date = new Datetime($banish['date_expire']);
					$now_date = new DateTime("now");

					$interval = date_diff($now_date, $banish_date);

					$interval = $interval->format('%a');

					$delta = $now_date < $banish_date;

					if ($delta == false) { 
						// Connecte l'utilisateur
						$this->authDb->logUserIn($user);


						// User-Agent
						$agent = new Agent;

						// On met à jour la dernière visite de l'utilisateur
						$data = [
							'date_connect_prev' => $user['date_connect_now'],
							'date_connect_now' 	=> date('Y-m-d H:i:s'),
							'ip_address'		=> \Tools\Utils::getIpAddress(),
							'agent'             => $agent->setUserAgent()
						];

						if ($this->usersDb->update($data, $user['id'])) {
							$this->redirectToRoute('default_home');
						}
					} else {
						$errors[] = 'Votre compte a été désactivé, vous ne pouvez pas vous connecter avant '.$interval.' jours ! ';
					}
				} else {
					$errors[] = 'Erreur d\'identifiant ou de mot de passe';
				}
			}
		}

		$this->render(self::PATH_VIEWS . '/login', [
			'errors' => $errors,
		]);
	}

	/**
	 * Déconnexion
	 */
	public function logout()
	{
		$this->redirectNotConnect();

		// On met à jour la dernière visite de l'utilisateur
		$data = [
			'date_connect_now' => date('Y-m-d H:i:s'),
		];

		if ($this->usersDb->update($data, $this->getUser()['id'])) {
			$this->authDb->logUserOut();
			$this->redirectToRoute('default_home');
		} else {
			$this->authDb->logUserOut();
			$this->redirectToRoute('default_home');
		}
	}

	/**
	 * Mot de passe oublié
	 */
	public function forgotPassword()
	{
		$this->redirectIsConnect();

		$post = $errors = [];
		if (!empty($_POST)) {
			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errors = [
				(!$this->usersDb->emailExists($post['login'])) ? 'Cette adresse email n\'existe pas' : null,
				// (!$this->usersDb->usernameExists($post['login'])) ? 'Ce nom d\'utilisateur n\'existe pas' : null,
			];

			$errors = array_filter($errors);


			if (count($errors) === 0) {
				$token = bin2hex(random_bytes(10));
				$tokenDb = new TokensModel();

				$date_expiration = date('Y-m-d H:i:s', strtotime('+ 2 days'));
				$data = [
					'login'	=> $post['login'],
					'token'	=> $token,
					'date_expiration' => $date_expiration,
				];

				if ($tokenDb->insert($data)) {
					$success = true;

					$link_to_reset = $this->generateUrl('users_reset_password', [], true);
					$link_to_reset .= '?token=' . $token . '&login=' . $post['login'];

					$subject = $this->sitename . ' - Récupération de mot de passe';

					$messageHTML = '<h2>Mot de passe perdu ?</h2>';
					$messageHTML .= '<p>Bonjour, vous avez indiqué avoir perdu votre mot de passe, veuillez cliquer sur le lien suivant pour récupérer l\'accès à votre compte.
									<br><a href="' . $link_to_reset . '">Réinitialiser mon mot de passe</a>
									<br>Si vous n\'êtes pas à l\'origine de cette demande, veuillez ignorer cet email. Vous pouvez continuer à utiliser votre mot de passe actuel.</p>
									<hr>';

					$this->sendEmail($subject, $messageHTML, $post['login']);
				}
			}
		}

		$this->render(self::PATH_VIEWS . '/password-forgot', [
			'post'		=> $post,
			'errors' 	=> $errors,
			'success' 	=> $success ?? false,
		]);
	}

	/**
	 * Reset mot de passe (via mot de passe oublié)
	 */
	public function resetPassword()
	{
		$this->redirectIsConnect();

		$success = false;
		$post = $errors = [];
		$tokenExists = (new TokensModel)->findLoginAndToken($_GET['login'], $_GET['token']);

		if (!empty($_POST)) {
			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errors = [
				(!v::notEmpty()->length(8, null)->validate($post['password'])) ? 'Votre mot de passe doit comporter au moins 8 caractères' : null,
				(!v::notEmpty()->identical($post['password'])->validate($post['password_confirm'])) ? 'Les mots de passe ne correspondent pas' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {
				$userDb = new UsersModel;
				$user = $userDb->findBy('email', $_GET['login']);
				$success = true;
				$data = [
					'password' => $this->authDb->hashPassword($post['password']),
				];
				$userDb->update($data, $user['id']);
			}
		}

		$this->render(self::PATH_VIEWS . '/password-reset', [
			'success' 		=> $success,
			'errors' 		=> $errors,
			'tokenExists' 	=> $tokenExists,
		]);
	}

	/**
	 * Page profil
	 */
	public function account()
	{
		$this->redirectNotConnect();

		$this->render(self::PATH_VIEWS . '/account', [
			'data' => $this->getUser(),

		]);
	}

	/**
	 * Page modifier profil
	 */
	public function editAccount()
	{
		$this->redirectNotConnect();

		$post = $errorsForm = $errorsImg = [];

		

		if (!empty($_POST)) {
			
			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errorsForm = [(!v::email()->validate($post['email'])) ? 'Votre adresse email est invalide' : null,];

			if ($post['email'] != $this->getUser()['email']) {

				$errorsForm[] = ($this->usersDb->emailExists($post['email'])) ? 'Cette adresse email est déjà enregistrée' : null;
			}
			$errorsForm = array_filter($errorsForm);

			$errors = array_merge($errorsForm, $errorsImg);

			if (count($errors) === 0) {
				$data = [
					'email' 			=> mb_strtolower($post['email'], 'UTF-8'),
					'facebook_id' 		=> null,
				];

				if(isset($post['newsletter-active'])){
					if($this->newslettersUsersDb->emailExist($this->getUser()['email'])){

						$newsletterUser =$this->newslettersUsersDb->findBy('email' , $this->getUser()['email']);

						$this->newslettersUsersDb->update(['active' => 1 ] , $newsletterUser['id'] );

					}else{
						$newsletterUser = [
							'email' => $this->getUser()['email'],
							'date_create' => date('Y-m-d H:i:s'),
							'active' => 1
						];
	
						$this->newslettersUsersDb->insert($newsletterUser);
					}
				};

				if(isset($post['newsletter-disabled'])){
					
					$newsletterUser =$this->newslettersUsersDb->findBy('email' , $this->getUser()['email']);

					$this->newslettersUsersDb->update(['active' => 0 ] , $newsletterUser['id'] );
				};

				$modif_user = $this->usersDb->update($data, $this->getUser()['id']);
				$this->authDb->refreshUser();


				if ($modif_user) {
					$this->flash('Vos modifications ont bien été prises en compte !', 'success');
					$this->redirectToRoute('users_account');
				}


			} else {
				$this->flash(implode('<br>', $errors), 'danger');
			}
		}

		if($this->newslettersUsersDb->emailExist($this->getUser()['email'])){
			$newslettersUser = $this->newslettersUsersDb->findBy('email', $this->getUser()['email'] );
			if($newslettersUser['active']){
				$newsletters = true ;
			}
		}


		$this->render(self::PATH_VIEWS . '/account-modified', [
			'data' 	=> $this->getUser(),
			'newsletters' => $newsletters ? $newsletters  : false

		]);
	}

	/**
	 * Page modifier mot de passe
	 */
	public function editPassword()
	{

		$this->redirectNotConnect();

		$post = $errors = [];

		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));
			$errors = [
				(!v::notEmpty()->length(8, null)->validate($post['password'])) ? 'Votre mot de passe doit comporter au moins 8 caractères' : null,
				(!v::notEmpty()->identical($post['password'])->validate($post['password_confirm'])) ? 'La confirmation de mot de passe ne correspond pas' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) == 0) {

				$data = [
					'password' => $this->authDb->hashPassword($post['password']),
				];

				$id = $this->getUser()['id'];
				$modif_pwd = $this->usersDb->update($data, $id);

				if ($modif_pwd) {

					$this->authDb->refreshUser();

					$this->flash('Votre modification du mot de passe à bien été prises en compte !', 'success');
					$this->redirectToRoute('users_account');
				}
			} else {
				$this->flash(implode('<br>', $errors), 'danger');
			}
		}

		$this->render(self::PATH_VIEWS . '/account-password', [
			'data' => $this->getUser(),

		]);
	}
	
	/**
	 * Page modifier avatar
	 */
	public function editImg()
	{
		if (isset($_POST["image"])) {

			$usersDb = $this->usersDb;
			$user = $this->getUser();

			$post = array_map('trim', array_map('strip_tags', $_POST));

			$imagename = $user['username'] . time() . '.png';

			$img = Image::make($post['image']);

			if (($this->document_root . ($user['avatar']) != $this->document_root) && file_exists($this->document_root . ($user['avatar']))){
				unlink($this->document_root . ($user['avatar']));
			}
	
			// Faire une verif si le save n'a pas marcher faire une erreur et qu'on puisse voir le retour
			$url = $img->save($this->root_upload . ('avatars/' . $imagename), 70);
		

			$data = ['avatar' => $this->path_upload . ('avatars/' . $imagename)];

			// Pareil pour l'update
			$usersDb->update($data, $user['id'], false);

			(new AuthentificationModel())->refreshUser();




			
		}
	}
}