<?php $this->layout('layout', [
    'title'         => 'Modifier mon profil',
    'flash_custom'  => true,

]); ?>

<?php $this->start('main_content'); ?>
<section class="pb-5 pt-3">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="title-page with-underline">Mon compte</h1>
        </div>
        <div class="row content pb-5 no-gutters justify-content-center">
            <?= $this->fetch('front/_partials/sidebar-account'); ?>
            <div class="col-12 col-lg-8  card bg-light border-0 account-overview">
                <h5 class="card-title text-color-1 m-3 fw-400">Modifier mes informations</h5>

                <?= $this->fetch('front/_partials/flash-message', ['flash_custom' => true]); ?>

                <div class="row ">
                    <form method="post" class="card-body" enctype="multipart/form-data">
                        <div class="col-12 col-md-8 m-auto">
                            <div class="card-body ">
                                <div class="info-group">
                                    <p class="text-muted">Nom d'utilisateur</p>
                                    <p class="mt-1"><?= $w_user['username']; ?></p>
                                </div>

                                <div class="info-group">
                                    <p class="text-muted">Adresse email</p>
                                    <input type="login" name="email" id="email" class="form-control mt-1 mb-4" value="<?= $post['email'] ?? $w_user['email']; ?>" placeholder="<?= $w_user['email']; ?>">
                                </div>


                                <div class="info-group">
                                    <p class=" text-muted">Compte Facebook</p>
                                    <p class="mt-1"><?= $w_user['facebook_id'] ?? 'Non connecté' ?></p>
                                </div>

                                <div class="info-group ">
                                    <p class="text-muted">Avatar</p>
                                    <p class="mt-1"href="<?= ($w_current_route === 'users_account_modified') ? '#uploadImageModal' : ''; ?>" role="button" data-toggle="modal" >Cliquer ici pour changer l'avatar !</p>
                                </div>

                                <?php if ($newsletters) : ?>
                                    <div class="info-group row m-0">
                                        <input class="col-1 text-muted" type="checkbox" id="newsletter" name="newsletter-disabled">
                                        <label class="col-10 mt-1" for="newsletter">Désinscription de la newsletter.</label>
                                    </div>
                                <?php else : ?>
                                    <div class="info-group row m-0">
                                        <input class="col-1 text-muted" type="checkbox" id="newsletter" name="newsletter-active">
                                        <label class="col-10 mt-1" for="newsletter">Inscription a la newsletter.</label>
                                    </div>
                                <?php endif ?>
                                <div class="info-group pt-2">
                                    <button type="submit" id="register" class="btn btn-block btn-first w-75 btn-rounded py-1 mx-auto">Enregistrer les modifications</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</section>
<?php $this->stop('main_content'); ?>
<?php $this->start('js'); ?>
<script>
    var urlUsersAccountImage = '<?= $this->url('users_account_image'); ?>'
</script>
<script src="<?= $this->assetUrl('js/front/users/account-modified.js'); ?>"></script>
<?php $this->stop('js'); ?>