<?php

namespace Model;

class ForumCategoryModel extends MasterModel
{ 

	public function __construct()
	{
		parent::__construct();
		$this->setTable('forum_category');

	}

	/**
	 * Récupère toutes les lignes de la table
	 * @param string $orderBy La colonne en fonction de laquelle trier
	 * @param string $orderDir La direction du tri, ASC ou DESC
	 * @param int $limit Le nombre maximum de résultat à récupérer
	 * @param int $offset La position à partir de laquelle récupérer les résultats
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function findAllCat($orderBy = '', $orderDir = 'ASC', $limit = null, $offset = null)
	{

		$this->usersTable = (new \Model\UsersModel)->getTable();
		$this->topicsTable = (new \Model\ForumTopicsModel)->getTable();

		$sql = 'SELECT f.*, t.datetime_last_post, t.title AS topic_title, t.id AS id_topic, u.username 
			FROM '.$this->table.' AS f 
			LEFT JOIN '.$this->topicsTable.' AS t ON t.datetime_last_post =(SELECT MAX(datetime_last_post) FROM '.$this->topicsTable.' WHERE id_category=f.id)  
			LEFT JOIN '.$this->usersTable.' AS u ON t.id_author = u.id 			
			';

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

			$sql .= ' ORDER BY '.$orderBy.' '.$orderDir;
		}
		if($limit){
			$sql .= ' LIMIT '.$limit;
			if($offset){
				$sql .= ' OFFSET '.$offset;
			}
		}
		$sth = $this->dbh->prepare($sql);
		$sth->execute();

		return $sth->fetchAll();
	}

	/**
	 * Récupère les dernier posts par date de création 
	 * @param int $limit Limite le nombre de retour par default 6
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function lastAnswerForum($limit = 6)
	{
		$this->usersTable = (new \Model\UsersModel)->getTable();
		$this->postsTable = (new \Model\ForumPostsModel)->getTable();
		$this->topicsTable = (new \Model\ForumTopicsModel)->getTable();

		$sql = 'SELECT t.title AS topic_title, t.id AS topic_id, f.auth, f.url AS forum_url, f.title AS forum_title, p.message, u.username, u.role 
		FROM '.$this->topicsTable.' AS t 
		LEFT JOIN '.$this->table.' AS f ON f.id = t.id_category
		LEFT JOIN '.$this->postsTable.' AS p ON p.date_create = t.datetime_last_post 
		LEFT JOIN '.$this->usersTable.' AS u ON u.id = p.id_author
		GROUP BY t.id
		ORDER BY p.date_create DESC LIMIT 6';


		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':limit', $limit, \PDO::PARAM_INT);
		$sth->execute();

		return $sth->fetchAll();
    }
}
