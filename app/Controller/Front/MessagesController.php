<?php

# Si on est dans le back, remplacer "Front" ici par "Back"
namespace Controller\Front;

use \Model\MessagesModel;
use \Model\UsersModel;
use \W\Security\AuthentificationModel;
use \Respect\Validation\Validator as v;
use \Tools\Utils;
use DateTime;

class MessagesController extends MasterFrontController
{

	// Dossiers des vues de ce controller
	const PATH_VIEWS = 'front/messages';

	protected $usersDb; // contient le model 

	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
		$this->usersDb = new UsersModel();
		$this->authDb = new AuthentificationModel();
		$this->messagesDb = new MessagesModel();
	}

	/**
	 * Accueil messagerie
	 */
	public function messagesHome()
	{
		$this->redirectNotConnect();

		$user = $this->getUser();

		// On récupère tous les messages ou l'utilisateur est mentionné
		$messages = $this->messagesDb->listMessages($user['id']);

		foreach ($messages as $key => $message) {

			// On crée un tableau des destinataires
			$message_id_to = explode(',', $message['id_to']);
			$array_id_to = array_filter($message_id_to);

			$username_copy = "";
			$username_to = "";
			foreach ($array_id_to as $key => $id_to) {
				if ($user['id'] == $id_to) {
					$username_to = "moi" . "," . $username_to;
				} else {
					$user_to = $this->usersDb->find($id_to);
					if ($user_to == false) {
						$user_to['username'] = 'Utilisateur introuvable';
					};
					$username_copy = $user_to['username'] . ',' . $username_copy;
				}
			}

			if ($message['username_from'] == $user['username']) {
				$message['username_from'] = "moi";
			}

			// Si la valeur d'id_message_parent n'est pas null on rentre
			if ($message['id_message_parent'] != null && !str_contains($message['id_from'], $message['id_to'])) {

				$message['previous'] =  $this->messagesDb->listMessagesByparent($user['username'], $message['date'], $message['id'], $message['id_message_parent']);
			};

			$return_message = [
				"answer" => $message['previous'] ?? null,
				"id" => $message['id'],
				"title" => $message['title'],
				"message" => $message['message'],
				"id_from" => $message['id_from'],
				"id_to" => $message['id_to'],
				"id_message_parent" => $message['id_message_parent'],
				"date" => $message['date'],
				"read_message" => $message['read_message'],
				"delete_message" => $message['delete_message'],
				"username_from" => $message['username_from'] ?? 'Utilisateur introuvable',
				"role_from" => $message['role_from'],
				"username_to" => $username_to,
				"username_copy" => $username_copy,
				"avatar_from" => $message['avatar_from']
			];
			$return_messages[] = $return_message;
		}

		$this->render(self::PATH_VIEWS . '/home', [
			'discussion' 	=> $return_messages ?? '',

		]);
	}

	/**
	 * Messagerie, marque message comme lu ou non lu
	 * @method AJAX
	 */
	public function readUnread()
	{

		if (!empty($_GET)) {

			$user = $this->getUser();

			$get = array_map('trim', array_map('strip_tags', $_GET));

			$errors = [
				(!v::intVal()->validate($get['id_post'])) ? 'id invalide' : null,
				(!v::intVal()->validate($get['mark'])) ? 'tinyint invalide' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {
				$update = (new MessagesModel())->updateRead($get['id_post'], $user['id'], $get['mark']);
				if ($update) {
					$countUnreadMessage = (new MessagesModel())->countUnreadMessage($this->getUser()['id']);
					if ($countUnreadMessage >= 100) {
						$countUnreadMessage = '99+';
					}
					$this->showJson($countUnreadMessage);
				}
			} else {
				die(implode('<br>', $errors));
			}
		}
	}

	/**
	 * Messagerie, Nouveau Message
	 * @method AJAX
	 */
	public function writeMessage()
	{


		// Redirection si non connecté
		$this->redirectNotConnect();
		// Recupere l'id de l'utilisateur connecté
		$current_user = $this->getUser();
		// Initialisation des tableaux
		$errors = $id_to = $to = [];
		// On initialise reply a false 
		$reply = false;

		// Effectue la fonction si $_POST n'est pas vide
		if (!empty($_POST)) {

			// Transforme de JSON en Array
			foreach ($_POST as $key => $value) {
				// Nettoie les données de tout les espaces et balises html
				if (is_array($value)) {
					$post[$key] = array_map('trim', array_map('strip_tags', $value));
				} else {
					$post[$key] = trim(strip_tags($value));
				}
			}

			// Validation des inputs 
			$errors = [
				(!v::notEmpty()->length(2, null)->validate($post['title'])) ? 'L\' objet doit comporter au moins 2 caractères !' : null,
				(!v::notEmpty()->length(3, null)->validate($post['message'])) ? 'Le message doit comporter au moins 3 caractères !' : null,
			];

			// Boucle pour remplir les tableaux $id_to et $to (username)
			if (!empty($post['to'])) {
				foreach ($post['to'] as $keys => $user_id) {
					$id_to[] = $user_id;
					$user = $this->usersDb->find($user_id);
					$username_to[] = $user['username'];
					$users[] = $user;
				}
			} else {
				$errors[] = 'Vous devez choisir au moins un utilisateur !';
			}

			// Permet de confirmer si c'est une reponse dans le json
			if (!empty($post['id_parent'])) {
				$errors[] = (!v::notEmpty()->intVal()->validate($post['id_parent'])) ? 'Erreur identifiant destinataire' . $post['id_parent'] : null;
				$reply = true;
			}

			// Nettoyage des champs vide
			$errors = array_filter($errors);

			// Si aucune erreur on effectue la fonction
			if (count($errors) === 0) {

				foreach ($users as $user) {
					if ($user['id'] != $id_to) {
						$origin = new DateTime("now");
						$target = new DateTime($user['date_connect_prev']);
						$interval = $origin->diff($target);
						$result = $interval->format('%h');
						if ($result > 24) {

							$subject = '[Contact du site] Nouveau message en date du ' . date('d/m/Y H:i');

							$message = '<h3>Vous avez un nouveau message de ' . $current_user['username'] . '</h3>';
							$message .= '<p>Le ' . date('d/m/Y H:i') . '</p>';
							$message .= '<br>';
							$message .= '<p>Connectez vous pour lire le message.</p>';

							$receiver = $user['email'];

							$this->sendEmail($subject, $message, $receiver);
						}
					}
				}

				// On definir avec l'utilisateur connecté
				$id_from = $current_user['id'];

				// On rassemble les elements du tableau en une chaîne de caractère
				$id_to = ',' . implode(',', $id_to);


				// On set les données a insérer
				$data = [
					'title'				=> $post['title'],
					'message' 			=> $post['message'],
					'id_from' 			=> $id_from,
					'id_to' 			=> $id_to,
					'id_message_parent'	=> ($post['id_parent']) ? $post['id_parent'] : null,
					'date'			 	=> date('Y-m-d H:i:s'),
				];

				// On crée une instance et on inser les données dans la bdd
				$insert = (new MessagesModel)->insert($data);


				// Si l'injection dans la bdd a fonctionné on effectue cette fonction 
				if ($insert) {


					$username_to = implode(', ', $username_to);

					// Alert pour dire que le message a bien été enregistré
					$alert = '<div class="alert alert-success alert-dismissible m-0" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						votre message a bien été envoyé !
					</div>';

					// Html pour la boite de message envoyé de l'expediteur
					$mpline = '
							<div  id="list-' . $insert['id'] . '" class="collapse send-box">	
								<div class="row px-2 py-1 mx-0 text-color-2 mpline border-bottom align-items-center">
									
									<div class="col-md-4 col-12 px-1 border-right p-0 text-truncate">
											
										<a class=" link-colored text-secondary m-0 align-middle" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">
											<div class=" d-inline-block mr-2 align-middle text-truncate">
													<i class="border-right pr-1 fas fa-inbox-out font13"></i>
													<small>à :</small>
													' . $username_to . '																																									<small></small>
											</div>
										</a>
									</div>
									
									<div class="col-md-5 col-12 px-1 text-truncate border-right p-0 read-mail" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">
										' . \Tools\Utils::cutString($insert['title'], $length = 39) . '										
										<span class="text-muted font14 pl-2">
											- ' . $insert['message'] . '											
										</span>
									</div>
									
									<div class="col-md-3 col-12 px-1 mp-date d-flex justify-content-between align-items-center p-0 read-mail write-post-line">
										<div class="mr-1 band">
											<div class="mail-date" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">' . \Tools\Utils::timeAgo($insert['date'], null, 'd/m/Y') . '</div>
											<div class="collapse mpline-show action-date">
													<input hidden type="text" value="' . $insert['id'] . '" name="id_post">
													<i class="fa fa-arrow-alt-right border-right px-1" data-toggle="collapse" data-title="' . $insert['title'] . '" data-message="' . $insert['message'] . '"></i>
													<i class="fas fa-trash border-right px-1" data-toggle="modal" data-target="#delete-' . $insert['id'] . '"></i>
											</div>
										</div>
										<div class="arrow ml-auto">
											<i class="fa fa-chevron-right fa-lg" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box " aria-hidden="true"></i>
										</div>
									</div>
								</div>
							</div>
							<!-- READ MESSAGE-->
							
							<div id="read-' . $insert['id'] . '" class="bg-white collapse mail px-4 pb-5 h-100">
								<div class="text-right">
									<a href="" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box" class="text-secondary font14"><i class="fa fa-undo mr-2"></i>retour messages envoyés</a>
									<hr class="mt-1 mx-n4">
								</div>

								<div>
									<div class="row">
										<div class="col-md-8 col-12">
											<div class="row">
												<div class="mr-4">
													<img id="imgAvatar" src="' . $user['avatar'] . '" class="rounded-circle border border-secondary" alt="Avatar de l\'expediteur">
												</div>
												<div class="text-truncate">
													<small>à : </small>' . $username_to . '	
													<br><small>objet : </small>' . $insert['title'] . '
												</div>
											</div>
										</div>
										<div class="col-md-4 col-12 mt-2 text-right">
											<div>
												<small>Envoyé le: </small>
												<span class="font14">' . \Tools\Utils::dateFr($insert['date'], 'd/m/Y \à H:i') . '</span>
											</div>
											<div class="icon p-1 d-inline-block font18">
												<span class="text-light">|</span>
												<i class="fas fa-trash mx-1" data-toggle="modal" data-target="#delete-' . $insert['id'] . '"></i>
												<span class="text-light">|</span>
											</div>
										</div>
									</div>
									<hr class="">
									<div class="mb-4">' . nl2br($insert['message']) . '</div>
								</div>
							';

					// Insertion HTML pour la suppresion d'un message
					$mpline .= '</div>
					 	<!-- MODAL -->
					 	<div class="modal fade" id="delete-' . $insert['id'] . '" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					 		<div class="modal-dialog modal-dialog-centered" role="document">
					 			<div class="modal-content">
					 				<div class="modal-header">
					 					<h5 class="modal-title" id="modalDeleteLabel">Confirmer la suppression</h5>
					 					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					 						<span aria-hidden="true">&times;</span>
					 					</button>
					 				</div>
					 				<div class="modal-body">
					 					Souhaitez-vous vraiment supprimer le message ?
					 				</div>
					 				<div class="modal-footer">
					 					<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
					 					<a href="" class="btn btn-first text-light">Oui, supprimer</a>
					 				</div>
					 			</div>
					 		</div>
					 	</div>';

					// Retour Json si le message a bien été envoyé
					$json = ['alert' => $alert, 'success' => true, 'mpline' => $mpline];
					$this->showJson($json);
				}
			} else {
				// Si l'insertion ne c'est pas faite on affiche une erreur
				$alert = '<div class="alert alert-danger alert-dismissible mb-4" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' .
					implode('<br>', $errors) . '	
					</div>';
				$json = ['alert' => $alert, 'success' => false, 'reply' => $reply];
				$this->showJson($json);
			}
		}
	}

	/**
	 * Messagerie, Répondre a Tous
	 * @method AJAX
	 */
	public function replyMessageAll()
	{

		$this->redirectNotConnect();
		$user = $this->getUser();
		$errors = $id_to = $to = [];
		$reply = false;



		if (!empty($_POST)) {

			// On nettoie le Post
			foreach ($_POST as $key => $value) {
				if (is_array($value)) {
					$post[$key] = array_map('trim', array_map('strip_tags', $value));
				} else {
					$post[$key] = trim(strip_tags($value));
				}
			}


			// On valide le message
			$errors = [
				(!v::notEmpty()->length(3, null)->validate($post['message'])) ? 'Le message doit comporter au moins 3 caractères !' : null,
			];

			// On recupère le message grace a l'id parent pour avoir les destinataires
			$message = $this->messagesDb->find($_POST['id_parent']);

			// On valide l'id parent
			if (!empty($post['id_parent'])) {
				$errors[] = (!v::notEmpty()->intVal()->validate($post['id_parent'])) ? 'Erreur identifiant destinataire' . $post['id_parent'] : null;
				$reply = true;
			}


			// On récupère les destinataires du message reçu
			// pour créer un tableau 
			$id_to =  explode(',', $message['id_to']);
			$id_to = array_filter($id_to);
			// On cherche l'expéditeur du message a envoyer
			// pour le supprimer des déstinataires
			$unset = array_search($user['id'], $id_to);
			unset($id_to[$unset]);
			// On crée la liste des destinataires 
			array_push($id_to, $message['id_from']);
			// On paramètre l'expéditeur 
			$id_from = $user['id'];
			// On vérifie que l'expéditeur n'est pas dans les déstinataires
			if (array_search($id_from, $id_to) !== false) {
				unset($id_to[array_search($id_from, $id_to)]);
			}
			// On vérifie que le ou les utilisateurs destinataires existent toujours
			foreach ($id_to as $user_to) {
				if (!$this->usersDb->find($user_to)) {
					$errors[] = 'Destinataire introuvable';
				}else{
					$to[] = $this->usersDb->find($user_to)['username'];
				}
			}
			// On reconstruit la chaine de destinataire 
			$id_to = ',' . implode(',', $id_to);

			// On filtre les erreurs
			$errors = array_filter($errors);

			// On verifie si il y a des errreurs
			if (count($errors) === 0) {

				$data = [
					'title'				=> $post['title'],
					'message' 			=> $post['message'],
					'id_from' 			=> $id_from,
					'id_to' 			=> $id_to,
					'id_message_parent'	=> ($post['id_parent']) ? $post['id_parent'] : null,
					'date'			 	=> date('Y-m-d H:i:s'),
				];

				$insert = (new \Model\MessagesModel)->insert($data);
				if ($insert) {
					$countAnswer = '';
					if ($insert['id_message_parent']) {
						$insert['answer'] = (new \Model\MessagesModel)->listMessagesByparent($user['username'], $insert['date'], $insert['id'], $insert['id_message_parent']);

						$countAnswer = count($insert['answer']) + 1;
						$countAnswer = '[' . $countAnswer . ']';
					}

					$to = implode(', ', $to);

					$message_parent_name = ($insert['id_message_parent']) ? ', moi' : '';

					$alert = '<div class="alert alert-success alert-dismissible m-0" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						votre message à bien été envoyé !
					</div>';

					$mpline = '
							<div  id="list-' . $insert['id'] . '" class="collapse send-box">	
							<div class="row px-2 py-1 mx-0 text-color-2 mpline border-bottom align-items-center">
								
								<div class="col-md-4 col-12 px-1 border-right p-0 text-truncate">
										
									<a class=" link-colored text-secondary m-0 align-middle" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">
										<div class=" d-inline-block mr-2 align-middle text-truncate">
												<i class="border-right pr-1 fas fa-inbox-out font13"></i>
												<small>à :</small>
												' . $to . '
												' . $message_parent_name . '
												<small>' . $countAnswer . '</small>																																									<small></small>
										</div>
									</a>
								</div>
								
								<div class="col-md-5 col-12 px-1 text-truncate border-right p-0 read-mail" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">
									' . \Tools\Utils::cutString($insert['title'], $length = 39) . '										
									<span class="text-muted font14 pl-2">
										- ' . $insert['message'] . '											
									</span>
								</div>	

								<div class="col-md-3 col-12 px-1 mp-date d-flex justify-content-between align-items-center p-0 read-mail write-post-line">
									<div class="mr-1 band">
										<div class="mail-date" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">' . \Tools\Utils::timeAgo($insert['date'], null, 'd/m/Y') . '</div>
										<div class="collapse mpline-show action-date">
												<input hidden type="text" value="' . $insert['id'] . '" name="id_post">
												<i class="fa fa-arrow-alt-right border-right px-1" data-toggle="collapse" data-title="' . $insert['title'] . '" data-message="' . $insert['message'] . '"></i>
												<i class="fas fa-trash border-right px-1" data-toggle="modal" data-target="#delete-' . $insert['id'] . '"></i>
										</div>
									</div>
									<div class="arrow ml-auto">
									<i class="fa fa-chevron-right fa-lg" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box " aria-hidden="true"></i>
									</div>
								</div>
							</div>
						</div>
						<!-- READ MESSAGE-->

							<div id="read-' . $insert['id'] . '" class="bg-white collapse mail px-4 pb-5 h-100">
							<div class="text-right">
								<a href="" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box" class="text-secondary font14"><i class="fa fa-undo mr-2"></i>retour messages envoyés</a>
								<hr class="mt-1 mx-n4">
							</div>

							<div>
								<div class="row">
									<div class="col-md-8 col-12">
										<div class="row">
											<div class="mr-4">
												<img id="imgAvatar" src="' . $user['avatar'] . '" class="rounded-circle border border-secondary" alt="Avatar de l\'expediteur">
											</div>
											<div class="text-truncate">
												<small>à : </small>' . $to . '	
												<br><small>objet : </small>' . $insert['title'] . '
											</div>
										</div>
									</div>
									<div class="col-md-4 col-12 mt-2 text-right">
										<div>
											<small>Envoyé le: </small>
											<span class="font14">' . \Tools\Utils::dateFr($insert['date'], 'd/m/Y \à H:i') . '</span>
										</div>
										<div class="icon p-1 d-inline-block font18">
											<span class="text-light">|</span>
											<i class="fas fa-trash mx-1" data-toggle="modal" data-target="#delete-' . $insert['id'] . '"></i>
											<span class="text-light">|</span>
										</div>
									</div>
								</div>
								<hr class="">
								<div class="mb-4">' . nl2br($insert['message']) . '</div>
							</div>
						';


					if (isset($insert['answer'])) {
						$ml = 0;
						foreach ($insert['answer'] as $message_parent) {
							$ml += 10;
							$username_parent = ($message_parent['username_from'] == $user['username']) ? 'moi' : $message_parent['username_from'];
							$mpline .= '
								<div class="p-2" style="background-color: #fff; margin-left:' . $ml . 'px; border-left:2px solid #ddd">

									<div class="d-flex justify-content-start align-items-end">
										<div><small>de:</small>' . $username_parent . '</div>
										<div class="font12 mx-2">' . \Tools\Utils::dateFr($message_parent['date'], 'd/m/Y \à H:i') . '</div>
									</div>
									<hr class="my-1">
									<div class="font14" id="collapse' . $message_parent['id'] . '" aria-labelledby="heading' . $message_parent['id'] . '">	
										Message : ' . nl2br($message_parent['message']) . '
									</div>
								</div>';
						}
					}

					$mpline .= '</div>
					 	<!-- MODAL -->
					 	<div class="modal fade" id="delete-' . $insert['id'] . '" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					 		<div class="modal-dialog modal-dialog-centered" role="document">
					 			<div class="modal-content">
					 				<div class="modal-header">
					 					<h5 class="modal-title" id="modalDeleteLabel">Confirmer la suppression</h5>
					 					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					 						<span aria-hidden="true">&times;</span>
					 					</button>
					 				</div>
					 				<div class="modal-body">
					 					Souhaitez-vous vraiment supprimer le message ?
					 				</div>
					 				<div class="modal-footer">
					 					<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
					 					<a href="" class="btn btn-first text-light">Oui, supprimer</a>
					 				</div>
					 			</div>
					 		</div>
					 	</div>';


					$json = ['alert' => $alert, 'success' => true, 'mpline' => $mpline];
					$this->showJson($json);
				}
			} else {
				$alert = '<div class="alert alert-danger alert-dismissible mb-4" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' .
					implode('<br>', $errors) . '	
					</div>';
				$json = ['alert' => $alert, 'success' => false, 'reply' => $reply];
				$this->showJson($json);
			}
		}
	}

	/**
	 * Messagerie, Répondre 
	 * @method AJAX
	 */
	public function replyMessage()
	{

		$this->redirectNotConnect();
		$user = $this->getUser();
		$errors = $id_to = $to = [];
		$reply = false;

		if (!empty($_POST)) {

			// On nettoie le Post
			foreach ($_POST as $key => $value) {
				if (is_array($value)) {
					$post[$key] = array_map('trim', array_map('strip_tags', $value));
				} else {
					$post[$key] = trim(strip_tags($value));
				}
			}

			// On valide le message
			$errors = [
				(!v::notEmpty()->length(3, null)->validate($post['message'])) ? 'Le message doit comporter au moins 3 caractères !' : null,
			];

			// On valide l'id parent
			if (!empty($post['id_parent'])) {
				$errors[] = (!v::notEmpty()->intVal()->validate($post['id_parent'])) ? 'Erreur identifiant destinataire' . $post['id_parent'] : null;
				$reply = true;
			}


			// On vérifie que le  destinataires existent toujours
			if (!$this->usersDb->find($post['to'])) {
				$errors[] = 'Destinataire introuvable';
			}else{
				$to[] = $this->usersDb->find($post['to'])['username'];
			}

			// On filtre les erreurs
			$errors = array_filter($errors);

			// On verifie si il y a des errreurs
			if (count($errors) === 0) {

				$id_to = ',' . $post['to'];

				$data = [
					'title'				=> $post['title'],
					'message' 			=> $post['message'],
					'id_from' 			=> $user['id'],
					'id_to' 			=> $id_to,
					'id_message_parent'	=> ($post['id_parent']) ? $post['id_parent'] : null,
					'date'			 	=> date('Y-m-d H:i:s'),
				];

				$insert = (new \Model\MessagesModel)->insert($data);
				if ($insert) {
					$countAnswer = '';
					if ($insert['id_message_parent']) {
						$insert['answer'] = (new \Model\MessagesModel)->listMessagesByparent($user['username'], $insert['date'], $insert['id'], $insert['id_message_parent']);

						$countAnswer = count($insert['answer']) + 1;
						$countAnswer = '[' . $countAnswer . ']';
					}

					$to = implode(', ', $to);

					$message_parent_name = ($insert['id_message_parent']) ? ', moi' : '';

					$alert = '<div class="alert alert-success alert-dismissible m-0" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						votre message à bien été envoyé !
					</div>';

					$mpline = '
							<div  id="list-' . $insert['id'] . '" class="collapse send-box">	
								<div class="row px-2 py-1 mx-0 text-color-2 mpline border-bottom align-items-center">
									
									<div class="col-md-4 col-12 px-1 border-right p-0 text-truncate">
											
										<a class=" link-colored text-secondary m-0 align-middle" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">
											<div class=" d-inline-block mr-2 align-middle text-truncate">
													<i class="border-right pr-1 fas fa-inbox-out font13"></i>
													<small>à :</small>
													' . $to . '
													' . $message_parent_name . '
													<small>' . $countAnswer . '</small>																																									<small></small>
											</div>
										</a>
									</div>
									
									<div class="col-md-5 col-12 px-1 text-truncate border-right p-0 read-mail" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">
										' . \Tools\Utils::cutString($insert['title'], $length = 39) . '										
										<span class="text-muted font14 pl-2">
											- ' . $insert['message'] . '											
										</span>
									</div>	

									<div class="col-md-3 col-12 px-1 mp-date d-flex justify-content-between align-items-center p-0 read-mail write-post-line">
										<div class="mr-1 band">
											<div class="mail-date" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box ">' . \Tools\Utils::timeAgo($insert['date'], null, 'd/m/Y') . '</div>
											<div class="collapse mpline-show action-date">
													<input hidden type="text" value="' . $insert['id'] . '" name="id_post">
													<i class="fa fa-arrow-alt-right border-right px-1" data-toggle="collapse" data-title="' . $insert['title'] . '" data-message="' . $insert['message'] . '"></i>
													<i class="fas fa-trash border-right px-1" data-toggle="modal" data-target="#delete-' . $insert['id'] . '"></i>
											</div>
										</div>
										<div class="arrow ml-auto">
										<i class="fa fa-chevron-right fa-lg" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box " aria-hidden="true"></i>
										</div>
									</div>
								</div>
							</div>
							<!-- READ MESSAGE-->

								<div id="read-' . $insert['id'] . '" class="bg-white collapse mail px-4 pb-5 h-100">
								<div class="text-right">
									<a href="" data-toggle="collapse" data-target="#read-' . $insert['id'] . ', .send-box" class="text-secondary font14"><i class="fa fa-undo mr-2"></i>retour messages envoyés</a>
									<hr class="mt-1 mx-n4">
								</div>

								<div>
									<div class="row">
										<div class="col-md-8 col-12">
											<div class="row">
												<div class="mr-4">
													<img id="imgAvatar" src="' . $user['avatar'] . '" class="rounded-circle border border-secondary" alt="Avatar de l\'expediteur">
												</div>
												<div class="text-truncate">
													<small>à : </small>' . $to . '	
													<br><small>objet : </small>' . $insert['title'] . '
												</div>
											</div>
										</div>
										<div class="col-md-4 col-12 mt-2 text-right">
											<div>
												<small>Envoyé le: </small>
												<span class="font14">' . \Tools\Utils::dateFr($insert['date'], 'd/m/Y \à H:i') . '</span>
											</div>
											<div class="icon p-1 d-inline-block font18">
												<span class="text-light">|</span>
												<i class="fas fa-trash mx-1" data-toggle="modal" data-target="#delete-' . $insert['id'] . '"></i>
												<span class="text-light">|</span>
											</div>
										</div>
									</div>
									<hr class="">
									<div class="mb-4">' . nl2br($insert['message']) . '</div>
								</div>
							';


					if (isset($insert['answer'])) {
						$ml = 0;
						foreach ($insert['answer'] as $message_parent) {
							$ml += 10;
							$username_parent = ($message_parent['username_from'] == $user['username']) ? 'moi' : $message_parent['username_from'];
							$mpline .= '
								<div class="p-2" style="background-color: #fff; margin-left:' . $ml . 'px; border-left:2px solid #ddd">

									<div class="d-flex justify-content-start align-items-end">
										<div><small>de:</small>' . $username_parent . '</div>
										<div class="font12 mx-2">' . \Tools\Utils::dateFr($message_parent['date'], 'd/m/Y \à H:i') . '</div>
									</div>
									<hr class="my-1">
									<div class="font14" id="collapse' . $message_parent['id'] . '" aria-labelledby="heading' . $message_parent['id'] . '">	
										Message : ' . nl2br($message_parent['message']) . '
									</div>
								</div>';
						}
					}

					$mpline .= '</div>
					 	<!-- MODAL -->
					 	<div class="modal fade" id="delete-' . $insert['id'] . '" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					 		<div class="modal-dialog modal-dialog-centered" role="document">
					 			<div class="modal-content">
					 				<div class="modal-header">
					 					<h5 class="modal-title" id="modalDeleteLabel">Confirmer la suppression</h5>
					 					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					 						<span aria-hidden="true">&times;</span>
					 					</button>
					 				</div>
					 				<div class="modal-body">
					 					Souhaitez-vous vraiment supprimer le message ?
					 				</div>
					 				<div class="modal-footer">
					 					<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
					 					<a href="" class="btn btn-first text-light">Oui, supprimer</a>
					 				</div>
					 			</div>
					 		</div>
					 	</div>';


					$json = ['alert' => $alert, 'success' => true, 'mpline' => $mpline];
					$this->showJson($json);
				}
			} else {
				$alert = '<div class="alert alert-danger alert-dismissible mb-4" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' .
					implode('<br>', $errors) . '	
					</div>';
				$json = ['alert' => $alert, 'success' => false, 'reply' => $reply];
				$this->showJson($json);
			}
		}
	}

	/**
	 * Messagerie, Suppression message
	 * @method AJAX
	 */
	public function deleteMessage()
	{

		if (!empty($_GET)) {

			$user = $this->getUser();
			$get = array_map('trim', array_map('strip_tags', $_GET));

			$errors = [
				(!v::intVal()->validate($get['id_message'])) ? 'id invalide' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {

				$update = $this->messagesDb->updateDelete($get['id_message'], $user['id']);
				if ($update) {
					$this->showJson(true);
				}
			} else {
				$this->showJson(false);
			}
		}
	}

	/**
	 * Messagerie, Autocompletion du destinataire Select2
	 * @method AJAX
	 */
	public function autocompleteMessage()
	{

		if (!empty($_GET)) {

			$get = array_map('trim', array_map('strip_tags', $_GET));

			$errors = [
				(!v::notEmpty()->length(2, null)->validate($get['search'])) ? 'La recherche doit comporter au moins 2 caractères' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {
				$search = [
					'username' => $get['search'],
				];
				$users = $this->usersDb->searchIdUsername($search);

				foreach ($users as $user) {
					$id = intval($user['id']);
					$text = $user['username'];
					$data[] =  ['id' => $id, 'text' => $text];
				}

				echo json_encode($data);
			} else {
				die(implode('<br>', $errors));
			}
		}
	}
}
