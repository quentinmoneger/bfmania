<?php

namespace Controller\Back;

use \Model\UsersModel;
use \Model\WarningsModel;
use \Model\BanishModel;
use \Model\NotesModel;
use \Respect\Validation\Validator as v;
use \W\Security\AuthentificationModel;
use \Intervention\Image\ImageManagerStatic as Image;

class UsersController extends MasterBackController
{
	const PATH_VIEWS = 'back/users';

	protected $usersDb; // contient le model 

	public function __construct()
	{
		parent::__construct();
		$this->usersDb = new UsersModel();
		$this->authDb = new AuthentificationModel();
		$this->warningDb = new WarningsModel();
		$this->banishDb = new BanishModel();
		$this->notesDb = new NotesModel();
	}

	/**
	 * Liste des utilisateurs dans Console / Gestion des utilisateurs
	 */
	public function listAll()
	{	 
		$w_user = $this->getUser();

		if(!in_array($w_user['role'], [50, 70, 99])){
			$this->showForbidden();
		}

		$this->render(self::PATH_VIEWS.'/list', [
			'multinick' => $this->usersDb->getMultinick(),
		]);
	}

	/**
	 * Ajout ou Edition d'un utilisateur
	 * @param string $id Identifiant utilisateur
	 */
	public function addOrEdit($id = null)
	{
		
		$w_user = $this->getUser();

		if(!in_array($w_user['role'], [50, 70, 99])){
			$this->showForbidden();
		}
		$form = (!empty($id)) ? 'edit' : 'add';

		// Edition d'utilisateur
		if($form == 'edit'){

			
			$user = $this->usersDb->find($id);

			$post = [
				'username' 		=> $user['username'],
				'email'			=> $user['email'],
				'avatar'		=> $user['avatar'],
				'role'			=> $user['role'],
				'facebook_id'	=> $user['facebook_id'],
			];
		}
	
		// Soumission formulaire ajout d'utilisateur
		if(!empty($_POST)){

			foreach ($_POST as $key => $value) {
				$post[$key] = trim(strip_tags($value));
			}

			$errors = [
				(!v::notEmpty()->email()->validate($post['email'])) ? 'Votre adresse email est invalide' : null,
				(!v::notEmpty()->noWhitespace()->alnum()->length(3, 15)->validate($post['username'])) ? 'Votre nom d\'utilisateur doit comporter entre 3 et 15 caractères' : null,
				(!v::notEmpty()->email()->validate($post['email'])) ? 'L\'adresse email est invalide' : '',
				//($upload_avatar['status'] === false) ? $upload_avatar['msg'] : null,
			];

			if($post['password'] && $user['password'] != $post['password'] || $form == 'add') {
				$errors [] = (!v::notEmpty()->length(8, null)->validate($post['password'])) ? 'Votre mot de passe doit comporter au moins 8 caractères' : null;
				$errors [] = (!v::notEmpty()->identical($post['password'])->validate($post['password_confirm'])) ? 'La confirmation de mot de passe ne correspond pas' : null;
			}

			if ($user['email'] && $user['email'] != $post['email'] || $form == 'add'){
				$errors [] = ($this->usersDb->emailExists($post['email'])) ? 'Cette adresse email est déjà enregistrée' : null;
			}

			if ($user['username'] && $user['username'] != $post['username'] || $form == 'add'){
				$errors [] = ($this->usersDb->usernameExists($post['username'])) ? 'Ce pseudo est déjà enregistrée' : null;
			}

			if(!empty($post['resultCrop'])){			
				$filename_avatar = 'avatars/'.$post['username'].'-'.time().'.png';
				$img = Image::make($post['resultCrop']);
			}

			$errors = array_filter($errors);
		
			if(count($errors) === 0){

				if($img){
					$imgSave = $img->save(str_replace('//', '/', $this->root_upload .$filename_avatar), 80);
					$filename_output_avatar = ($imgSave) ? '/assets/uploads/'.$filename_avatar : false;
				}
				
				$rows = [
					'username'			=> $post['username'],
					'email' 			=> mb_strtolower($post['email'], 'UTF-8'),
					'role'				=> $post['role'],
					'facebook_id' 		=> ($post['facebook_id']) ? $post['facebook_id'] : null,
				];

				if ($form == 'add'){
					$rows['password'] = $this->authDb->hashPassword($post['password']);
					$rows['date_registered'] = date('Y-m-d H:i:s');
					$rows['date_connect_prev'] = date('Y-m-d H:i:s');
					$rows['date_connect_now'] = date('Y-m-d H:i:s');
					$rows['ip_address'] = \Tools\Utils::getIpAddress();
					$rows['avatar'] = ($filename_output_avatar) ? $filename_output_avatar : null;
				}

				if ($form == 'edit' && !empty($_POST['password'])){
					$rows['password'] = $this->authDb->hashPassword($post['password']);
				}


				if($form == 'edit'){

					// Suppression des variables qu'on ne souhaite pas modifier
					unset($rows['date_registered'], $rows['date_connect_prev'], $rows['date_connect_now']);
					$rows['avatar'] = ($filename_output_avatar) ? $filename_output_avatar : $post['avatar'];

					$update = $this->usersDb->update($rows, $id);
					if($update){
						if($filename_output_avatar && !empty($user['avatar'])){
							unlink(str_replace('//', '/', $this->document_root.$post['avatar']));
						}
						$this->addFlash('success', 'Le profil a été modifié avec succès');
						$this->redirectToRoute('back_users_edit', ['id' => $id ]);
					}
				}
				elseif($form == 'add'){

					$insert = $this->usersDb->insert($rows);
					if($insert){
						$this->addFlash('success', 'Le profil a été ajouté avec succès');
						$this->redirectToRoute('back_users_edit', ['id' => $insert['id']]);
					}
				}
			} else {
				$this->flash(implode('<br>', $errors), 'danger');
			}
		}

		if ($form == 'add'){
			$this->render(self::PATH_VIEWS.'/add', [
				'form' 			=> $form,
			]);
		} else {

			$this->render(self::PATH_VIEWS.'/edit', [
				'form' 			=> $form,
				'post'			=> $user,
				'multinick'		=> $this->usersDb->findAllBy('ip_address', $user['ip_address']),
				'banish'		=> $this->banishDb->listBanishBy('id_user',$id),
				'warning'		=> $this->warningDb->listWarningBy('id_user',$id),
			]);

		}

	}

