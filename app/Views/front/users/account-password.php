<?php $this->layout('layout', [
    'title'         => 'Modifier mon mot de passe',
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
            <div class="col-12 col-lg-8 card bg-light border-0 account-overview">
                <h5 class="card-title text-color-1 m-3 fw-400">Modifier mon mot de passe</h5>


                <div class="row">
                    <?= $this->fetch('front/_partials/flash-message', ['flash_custom' => true]); ?>
                    <div class="col-md-6 col-12 m-auto">

                        <div class="card-body">
                            <form method="post">

                                <div class="info-group mb-3">
                                    <label class="form-control-label text-muted" for="password">Mot de passe</label>
                                    <input type="password" name="password" id="password" class="form-control mt-1" placeholder="Minimum 8 caractères" required>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="form-control-labeltext-muted" for="password_confirm">Confirmation de mot de passe</label>
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control mt-1" placeholder="Confirmez votre mot de passe" required>
                                </div>

                                <div class="info-group">
                                    <button type="submit" id="register" class="btn btn-block btn-first w-75 btn-rounded py-1 mx-auto">Valider mon mot de passe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->stop('main_content'); ?>

<?php $this->start('js'); ?>
<script src="<?= $this->assetUrl('js/front/users/account-password.js'); ?>"></script>
<?php $this->stop('js'); ?>