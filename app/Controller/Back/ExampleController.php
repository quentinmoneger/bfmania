<?php

# Si on est dans le front, remplacer "Back" ici par "Front"
namespace Controller\Back;

use \Model\ExampleModel;
use \Behat\Transliterator\Transliterator as tr;

class ExampleController extends MasterBackController
{

	// Dossiers des vues de ce controller
	const PATH_VIEWS = 'back/example';


	/**
	 * Constructeur. Fonction qui sera automatiquement appelé a chaque fois 
	 */
	public function __construct()
	{
		// On récupère celui du parent, ici MasterBackController
		parent::__construct();
	}


	/**
	 * Exemple d'une fonction. La route devra appelé cette fonction
	 */
	public function helloWorld()
	{

		// J'instancie le model voulu
		$exempleDb = new ExampleModel();

		// Exemple de recherche avec une fonction existante par défaut (select where id)
		$datas = $exempleDb->find($_GET['id']);


		// Exemple de fonction personnelle dans le model
		// Cette fonction va chercher tous utilisateur ayant un roles supérieur à XX
		// XX correspond à la valeur (ici 50) se trouvant dans les parenthèses
		$exempleDb->findAllByMinimalRole(50);


		$this->render(self::PATH_VIEWS.'/fichier_de_vue');
	}


}