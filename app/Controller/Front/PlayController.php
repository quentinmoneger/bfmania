<?php

namespace Controller\Front;

class PlayController extends MasterFrontController
{

	const PATH_VIEWS = 'front/play';

	public function __construct()
	{
		parent::__construct();

	}

	public function view()
    {
        $this->render(self::PATH_VIEWS.'/view', [

		]);
    }
}