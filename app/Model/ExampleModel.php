<?php

namespace Model;

class ExampleModel extends MasterModel
{



	############################################################################################
	############################################################################################
	#
	# En créant un model (même vide) on hérite forcémént des méthodes suivantes :		
	#
	# find($id) => Récupère une ligne. Requete SQL : SELECT * FROM ma_table WHERE id = $id
	# 
	# findAll() => Récupère toutes les lignes de la table. Requete SQL : SELECT * FROM ma_table
	#
	# findBy($column, $value) => Récupère une ligne ou $value se trouve  dans $column. $column doit être une colonne SQL de la table. $value, la valeur qu'on recherche
	# Exemple : findBy('email', $post['email']); Requete SQL : SELECT * FROM ma_table WHERE $column = $value LIMIT 1
	#
	# findAllBy($column, $value) => Similaire à la requete si dessus, mais récupère PLUSIEURS RESULTATS si possible. Utilisation identique
	#
	# insert($datas) => Permet d'effectuer une insertion. $datas est un tableau associatif dans le quel les clés sont les colonnes SQL et les valeurs sont les valeurs à insérer
	#
	#
	# update($datas) => Permet d'effectuer une mise à jour. Le tableau $datas fonctionne de la même manière que pour l'insertion. 
	# Le paramètre $id permet de choisir l'id qu'on veut mettre à jour (WHERE id = $id)
	#
	#
	#
	# On peut également ajouter nos propres requetes SQL, comme ci-dessous. Seulement si on en a besoin.
	#
	# $this->table correspond au nom de la table.
	# Le framework devine automatiquement le nom de la table en fonction du nom du model.
	# Exemple : 
	# ProductsModel => cherchera une table nommé "products"
	#
	# ForumCategoriesModel => cherchera une table nommée "forum_categories"
	#
	# ForumPostsModel => cherchea une table nommée "forum_posts"
	#
	#
	# D'autres info ici : https://framework-w.axessweb.io/?p=modeles	
	#
	############################################################################################
	############################################################################################


	public function findAllByMinimalRole($minimum_role)
	{
		$sql = 'SELECT * FROM ' . $this->table . ' WHERE role >= :role_minimal_search';
		$sth = $this->dbh->prepare($sql);


		/**************************************************************/
		/* Methode 1												  */
		/* 															  */
		/* on utilise soit la méthode 1, soit la méthode 2. Au choix  */
		/**************************************************************/
		$sth->bindValue(':role_minimal_search', $minimum_role);
		$sth->execute();

		/**************************************************************/
		/* OU méthode 2 (c'est EXACTEMMENT la même chose que l'autre) */
		/* 															  */
		/* on utilise soit la méthode 1, soit la méthode 2. Au choix  */
		/**************************************************************/
		$paramsRequest = [':role_minimal_search' => $minimum_role];
		$sth->execute($paramsRequest);
	}
}
