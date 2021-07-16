<?php

namespace Model;

class WarningsModel extends MasterModel
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable('warnings');

	}

	/**
	 * Model de déclaration d'un avertissement
	 */
	public function warning()
	{
		
		$usersTable = (new \Model\UsersModel)->getTable();

		$sql = 'SELECT b.*, u.ip_address FROM '.$this->table.' AS b JOIN '.$usersTable.' AS u ON b.id_user=u.id WHERE date_expire > NOW()';
		$sth = $this->dbh->prepare($sql);
		$sth->execute();
		return $sth->fetchAll();
	}

	/**
	 * Récupère la liste des avertissements
	 * @param string $orderBy La colonne en fonction de laquelle trier 
	 * @param string $orderDir La direction du tri, ASC ou DESC , ASC par default
	 * @param int $limit Le nombre maximum de résultat par page
	 * @param int $offset La position à partir de laquelle récupérer les résultats
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function listWarning($orderBy = '', $orderDir = 'ASC', $limit = null, $offset = null)
	{
		$usersTable = (new \Model\UsersModel)->getTable();

		$sql = 'SELECT b.*, u.username, a.username AS author 
		FROM '.$this->table.' AS b  JOIN '.$usersTable.' AS u ON b.id_user=u.id  
		JOIN '.$usersTable.' AS a ON b.id_author=a.id  WHERE date_expire > NOW()';

		if (!empty($orderBy)){

			//sécurisation des paramètres, pour éviter les injections SQL
			if(!preg_match('#^[a-zA-Z0-9_$]+$#', $orderBy)){
				die('Error: invalid orderBy param');
			}
			$orderDir = strtoupper($orderDir);
			if($orderDir != 'ASC' && $orderDir != 'DESC'){
				die('Error: invalid orderDir param');
			}
			if ($limit && !is_int($limit)){
				die('Error: invalid limit param');
			}
			if ($offset && !is_int($offset)){
				die('Error: invalid offset param');
			}

			$sql.= ' ORDER BY '.$orderBy.' '.$orderDir;
		}
		if($limit){
			$sql.= ' LIMIT '.$limit;
			if($offset){
				$sql.= ' OFFSET '.$offset;
			}
		}
		$sth = $this->dbh->prepare($sql);
		$sth->execute(); 
		return $sth->fetchAll();
	}

	/**
	 * Recherche un avertissement par colonne et valeur
	 * @param string $column nom de la colonne
	 * @param string $value valeur de la colonne
	 * @param string $orderBy La colonne en fonction de laquelle trier
	 * @param string $orderDir La direction du tri, ASC ou DESC
	 * @param int $limit Le nombre maximum de résultat à récupérer
	 * @param int $offset La position à partir de laquelle récupérer les résultats
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function listWarningBy($column = '', $value = '', $orderBy = '', $orderDir = 'ASC', $limit = null, $offset = null)
	{
		$usersTable = (new \Model\UsersModel)->getTable();

		$sql = 'SELECT b.*, u.username, a.username AS author 
		FROM ' . $this->table. ' AS b JOIN '.$usersTable.' AS u ON b.id_user=u.id  
		JOIN '.$usersTable.' AS a ON b.id_author=a.id  WHERE `' . $column . '` = :value';
		
		if (!empty($orderBy)){

			//sécurisation des paramètres, pour éviter les injections SQL
			if(!preg_match('#^[a-zA-Z0-9_$]+$#', $orderBy)){
				die('Error: invalid orderBy param');
			}
			$orderDir = strtoupper($orderDir);
			if($orderDir != 'ASC' && $orderDir != 'DESC'){
				die('Error: invalid orderDir param');
			}
			if ($limit && !is_int($limit)){
				die('Error: invalid limit param');
			}
			if ($offset && !is_int($offset)){
				die('Error: invalid offset param');
			}

			$sql.= ' ORDER BY '.$orderBy.' '.$orderDir;
		}
		if($limit){
			$sql.= ' LIMIT '.$limit;
			if($offset){
				$sql.= ' OFFSET '.$offset;
			}
		}
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':value', $value);
		$sth->execute();

		return $sth->fetchAll();
	}


}
