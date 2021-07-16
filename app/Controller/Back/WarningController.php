<?php

namespace Controller\Back;
use \Model\WarningsModel;
use \Model\UsersModel;

class WarningController extends MasterBackController
{	
	const PATH_VIEWS = 'back/warning';
	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
		$this->WarningsDb = new WarningsModel();

	}

	/**
	 *  liste tout les avertissements
	 * @param get Method Get pour ordoner / par default ASC par id
	 * @return render console / Avertissements  
	 */
	public function listAll()
	{
		$order = [ 
			'username' => 'ASC',
			'date_create' => 'ASC',
			'date_expire' => 'ASC',
			'author' => 'ASC',
		];
		$order_by = 'id';
		$dir = 'ASC';
		
		if(!empty($_GET)){
			$get = array_map('trim', array_map('strip_tags', $_GET));
			if(in_array($get['sort'], ['username', 'date_create', 'date_expire', 'author']) && in_array($get['dir'], ['ASC', 'DESC'])){

				$dir = $get['dir'];
				
				switch($get['sort']) {
					case 'username':
						$order_by = 'username';
						($get['dir'] == 'ASC') ? $order['username'] = 'DESC' : $order['username'] = 'ASC';
					break;
					
					case 'date_create':
						$order_by = 'date_create';
						($get['dir'] == 'ASC') ? $order['date_create'] = 'DESC' : $order['date_create'] = 'ASC';
					break;

					case 'date_expire':
						$order_by = 'date_expire';
						($get['dir'] == 'ASC') ? $order['date_expire'] = 'DESC' : $order['date_expire'] = 'ASC';
					break;

					case 'author':
						$order_by = 'author';
						($get['dir'] == 'ASC') ? $order['author'] = 'DESC' : $order['author'] = 'ASC';
					break;
				}

			}else {
				$this->showForbidden();
			}
		}
		$this->render(self::PATH_VIEWS . '/list', [
			'warning' 	=> $this->WarningsDb->listWarning($order_by, $dir),
			'order'		=> $order
		]);
	}

	/**
	 *  suppression d'un avertissement
	 * @param int $id Identifiant de l'utilisateur
	 * @return rendre sur listAll
	 */
	public function delete($id)
	{

		$warning = $this->WarningsDb->find($id);
		$user = (new UsersModel())->find($warning['id_user']);

		if(empty($warning)){
			$this->showNotFound();
		} 

		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if (isset($post['delete']) && $post['delete'] == 'yes') {
				$this->WarningsDb->delete($id);

				$this->flash('L\'avertissement à été supprimée avec succès', 'success');
				$this->redirectToRoute('back_warning_list');
			}
		}

		$this->render(self::PATH_VIEWS.'/delete', [
			'user' => $user,
			'warning' => $warning,
		]);
	}

}