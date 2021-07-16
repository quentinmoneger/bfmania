<?php

namespace Model;

class MessagesModel extends MasterModel 
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable('messages');

	}

	/**
	 * Récupère tous les messages de l'utilisateur
	 * @param int $id_ user L'id de l'utilisateur courant
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function listMessages($id_user)
	{;

		$usersTable = (new \Model\UsersModel)->getTable();

		$sql = 'SELECT  m.*, u.username AS username_from, u.role AS role_from, GROUP_CONCAT(uto.username) AS username_to,  u.avatar AS avatar_from
		FROM '.$this->table.' AS m 
		LEFT JOIN '.$usersTable.' AS u ON m.id_from = u.id
		JOIN '.$usersTable.' AS uto 
		WHERE  ( FIND_IN_SET(:id_user , id_from) OR FIND_IN_SET(:id_user , id_to) ) AND NOT FIND_IN_SET(:id_user , delete_message) 
		GROUP BY m.id_message_parent, m.id
		ORDER BY m.date DESC
		';
		$sth = $this->dbh->prepare($sql);
		$sth->execute([
			':id_user' => (int) $id_user
		]);

	
		return $sth->fetchAll();

	}

	/**
	 * Récupère les message parents
	 * @param int $idMessageParent L'id du message parent du message actuel
	 * @return array Les données sous forme de tableau multidimensionnel
	 */
	public function listMessagesByparent( $user ,$date, $id, $idMessageParent)
	{
		$usersTable = (new \Model\UsersModel)->getTable();
		
		$sql = 'SELECT  m.*, u.username AS username_from, uto.username AS username_to, u.avatar AS avatar_from, u.role AS role_from,  uto.avatar AS avatar_to, uto.role AS role_to
				FROM '.$this->table.' AS m 
				JOIN '.$usersTable.' AS u ON m.id_from=u.id
				JOIN '.$usersTable.' AS uto ON LOCATE(uto.id, m.id_to)
		  		WHERE  (m.id_message_parent = :id_message_parent OR m.id = :id_message_parent ) AND NOT m.id = :id AND m.date < :date AND (:user = u.username OR :user = uto.username) 
		  		ORDER BY m.date  DESC';
		$sth = $this->dbh->prepare($sql);
		$sth->execute([
			':user' => $user,
			':date' => $date,
			':id' => (int) $id,
			':id_message_parent' => (int) $idMessageParent,
		]);
		return $sth->fetchAll();

	}


	/**
	 * Modifie une ligne en fonction d'un identifiant
	 * @param mixed $id L'identifiant de la ligne à modifier
	 * @param mixed $uid l'id de l'utilisateur
	 * @param boolean $read on ajoute ou on retire l'id de l'utilisateur
	 * @return mixed false si erreur, true sinon
	 */
	public function updateRead($id, $uid, $read)
	{
		if (!is_numeric($id)){
			return false;
		}

		$sql = 'UPDATE ' . $this->table . ' SET read_message = ';
		if($read){
			
			$sql .= ' CONCAT(read_message,:uid) ';
			
		} else {
			$sql .= ' REPLACE(read_message, :uid, "") ';	
		}

		$sql .= ' WHERE ' . $this->primaryKey .' = :id';
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		$sth->bindValue(':uid', $uid.',');
		if(!$sth->execute()){
			return false;
		}
		return true;
	}


	/**
	 * Modifie une ligne en fonction d'un identifiant
	 * @param mixed $id L'identifiant de la ligne à modifier
	 * @param mixed $uid l'id de l'utilisateur
	 * @return mixed false si erreur, true sinon
	 */
	public function updateDelete($id, $uid)
	{
		if (!is_numeric($id)){
			return false;
		}

		$sql = 'UPDATE ' . $this->table . ' SET delete_message = ';
		
		$sql .= ' CONCAT(delete_message,:uid) ';	
		$sql .= ' WHERE ' . $this->primaryKey .' = :id';
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		$sth->bindValue(':uid', ','.$uid);
		if(!$sth->execute()){
			return false;
		}
		return true;
	}

	/**
	 * Compte le nombre de messages non lus
	 */
	public function countUnreadMessage($uid)
	{
		$sql = 'SELECT COUNT(*) AS total FROM '.$this->table.' WHERE LOCATE(:id_to,id_to) AND NOT LOCATE(:id_to,read_message) AND NOT LOCATE(:id_to,delete_message)';
		$sth = $this->dbh->prepare($sql);
		$sth->execute([':id_to' => $uid]);

		return $sth->fetch()['total'];
	}

}