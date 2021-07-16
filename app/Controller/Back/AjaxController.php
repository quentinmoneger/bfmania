<?php

namespace Controller\Back;

use \Respect\Validation\Validator as v;
use \Model\UsersModel;
use \Tools\Utils;

class AjaxController extends MasterBackController
{
	const PATH_VIEWS = 'back/ajax';

	public function __construct()
	{
		parent::__construct();
		$this->allowTo(array_keys(\Tools\Utils::listRoles()));
		$this->UtilsTool = new Utils;
	}

	/**
	 * Upload media 
	 */
	public function mediaUpload()
	{
		(new MediasController())->upload();
	}

	/**
	 * Liste media 
	 */
	public function mediaList()
	{
		(new MediasController())->listAll();
	}

	/**
	 * Liste des membres + Recherche Autocompletion
	 * View Admin / Gestion des utilisateurs
	 * @param string $limit Le nombre maximum de résultat à récupérer / par default 10
	 * @param string $order_by La colonne en fonction de laquelle trier / par default id
	 * @param string $order_dir La direction du tri, ASC ou DESC / par default DESC
	 * @param mixed $role Choix du role / par default 100
	 * @return showJson
	 */
	public function getSearch($limit = '10', $order_by = 'id', $order_dir = 'DESC', $role = '100')
	{
		$usersDb = new UsersModel;
		$html = '';

		if(!empty($_GET)){
		
			foreach ($_GET as $key => $value) {
				$get[$key] = trim(strip_tags($value));
			}
			
			if(isset($get['nb_users'])){
				if (is_numeric($get['nb_users']) && $get['nb_users'] < 101){
					$limit = $get['nb_users'];
				}
				else {
					if(!empty($get['nb_users'])){
						die('Error: invalid nb_users param');
					}
				}
			}

			if(isset($get['role'])){
				$roles = [0, 30 , 50, 70, 99, 100];
				if (in_array($get['role'], $roles)){
					 	$role = $get['role'];	
				}
				else {
					if(!empty($get['nb_users'])){
						die('Error: invalid role param');
					}
				}
			}			

			if(isset($get['sort'])){
				$sort = ['a-z', 'z-a','date_ASC', 'date_DESC'];
				if (in_array($get['sort'], $sort)){
					
					switch($get['sort']) {
						case 'a-z':
							$order_by = 'username';
							$order_dir = 'ASC';
						break;
						
						case 'z-a':
							$order_by = 'username';
							$order_dir = 'DESC';
						break;

						case 'date_ASC':
							$order_by = 'date_registered';
							$order_dir = 'ASC';
						break;

						case 'date_DESC':
							$order_by = 'date_registered';
							$order_dir = 'DESC';
						break;
					}
					$order = $get['sort'];
				}
				else {
					if(!empty($get['sort'])){
						die('Error: invalid sort param');
					}
				}
			}

			if(isset($get['order'])){
				$orders = ['id', 'username' , 'email', 'ip_address', 'date_registered', 'date_connect_prev' , 'role', 'agent'];
				if (in_array($get['order'], $orders)){
					 	$order_by = $get['order'];	
				}
				else {
					if(!empty($get['order'])){
						die('Error: invalid order param');
					}
				}
			}
			if(isset($get['dir'])){
				$dirs = ['ASC', 'DESC'];
				if (in_array($get['dir'], $dirs)){
					 	$order_dir = $get['dir'];	
				}
				else {
					if(!empty($get['dir'])){
						die('Error: invalid dir param');
					}
				}
			}
			

			$currentPage = $_GET['page'] ?? 1;

			if(isset($get['search']) && !empty($get['search'])){
				$users = $usersDb->paginate($currentPage, $limit, $order_by, $order_dir, $role, $get['search']);
			}
			else {
				$users = $usersDb->paginate($currentPage, $limit, $order_by, $order_dir, $role);		
			}

			if ($users){

				$totalPages = $usersDb->totalPages;
				$userRole = $_SESSION['user']['role'];
				foreach ($users as $key => $value) {
					if ($userRole < $users[$key]['role']){
						$users[$key]['email']= '................';
					}
					if(!empty($users[$key]['agent'])){
					 $users[$key]['agent'] = $this->UtilsTool->getInfoUserAgent($users[$key]['agent'], 'html');
					}
				}
				$result = [$users, 'totalPages' => $totalPages, 'currentPage' => $currentPage, 'userRole'=> $userRole];

				$this->showJson($result);
				
			}
			else {
				$this->showJson($html);
			}
		}
	}

	/**
	 * Suppression d'une note utilisateur
	 * @param post Method Post 
	 * @return json
	 */
	public function deleteNote()
	{
		if(!empty($_POST)){

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if(!is_numeric($post['id_note'])){
				// Si usage normal.. on devrait pas arriver là.
				$this->json([
					'status' => false,
					'error'  => 'ID non numérique'
				]);
			}
			else {
				// Suppression
				(new \Model\NotesModel)->delete($post['id_note']);
				$this->json([
					'status' => true,
				]);
			}
		}
	}

	/**
	 * Liste des notes sur les utilisateurs
	 * @param post Method Post 
	 * @return json
	 */
	public function listNotes()
	{
		$html = '';
		if(!empty($_POST)){

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if(!is_numeric($post['id_user'])){
				// Si usage normal.. on devrait pas arriver là.
				$this->json([
					'status' => false,
					'error'  => 'Une erreur est survenue'
				]);
			}
			else {
				// Suppression
				$notes = (new \Model\NotesModel)->findAllBy('id_user', $post['id_user']);
				
				foreach ($notes as $note){

					$html.= '
					<tr>
					<td>'.$note['id'].'</td>
					<td class="w-50">'.$note['content'].'</td>
					<td>'.\Tools\Utils::dateFr($note['date_create'], 'd/m/Y \à H:i').'</td>
					<td>'.$note['id_author'].'</td>
					<td class="text-danger">
					<i class="text-danger fas fa-times mr-1" aria-hidden="true"></i>
					<a href="#" data-id-note="'.$note['id'].'" class="deleteNote text-danger">Supprimer</a>
					</td>
					</tr>';
				}

				$resultJson =['html' =>$html, 'notes'=>count($notes)];

				$this->json($resultJson);
			}
		}
	}

	/**
	 * Ajout d'une note
	 * @param post Method Post 
	 * @return json
	 */
	public function addNote()
	{
		
		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errors = [
				(!v::notEmpty()->length(3, null)->validate($post['note'])) ? 'La note doit contenir au minimum 3 caractères&nbsp;!' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {

				$data = [
					'id_user'			=> $post['id_user'],
					'content'			=> $post['note'],
					'date_create' 		=> date('Y-m-d H:i:s'),
					'id_author'			=> $_SESSION['user']['id'],
				];

				$note = (new \Model\NotesModel)->insert($data);

				if ($note) {

					$this->json([
					'status' => true,
					'success' => 'Note ajoutée avec succès',

				]);
				
				}
				$this->json([
					'status' => false,
					'error'  => 'ID non numérique'
				]);
			}

			$this->json([
					'status' => false,
					'errors'  => implode('<br>', $errors),
				]);

		}	
	}
}