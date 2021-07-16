<?php

namespace Model;


class ForumTopicsModel extends MasterModel
{ 

	public function __construct()
	{
		parent::__construct();
		$this->setTable('forum_topics ');

	}

	/**
	 * Récupère les topics paginés par catégorie
	 * @param  int $id L'id de la categoerie
	 * @param int $limit Le nombre maximum de résultat par page
	 * @param int $page Le nombre total de page
	 * @param string $orderBy La colonne en fonction de laquelle trier 
	 * @param string $orderDir La direction du tri, ASC ou DESC , ASC par default
	 * @param int $offset La position à partir de laquelle récupérer les résultats
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function findAllTopicsPaginate($id, $limit, $page, $orderBy = '', $orderDir = 'ASC', $offset = null)
	{
		$usersTable = (new \Model\UsersModel)->getTable();
		$postsTable = (new \Model\ForumPostsModel)->getTable();
		$categoryTable = (new \Model\ForumCategoryModel)->getTable();

		if (!is_numeric($id)){
			return false;
		}

		$sql = 'SELECT  SQL_CALC_FOUND_ROWS t.*, lp.date_create, ta.role AS roleTopicCreator, f.auth, ta.username AS firstPostUsername, pa.username AS lastPostUsername 
			FROM '.$this->table.' AS t 
			LEFT JOIN '.$categoryTable.' AS f ON f.id = t.id_category
			LEFT JOIN '.$postsTable.' AS lp ON lp.date_create = (SELECT MIN(date_create) FROM '.$postsTable.' WHERE id_topic = t.id)
			LEFT JOIN '.$postsTable.' AS p ON p.id_topic = t.id
			LEFT JOIN '.$usersTable.' AS ta ON ta.id = t.id_author 
			LEFT JOIN '.$usersTable.' AS pa ON pa.id = p.id_author 
			WHERE p.date_create = (SELECT MAX(date_create) FROM '.$postsTable.' AS po WHERE po.id_topic = t.id) 
			AND t.id_category = :id
			ORDER BY t.pin DESC, lp.date_create ASC
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

			//$sql.= ' ORDER BY t.pin AND p.'.$orderBy.' '.$orderDir;
		}

		if($limit){
			if (!is_numeric($limit)){
				die('Error: invalid limit param');
			}

			$this->pageLimit = $limit;
			$offset = $this->pageLimit * ($page - 1);
			
			$sql.= ' LIMIT :offset, :limit ';		
		}

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id' , $id);
		
		if($limit){
			$sth->bindValue(':limit' , $limit, \PDO::PARAM_INT);
			$sth->bindValue(':offset' , $offset, \PDO::PARAM_INT);
		}
		$sth->execute();
		$res = $sth->fetchAll();

		$this->countResult();
		if($limit){
			// On compte le nombre de résultats
			$this->totalPages = ceil($this->totalCount / $this->pageLimit);
		}

		return $res;
	}

	/** 
	* Recupère le nombre de topics par categorie
	*@param string $url L'url de la catégorie
	*@return int le nombre de topics
	*/
	public function CountPostByTopic($url)
	{
		$postsTable = (new \Model\ForumPostsModel)->getTable();
		$categoryTable = (new \Model\ForumCategoryModel)->getTable();

		$sql = 'SELECT ft.id, COUNT(fp.id) AS nbPosts FROM '.$this->table.' AS ft
		LEFT JOIN '.$postsTable.' AS fp ON ft.id = fp.id_topic
		LEFT JOIN '.$categoryTable.' AS fc ON ft.id_category = fc.id 
		WHERE fc.url = :fc_url 
		GROUP BY ft.id';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':fc_url', $url);
		$sth->execute();
		return $sth->fetchAll();
	}

	
	/** 
	* Recupère le nombre de posts par topic
	*@param int $id L'id du topic
	*@return int le nombre de posts du topic
	*/
	public function CountPostsForPaginate($id)
	{
		$postsTable = (new \Model\ForumPostsModel)->getTable();

		$sql = 'SELECT ft.title, ft.id, COUNT(fp.id) AS nbPosts
		 FROM '.$this->table.' AS ft
		 LEFT JOIN '.$postsTable.' AS fp ON ft.id = fp.id_topic WHERE ft.id = :topic_id';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':topic_id', $id);
		$sth->execute();
		
		return $sth->fetchAll();
	}


	/**
	 * Récupère une ligne de la table en fonction d'un identifiant
	 * @param  int Identifiant
	 * @return mixed Les données sous forme de tableau associatif
	 */
	public function findTopic($id)
	{
		$categoryTable = (new \Model\ForumCategoryModel)->getTable();

		if (!is_numeric($id)){
			return false;
		}

		$sql = 'SELECT ft.*, fc.url FROM '.$this->table.' AS ft 
		JOIN '.$categoryTable.' AS fc ON fc.id = ft.id_category WHERE ft.id  = :id LIMIT 1';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		$sth->execute();

		return $sth->fetch();
	}
	
	/**
	 * Suppression du topic
	 * @param  int Id du topic
	 */
	public function delTopic($id)
	{
		if (!is_numeric($id)){
			return false;
		}

		$sql = 'DELETE t.*, p.* FROM '.$this->table.' AS t LEFT JOIN forum_posts AS p ON p.id_topic = t.id WHERE t.id = :id';
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		return $sth->execute();
	}

	/** 
	* Recupère les dernières reponse des topics
	*@param string $url L'url du topic
	*@return array Les données sous forme de tableau multidimensionnel
	*/
	public function lastAnswerTopic($url)
	{
		$usersTable = (new \Model\UsersModel)->getTable();
		$postsTable = (new \Model\ForumPostsModel)->getTable();
		$categoryTable = (new \Model\ForumCategoryModel)->getTable();

		$sql = 'SELECT ft.id, ft.title, fc.auth, fp.message, datetime_last_post, u.username 
		FROM '.$this->table.' AS ft 
		LEFT JOIN '.$categoryTable.' AS fc ON fc.id = ft.id_category
		LEFT JOIN '.$postsTable.' AS fp ON fp.date_create = ft.datetime_last_post
		LEFT JOIN '.$usersTable.' AS u ON u.id = fp.id_author
		WHERE fc.url = :url_forum
		GROUP BY ft.id
		HAVING fp.message != ""
		ORDER BY ft.datetime_last_post DESC LIMIT 5';
		
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':url_forum', $url);
		$sth->execute();

		return $sth->fetchAll();
    }

	/** 
	* Upgrade colonne id_last_post (dans table topic) si le dernier post du topic concerné a été supprimé
	*@param string $id_topic
	*@return array Les données sous forme de tableau multidimensionnel
	*/
	public function findLastPost($id_topic)
    {
		$postsTable = (new \Model\ForumPostsModel)->getTable();
		
		$sql = 'SELECT fp.id, fp.date_create, ft.datetime_last_post, ft.id_last_post 
		FROM '.$postsTable.' AS fp
		LEFT JOIN '.$this->table.' as ft ON ft.id = :id_topic
		WHERE fp.id_topic = :id_topic
		ORDER BY fp.id DESC LIMIT 1';
		

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id_topic', $id_topic);
		$sth->execute();

		return $sth->fetchAll();
    }
}
