<?php

namespace Controller\Back;

use \Model\AdminUsersModel;
use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;
use \Jenssegers\Agent\Agent;
use Ifsnop\Mysqldump as IMysqldump;

class DefaultController extends MasterBackController
{

	const PATH_VIEWS = 'back/default';

	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Page d'accueil console par défaut
	 */
	public function dashboard()
	{

		$this->allowTo(array_keys(\Tools\Utils::listRoles()));
		$this->render(self::PATH_VIEWS.'/dashboard', [
			'agent'	=> new Agent(),
		]);
	}

	/**
	 * Gestion des options
	 */
	public function options()
	{

		$this->allowTo('99');

		$optionsDb = new \Model\OptionsModel;
		$options = $optionsDb->findAll();

		// On load les infos en db
		foreach($options as $option) {
			$post[$option['name']] = $option['value'];
		}

		// Traitement du formulaire
		if(!empty($_POST)){
			$post = [];
			foreach($_POST as $key => $value){
				if(is_array($value)){
					$post[$key] = array_map('trim', array_map('strip_tags', $value));
				}
				else {
					$post[$key] = trim(strip_tags($value));
				}
			}

			$errors = [
				(!v::notEmpty()->validate($post['company_name'])) ? 'La dénomination commerciale ne peut être vide' : null,
				(!v::notEmpty()->validate($post['company_street'])) ? 'L\'adresse ne peut être vide' : null,
				(!v::notEmpty()->validate($post['company_zipcode'])) ? 'Le code postal ne peut être vide' : null,
				(!v::notEmpty()->validate($post['company_city'])) ? 'La ville ne peut être vide' : null,
				(!v::notEmpty()->validate($post['company_country'])) ? 'Le pays ne peut être vide' : null,
				(!empty($post['company_siret']) && !v::notEmpty()->validate($post['company_siret'])) ? 'Le numéro de siret ne peut être vide' : null,
				(!empty($post['company_capital']) && !v::notEmpty()->validate($post['company_capital'])) ? 'Le capital ne peut être vide' : null,
				(!v::notEmpty()->validate($post['company_phone'])) ? 'Le numéro de téléphone ne peut être vide' : null,
				(!v::notEmpty()->email()->validate($post['company_email'])) ? 'L\'adresse email de contact ne peut être vide' : null,
			];


			$errors = array_filter($errors);

			if(count($errors) === 0){

				foreach($post as $row_name => $row_value){
					$optionsDb->updateByName($row_value, $row_name);
				}

				$this->flash('Les options ont bien été mises à jour', 'success');
				$this->redirectToRoute('back_options');

			}
		}

		$this->render(self::PATH_VIEWS.'/options', [
			'post' 				=> $post ?? $options ?? [],
			'errors' 			=> $errors ?? [],
		]);
	}

	/**
	 * Dump SQL
	 */
	public function dump()
	{	
		$this->allowTo(99);
		try {
			$app = getApp();
			$dump = new IMysqldump\Mysqldump('mysql:host=' . $app->getConfig('db_host') . ';dbname=' . $app->getConfig('db_name'), $app->getConfig('db_user'), $app->getConfig('db_pass'));
			$dump->start($this->document_root . '/../sql/dump-' . date('Ymd-Hi') . '.sql');
		} catch (\Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
	}
}