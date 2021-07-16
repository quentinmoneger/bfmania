<?php

namespace Controller\Back;

use \Model\NewslettersModel;
use \Model\NewsletterUsersModel;
use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;

class NewslettersController extends MasterBackController
{

	const PATH_VIEWS = 'back/newsletters';

	protected $subscribersDb; // contient le model 

	protected $newslettersDb; // contient le model 

	public function __construct()
	{
		parent::__construct();
		$this->newslettersDb = new NewslettersModel;
		$this->subscribersDb = new NewsletterUsersModel;

		// Limitation d'accès
		if(getApp()->getConfig('plugin_newsletter') == false){
			$this->showForbidden();
		}

		$this->allowTo(['70', '99']);
	}

	/** 
	 * Liste des newsletters
	 */
	public function listAll($page = 1)
	{
		$this->render(self::PATH_VIEWS.'/list', [
			'newsletters' => $this->newslettersDb->findAll('id', 'DESC') ?? [],
		]);
	}


	/**
	 * Ajout ou modification d'une newsletter
	 * @param $id L'id de la newsletter
	 */
	public function addEdit($id = null)
	{

		$form = (empty($id)) ? 'add' : 'edit';

		$is_draft = $is_test = false;

		// @todo create cron for send newsletter

		if($form === 'edit'){
			$news = $this->newslettersDb->find($id);
			if(!empty($news['date_start'])){
				$this->showForbidden();
			}

			$post = [
				'subject' 	 => $news['subject'],
				'content' 	 => $news['content'],
				'recipients' => $news['recipients'] ?? '',
			];
		}


		if(!empty($_POST)){
			$post = array_map('trim', $_POST);

			if(isset($post['date_send']) && isset($post['hour_send']) && isset($post['min_send'])){
				$min_send = ($post['min_send'] == '01') ? '00' : $post['min_send'];
				$datetime = $post['date_send'].' '.$post['hour_send'].':'.$min_send.':00'; // SQL
			}
			else {
				$datetime = '0000-00-00 00:00:00';
			}

			if($post['submit'] === 'save'){
				$errors = [
					(!v::notEmpty()->length(10, 130)->validate($post['subject'])) ? 'La longueur de l\'objet doit contenir entre 10 et 130 caractères' : null,
					(!v::notEmpty()->length(10, null)->validate($post['content'])) ? 'Le contenu de la newsletter doit contenir au moins 10 caractères' : null,
				];

				$errors = array_filter($errors);

				$is_draft = true;

				$flash_msg = 'La newsletter a bien été sauvegardée';
			}
			elseif($post['submit'] === 'test'){
				$emails = explode(PHP_EOL, $post['emails']);
				$emails = array_filter(array_map('trim', $emails));
				$emails_to = implode('|', $emails);

				foreach($emails as $to){
					$errors_to[] = (!v::notEmpty()->email()->validate($to)) ? "Adresse email incorrecte : <em>$to</em>" : null;
				}

				$errors = [
					(!v::notEmpty()->length(10, 130)->validate($post['subject'])) ? 'La longueur de l\'objet doit contenir entre 10 et 130 caractères' : null,
					(!v::notEmpty()->length(10, null)->validate($post['content'])) ? 'Le contenu de la newsletter doit contenir au moins 10 caractères' : null,
				];

				$errors = array_filter(array_merge($errors, $errors_to));

				$is_test = true;
				$datetime = date('Y-m-d H:i:s');

			}
			elseif($post['submit'] === 'send') {
				$errors = [
					(!v::notEmpty()->date('Y-m-d H:i:s')->validate($datetime) || $datetime == '0000-00-00 00:00:00') ? 'La date et l\'heure d\'envoi sont invalides' : null,
				];

				$errors = array_filter($errors);

				$flash_msg = 'La newsletter a bien été enregistrée et sera envoyée à la date et l\'heure programmée';
			}


			if(count($errors) === 0){

				$rows = [
					'subject'		=> strip_tags($post['subject']),
					'content'		=> $post['content'],
					'emails_to'		=> $emails_to ?? null,
					'is_draft'		=> ($is_draft) ? 1 : 0,
					'date_create'	=> date('Y-m-d H:i:s'),
					'date_send'		=> $datetime,
				];


				// send test email
				if(!empty($emails) && $is_test){

					$test_subject = strip_tags($post['subject']);
					$test_message = $post['content'];

					$this->sendEmailTest($test_subject, $test_message, $emails);
					$flash_msg = 'La newsletter de test a bien été envoyée';
				}


				$cronJobDb = (new \Model\CronJobModel);
				if($form === 'edit'){
					$db = $this->newslettersDb->update($rows, $id, false);
					$task_id = $cronJobDb->findByTypeAndTargetId('send_newsletter', $id);
					if($task_id && !$is_draft && !$is_test){
						$cron = (new \Controller\CronJobController)->updateTask($task_id, 'send_newsletter', $db['id'], $datetime);
					}
					elseif(!$is_draft && !$is_test){
						$cron = (new \Controller\CronJobController)->createTask('send_newsletter', $db['id'], $datetime);
					}
				}
				else {
					$db = $this->newslettersDb->insert($rows, false);
					if(!$is_draft && !$is_test){
						$cron = (new \Controller\CronJobController)->createTask('send_newsletter', $db['id'], $datetime);
					}
				}

				if($db){
					$this->flash($flash_msg, 'success');
					$this->redirectToRoute('back_newsletters_list');
				}
			}
		}

		$this->render(self::PATH_VIEWS.'/add-edit', [
			'form'		=> $form,
			'errors'	=> $errors ?? [],
			'post'		=> $post ?? [],
		]);
	}
	/**
	 * Visualisation d'une newsletter
	 * @param $id L'id de la newsletter
	 */
	public function view($id)
	{
		$news = $this->newslettersDb->find($id);

		$this->render(self::PATH_VIEWS.'/view', [
			'news' => $news,
		]);
	}

	/**
	 * Liste des inscrits
	 */
	public function subscribers()
	{
		$this->render(self::PATH_VIEWS.'/subscribers', [
			'subscribers' => $this->subscribersDb->findAll('id', 'DESC'),
		]);
	}

	/**
	 * Envoi mail de test
	 */
	private function sendEmailTest($subject, $messageHTML, $address_to)
	{
		$this->sendEmail($subject, $messageHTML, $address_to, null, 9999);
	}
}