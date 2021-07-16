<?php 

$w_config = [
	//information de connexion à la bdd
	'db_charset'				=> 'utf8mb4', 				//type d'encodage, devrait être utf8 où utf8mb4
	'db_host'					=> 'localhost',				//hôte (ip, domaine) de la bdd
	'db_port'					=> 3306,					//port de connexion de la bdd
	'db_user'					=> 'root',					//nom d'utilisateur pour la bdd
	'db_pass'					=> '',						//mot de passe de la bdd
	'db_name'					=> 'bfmania',				//nom de la bdd
	'db_table_prefix' 			=> '',						//préfixe ajouté aux noms de table

	//authentification, autorisation
	'security_user_table' 		=> 'users',				//nom de la table contenant les infos des utilisateurs
	'security_id_property' 		=> 'id',				//nom de la colonne pour la clef primaire
	'security_username_property'=> 'username',			//nom de la colonne pour le "pseudo"
	'security_email_property'	=> 'email',				//nom de la colonne pour l'"email"
	'security_password_property'=> 'password',			//nom de la colonne pour le "mot de passe"
	'security_role_property' 	=> 'role',				//nom de la colonne pour le "role"
	'security_login_route_name' => 'users_login',		//nom de la route affichant le formulaire de connexion

	// configuration globale
	'site_name'					=> 'bfmania',			// contiendra le nom du site
	'upload_dir'				=> '/uploads/',			// le repertoire d'upload

	// emails 
	'email_enable'				=> true,				// Si on active les mails ou non
	'email_by_smtp'				=> true,				// Si on veut envoyer via SMTP
	'email_smtp_host'			=> 'smtp.mailtrap.io',					// Hôte SMTP
	'email_smtp_username'		=> 'c58cbe89e765f8',					// Identifiant SMTP
	'email_smtp_password'		=> '847dd28c61e53e', 					// Mot de passe SMTP
	'email_smtp_protocol'		=> '',					// Protocol SMTP : ssl, tls ou chaine vide
	'email_smtp_port'			=> 2525 ,				// Port de connexion SMTP
	'email_dkim'				=> false,				// Si on active la vérification DKIM

	'email_host'				=> '', 								// Le host pour construire les liens, les images etc.. sous forme de http:/example.org
	'email_logo'				=> '/assets/img/logo-email.png',	// Le logo affiché dans les emails
	'email_notif'				=> 'notifications@bfmania.com',		// Adresse email qui envoi les emails
	'email_contact'				=> 'contact@bfmania.com', 			// Adresse email qui reçoit les emails du form contact

	// activation plugin / module
	'plugin_admin_user'			=> true, 		// Gestion des utilisateurs en administration
	'plugin_pages'				=> true,		// Gestion des pages
	'plugin_newsletter'			=> false,		// Newsletter

];

require('routes.php');