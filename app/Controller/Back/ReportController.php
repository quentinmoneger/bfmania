<?php

namespace Controller\Back;
use \Model\ReportsModel;
use \Model\MessagesModel;
use \Model\ChatsModel;
use \Model\ForumPostsModel;
use Model\ForumTopicsModel;
use \Model\UsersModel;
use \Respect\Validation\Validator as v;

class ReportController extends MasterBackController
{	
	const PATH_VIEWS = 'back/report';
	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
		$this->reportsDb = new ReportsModel();
		$this->messagesDb = new MessagesModel();
		$this->topicsDb = new ForumTopicsModel();
		$this->postsDb = new ForumPostsModel();
		$this->chatsDb = new ChatsModel();
		$this->usersDb = new UsersModel();

	}

	/**
	 * liste tous les Signalements
	 * @return render Render sur page console / Signalement
	 */
	public function listAll()
	{		
		
		$reports = $this->reportsDb->findAll();

		foreach ( $reports as $key => $value ){

			if($reports[$key]['type'] === "messagerie"){

				$message = $this->messagesDb->find($value['id_message']);

				$reports[$key]['content'] = $message['message'];
	
				$reports[$key]['content_title'] = $message['title'];
	
				$user_from = $this->usersDb->find($message['id_from']) ;
	
				$reports[$key]['username_from'] = ($user_from['username'] ?? 'Expediteur Introuvable');
				
				$user = $this->usersDb->find($value['id_user']);
	
				$reports[$key]['username'] = ($user['username'] ?? 'Destinataire Introuvable');

			} elseif ($reports[$key]['type'] === "forum"){

				$message = $this->postsDb->find($value['id_message']);

				$reports[$key]['content'] = $message['message'];
	
				$reports[$key]['id_topic'] = $message['id_topic'];

				$user_from = $this->usersDb->find($message['id_author']) ;
	
				$reports[$key]['username_from'] = ($user_from['username'] ?? 'Expediteur Introuvable');
	
				$user = $this->usersDb->find($value['id_user']) ;

				$reports[$key]['username'] = ($user['username'] ?? 'Expediteur Introuvable');

				$topic =  $message['id_topic'];

				$message = $value['id_message'];

			} elseif ($reports[$key]['type'] === "chat"){

				$message = $this->chatsDb->find($value['id_message']);

				$reports[$key]['content'] = $message['message'];
	
				$reports[$key]['content_title'] = $message['room'];

				$user_from = $this->usersDb->findBY('uuid',$message['uuid_from']);
	
				$reports[$key]['username_from'] = ($user_from['username'] ?? 'Expediteur Introuvable');
	
				$user = $this->usersDb->find($value['id_user']) ;

				$reports[$key]['username'] = ($user['username'] ?? 'Expediteur Introuvable');
			}
		}

		krsort($reports);

		$this->render(self::PATH_VIEWS . '/list', [
			'reports' 	=> $reports
		] );
	}

	/**
	 * Signaler un message
	 * @param post Method Post pour l'ajout
	 * @return render Render sur page console / Signalement
	 */
	public function reportMessage()
	{
		$errors = [];
		
		if (!empty($_POST)){

			// On nettoie le Post
			foreach ($_POST as $key => $value) {
				if (is_array($value)) {
					$post[$key] = array_map('trim', array_map('strip_tags', $value));
				} else {
					$post[$key] = trim(strip_tags($value));
				}
			}

			$user = $this->getUser(); 

			$errors = [
				(!v::notEmpty()->length(15, null)->validate($post['report_message'])) ? 'Le signalement doit contenir au minimum 15 caractères' : null
			];
			

			$errors = array_filter($errors);

			if(count($errors) === 0){

				$date = date("Y-m-d H:i:s");
				
				$data = [
					'id_user'			=> $user['id'],
					'date' 			    => $date,
					'id_message'		=> $post['id_message'],
					'type'			    => $post['type'],
					'report_message'    => $post['report_message'],
					'inform_report'     => $post['inform_report']
				];

				$new_report = $this->reportsDb->insert($data);

				if ($new_report){

					$alert = "votre signalement à bien été envoyé !";
					$json = ['alert'=>$alert, 'success'=>true ];
					$this->showJson($json);

				}
			}else {

				$alert = implode (" ", $errors);
				$json= ['alert'=>$alert, 'success'=>false];
				$this->showJson($json);
			}
						
		}
	}

	/**
	 *  suppression d'un signalement
	 * @param post Method Post pour la suppression
	 * @return render Render sur page console / Signalement
	 */
	public function delete()
	{	
		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			$report = $this->reportsDb->find($post['id_report']);

			// Si signalement d'un message de la messagerie
			if ($report['type'] == "messagerie"){
				if (!empty($report)) {

					$data = [
						'message' => 'Le message a été supprimé pour non respect des règles !!!!',
						'title'   => 'Message Supprimé',
					];
					
					$update_message = $this->messagesDb->update($data ,$report['id_message'], true);

					$data = [
						'delete' => 1
					];

					$update_report = $this->reportsDb->update($data, $report['id'], true);


					if($update_message && $update_report){

						
						$user = $this->usersDb->find($report['id_user']);

						if($report['inform_report']){

							$subject = $this->sitename . ' Suivi de signalement';

							$messageHTML = '<p>Bonjour '.$user['username'].',</p><br><p> Vous avez fait un signalement celui ci a été traité par notre equipe. </p><p>Cordialement,<br>L\'equipe Bfmania</p>';
	
							$this->sendEmail($subject, $messageHTML, $user['email']);

							$this->flash('La personne qui a signalé a été averti par email ! ');
	
						}

						$this->flash('Le message à été supprimée avec succès', 'success');
						$this->close($post['id_report']);
					}else{
						$this->flash('Une erreur c\'est produite pendant la modification' , 'danger');
					}

				}else{
					$this->flash('Le message n\'existe pas', 'danger');
					$this->redirectToRoute('back_report_list');
				}
			}

			// Si signalement d'un post du forum
			if ($report['type'] == "forum"){
				if (!empty($report)) {

					$data = [
						'message' => 'Le post a été supprimé pour non respect des règles !!!!'
					];
					
					$update_message = $this->postsDb->update($data ,$report['id_message'], true);

					$data = [
						'delete' => 1
					];

					$update_report = $this->reportsDb->update($data, $report['id'], true);

					if($update_message && $update_report){

						$user = $this->usersDb->find($report['id_user']);

						if($report['inform_report']){

							$subject = $this->sitename . ' Suivi de signalement';

							$messageHTML = '<p>Bonjour '.$user['username'].',</p><br><p> Vous avez fait un signalement, celui-ci a été traité par notre équipe. </p><p>Cordialement,<br>L\'équipe Bfmania</p>';
	
							$this->sendEmail($subject, $messageHTML, $user['email']);

							$this->flash('La personne qui a signalé a été averti par email ! ');
	
						}
						$this->flash('Le post à été supprimé avec succès', 'success');
						$this->close($post['id_report']);
					}else{
						$this->flash('Une erreur c\'est produite pendant la modification' , 'danger');
					}

				}else{
					$this->flash('Le message n\'existe pas', 'danger');
					$this->redirectToRoute('back_report_list');
				}
			}

			// Si signalement d'un message du chat
			if ($report['type'] == "chat"){
				if (!empty($report)) {

					$data = [
						'message' => 'Le message a été supprimé pour non respect des règles !!!!'
					];
					
					$update_message = $this->chatsDb->update($data ,$report['id_message'], true);

					$data = [
						'delete' => 1
					];

					$update_report = $this->reportsDb->update($data, $report['id'], true);

					if($update_message && $update_report){

						$user = $this->usersDb->find($report['id_user']);

						if($report['inform_report']){

							$subject = $this->sitename . ' Suivi de signalement';

							$messageHTML = '<p>Bonjour '.$user['username'].',</p><br><p> Vous avez fait un signalement, celui-ci a été traité par notre équipe. </p><p>Cordialement,<br>L\'équipe Bfmania</p>';
	
							$this->sendEmail($subject, $messageHTML, $user['email']);

							$this->flash('La personne qui a signalé a été averti par email ! ');
	
						}

						$this->flash('Le message à été supprimé avec succès', 'success');
						$this->close($post['id_report']);
					}else{
						$this->flash('Une erreur c\'est produite pendant la modification' , 'danger');
					}

				}else{
					$this->flash('Le message n\'existe pas', 'danger');
					$this->redirectToRoute('back_report_list');
				}
			}
		}

	}

	/**
	 * cloturer un signalement
	 * @param post Method Post pour la cloture
	 * @return render Render sur page console / Signalement
	 */
	public function close()
	{

		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			$report = $this->reportsDb->find($post['id_report']);

			$date = date("Y-m-d H:i:s");

			if (!empty($report)) {

				$id = $post['id_report'];

				$user = $this->getUser(); 

				$data = [
					'id_modo'   => $user['id'],
					'state'     => 1 , 
					'date_state'=> $date
				];

				$update_close = $this->reportsDb->update($data , $id, true);

				if($update_close){
					$this->flash('Le signalement à été cloturé avec succès', 'success');
					$this->redirectToRoute('back_report_list');

				}
			}else{
				$this->flash('Le signalement n\'existe pas', 'danger');	
				$this->redirectToRoute('back_report_list');							
			}
		}

	}

	/**
	 * visualisation post forum
	 * @param $id_post L'identifiant du post signalé
	 * @return render Redirection sur le post dans le forum
	 */
	public function view($id_post)
	{
		$id_post = strip_tags($id_post);

		if($id_post){

			$post = $this->postsDb->find($id_post);

			$id_topic = $post['id_topic'];
			
			$limit = 6 - 1;

			$under_post = $this->postsDb->orderOfPostInTopic($id_topic, $id_post);

			if( $under_post['count(*)'] > $limit){
				$page = $under_post['count(*)'] / $limit;
				$page = ceil($page);
				$page = intval($page);
			}else{
				$page = 1;
			}

			$url = $this->generateUrl('view_topic', ['id' => $id_topic , 'page' => $page] );
			$url = $url.'#p'.$id_post;
			$this->redirect($url);
		}
	}
}