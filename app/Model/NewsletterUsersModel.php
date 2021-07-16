<?php

namespace Model;

class NewsletterUsersModel extends MasterModel 
{
	
	/**
	 * Vérifie qu'un email est présent ou non dans la base
	 * @param $email L'email à vérifier
	 * @return true si email trouvé, false sinon
	 */
	public function emailExist($email)
	{
		$found = $this->findBy('email', $email);
		if(!empty($found)){
			return true;
		}

		return false;
	}

}