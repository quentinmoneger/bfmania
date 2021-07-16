<?php

namespace Controller\Back;

use \Model\PagesModel;
use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;

class PagesController extends MasterBackController
{

	const PATH_VIEWS = 'back/pages';


	protected $pagesDb;


	/**
	 * @var (bool) active ou non la méta description
	 */
	protected $enable_meta_description = true;


	/**
	 * @var (bool) active ou non la photo de couverture
	 */
	protected $enable_picture_cover = true;



	/**
	 * @var (array) les templates disponibles dans le model
	 */
	public $templates_availables;

	public function __construct()
	{
		parent::__construct();

		$this->pagesDb = new PagesModel;

		$this->templates_availables = $this->pagesDb->templates_availables;

		// Limitation d'accès
		if (getApp()->getConfig('plugin_pages') == false) {
			$this->showForbidden();
		}

		$this->allowTo(array_keys(\Tools\Utils::listRoles()));
	}


	/**
	 * Liste les pages administrables 
	 * Dans console / Administation
	 */
	public function listAll()
	{
		$this->render(self::PATH_VIEWS . '/list', [
			'pages' => $this->pagesDb->findAll('id', 'DESC'),
		]);
	}

	/**
	 * Choix du template de page
	 */
	public function template()
	{
		$this->render(self::PATH_VIEWS . '/template', [
			'templates_availables' => $this->templates_availables,
		]);
	}

	/**
	 * Ajoute ou modifie une page
	 * @param string $template l'id du template (voir haut de page)
	 * @param string $id L'id de la page en cas d'édition
	 */
	public function addOrEdit($template = null, $id = null)
	{
		$form = (!empty($id)) ? 'edit' : 'add';

		if (!array_key_exists($template, $this->templates_availables)) {
			$this->showNotFound();
		}
		if (empty($template)) {
			$this->redirectToRoute('back_pages_choose_tpl');
		}

		if ($form == 'edit') {

			$page = $this->pagesDb->find($id);

			$post = [
				'title' 			=> $page['title'],
				'content'			=> json_decode($page['content'], true),
				'status'			=> $page['status'],
				'meta_description'	=> $page['meta_description'],
				'picture_cover'		=> $page['picture_cover'],
			];
		}

		// Soumission formulaire
		if (!empty($_POST)) {

			foreach ($_POST as $key => $value) {
				if (is_array($value)) {
					$post[$key] = array_map('trim', $value);
				} else {
					$post[$key] = trim($value);
				}
			}

			$errors = [
				(!v::notEmpty()->length(6, 140)->validate($post['title'])) ? 'Le titre doit comporter entre 6 et 140 caractères' : null,
			];

			foreach ($post['content'] as $key => $content) {
				$errors[] = (!v::notEmpty()->length(15, null)->validate($content)) ? 'Le contenu du block #' . $key . ' doit comporter au moins 15 caractères' : null;
			}

			if ($this->enable_meta_description) {
				$errors[] = (!v::notEmpty()->length(15, 250)->validate($post['meta_description'])) ? 'La meta description doit comporter entre 15 et 250 caractères' : null;
			}
			// Photo de couverture
			if ($this->enable_picture_cover) {
				// Edition sans changement d'image
				if ($form === 'edit' && !is_array($post['picture_cover'])) {
					$upload_cover = [
						'status' => true,
						'msg'	 => $page['picture_cover'],
					];

					$filename_output_cover = $page['picture_cover'];
				} else {
					$filename_cover = $post['title'] . '-' . time();
					$upload_cover = $this->uploadImage($post['picture_cover'], 'blog', 1920, 200, $filename_cover, 3, null, null, 80, true);
					$filename_output_cover = ($upload_cover['status'] === true) ? $upload_cover['msg'] : null;
				}
			}

			$errors = array_filter($errors);

			if (count($errors) === 0) {
				$rows = [
					'title' 	 		=> strip_tags($post['title']),
					'content' 	 		=> json_encode($post['content'], JSON_UNESCAPED_UNICODE),
					'url'		 		=> $this->buildUrl($post['title'], 'PagesModel'),
					'status'	 		=> (isset($post['status']) && $post['status'] == 'on') ? 1 : 0,
					'meta_description'	=> $post['meta_description'] ?? '',
					'picture_cover'		=> $filename_output_cover ?? '',
					'date_create'		=> date('Y-m-d H:i:s'),
					'not_deletable'		=> 0,
					'template'			=> $template,
				];

				if ($form == 'edit') {
					// Suppression des variables qu'on ne souhaite pas modifier
					unset($rows['date_create'], $rows['url']);

					$update = $this->pagesDb->update($rows, $id, false);
					if ($update) {
						$this->flash('La page a été modifiée avec succès', 'success');
						$this->redirectToRoute('back_pages_list');
					}
				} elseif ($form == 'add') {

					$insert = $this->pagesDb->insert($rows, false);
					if ($insert) {
						$this->flash('La page a été modifiée avec succès', 'success');
						$this->redirectToRoute('back_pages_list');
					}
				}
			}
		}

		$this->render(self::PATH_VIEWS . '/add-edit', [
			'template'  => $this->templates_availables[$template],
			'form'		=> $form,
			'post'		=> $post ?? [],
			'errors'	=> $errors ?? [],
			'enable_picture_cover'	  => $this->enable_picture_cover,
			'enable_meta_description' => $this->enable_meta_description,
		]);
	}

	/**
	 * Suppression de la page
	 * @param string $id Identifiant de la page
	 */
	public function delete($id)
	{

		$page = $this->pagesDb->find($id);

		if (empty($page)) {
			$this->showNotFound();
		}

		// Soumission formulaire
		if (!empty($_POST)) {

			$post = array_map('trim', array_map('strip_tags', $_POST));

			if (isset($post['delete']) && $post['delete'] == 'yes') {
				$this->pagesDb->delete($id);

				$this->flash('La page a été supprimée avec succès', 'success');
				$this->redirectToRoute('back_pages_list');
			}
		}


		$this->render(self::PATH_VIEWS . '/delete', [
			'page' => $page,
		]);
	}
}
