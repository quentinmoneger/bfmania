<?php

namespace Model;

class ChatsModel extends MasterModel
{
    public function __construct()
	{
		parent::__construct();
		$this->setTable('chats');

	}
}