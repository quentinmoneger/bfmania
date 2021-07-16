<?php

namespace Model;

class MasterModel extends \W\Model\Model
{

	/**
	 *  Le nombre de résultats par page
	 * @var int
	 */
	public $pageLimit = 50;

	/**
	 * Le nombre total de page
	 * @var int
	 */
	public $totalPages = 0;

	/**
	 * Le nombre total de résultats
	 * @var int
	 */
	public $totalCount = 0;

	/**
	 * Fonction de pagination
	 * @param $page int Le numéro de page
	 */
	public function paginate($page = 1)
	{
		// On définit l'offset en fonction de la page actuelle
		$offset = $this->pageLimit * ($page - 1);
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $this->table . ' LIMIT :offset, :limit';
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':offset', $offset, \PDO::PARAM_INT);
		$sth->bindValue(':limit', $this->pageLimit, \PDO::PARAM_INT);
		$sth->execute();

		$res = $sth->fetchAll();

		// On compte le nombre de résultat
		$this->countResult();

		// Le nombre de page
		$this->totalPages = ceil($this->totalCount / $this->pageLimit);

		// On retourne le contenu
		return $res;
	}


	/**
	 * Compte le nombre de résultats
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
	 * Recherche une ligné par son URL
	 * @param string $url L'url 
	 * @return mixed Les données sous forme de tableau associatif
	 */
	public function findByUrl($url)
	{
		if (!is_string($url)) {
			return false;
		}

		return $this->findBy('url', $url);
	}

	/* Alias de méthode */
	public function findUrl($url)
	{
		return $this->findByUrl($url);
	}

	/**
	 * Vérifie qu'une url existe ou non
	 * @param string $url L'url a tester
	 * @return bool true si l'url existe, false sinon
	 */
	public function urlExist($url)
	{
		if (!empty($this->findByUrl($url))) {
			return true;
		}
		return false;
	}

	/**
	 * Supprime toutes les entrées d'une table
	 */
	public function deleteAll()
	{
		$sql = 'TRUNCATE ' . $this->table;

		$sth = $this->dbh->prepare($sql);
		$sth->execute();
	}
}
