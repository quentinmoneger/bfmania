<?php

namespace Controller\Front;

use \Model\UsersModel;

use \Respect\Validation\Validator as v;

class ChatController extends MasterFrontController
{
	const PATH_VIEWS = 'front';
	protected $userDb;

	public function __construct()
	{
		parent::__construct();
		$this->userDb = new UsersModel();
	}

	/**
	 * Page d'accueil par défaut 
	 * Utilisation de node js pour le chat
	 * @return render sur accueil forum
	 */
	public function home()
	{
		$this->render(self::PATH_VIEWS . '/chat/index.html' );
	}

}