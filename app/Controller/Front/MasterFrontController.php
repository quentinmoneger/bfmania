<?php

namespace Controller\Front;	


class MasterFrontController extends \Controller\MasterController
{	

	public function __construct()
	{
		parent::__construct();

		$optionsDb = new \Model\OptionsModel;
		$messagesDb = new \Model\MessagesModel;
		
		$entreprise = [
			'name'		=> $optionsDb->findBy('name', 'company_name')['value'],
			'street'	=> $optionsDb->findBy('name', 'company_street')['value'],
			'zipcode'	=> $optionsDb->findBy('name', 'company_zipcode')['value'],
			'city'		=> $optionsDb->findBy('name', 'company_city')['value'],
			'phone'		=> $optionsDb->findBy('name', 'company_phone')['value'],
			'email'		=> $optionsDb->findBy('name', 'company_email')['value'],
		];
		
		if($this->isConnect()){
			$countUnreadMessage = $messagesDb->countUnreadMessage($this->getUser()['id']);
			if ($countUnreadMessage >= 100) {
				$countUnreadMessage = '99+';
			}
		}
		// On ajoute des données à la vue
		$this->addDataViews = array_merge($this->addDataViews, [
			'entreprise' => $entreprise,
			'countUnreadMessage' 	=> $countUnreadMessage ?? 0,
		]);
	}

	/**
	 * Redirige un utilisateur utilisateur non connecté
	 */
	protected function redirectNotConnect()
	{
		if($this->isConnect() == false){
			$this->flash('Vous devez être connecté pour accéder à cette page', 'error');
			$this->redirectToRoute('default_home');
		}
	}

	/**
	 * Redirige un utilisateur utilisateur connecté
	 */
	protected function redirectIsConnect()
	{
		if($this->isConnect() != false){
			$this->flash('Vous êtes déjà connecté', 'info');
			$this->redirectToRoute('users_account');
		}
	}

	/**
	 * Vérifie qu'un utilisateur est connecté
	 * @return array Les infos de l'utilisateur en session, false sinon
	 */
	protected function isConnect()
	{
		if(!empty($this->getUser())){
			return $this->getUser();
		}

		return false;
	}

}