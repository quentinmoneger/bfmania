<?php

namespace Model;

class ForumPostsModel extends MasterModel
{ 
	public function __construct()
	{
		parent::__construct();
		$this->setTable('forum_posts ');

	}

	public $pageLimit = 20;
	public $totalPages = 0;

	/**
	 * Récupère toutes les lignes de la table
	 * @param string $orderBy La colonne en fonction de laquelle trier 
	 * @param string $orderDir La direction du tri, ASC ou DESC, ASC par default
	 * @param int $limit Le nombre maximum de résultat à récupérer
	 * @param int $offset La position à partir de laquelle récupérer les résultats
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function findAllPostsById($id, $orderBy = '', $orderDir = 'ASC', $limit = null, $offset = null)
	{
		$this->usersTable = (new \Model\UsersModel)->getTable();

		$sql = 'SELECT p.*, u.username, u.avatar, u.role 
			FROM '.$this->table.' AS p 
			LEFT JOIN '.$this->usersTable.' AS u ON u.id = p.id_author 
			WHERE p.id_topic = :id	
		';	

		if (!is_numeric($id)){
			return false;
		}
		
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
		$sth->bindValue(':id' , $id);
		$sth->execute();
		return $sth->fetchAll();
	}

	/**
	 * Récupère toutes les lignes de la table
	 * @param  int $id L'id du topic 
	 * @param int $count Nombre de post dans le topic
	 * @param int $limit Le nombre maximum de résultat par page
	 * @param int $page Le nombre total de page
	 * @param string $orderBy La colonne en fonction de laquelle trier 
	 * @param string $orderDir La direction du tri, ASC ou DESC , ASC par default
	 * @param int $offset La position à partir de laquelle récupérer les résultats
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function findAllPostsPaginate($id, $count, $limit, $page, $orderBy = '', $orderDir = 'ASC',  $offset = null)
	{

		$this->usersTable = (new \Model\UsersModel)->getTable();
		$this->categoryTable = (new \Model\ForumCategoryModel)->getTable();
		$this->topicsTable = (new \Model\ForumTopicsModel)->getTable();

		if(empty($page)){
			$page == 1;
		}

		$sql = 'SELECT p.*, u.username, u.avatar, auth, u.role 
			FROM '.$this->table.' AS p 
			LEFT JOIN '.$this->usersTable.' AS u ON u.id = p.id_author
			LEFT JOIN '.$this->topicsTable.' As t ON t.id = p.id_topic
			LEFT JOIN '.$this->categoryTable.' AS f ON f.id = t.id_category 
			WHERE p.id_topic = :id	
		';	

		if (!is_numeric($id)){
			return false;
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
			$sql .= ' ORDER BY '.$orderBy.' '.$orderDir.' ';
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

		if($limit){
			// On compte le nombre de résultats
			$this->totalPages = ceil($count[0]['nbPosts'] / $this->pageLimit);
		}

		return $res;
	}

	/**
	 * Récupère la position du post dans le topic
	 * @param  int $id_topic L'id du topic du post
	 * @param int $id_post L'id du post concerné 
	 * @return int $res Retourne un nombre
	 */
	public function orderOfPostInTopic( $id_topic , $id_post)
    {
		$sql = 'SELECT count(*) FROM '.$this->table.' As p WHERE p.id_topic = :id_topic AND ( p.id < (SELECT p.id FROM '.$this->table.' AS p WHERE p.id = :id_post))';

		$sth = $this->dbh->prepare($sql);

		$sth->bindValue(":id_topic" , $id_topic, \PDO::PARAM_INT);
		$sth->bindValue(":id_post" , $id_post, \PDO::PARAM_INT);

		$sth->execute();
		$res = $sth->fetch();
		
		return $res;

    }
	
}
