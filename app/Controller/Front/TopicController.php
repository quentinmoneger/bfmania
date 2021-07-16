<?php

namespace Controller\Front;

use Controller\Back\ForumCategoryController;
use \Model\ForumTopicsModel;
use \Model\ForumCategoryModel;
use \Model\ForumPostsModel;
use \Respect\Validation\Validator as v;

class TopicController extends MasterFrontController
{
    const PATH_VIEWS = 'front/forum';

    protected $topicDb;
    
    protected $postsDb;

    public function __construct()
    {
        parent::__construct();

        $this->topicDb = new ForumTopicsModel;
        $this->postsDb = new ForumPostsModel;

    }

    /**
     * Liste des topics
     * @param STRING $url nom de la categorie
     * @param INT $page numéro de la page
     */
    public function listTopics($url, $page = 1)
    {
       
        $ctgDb = new ForumCategoryModel;
        $ctg = $ctgDb->findByUrl($url);
        if(!$ctg){
            die('Erreur T001. Aucune categorie de forum correspondante !');
        }
        $count = $this->topicDb->CountPostByTopic($url);
        $limit = 8;
        
        $topics = $this->topicDb->findAllTopicsPaginate($ctg['id'], $limit, $page, 'id', 'DESC');
        foreach ($topics as $key => $value) {
            foreach ($count as $value) {
                if ($value['id'] == $topics[$key]['id']) {
                    $topics[$key]['nbPosts'] = $value['nbPosts'];
                }
            }
        }

        // On verfifie si l'utilisateur à l'autorisation de voir le topic
        $w_user = $this->getUser();
        foreach ($topics as $topic) {
            $auths = json_decode($topic['auth']);
            foreach ($auths as $auth => $role) {
                if($w_user['role'] == $auth){
                    if($role == 0){
                        $this->showForbidden();
                    }
                }
            }
        }

        $this->redirectNotConnect();

        $topic_lasts = $this->topicDb->lastAnswerTopic($url);

        $this->render(self::PATH_VIEWS . '/list_topics', [
            'topics' => $topics,
            'ctg'    => $ctg,
            'topic_lasts' => $topic_lasts,
            'currentPage' => intval($page) ?? 1,
            'totalPage' => $this->topicDb->totalPages,
            'totalCount' => $this->topicDb->totalCount,
            'url' => $url,
            'count' => $count,
            'limit' => $limit,
            'id' => $ctg['id'],
        ]);
    }

    /**
     * Ajout d'un topic
     * @param INT $category id de la categorie choisie
     * @param STRING $url nom de la categorie
     */
    public function addTopic($category, $url)
    {
        $this->redirectNotConnect();

        if (!empty($_POST)) {
            $post = array_map('trim', $_POST);

            $errors = [
                (!v::notEmpty()->length(8, null)->validate(strip_tags($post['topic_title']))) ? 'Le titre doit comporter au moins 8 caractères' : null,
                (!v::notEmpty()->length(8, null)->validate(strip_tags($post['topic_msg']))) ? 'Le contenu de votre message doit comporter au moins 8 caractères' : null,
            ];

            $errors = array_filter($errors);

            if (count($errors) === 0) {
                $user = $this->getUser();
                $data = [
                    'title'              => strip_tags($post['topic_title']),
                    'id_author'          => $user['id'],
                    'id_category'        => $category,
                    'id_last_post'       => 0,
                    'datetime_last_post' => date('Y-m-d H:i:s'),
                ];

                $topic = $this->topicDb->insert($data, false);

                if ($topic) {
                    $postData = [
                        'message'            => $post['topic_msg'],
                        'id_author'          => $user['id'],
                        'date_create'        => date('Y-m-d H:i:s'),
                        'id_topic'           => $topic['id'],
                        'id_author_update'   => $user['id'],
                        'datetime_update'    => date('Y-m-d H:i:s'),
                    ];

                    $msg = $this->postsDb->insert($postData, false);

                    if($msg) {
                        //$this->flash('Votre message à bien été publié');
                        $this->topicDb->update(['id_last_post' => $msg['id']], $topic['id']);
                        $this->redirectToRoute('list_topics', ['url' => $url]);
                    } 
                    else {
                        die('Erreur T002.');
                    }
                } 
                else {
                    die('Erreur T003.');
                }
            } 
            else {
                $this->flash(implode('<br>', $errors), 'danger');
            }
        }

        $this->render(self::PATH_VIEWS . '/add_topic', [
            'category' => $category,
            'url'      => $url,
        ]);
    }

    /**
     * vue d'un topic
     * @param INT $id id du topic
     * @param INT $page / par default 1
     */
    public function viewTopic($id, $page = 1)
    {
        
        $this->redirectNotConnect();
     
        $count = $this->topicDb->countPostsForPaginate($id);

        // Nombre de posts visibles par page
		$limit = 6;
        
        // On verfifie si l'utilisateur à l'autorisation de voir le topic
        $w_user = $this->getUser();

        $paginates =  $this->postsDb->findAllPostsPaginate($id, $count, $limit, $page);
        foreach ($paginates as $paginate) {
            $auths = json_decode($paginate['auth']);
            foreach ($auths as $auth => $role) {
                if($w_user['role'] == $auth){
                    if($role == 0){
                        $this->showForbidden();
                    }
                }
            }
        }

        $this->render(self::PATH_VIEWS . '/view_topic', [
            'topic' => $this->topicDb->findTopic($id),
            'get' => $get,
            'posts' => $this->postsDb->findAllPostsById($id, 'date_create', 'ASC'),
            'paginates' => $paginates,
            'count' => $count,
            'currentPage' => intval($page) ?? 1,
            'limit' => $limit,
            'id_topic' => $id,
        ]);

    }

    /**
     * Epingler un topic / Activer au clic sur chevron
     * @param STRING $url nom du topic
     */
    public function pinTopic($url)
    {
        $this->redirectNotConnect();
        
        if (!empty($_POST)) {
            $post = array_map('trim', array_map('strip_tags', $_POST));

            $errors = [
                (!v::numericVal()->between(0, 1)->validate($post['pin'])) ? 'Tiny Int non valide' : null,
                (!v::notEmpty()->numericVal()->positive()->validate($post['id_topic'])) ? 'Id du topic non valide' : null,
            ];

            $errors = array_filter($errors);

            if (count($errors) === 0) {
                $data = [
                    'pin' => $post['pin']
                ];

                if ($this->topicDb->update($data, $post['id_topic'])) {
                    $this->redirectToRoute('list_topics', ['url' => $url]);
                }
            } else {
                $this->flash(implode('<br>', $errors), 'danger');
                $this->redirectToRoute('list_topics', ['url' => $url]);
            }
        }

        $this->redirectToRoute('list_topics', ['url' => $url]);
    }

    /**
     * Suppression d'un topic 
     * @param STRING $id_topic Identifiant du topic a supprimer
     * @param MIXED $authoriz_role Niveau d'autorisation necessaire 
     */
    public function deleteTopic($id_topic, $authoriz_role)
    {

        $user = $this->getUser();
        
        if ($user['role'] <= $authoriz_role && $user['role'] != $authoriz_role) {
            $this->showForbidden();  
        }

        if (empty($user)) {
            $this->showNotFound();
        }
  
        $delete = $this->topicDb->delTopic($id_topic);

        if ($delete) {
            $this->flash('Le topic à été supprimé avec succès', 'success');
            $this->redirectToRoute('list_forum');
        } 
        else {
            die('Erreur SQL');
        }

        $this->redirectToRoute('list_forum');
          
    }

}
