<?php

namespace Model;

class TokensModel extends MasterModel 
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable('tokens');

	}
	
	/**
	 * Récupère une ligne de la table tokens en fonction du login et du token en $_GET()
	 * @param  string $login Le login en $_GET('login')
	 * @param  string $token Le token en $_GET('token')
	 * @return mixed Les données sous forme de tableau associatif
	 */
	public function findLoginAndToken($login, $token)
	{
		if(empty($login) || empty($token)) {
			return false;
		}

		$sql = 'SELECT * FROM '.$this->table.' WHERE login = :login AND token = :token';
		$sth = $this->dbh->prepare($sql);
		$paramsRequest = [
			':login' => $_GET['login'],
			':token' => $_GET['token']
		];
		$sth->execute($paramsRequest);

		return $sth->fetch();
	}
}