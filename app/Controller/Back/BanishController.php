<?php

namespace Controller\Back;
use \Model\BanishModel;
use \Model\UsersModel;

class BanishController extends MasterBackController
{	
	const PATH_VIEWS = 'back/banish';

	public function __construct()
	{
		parent::__construct();
		$this->banishDb = new BanishModel();
	}

	/**
	 * Liste tout les bannissement
	 * @param get Method Get
	 * @return render Render sur page console / bannissements
	 */
	public function listAll()
	{	
		$order = [ 
			'username' 		=> 'ASC',
			'date_create' 	=> 'ASC',
			'date_expire' 	=> 'ASC',
			'author' 		=> 'ASC',
		];
		
		// Au chargement de la page on ordonne par id et Ascendant
		$order_by = 'id';
		$dir = 'ASC';
		
		// Si requete Get d'ordre 
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
			'banish' => $this->banishDb->listBanish($order_by, $dir),
			'order'	 => $order,
		]);
	}

	/**
	 * Suppression d'un bannissement
	 * @param int $id du bannissement
	 */
	public function delete($id)
	{
		$banish = $this->banishDb->find($id);
		$user = (new UsersModel())->find($banish['id_user']);

		if(empty($banish)){
			$this->showNotFound();
		} 

		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if (isset($post['delete']) && $post['delete'] == 'yes') {
				$this->banishDb->delete($id);

				$this->flash('Le bannissement à été supprimée avec succès', 'success');
				$this->redirectToRoute('back_banish_list');
			}
		}

		$this->render(self::PATH_VIEWS.'/delete', [
			'user' => $user,
			'banish' => $banish,
		]);
	}

}