	/**
	 * Suppression d'un utilisateur
	 * @param string $id Identifiant utilisateur
	 */
	public function delete($id)
	{
		$this->allowTo([50,70,99]);

		$user = $this->usersDb->find($id);

		if ($this->getUser()['role'] <= $user['role']){

			$this->flash('Vous ne pouvez pas supprimer un utilisateur avec un rôle supérieur ou égal au votre', 'danger');
			$this->redirectToRoute('back_users_list');

		}

		if(empty($user)){
			$this->showNotFound();
		}

		// Soumission formulaire
		if(!empty($_POST)){

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if($post['delete'] == 'yes'){

				if (($this->document_root . ($user['avatar']) != $this->document_root) && file_exists($this->document_root . ($user['avatar']))){
					unlink($this->document_root . ($user['avatar']));
				}
				
				$delete = $this->usersDb->deleteUser($id);

				if ($delete){

					$this->flash('L\' utilisateur à été supprimée avec succès', 'success');
					$this->redirectToRoute('back_users_list');

				}
				else{
					die('Erreur SQL');
				}
			}
		}

		$this->render(self::PATH_VIEWS.'/delete', [
			'user' => $user,
		]);
	}

	/**
	 * Suppression d'une note utilisateur
	 * @param string $id Identifiant de la note
	 * @param string $uid Identifiant de l'utilisateur
	 */
	public function noteDelete($id, $uid)
	{

		$this->notesDb->delete($id);

		$this->redirectToRoute('back_users_edit',[
			'id' => $uid,
		]);
	}

