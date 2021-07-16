<?php

namespace Controller\Back;

use \Model\ForumCategoryModel;
use \Respect\Validation\Validator as v;


class ForumCategoryController extends MasterBackController
{
    const PATH_VIEWS = 'back/forum';

    protected $ctgDb;
    protected $rolesAvailable;

    public function __construct()
    {
        parent::__construct();
        $this->ctgDb = new ForumCategoryModel();

        $rolesAvailable['visitor'] = 'Visiteur';
        foreach (\Tools\Utils::listRoles() as $key => $value) {
            $rolesAvailable[$key] = $value;
        }

        $this->rolesAvailable = $rolesAvailable;
    }


    /**
     * Ajoute une catégorie dans la console / Forum
     * @param post Method Post pour l'ajout
	 * @return render Render sur page console / Forum
	 */
    public function addCategory()
    {
        if (!empty($_POST)) {

            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $post[$key] = array_map('trim', array_map('strip_tags', $value));
                } else {
                    $post[$key] = trim(strip_tags($value));
                }
            }
            $errors = [
                (!v::notEmpty()->length(6, 140)->validate($post['title'])) ? 'Le titre doit comporter entre 6 et 140 caractères' : null,
                (!v::notEmpty()->length(20, null)->validate($post['description'])) ? 'La description doit avoir un minimum de 20 caractères' : null,
                (!v::notEmpty()->numericVal()->between(1, 99)->validate($post['position'])) ? 'La position doit être comprise entre le chiffre  1 et 99' : null,

            ];

            $errors = array_filter($errors);

            if (count($errors) === 0) {
                $myAuth = [];

                // Le tableau des rôles est rempli en fonction des selections du post
                foreach ($this->rolesAvailable as $kRole => $vRole) {
                    $myAuth[$kRole] = $post['auth'][$kRole] ?? 0;
                }

                $data = [
                    'title'         => $post['title'],
                    'description'   => $post['description'],
                    'position'      => $post['position'],
                    'url'           => $this->buildUrl($post['title'], 'ForumCategoryModel'),
                    'auth'          => json_encode($myAuth, JSON_UNESCAPED_UNICODE),
                    'date_create'   => date('Y-m-d H:i:s'),
                ];

                $newCategory = $this->ctgDb->insert($data);
                if ($newCategory) {
                    $this->flash('Une nouvelle catégorie as été créée', 'success');
                    $this->redirectToRoute('forum_list_categories');
                }
            }
        };

        $this->render(self::PATH_VIEWS . '/categories_add', [
            'rolesAvailable' => $this->rolesAvailable,
            'errors'    => $errors ?? [],
            'post' => $post ?? [],

        ]);
    }



    /**
     * Liste toutes les catégories dans la console / forum
     */
    public function listAllCategory()
    {

        $this->render(self::PATH_VIEWS . '/categories_list', [
            'ctgDb'             => $this->ctgDb->findAll('position', 'ASC'),
            'rolesAvailable'    => $this->rolesAvailable,
        ]);
    }


    /**
     * Modifie une catégorie 
     * @param string $id Identifiant de la catégorie
     * @param post Method Post pour l'edition
	 * @return render Render sur page console / Forum
	 */
    public function editCategory($id)
    {
        $ctg = $this->ctgDb->find($id);

        if (!empty($_POST)) {

            // Soumission formulaire
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $post[$key] = array_map('trim', array_map('strip_tags', $value));
                } else {
                    $post[$key] = trim(strip_tags($value));
                }
            }
            $errors = [
                (!v::notEmpty()->length(6, 140)->validate($post['title'])) ? 'Le titre doit comporter entre 6 et 140 caractères' : null,
                (!v::notEmpty()->length(20, null)->validate($post['description'])) ? 'La description doit avoir un minimum de 20 caractères' : null,
                (!v::notEmpty()->numericVal()->between(1, 99)->validate($post['position'])) ? 'La position doit être comprise entre le chiffre  1 et 99' : null,

            ];


            $errors = array_filter($errors);

            if (count($errors) === 0) {
                $myAuth = [];

                // Le tableau des rôles est rempli en fonction des selections du post
                foreach ($this->rolesAvailable as $kRole => $vRole) {
                    $myAuth[$kRole] = $post['auth'][$kRole] ?? 0;
                }


                $data = [
                    'title'         => $post['title'],
                    'description'   => $post['description'],
                    'position'      => $post['position'],
                    'url'           => $this->buildUrl($post['title'], 'ForumCategoryModel'),
                    'auth'          => json_encode($myAuth, JSON_UNESCAPED_UNICODE),
                ];

                $update = $this->ctgDb->update($data, $id);
                if ($update) {
                    $this->flash('La catégorie a été modifiée avec succès', 'success');
                    $this->redirectToRoute('forum_list_categories');
                }
            }
        }
        $this->render(self::PATH_VIEWS . '/categories_edit', [
            'ctgDb'     => $ctg,
            'errors'    => $errors ?? [],
            'rolesAvailable'    => $this->rolesAvailable
        ]);
    }


    /**
     * Suppression d'une catégorie 
     * @param string $id Identifiant de la catégorie
     * @return render Render sur page console / Forum
     */
    public function deleteCategory($id)
    {
        $ctg = $this->ctgDb->find($id);

        // Soumission formulaire
        if (!empty($_POST)) {

            $post = array_map('trim', array_map('strip_tags', $_POST));

            if (isset($post['delete']) && $post['delete'] == 'yes') {
                $this->ctgDb->delete($id);

                $this->flash('La catégorie a été supprimée avec succès', 'success');
                $this->redirectToRoute('forum_list_categories');
            }
        }

        $this->render(self::PATH_VIEWS . '/categories_delete', [
            'ctgDb' => $ctg,
        ]);
    }
}
