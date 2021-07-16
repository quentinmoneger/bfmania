<?php

namespace Model;

class OptionsModel extends MasterModel 
{

	public function __construct()
	{
		parent::__construct();
		$this->setTable('options');

	}
	
	/**
	 * Modifie une ligne en fonction de son nom
	 * @param $value La nouvelle valeur
	 * @param $name Le nom de l'option
	 * @param boolean $stripTags Active le strip_tags automatique sur toutes les valeurs
	 * @return mixed false si erreur, les données mises à jour sinon
	 */
	public function updateByName($value, $name, $stripTags = true)
	{
		if(empty($name)){
			return false;
		}
		
		$sql = 'UPDATE ' . $this->table . ' SET value = :value WHERE name = :name LIMIT 1';
		$sth = $this->dbh->prepare($sql);

		$sth->bindValue(':name', $name);
		$sth->bindValue(':value', ($stripTags) ? strip_tags($value) : $value);

		if(!$sth->execute()){
			return false;
		}

		return $this->findBy('name', $name);
	}
}