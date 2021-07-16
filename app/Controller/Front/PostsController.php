<?php

namespace Controller\Front;

use Controller\Front\MasterFrontController;

use \Model\ForumPostsModel;
use \Model\ForumTopicsModel;
use \Respect\Validation\Validator as v;

class PostsController extends MasterFrontController
{ 
    protected $topicDb;
    protected $postsDb;

    public function __construct()
    {
        parent::__construct();

        $this->topicDb = new ForumTopicsModel;
        $this->postsDb = new ForumPostsModel;

    }
    
    /**
     * vue des posts dans un topic
     * @param INT $id_topic id du topic avec lequel le post est en relation
     */
    public function addPost($id_topic)
    {
        if (!empty($_POST)) {
            $post = array_map('trim', $_POST);

            $errors = [
                (!v::notEmpty()->length(2, null)->validate(strip_tags($post['post_msg']))) ? 'Votre réponse doit comporter au moins 2 caractères' : null,
            ];

            $errors = array_filter($errors);

            if (count($errors) === 0) {
                $user = $this->getUser();
                $postData = [
                    'message'            => $post['post_msg'],
                    'id_topic'           => $id_topic,
                    'id_author'          => $user['id'],
                    'date_create'        => date('Y-m-d H:i:s'),
                    'id_author_update'   => $user['id'],
                    'datetime_update'    => date('Y-m-d H:i:s'),
                ];

                $postInsert = $this->postsDb->insert($postData, false);

                if ($postInsert) {
                    $data = [
                        'id_last_post'       => $postInsert['id'],
                        'datetime_last_post' => $postInsert['date_create'],
                    ];
                    
                    if($this->topicDb->update($data, $id_topic)) {
                        $this->flash('Votre message à bien été publié', 'success');
                        $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
                    } else {
                        die('Erreur T004.');
                    }
                } else {
                    die('Erreur T005');
                }
            } else {
                $this->flash(implode('<br>', $errors), 'danger');
            }
        }

        $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
    }

    /**
     * vue des posts dans un topic
     * @param INT $id_post id du post à modifier
     * @param INT $id_topic id du topic avec lequel le post est en relation
     * @param INT $authoriz_role role de l'auteur du post
     */
    public function updatePost($id_post, $id_topic, $authoriz_role)
    {
        $user = $this->getUser();

        if (!in_array($user['role'], [50, 70, 99]) && $user['role'] != $authoriz_role) {
            $this->showForbidden();
        }

        if ($user['role'] <= $authoriz_role && $user['role'] != $authoriz_role) {
            $this->flash('Vous ne pouvez pas modifier le message d\'un membre avec un rôle supérieur ou égal au votre', 'danger');
            $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
        }

        if (!empty($_POST)) {
            $post = array_map('trim', $_POST);

            $errors = [
                (!v::notEmpty()->length(2, null)->validate(strip_tags($post['post_msg']))) ? 'Votre réponse doit comporter au moins 2 caractères' : null,
            ];

            $errors = array_filter($errors);

            if (count($errors) === 0) {
                $postData = [
                    'message'            => $post['post_msg'],
                    'id_author_update'   => $user['id'],
                    'datetime_update'    => date('Y-m-d H:i:s'),
                ];

                $postInsert = $this->postsDb->update($postData, $id_post);

                if ($postInsert) {
                    $this->flash('Votre message à bien été modifié', 'success');
                    $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
                } else {
                    die('Error SQL !');
                }
            } else {
                $this->flash(implode('<br>', $errors), 'danger');
            }
        }

        $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
    }

    /**
     * vue des posts dans un topic
     * @param INT $id_post id du post à supprimer
     * @param INT $id_topic id du topic avec lequel le post est en relation
     * @param INT $authoriz_role role de l'auteur du post
     */
    public function deletePost($id_post, $id_topic, $authoriz_role)
    {
        $user = $this->getUser();
        
        if (!in_array($user['role'], [50, 70, 99]) && $user['role'] != $authoriz_role) {
            $this->showForbidden();
            die;
        }

        if ($user['role'] <= $authoriz_role && $user['role'] != $authoriz_role) {
            $this->flash('Vous ne pouvez pas supprimer le message d\'un membre avec un rôle supérieur ou égal au votre', 'danger');
            $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
        }
        if (empty($user)) {
            $this->showNotFound();
        }
        
        if (!empty($_POST)) {

            $post = array_map('trim', array_map('strip_tags', $_POST));

            if($post['delete'] == 'delete') {
                $delete = $this->postsDb->delete($id_post);

                if($delete) {
                    // Upgrade colonne id_last_post (dans table topic) si le dernier post du topic concerné a été supprimé
                    $topicUpgrade = $this->topicDb->findLastPost($id_topic);
                    if($topicUpgrade != []){
                        $data = [
                            'id_last_post'       => $topicUpgrade[0]['id'],
                            'datetime_last_post' => $topicUpgrade[0]['date_create'],
                        ];
                        $this->topicDb->update($data, $id_topic);
                    }            

                    $postNbr = $this->topicDb->CountPostsForPaginate($id_topic)[0]['nbPosts'];
                    if($postNbr == 0){
                        $deleteTopic = $this->topicDb->delTopic($id_topic);

                        if($deleteTopic){
                            $this->flash('Le message et le topic ont été supprimés avec succès', 'success');
                            $this->redirectToRoute('list_forum');
                        } else {
                            die('Erreur SQL');
                        }
                    }
                    $this->flash('Le message à été supprimée avec succès', 'success');
                    $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
                } else {
                    die('Erreur SQL');
                }
            }
        }

        $this->redirectToRoute('view_topic', ['id' => $id_topic, 'page' => 1]);
    }

}
