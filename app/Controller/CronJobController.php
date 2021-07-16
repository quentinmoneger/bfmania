<?php

namespace Controller;

use \Model\CronJobModel;

class CronJobController extends MasterController
{

	private static $cronJobDb; // contient le model 

	private static $http; // Protocole pour builder les liens


	public function __construct()
	{
		self::$cronJobDb = new CronJobModel;

		self::$http = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
	}

	/**
	 * Créer une tache cron job
	 * @param $type Le type de cron
	 * @param $id_target L'id cible. Un id produit ou un id newsletter par exemple
	 * @param $date_create La date de création, a mettre dans le futur pour un cron job ultérieur
	 */
	public static function createTask($type, $id_target = 0, $date_create = null)
	{
		if (!empty($type)) {

			$type = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $type));;

			$rows = [
				'type'		  		=> $type,
				'id_target'  		=> $id_target,
				'date_create' 		=> $date_create ?? date('Y-m-d H:i:s'),
				'date_execute_start' => null,
				'date_execute_end' 	=> null,
			];

			if (!self::$cronJobDb->insert($rows)) {
				die('ERREUR insertion cron');
			}
		}
	}

	/**
	 * Met à jour une tache cron job
	 * @param $id_task L'id de la tâche
	 * @param $type Le type de cron
	 * @param $id_target L'id cible. Un id produit ou un id newsletter par exemple
	 * @param $date_create La date de création
	 */
	public static function updateTask($id_task, $type, $id_target = 0, $date_create = null)
	{

		if (!empty($type)) {

			$rows = [
				'type'		  	=> $type,
				'id_target'  	=> $id_target,
				'date_create' 	=> $date_create ?? date('Y-m-d H:i:s'),
				'date_execute_start' => null,
				'date_execute_end' 	=> null,
			];

			if (!self::$cronJobDb->insert($rows)) {
				die('ERREUR insertion cron');
			}
		}
	}

	/**
	 * Met à jour la date de début
	 */
	private static function startDateTask($id_task)
	{
		self::$cronJobDb->update([
			'date_execute_start' => date('Y-m-d H:i:s'),
		], $id_task);
	}

	/**
	 * Met à jour la date de fin
	 */
	private static function closeDateTask($id_task)
	{
		self::$cronJobDb->update([
			'date_execute_end' => date('Y-m-d H:i:s'),
		], $id_task);
	}


	/**
	 * Execute la première tâche en attente
	 */
	public static function executeTask()
	{
		@set_time_limit(0);
		$task = self::$cronJobDb->findFirstPending();
		if (!empty($task)) {
			$method_name = lcfirst(str_replace('_', '', ucwords($task['type'], '_')));

			if (method_exists(__CLASS__, $method_name)) {
				call_user_func_array(__CLASS__ . '::' . $method_name, ['id_task' => $task['id']]);
				return;
			} else {
				die('[' . date('Y-m-d H:i:s') . '] Method not exist : ' . $method_name . ' - Called task : ' . $task['type']);
			}
		}
		return false;
	}

	/**
	 * Envoi une newsletter
	 */
	private static function sendNewsletter($id_task)
	{
		self::startDateTask($id_task);

		$task = self::$cronJobDb->find($id_task);
		if (!empty($task['id_target'])) {
			(new \Controller\Front\NewsletterController)->sendNewsletter($task['id_target']);
		}

		self::closeDateTask($id_task);
	}
}
