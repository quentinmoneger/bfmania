<?php

namespace Controller\Front;

use \Model\ForumCategoryModel;

class ForumController extends MasterFrontController
{
    const PATH_VIEWS = 'front/forum';

    protected $ctgDb;

    public function __construct()
    {
        parent::__construct();
        $this->forumDb = new ForumCategoryModel();
    }

    /**
     * Affichage des catégories sur la page d'accueil du forum
     */
    public function listForum()
    {   
        $listCategories = $this->forumDb->findAllCat('position', 'ASC');
        $forum_lasts = $this->forumDb->lastAnswerForum();
        
        $this->render(self::PATH_VIEWS . '/list', [
            'categories'    => $listCategories,
            'forum_lasts'   => $forum_lasts
        ]);
    }

}
