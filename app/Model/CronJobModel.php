<?php

namespace Model;

class CronJobModel extends MasterModel 
{

	/**
	 * Récupère le premier cron en attente
	 */
	public function findFirstPending()
	{

		$sql = 'SELECT * FROM '.$this->table.' 
				WHERE date_execute_start IS NULL 
				AND date_execute_end IS NULL 
				AND date_create <= NOW() 
				ORDER BY date_create ASC LIMIT 1';
		$sth = $this->dbh->prepare($sql);

		$sth->execute();

		return $sth->fetch();
	}

	/**
	 * Recherche par id_target et par type puis retourne l'id de la tâche
	 * @param $id_target 
	 */
	public function findByTypeAndTargetId($type, $id_target)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE type = :type AND id_target = :id_target';

		$sth = $this->dbh->prepare($sql);

		$sth->bindValue(':type', $type);
		$sth->bindValue(':id_target', \PDO::PARAM_INT);

		$sth->execute();

		return $sth->fetch()['id'];
	}

}