<?php $this->layout('layout_back', [
    'title' => 'Ajouter une catégorie',

]);
$post['auth'] = $post['auth'] ?? [];
?>

<?php $this->start('main_content'); ?>

<h1 class="title font-weight-normal"><i class="fad fa-plus-circle"></i> Ajouter une catégorie</h1>
<?php if ($w_user['role'] >= 70) : ?>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="alert alert-light border clearfix" role="alert">
                Création d'une catégorie à rajouter au forum
                <span class="float-right">
                    <a href="<?= $this->url('forum_add_categories'); ?>" class="btn btn-outline-dark btn-sm">
                        <i class="far fa-sm fa-plus-circle"></i> Créer une nouvelle catégorie
                    </a>
                </span>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="bg-white border p-4">
                <div class="col-xxl-8 mx-auto ">
                    <?php if (!empty($errors)) : ?>
                        <div class="alert alert-danger mb-3" role="alert">
                            <?= implode('<br>', $errors); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="input-group mb-3">

                            <div class="input-group-prepend">
                                <span class="input-group-text">Titre</span>
                            </div>
                            <input type="text" required name="title" id="title" class="form-control" placeholder="Minimum 6 caractères" value="<?= $post['title'] ?? '' ?>">
                        </div>
                        <div class="input-group ml-auto mb-n4" style="max-width: 180px">
                            <input type="number" required name="position" id="position" class="form-control" value="<?= $post['position'] ?? '' ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">Position</span>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="mb-n1 p-2 input-group-text border-bottom-0" style="max-width: 140px;">Description</label>
                            <textarea rows="5" required name="description" id="description" class="form-control" placeholder="Minimum 20 caractères soyez explicite merci"><?= $post['description'] ?? '' ?></textarea>
                        </div>

                        <div class="mb-3 mx-0 row">

                            <div class="col-4  col-md-2 bg-light rounded-left border d-flex align-items-center" style="color: #495057">
                                Autorisation
                            </div>

                            <div class=" col-8 col-md-10 p-1 rounded-right border border-left-0" style="min-height: 40px">
                                <?php foreach ($rolesAvailable as $key => $value) : ?>
                                <label>
                                    <span class="d-inline-block "
                                    ><input type="checkbox" class="ml-3 mr-1" name="auth[<?= $key; ?>]" <?= (array_key_exists('allow_' . $key, $post['auth'])) ? 'checked value="1"' : 'value="0"' ?> onclick="if(this.checked) this.value=1; else this.value=0"> <?= $value; ?>
                                    </span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" class="btn btn-first btn-sm px-5" name="submit" value="Créer la catégorie">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<?php endif ?>
<?php $this->stop('main_content'); ?>