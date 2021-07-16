<?php $cache = rand(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<title><?= $this->e($title . ' - Console ' . $w_site_name); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- CSS Local Library -->
	<link rel="stylesheet" href="<?= $this->assetUrl('libs/bootstrap-4.3.1-custom/css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="<?= $this->assetUrl('libs/bootstrap-sweetalert/sweetalert.min.css'); ?>">
	<link rel="stylesheet" href="<?= $this->assetUrl('libs/ekko-lightbox/ekko-lightbox.css'); ?>">
	<link rel="stylesheet" href="<?= $this->assetUrl('fontawesome-pro-5.15.2-web/css/all.css'); ?>">
	<!-- CSS CDN Library -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.css" integrity="sha256-M8o9uqnAVROBWo3/2ZHSIJG+ZHbaQdpljJLLvdpeKcI=" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Vibes&display=swap">
	<!-- CSS Internal -->
	<link rel="stylesheet" href="<?= $this->assetUrl('css/back.css') . '?v=' . $cache; ?>">
	<!-- // Favicons -->
	<?= $this->section('css'); ?>
</head>

<body class="<?= (empty($w_user)) ? 'unlogged' : 'logged'; ?>">
	<nav class="navbar navbar-expand-xl navbar-dark fixed-top custom-navbar" id="menuNavbar">
		<a href="<?= $this->url('back_dashboard'); ?>" class="navbar-brand"><?= $this->e($w_site_name); ?> &nbsp;<small class="d-none d-md-inline-block">console</small></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Menu">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav navbar-rightbar ml-auto">

				<?php if ($w_user) : ?>
					<li class="nav-item nav-user d-none d-xl-block">
						<i class="fad fa-fw fa-user-circle"></i> <?= $w_user['username']; ?>
					</li>
				<?php endif; ?>
				<li class="nav-item">
					<a href="<?= $this->url('default_home'); ?>" class="nav-link" target="_blank">
						<i class="fad fa-fw fa-angle-double-right"></i> Voir le site
					</a>
				</li>
				<?php if ($w_user) : ?>
					<li class="nav-item">
						<a href="<?= $this->url('users_logout'); ?>" class="nav-link">
							<i class="fad fa-fw fa-sign-out"></i> Déconnexion
						</a>
					</li>
				<?php endif; ?>
			</ul>

			<?php if ($w_user) : ?>
				<ul class="navbar-nav navbar-sidebar">
					<li class="nav-item">
						<a href="<?= $this->url('back_dashboard'); ?>" class="nav-link"><i class="fal fa-fw fa-tachometer-alt"></i> Tableau de bord</a>
					</li>
					<li class="nav-item">
						<a href="<?= $this->url('back_medias'); ?>" class="nav-link"><i class="fal fa-fw fa-photo-video"></i> Bibliothèque médias</a>
					</li>


					<?php if (in_array($w_user['role'], [30, 70, 99])) : ?>
						<li class="nav-item nav-title nav-title-collapse">

							<a href="#radioCollapse" class="nav-link-collapse no-icon" role="button" aria-expanded="false" aria-controls="radioCollapse">Animation Radio</a>

							<ul class="sidenav-second-level collapse show" id="radioCollapse">

							</ul>
						</li>
					<?php endif; ?>

					<?php if (in_array($w_user['role'], [50, 70, 99])) : ?>
						<li class="nav-item nav-title nav-title-collapse">
							<a href="#modoCollapse" class="nav-link-collapse no-icon" role="button" aria-expanded="false" aria-controls="modoCollapse">Modération</a>

							<ul class="sidenav-second-level collapse show" id="modoCollapse">
								<li class="nav-item">
									<a href="<?= $this->url('back_users_list'); ?>" class="nav-link"><i class="fal fa-fw fa-user-alt"></i> Gestion des utilisateurs</a>
								</li>
								<li class="nav-item">
									<a href="<?= $this->url('back_banish_list'); ?>" class="nav-link"><i class="fal fa-fw fa-radiation"></i> Bannissements</a>
								</li>
								<li class="nav-item">
									<a href="<?= $this->url('back_warning_list'); ?>" class="nav-link"><i class="fal fa-exclamation-triangle"></i> Avertissements</a>
								</li>
								<li class="nav-item">
									<a href="<?= $this->url('back_report_list'); ?>" class="nav-link"><i class="fal fa-comment-alt-exclamation"></i> Signalements</a>
								</li>
							</ul>
						</li>
					<?php endif; ?>


					<?php if (in_array($w_user['role'], [70, 99]) && getApp()->getConfig('plugin_newsletter') === true) : ?>
						<li class="nav-item nav-title nav-title-collapse">
							<a href="#newsCollapse" class="nav-link-collapse no-icon" role="button" aria-expanded="false" aria-controls="newsCollapse">Newsletter</a>

							<ul class="sidenav-second-level collapse show" id="newsCollapse">
								<li class="nav-item">
									<a href="<?= $this->url('back_newsletters_list'); ?>" class="nav-link"><i class="fal fa-fw fa-at" aria-hidden="true"></i> Gestion des newsletters</a>
								</li>
								<li class="nav-item">
									<a href="<?= $this->url('back_newsletters_subscribers'); ?>" class="nav-link"><i class="fal fa-fw fa-user-tag" aria-hidden="true"></i> Liste des inscrits</a>
								</li>
							</ul>
						</li>
					<?php endif; ?>

					<?php if (in_array($w_user['role'], [70, 99]) && getApp()->getConfig('plugin_newsletter') === true) : ?>
						<li class="nav-item nav-title nav-title-collapse">
							<a href="#newsCollapse" class="nav-link-collapse no-icon" role="button" aria-expanded="false" aria-controls="newsCollapse">Newsletter</a>

							<ul class="sidenav-second-level collapse show" id="newsCollapse">
								<li class="nav-item">
									<a href="<?= $this->url('back_newsletters_list'); ?>" class="nav-link"><i class="fal fa-fw fa-at" aria-hidden="true"></i> Gestion des newsletters</a>
								</li>
								<li class="nav-item">
									<a href="<?= $this->url('back_newsletters_subscribers'); ?>" class="nav-link"><i class="fal fa-fw fa-user-tag" aria-hidden="true"></i> Liste des inscrits</a>
								</li>
							</ul>
						</li>
					<?php endif; ?>

					<?php if (in_array($w_user['role'], [70, 99])) : ?>
						<li class="nav-item nav-title nav-title-collapse">
							<a href="#paramCollapse" class="nav-link-collapse no-icon" role="button" aria-expanded="false" aria-controls="paramCollapse">Administration</a>

							<ul class="sidenav-second-level collapse show" id="paramCollapse">
								<?php if (getApp()->getConfig('plugin_pages') === true) : ?>
									<li class="nav-item">
										<a href="<?= $this->url('back_pages_list'); ?>" class="nav-link"><i class="fal fa-fw fa-file-alt fa-swap-opacity"></i> Gestion des pages</a>
									</li>
								<?php endif; ?>
								<li class="nav-item">
									<a href="<?= $this->url('back_faq_list'); ?>" class="nav-link"><i class="fal fa-question-circle"></i> Foire aux questions</a>
								</li>
								<?php if (in_array($w_user['role'], [99])) : ?>
									<li class="nav-item">
										<a href="<?= $this->url('back_options'); ?>" class="nav-link"><i class="fal fa-fw fa-cogs"></i> Paramètres du site</a>
									</li>
								<?php endif; ?>
							</ul>
						</li>
					<?php endif; ?>

					<?php if (in_array($w_user['role'], [70, 99])) : ?>
						<li class="nav-item nav-title nav-title-collapse">
							<a href="#paramCollapse" class="nav-link-collapse no-icon" role="button" aria-expanded="false" aria-controls="paramCollapse">Forum</a>

							<ul class="sidenav-second-level collapse show" id="paramCollapse">
								<li class="nav-item">
									<a href="<?= $this->url('forum_list_categories'); ?>" class="nav-link"><i class="fal fa-fw fa-sitemap fa-swap-opacity"></i> Gestion des catégories</a>
								</li>
							</ul>
						</li>
					<?php endif; ?>

					<li class="mt-auto d-none d-xl-block">
						<img src="<?= $this->assetUrl('img/copyright/ax_inline_white.png'); ?>" title="Création axessweb.io" class="sidebar-width px-5 pb-3">
					</li>
				</ul>
			<?php endif; ?>
		</div>
	</nav>

	<div id="contentWrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 pb-5 pt-3">
					<?php if (!empty($w_flash_message)) : ?>
						<?php foreach($w_flash_message as $flash_msg):?>
							<div class="alert alert-<?=$flash_msg['level'];?> alert-dismissible alert-custom" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
								<?=$flash_msg['message'];?>
							</div>
						<?php endforeach;?>
					<?php endif; ?>
					<?= $this->section('main_content'); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- Js CDN Library -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
    <!-- Js Local Library -->
	<script src="<?= $this->assetUrl('libs/bootstrap-4.3.1-custom/js/bootstrap.bundle.min.js'); ?>"></script>
	<script src="<?= $this->assetUrl('libs/bootstrap-sweetalert/sweetalert.min.js'); ?>"></script>
	<script src="<?= $this->assetUrl('libs/ekko-lightbox/ekko-lightbox.min.js'); ?>"></script>
	<script src="<?= $this->assetUrl('libs/bootstrap-awmedias/awmedias.js') . '?v=' . $cache; ?>"></script>
	<!-- Js Internal -->	
	<script src="<?= $this->assetUrl('js/back/back.js') . '?v=' . $cache; ?>"></script>
	
	<script>
	// Users URL
	var urlAjaxGetSearch = '<?=$this->url('ajax_get_search');?>';
	    urlBackUsersEdit = '<?=$this->url('back_users_edit', ['id' => 'userid']);?>'
		urlBackUsersDelete = '<?=$this->url('back_users_delete', ['id' => 'userid']);?>'	
	
	// Media / Ckeditor URL
	var urlBackMediasCkeditor = '<?= $this->url('back_medias_ckeditor'); ?>?action=ckeditor',
	    urlAjaxMedialist      = '<?= $this->url('ajax_media_list'); ?>',
		urlAjaxMediaUpload    = '<?= $this->url('ajax_media_upload'); ?>'
	</script>
	<?= $this->section('js'); ?>
</body>

</html>