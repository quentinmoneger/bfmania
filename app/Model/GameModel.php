<?php

namespace Model;

class GameModel extends MasterModel
{ 
	public function __construct()
	{
		parent::__construct();
		$this->setTable('game');

	}

    /**
	 * Verifie si une ligne de score avec l'utilisateur et la Game existe déjà
     * @param int $id_game L'id du jeu 
     * @param int $id_user L'id du joueur 
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function findGame($id_game, $id_user)
    {

		$sql = 'SELECT * FROM '.$this->table.' As g WHERE g.id_user = :id_user AND g.id_game = :id_game';

		$sth = $this->dbh->prepare($sql);

		$sth->bindValue(":id_user" , $id_user, \PDO::PARAM_INT);
		$sth->bindValue(":id_game" , $id_game, \PDO::PARAM_INT);

		$sth->execute();
		$res = $sth->fetch();
		
		return $res;

    }

      /**
	 * Recupère le meilleur score d'un jeu
     * @param int $id_game L'id du jeu  
	 * @param int $limit Nombre de resultats
	 * @return array L'id du joueur et son score
	 */
	public function bestScoreRankWithUsername($id_game, $limit)
    {
        $usersTable = (new \Model\UsersModel)->getTable();

		$sql = 'SELECT g.id_user, u.username, g.bestresult, g.date_bestresult
        FROM '.$this->table.' AS g 
        JOIN '.$usersTable.' AS u ON g.id_user = u.id
        WHERE g.id_game = :id_game 
		ORDER BY g.bestresult DESC
		LIMIT '.$limit.' ';

		$sth = $this->dbh->prepare($sql);

		$sth->bindValue(":id_game" , $id_game, \PDO::PARAM_INT);

		$sth->execute();
		$res = $sth->fetchAll();
		
		return $res;

    }
}