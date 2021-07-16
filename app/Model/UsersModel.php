<?php

namespace Model;

class UsersModel extends \W\Model\UsersModel 
{

	public function uuidExist($uuid)
	{
		$founded = $this->findBy('uuid', $uuid);
		if(!empty($founded)){
			return true;
		}
		return false;
	}

	/**
	 * Fonction de pagination
	 * @param int $page  Le numéro de page / default 1
	 * @param int $limit la limite d'occurrence par page / default 10
	 * @param string $orderBY colonne de tri
	 * @param string $orderDir ACS ou DESC direction du tri / default ASC
	 * @param string $where colonne de recherche
	 * @param mixed $LikeValue value recherché
	 */
	public function paginate($page = 1, $limit = 10 , $orderBy = '', $orderDir = 'ASC', $where= '',  $likeValue = '')
	{
		// On définit l'offset en fonction de la page actuelle
		$limit = intval($limit);
		//debug($where);

		$offset = $limit * ($page - 1);
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->table ;

		if($where !== '100'){		
			$sql .= ' WHERE ( role = '.$where.') ';
			if(!empty($likeValue)){
				if(!preg_match('#^[a-zA-Z0-9_.$]+$#', $likeValue)){
					die('Error: invalid like param');
				}	
				$sql .= ' AND (username LIKE :likeValue OR email LIKE :likeValue OR ip_address LIKE :ipValue) ';
			}
		} else {

			if(!empty($likeValue)){
				if(!preg_match('#^[a-zA-Z0-9_.$]+$#', $likeValue)){
					die('Error: invalid like param');
				}	
				$sql .= ' WHERE (username LIKE :likeValue OR email LIKE :likeValue OR ip_address LIKE :ipValue) ';
			}
		}

		if(!empty($orderBy)){

			//sécurisation des paramètres, pour éviter les injections SQL
			if(!preg_match('#^[a-zA-Z0-9_.$]+$#', $orderBy)){
				die('Error: invalid orderBy param');
			}
			$orderDir = strtoupper($orderDir);
			if($orderDir != 'ASC' && $orderDir != 'DESC'){
				die('Error: invalid orderDir param');
			}
			
			$sql .= ' ORDER BY '.$orderBy.' '.$orderDir;
		}

		$sql .= ' LIMIT :offset, :limit';

		$sth = $this->dbh->prepare($sql);
		if(!empty($likeValue)){
			$sth->bindValue(':likeValue', '%'.$likeValue.'%');
			$sth->bindValue(':ipValue', $likeValue);
		}
		
		$sth->bindValue(':offset', $offset, \PDO::PARAM_INT);
		$sth->bindValue(':limit', $limit, \PDO::PARAM_INT);
		$sth->execute();

		$res = $sth->fetchAll();

		// On compte le nombre de résultat
		$this->countResult();

		// Le nombre de page
		$this->totalPages = ceil($this->totalCount / $limit);

		// On retourne le contenu
		return $res;

	}

	/**
	 * Compte le nombre de résultats pour la pagination
	 */
	protected function countResult()
	{
		$sql = 'SELECT FOUND_ROWS() as nb_results';
		$sth = $this->dbh->prepare($sql);
		$sth->execute();
		$res = $sth->fetch();
		$this->totalCount = $res['nb_results']; 
	}

	/**
	 * Recupère les multiprofils
	 */
	public function getMultinick()
	{
		$sql = 'SELECT COUNT(ip_address) AS multinick, ip_address FROM '. $this->table .' 
		GROUP BY ip_address HAVING COUNT(ip_address) > 1';

		$sth = $this->dbh->prepare($sql);
		if($sth->execute()){
			$foundMultinick = $sth->fetchAll();
			if($foundMultinick){
				return $foundMultinick;
			}
			else {
				return false;
			}
		}

		return false;
	}
	
	/**
	 * Suppression d'un utilisateur
	 * @param int $id id de l'utilisateur
	 */
	public function deleteUser($id)
	{
		
		if (!is_numeric($id)){
			return false;
		}

		$sql = 'DELETE u.*, b.*, w.*, n.* FROM users AS u LEFT JOIN banish AS b ON b.id_user= u.id LEFT JOIN warnings AS w ON w.id_user= u.id LEFT JOIN notes AS n ON n.id_user= u.id WHERE u.id = :id';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id' , $id);
		$sth->execute();
		
		if ($sth){
			return true;
		}
		else{
			return false;
		}
	}  


	/**
	 * Effectue une recherche
	 * @param array $data Un tableau associatif des valeurs à rechercher
	 * @param string $operator La direction du tri, AND ou OR
	 * @param boolean $stripTags Active le strip_tags automatique sur toutes les valeurs
	 * @return mixed false si erreur, le résultat de la recherche sinon
	 */
	public function searchIdUsername(array $search, $operator = 'OR', $stripTags = true){

		// Sécurisation de l'opérateur
		$operator = strtoupper($operator);
		if($operator != 'OR' && $operator != 'AND'){
			die('Error: invalid operator param');
		}

        $sql = 'SELECT id, username FROM ' . $this->table.' WHERE';
                
		foreach($search as $key => $value){
			$sql .= " `$key` LIKE :$key ";
			$sql .= $operator;
		}
		// Supprime les caractères superflus en fin de requète
		if($operator == 'OR') {
			$sql = substr($sql, 0, -3);
		}
		elseif($operator == 'AND') {
			$sql = substr($sql, 0, -4);
		}

		$sth = $this->dbh->prepare($sql);

		foreach($search as $key => $value){
			$value = ($stripTags) ? strip_tags($value) : $value;
			$sth->bindValue(':'.$key, '%'.$value.'%');
		}
		if(!$sth->execute()){
			return false;
		}
        return $sth->fetchAll();
	}
}