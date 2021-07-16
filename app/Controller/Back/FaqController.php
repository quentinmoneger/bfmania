<?php

namespace Controller\Back;

use \Model\FaqModel;
use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;

class FaqController extends MasterBackController
{

	const PATH_VIEWS = 'back/faq';


	protected $faqDb;

	public function __construct()
	{
		parent::__construct();
		$this->faqDb = new FaqModel();
	}

	/**
	 * Liste des categories de la FAQ
	 * @param get Method Get pour le trie
	 * @return render Render sur page console / FAQ
	 */
	public function listAll()
	{ 
		$order = [ 
			'id_category' => '',
			'date_create' => 'DESC',
		];
		//$order_by = 'id';
		$dir = 'DESC';
		$faqs=null;
		
		if(!empty($_GET)){
			$get = array_map('trim', array_map('strip_tags', $_GET));
			if(isset($get['sort']) && in_array($get['sort'], ['category','date_create'])){

				(isset($get['id_category']) && is_numeric($get['id_category']))? $order['id_category'] = $get['id_category'] : $order['id_category'] = '';
				
				switch($get['sort']) {
					case 'category':
						$order['date_create'] = 'ASC';
						$faqs = $this->faqDb->findAllBy('id_category',$get['id_category'],'date_create', 'DESC');
					break;
					
					case 'date_create':
						if(isset($get['dir']) && in_array($get['dir'], ['ASC', 'DESC'])){
							($get['dir'] == 'ASC') ? $order['date_create'] = 'DESC' : $order['date_create'] = 'ASC';
							(!empty($get['id_category'])) ? $faqs = $this->faqDb->findAllBy('id_category',$get['id_category'],'date_create', $get['dir']) :$faqs=$this->faqDb->findAll('date_create', $get['dir']) ;
						}else {
							$this->showForbidden();
						}
						
					break;
				}

			}else {
				$this->showForbidden();
			}
		}

		$this->render(self::PATH_VIEWS . '/list', [
			'faqs' 	=> ($faqs)? $faqs : $this->faqDb->findAll('date_create', $dir),
			'order'	=> $order,
			'categories' => $this->faqDb->getCategories(),

		]);
	}

	/**
	 * Ajout d'une categorie pour la FAQ
	 * @param post Method Post pour l'ajout
	 * @return render Render sur page console / FAQ
	 */
	public function add()
	{
		$categories = $this->faqDb->getCategories();

		$errors = [];

		if (!empty($_POST)) {

			$post = array_map('trim', $_POST);

			$errors = [
				(!v::notEmpty()->length(8, null)->validate($post['question'])) ? 'Votre question doit comporter au moins 8 caractères' : null,
				(!v::notEmpty()->length(8, null)->validate(strip_tags($post['answer']))) ? 'Votre réponse doit comporter au moins 8 caractères' : null,
			];

			if (!isset($post['category'])) {
				$errors[] = 'Merci de sélectionner une catégorie';
			}

			$errors = array_filter($errors);

			if (count($errors) === 0) {
				$data = [
					'question'			=> strip_tags($post['question']),
					'answer'			=> $post['answer'],
					'id_category'		=> (int) $post['category'],
					'date_create' 		=> date('Y-m-d H:i:s'),
				];

				$new_question = $this->faqDb->insert($data, false);

				if ($new_question) {
					$this->flash('Votre question a bien été ajoutée à la liste des question/réponse de la Foire Aux Questions.', 'success');
					$this->redirectToRoute('back_faq_list');
				}
			}
		}

		$this->render(self::PATH_VIEWS . '/add', [
			'categories' => $categories,
			'errors' 	=> $errors,
			'post' 		=> $post ?? [],
		]);
	}

	/**
	 * Suppression d'une categorie pour la FAQ
	 * @param post Method Post pour la suppression
	 * @return render Render sur page console / FAQ
	 */
	public function delete($id)
	{
		$question = $this->faqDb->find($id);

		if (empty($question)) {
			$this->showNotFound();
		}

		// Soumission formulaire
		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if (isset($post['delete']) && $post['delete'] == 'yes') {
				$this->faqDb->delete($id);

				$this->flash('La question a été supprimée avec succès', 'success');
				$this->redirectToRoute('back_faq_list');
			}
		}

		$this->render(self::PATH_VIEWS . '/delete', [
			'question' => $question,
		]);
	}

	/**
	 * Edition d'une categorie pour la FAQ
	 * @param post Method Post pour l'edition
	 * @return render Render sur page console / FAQ
	 */
	public function edit($id)
	{
		$question = $this->faqDb->find($id);
		$categories = $this->faqDb->getCategories();


		if (empty($question)) {
			$this->showNotFound();
		}

		$errors = $post = [];

		if (!empty($_POST)) {

			$post = array_map('trim', $_POST);

			$errors = [
				(!v::notEmpty()->length(8, null)->validate($post['question'])) ? 'Votre question doit comporter au moins 8 caractères' : null,
				(!v::notEmpty()->length(8, null)->validate(strip_tags($post['answer']))) ? 'Votre réponse doit comporter au moins 8 caractères' : null,
			];


			if (!isset($post['category'])) {
				$errors[] = 'Merci de sélectionner une catégorie';
			}

			$errors = array_filter($errors);

			if (count($errors) === 0) {

				$data = [
					'question'			=> strip_tags($post['question']),
					'answer'			=> $post['answer'],
					'id_category'		=> (int) $post['category'],
				];

				$update_question = $this->faqDb->update($data, $question['id'], false);

				if ($update_question) {
					$this->flash('Votre question a bien été modifiée.', 'success');
					$this->redirectToRoute('back_faq_list');
				}
			}
		}

		$this->render(self::PATH_VIEWS . '/edit', [
			'question' 	=> $question,
			'categories' => $categories,
			'errors' 	=> $errors ?? [],
			'post' 		=> $post ?? [],
		]);
	}
}
