<?php

namespace Controller\Front;

use \Model\PagesModel;
use \Model\OptionsModel;
use \Model\FaqModel;
use \Model\UsersModel;

use \Respect\Validation\Validator as v;

class DefaultController extends MasterFrontController
{


	const PATH_VIEWS = 'front/default';
	protected $faqDb;
	protected $userDb;

	public function __construct()
	{
		parent::__construct();
		$this->faqDb = new faqModel();
		$this->userDb = new UsersModel();
	}

	/**
	 * Page d'accueil par défaut du site web
	 */
	public function home()
	{

		$this->render(self::PATH_VIEWS . '/home', []);
	}

	
    /**
	 * Page d'accueil par défaut du site web pour affichage unsubscribe
	 */
	public function homeUnsubscribe($unsubscribe)
	{       
		
		if(isset($unsubscribe)){

            $safeUnsubscribe = trim(strip_tags($unsubscribe));

			if( $safeUnsubscribe == 'error' ){
				$unsubscribe_alert = 'error';
			}else{
				$unsubscribe_alert = 'valid';
			}
		} 

		$this->render(self::PATH_VIEWS . '/home', [
			'unsubscribe_alert' => $unsubscribe_alert
		]);
	}

	/**
	 * Affichage page administrable
	 * @param string Recupération de la vue par url
	 * @return return On affiche la vue
	 */
	public function page($url)
	{
		$pagesDb = new PagesModel;
		$page = $pagesDb->findByUrl($url);
		if (empty($page) || $page['status'] == 0) {
			$this->showNotFound();
		}

		$this->render(self::PATH_VIEWS . '/page', [
			'page'		=> $page,
			'template'	=> $pagesDb->templates_availables[$page['template']],
		]);
	}

	/**
	 * Fonction pour les jeux
	 * A CONSTRUIRE 
	 */
	public function playGame($action)
	{
		$this->redirectNotConnect();

		$this->render(self::PATH_VIEWS.'/play', [
			'action' => $action
		]);
	}


	/**
	 * FAQ - Liste des catégories
	 */
	public function faqHome()
	{
		$categoriesList = $this->faqDb->getCategories();

		$this->render(self::PATH_VIEWS . '/support/faq/home', [
			'categoriesList' => $categoriesList,
		]);
	}

	/** 
	 * FAQ - Liste des questions d'une catégorie
	 */
	public function faqCategory($category)
	{
		$categoriesList = $this->faqDb->getCategories();


		foreach ($categoriesList as $catKey => $catValue) {
			if ($catValue['url'] == $category) {
				$id_category_request = $catKey;
			}
		}


		$data = $this->faqDb->findAllBy('id_category', $id_category_request, 'date_create', 'DESC');


		$this->render(self::PATH_VIEWS . '/support/faq/category', [
			'faqData' => $data,
			'categoriesList' => $categoriesList,
			'id_category' => $id_category_request,

		]);
	}

	/**
	 * Affichage contact
	 */
	public function contact()
	{

		if (!empty($_POST)) {
			$post = array_map('trim', array_map('strip_tags', $_POST));

			$errors = [
				(!v::notEmpty()->length(5)->validate($post['fullname'])) ? 'Votre nom complet doit comporter au moins 5 caractères' : null,
				(!v::notEmpty()->email()->validate($post['email'])) ? 'Votre adresse email est invalide' : null,
				(!v::notEmpty()->phone()->validate($post['phone'])) ? 'Votre numéro de téléphone est invalide' : null,
				(!v::notEmpty()->length(15)->validate($post['message'])) ? 'Votre message doit comporter au moins 15 caractères' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) === 0) {

				$receiver = (new OptionsModel)->findBy('name', 'company_email');
				$receiver = $receiver ?? getApp()->getConfig('email_contact');


				$subject = '[Contact du site] Nouveau message en date du ' . date('d/m/Y H:i');

				$message = '<h3>Vous avez un nouveau message de contact du site</h3>';
				$message .= '<p>Le ' . date('d/m/Y H:i') . '</p>';
				$message .= '<p><strong>Nom : </strong>' . ucwords(mb_strtolower($post['fullname'])) . '</p>';
				$message .= '<p><strong>Adresse email : </strong> ' . $post['email'] . '</p>';
				$message .= '<p><strong>Téléphone : </strong>' . $post['phone'] . '</p>';
				$message .= '<p><strong>Message : </strong><br>' . nl2br($post['message']) . '</p>';
				$message .= '<br>';
				$message .= '<p>Vous pouvez directement répondre à cet email.</p>';


				$this->sendEmail($subject, $message, $receiver, $post['email']);

				$post = []; // On vide le post pour que le formulaire ne soit pas renvoyé.
				$this->flash('Votre message a été envoyé. Nous y répondrons dans les plus brefs délais', 'success');
				$this->redirectToRoute('default_contact');
			}
		}

		$this->render(self::PATH_VIEWS . '/contact');
	}

	/**
	 * Affichage de l'equipe bfmania
	 */
	public function staffList()
	{

		$this->render(self::PATH_VIEWS . '/support/staff/list',[
			'radios' => $this->userDb->findAllBy('role', 30),
			'mods' => $this->userDb->findAllBy('role', 50),
			'admins' => $this->userDb->findAllBy('role', 70),
			'webmasters' => $this->userDb->findAllBy('role', 99),
		]);
	}


}
