<?php

namespace Controller\Front;

use \Model\UsersModel;

use \Respect\Validation\Validator as v;

class WebServiceController extends MasterFrontController
{

	protected $usersDb;

	public function __construct()
	{
		parent::__construct();

		$this->usersDb = new UsersModel;
		$this->allowHttpOrigin = [
			'http://bfmania.loc',
			'http://bfmania.loc:8080',
			'http://localhost:8080',
			'http://192.168.1.23:8080',
			'http://188.165.122.98:8080',
		];
	}


	public function auth()
	{
		$http_origin = $_SERVER['HTTP_ORIGIN'];

		if(in_array($http_origin, $this->allowHttpOrigin)){
			header("Access-Control-Allow-Origin: $http_origin");
			header("Access-Control-Allow-Credentials: true");

			(new \W\Security\AuthentificationModel)->refreshUser();

			if($user = $this->isConnect()){
				$this->json([
					'uid' 			=> $user['id'],
					'pseudo'		=> $user['username'],
					'privilege'		=> $user['role'],
					'avatar'		=> '',
					'couleurPseudo' => $user['ws_color_user'],
					'couleurMsg' 	=> $user['ws_color_msg'],
					'authorized' 	=> 1,
					'token'			=> '',
					'result'		=> 'success',
				]);
			}
		}

		/*$this->json([
			'uid' 			=> 111 ?? $user['id'],
			'pseudo'		=> 'test_111' ?? $user['username'],
			'privilege'		=> 99 ?? $user['role'],
			'avatar'		=> '',
			'couleurPseudo' => $user['ws_color_user'],
			'couleurMsg' 	=> $user['ws_color_msg'],
			'authorized' 	=> 1,
			'token'			=> '',
			'result'		=> 'success',
		]);*/

		$this->json([
			'result' => 'error',
		]);
	}


	public function update()
	{
		$http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

		if(in_array($http_origin, $this->allowHttpOrigin)){
			header("Access-Control-Allow-Headers: Content-Type");
			header("Access-Control-Allow-Origin: $http_origin");
			header("Access-Control-Allow-Credentials: true");

			(new \W\Security\AuthentificationModel)->refreshUser();

			$php_input = file_get_contents('php://input');
			if(!empty($php_input)){
				$post = array_map('trim', array_map('strip_tags', json_decode($php_input, true)));

				if(!empty($post['uid']) && is_numeric($post['uid'])){
					if(isset($post['colorMsg'])){
						$rows = [
							'ws_color_msg' => $post['colorMsg'],
						];
					}
					elseif(isset($post['colorName'])){
						$rows = [
							'ws_color_user' => $post['colorName'],
						];
					}

					(new UsersModel)->update($rows, $post['uid']);
					echo 'ok';
				}
			}
		}
	}

}