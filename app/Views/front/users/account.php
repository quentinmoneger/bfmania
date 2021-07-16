<?php $this->layout('layout', [
    'title'         => 'Mon compte',
    'flash_custom'  => true,
]); ?>

<?php $this->start('main_content'); ?>
<section class="pb-5 pt-3">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="title-page with-underline">Mon compte </h1>
        </div>
        <div class="row content pb-5 no-gutters justify-content-center">
            <?= $this->fetch('front/_partials/sidebar-account'); ?>
            <div class="col-12 col-lg-8 card bg-light border-0 account-overview">
                <h5 class="card-title text-color-1 m-3 fw-400">Mes informations</h5>

                <?= $this->fetch('front/_partials/flash-message', ['flash_custom' => true]); ?>

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card-body py-0">
                            <div class="info-group">
                                <p class="text-muted">Nom d'utilisateur</p>
                                <p class="mt-1"><?= $w_user['username']; ?></p>
                            </div>

                            <div class="info-group">
                                <p>Adresse email</p>
                                <p class="mt-1"><?= $w_user['email']; ?></p>
                            </div>

                            <div class="info-group">
                                <p>Compte Facebook</p>
                                <p class="mt-1"><?= $w_user['facebook_id'] ?? 'Non connecté' ?></p>
                            </div>

                            <div class="info-group">
                                <p>Rôle</p>
                                <p class="mt-1"><?= \Tools\Utils::getHumanRole($w_user['role']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card-body py-0">
                            <div class="info-group">
                                <p>Date d'inscription</p>
                                <p class="mt-1"><?= \Tools\Utils::dateFr($w_user['date_registered'], 'd F Y'); ?></p>
                            </div>

                            <div class="info-group">
                                <p>Dernière connexion</p>
                                <p class="mt-1"><?= \Tools\Utils::dateFr($w_user['date_connect_prev'], 'd F Y \à H:i'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<?php $this->stop('main_content'); ?>