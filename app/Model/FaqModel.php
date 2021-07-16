<?php

namespace Model;

class FaqModel extends MasterModel
{

	public function getCategories()
	{

		return array(
			1 => [
				'name'	=> 'Mon compte utilisateur',
				'title'	=> 'Mon compte utilisateur',
				'url' 	=> 'mon-compte',
				'icon'	=> 'fa-user'
			],
			2 => [
				'name'	=> 'Jouer sur<br>BFmania',
				'title'	=> 'Jouer sur BFmania',
				'url' 	=> 'jouer-sur-bfmania',
				'icon'	=> 'fa-dice',
			],
			3 => [
				'name'	=> 'Bien utiliser<br>le forum',
				'title'	=> 'Bien utiliser le forum',
				'url' 	=> 'utiliser-le-forum',
				'icon'	=> 'fa-comments-alt',
			],
		);
	}
}
