<?php

namespace Model;

class PagesModel extends MasterModel 
{

	/**
	 * | represente une nouvelle row
	 * , represente une col
	 * ATTENTION : ne pas modifié une fois le site livré, cela pourrait poser des problèmes d'affichages
	 */
	public $templates_availables = [
		'tpl_1' 	=> '12',
		'tpl_2'		=> '6,6',
		'tpl_3' 	=> '12|6,6',
		'tpl_4' 	=> '4,4,4|12|4,4,4',
		'tpl_5' 	=> '6,6|12|6,6',
		'tpl_6' 	=> '12|6,6|12|4,4,4',
	];
}