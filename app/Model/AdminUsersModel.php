<?php

namespace Model;

use \W\Security\AuthentificationModel;

class AdminUsersModel extends MasterModel 
{

	/**
	 * La clé de session
	 */
	protected $session_name = 'admin_user';


	public function __construct()
	{
		parent::__construct();
		$this->setTable('admin_users');
	}

	/**
	 * Vérifie qu'une combinaison d'email/username et mot de passe (en clair) sont présents en bdd et valides
	 * @param string $usernameOrEmail Le pseudo ou l'email à test
	 * @param string $plainPassword Le mot de passe en clair à tester
	 * @return int  0 si invalide, l'identifiant de l'utilisateur si valide
	 */
	public function isValidLoginInfo($usernameOrEmail, $plainPassword)
	{

		$app = getApp();
		$usernameOrEmail = strip_tags(trim($usernameOrEmail));
		$foundUser = $this->getUserByUsernameOrEmail($usernameOrEmail);
		if(!$foundUser){
			return 0;
		}

		if(password_verify($plainPassword, $foundUser[$app->getConfig('security_password_property')])){
			return (int) $foundUser[$app->getConfig('security_id_property')];
		}

		return 0;
	}

	/**
	 * Connecte un utilisateur
	 * @param  array $user Le tableau contenant les données utilisateur
	 */
	public function logUserIn($user)
	{
		// Retire le mot de passe de la session
		unset($user[getApp()->getConfig('security_password_property')]);

		$_SESSION[$this->session_name] = $user;
	}

	/**
	 * Déconnecte un utilisateur
	 */
	public function logUserOut()
	{
		unset($_SESSION[$this->session_name]);
	}

	/**
	 * Retourne les données présente en session sur l'utilisateur connecté
	 * @return mixed Le tableau des données utilisateur, null si non présent
	 */
	public function getLoggedUser()
	{
		return $_SESSION[$this->session_name] ?? null;
	}

	

	/**
	 * Utilise les données utilisateurs présentes en base pour mettre à jour les données en session
	 * @return boolean
	 */
	public function refreshUser()
	{
		$app = getApp();
		$userFromSession = $this->getLoggedUser();
		if($userFromSession){
			$userFromDb = $this->find($userFromSession[$app->getConfig('security_id_property')]);
			if($userFromDb){
				$this->logUserIn($userFromDb);
				return true;
			}
		}

		return false;
	}

	/**
	 * Créer un hash simple d'un mot de passe en utilisant l'algorithme par défaut
	 * @param  string $plainPassword Le mot de passe en clair à hasher
	 * @return string Le mot de passé hashé ou false si une erreur survient
	 */
	public function hashPassword($plainPassword)
	{
		return password_hash($plainPassword, PASSWORD_DEFAULT);
	}

	/**
	 * Récupère un utilisateur en fonction de son email ou de son pseudo
	 * @param string $email L'email d'un utilisateur
	 * @return mixed L'utilisateur, ou false si non trouvé
	 */
	public function getUserByUsernameOrEmail($email)
	{

		$app = getApp();

		$sql = 'SELECT * FROM ' . $this->table .' WHERE ' . getApp()->getConfig('security_email_property') . ' = :email LIMIT 1';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':email', $email);
		
		if($sth->execute()){
			$foundUser = $sth->fetch();
			if($foundUser){
				return $foundUser;
			}
		}

		return false;
	}

	/**
	* Teste si un email est présent en base de données
	* @param string $email L'email à tester
	* @return boolean true si présent en base de données, false sinon
	*/
	public function emailExists($email)
	{
		$app = getApp();

		$sql = 'SELECT ' . $app->getConfig('security_email_property') . ' FROM ' . $this->table .
			   ' WHERE ' . $app->getConfig('security_email_property') . ' = :email LIMIT 1';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':email', $email);
		if($sth->execute()){
			$foundUser = $sth->fetch();
			if($foundUser){
				return true;
			}
		}

		return false;
	}

	/**
	 * Vérifie les droits d'accès au back office
	 * @param  string  	$role Le rôle pour lequel on souhaite vérifier les droits d'accès
	 * @return boolean 	true si droit d'accès, false sinon
	 */
	public function isGrantedBack($role)
	{
		$app = getApp();
		$roleProperty = $app->getConfig('security_role_property');

		//récupère les données en session sur l'utilisateur
		$this->refreshUser();
		$loggedUser = $this->getLoggedUser();

		// Si utilisateur non connecté
		if (!$loggedUser){
			// Redirige vers le login
			$controller = new \Controller\Back\MasterBackController();
			$controller->redirectToRoute('back_login');
		}

		if (!empty($loggedUser[$roleProperty]) && $loggedUser[$roleProperty] == $role){
			return true;
		}

		return false;
	}



}