	/**
	 * Banissement d'un utilisateur
	 * @param string $id Identifiant utilisateur
	 */
	public function banish($id)
	{
		$user = $this->usersDb->find($id);

		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));
	 
			$errors = [
				(!v::notEmpty()->length(15, null)->validate($post['banishMsg'])) ? 'La motif doit contenir au minimum 15 carctères !' : null,
				(!v::notEmpty()->validate($post['username_author'])) ? 'le nom de l\'auteur est incorrect' : null,
				(!v::notEmpty()->dateTime()->validate($post['hiddenDateExpire'])) ? 'la date est incorrecte' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {

				$data = [
					'id_user'			=> $id,
					'reason'			=> $post['banishMsg'],
					'date_create' 		=> date('Y-m-d H:i:s'),
					'date_expire' 		=> $post['hiddenDateExpire'],
					'id_author'			=> $_SESSION['user']['id'],
				];

				$banish = $this->banishDb->insert($data);

				if ($banish) {

					$subject = $this->sitename . ' - Bannissement du site';
					$messageHTML = '<p>Bonjour '.$user['username'].', </p><p> Vous avez été bannis du site '.$this->sitename.' par '.$post['username_author'].' jusqu\' au  '.\Tools\Utils::dateFr($post['hiddenDateExpire'], 'd/m/Y \à H:i').' pour le motif suivant :</p><p><strong>'.$post['banishMsg'].'</strong></p><p>Cordialement,<br>L\'equipe Bfmania</p>';
					$this->sendEmail($subject, $messageHTML, $user['email']);

					$this->flash('L\'utilisateur '.$user['username'].' à bien été bannis jusqu\'au '.\Tools\Utils::dateFr($post['hiddenDateExpire'], 'd/m/Y \à H:i'), 'success') ;
					$this->redirectToRoute('back_users_edit',[
						'id' => $id,
					]);
				} else {
					die('Sql Error !');
				}
			}

			$this->flash(implode('<br>', $errors), 'danger');
			$this->redirectToRoute('back_users_edit',[
				'id' => $id,
			]);
		}

		$this->redirectToRoute('back_users_edit',[
			'id' => $id,
		]);
	}

	/**
	 * Avertissement d'un utilisateur
	 * @param string $id Identifiant utilisateur
	 */
	public function warning($id)
	{
		$user = $this->usersDb->find($id);

		$errors = [];
		
		if (!empty($_POST)){

			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errors = [
				(!v::notEmpty()->length(15, null)->validate($post['warningMsg'])) ? 'La raison doit contenir au minimum 15 carctères' : null,
				(!v::notEmpty()->validate($post['warning_author'])) ? 'le nom de l\'auteur est incorrect' : null,
			];
			
			$errors = array_filter($errors);

			if(count($errors) === 0){

				$data = [
					'id_user'			=> $id,
					'reason' 			=> $post['warningMsg'],
					'date_create'		=> date('Y-m-d H:i:s'),
					'date_expire'		=> date('Y-m-d H:i:s', strtotime('+ 45 days')),
					'id_author'			=> $_SESSION['user']['id'],
				];

				$new_warning = $this->warningDb->insert($data);

				if ($new_warning){

					$subject = $this->sitename . ' - Avertissement du site';
					$messageHTML = '<p>Bonjour '.$user['username'].', Vous avez été par avertis par '.$post['warning_author'].' pour le motif suivant :</p><p>'.$post['warningMsg'].'</p><p>Cordialement,<br>L\'equipe Bfmania</p>';

					$this->sendEmail($subject, $messageHTML, $user['email']);

					$this->flash('L\'utilisateur '.$user['username'].' à bien été averti pour une durée de 45 jours');
					$this->redirectToRoute('back_users_edit',[
						'id' => $id,
					]);
				}
			}

			$this->flash(implode('<br>', $errors), 'danger') ;
			$this->redirectToRoute('back_users_edit',[
					'id' => $id,
				]);
		}

		$this->redirectToRoute('back_users_edit',[
			'id' => $id,
		]);
	}

}
