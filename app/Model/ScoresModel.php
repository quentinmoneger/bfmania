<?php

namespace Model;

class ScoresModel extends MasterModel
{
	/**
	 * Récupère les scores par utilisateur
	 * @param  int $id L'id du player
	 * @param mixed $monthdate Mois demandé 
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function listScoresByUser($id, $monthdate)
	{
		$usersTable = (new \Model\UsersModel)->getTable();

		$nextMonth = mktime(0, 0, 0, date('m', $monthdate)+1, 1, date("Y"));
		$monthdate = date('Y-m-d H:i:s', $monthdate);
		$nextMonth = date('Y-m-d H:i:s', $nextMonth);
		$sql = 'SELECT s.*, u1.username AS username_1, u2.username AS username_2, u3.username AS username_3, u4.username AS username_4, u5.username AS username_5 
		FROM '.$this->table.' AS s 
		LEFT JOIN '.$usersTable.' AS u1 ON s.player_1=u1.id 
		LEFT JOIN '.$usersTable.' AS u2 ON s.player_2=u2.id 
		LEFT JOIN '.$usersTable.' AS u3 ON s.player_3=u3.id  
		LEFT JOIN '.$usersTable.' AS u4 ON s.player_4=u4.id  
		LEFT JOIN '.$usersTable.' AS u5 ON s.player_5=u5.id 
		WHERE (s.player_1 = :id OR s.player_2 = :id OR s.player_3 = :id OR s.player_4 = :id OR s.player_5 = :id) AND (date_create BETWEEN :monthdate AND :nextMonth)';

		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		$sth->bindValue(':monthdate', $monthdate);
		$sth->bindValue(':nextMonth', $nextMonth);
		$sth->execute(); 
		return $sth->fetchAll();
	}

}
  