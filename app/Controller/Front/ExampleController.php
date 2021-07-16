<?php

# Si on est dans le back, remplacer "Front" ici par "Back"
namespace Controller\Back;

use \Model\ExampleModel;
use \Behat\Transliterator\Transliterator as tr;

class ExampleController extends MasterBackController
{

	// Dossiers des vues de ce controller
	const PATH_VIEWS = 'front/example';


	/**
	 * Constructeur. Fonction qui sera automatiquement appelé a chaque fois 
	 */
	public function __construct()
	{
		// On récupère celui du parent, ici MasterFrontController
		parent::__construct();
	}


	/**
	 * Exemple d'une fonction. La route devra appelé cette fonction
	 */
	public function helloWorld()
	{

		// J'instancie le model voulu.
		// La variable $exempleDb contient donc toutes les fonctions disponibles dans mon model
		$exempleDb = new ExampleModel();


		// Exemple de recherche avec une fonction existante par défaut (select where id)
		$datas = $exempleDb->find($_GET['id']);


		// Exemple de fonction personnelle dans le model
		// Cette fonction va chercher tous utilisateur ayant un roles supérieur à XX
		// XX correspond à la valeur (ici 50) se trouvant dans les parenthèses
		$datas_roles = $exempleDb->findAllByMinimalRole(50);


		// On envoi à la vue
		$this->render(self::PATH_VIEWS.'/fichier_de_vue', [
			'variable_dans_la_vue' => $value ?? '',
		]);
	}


}