<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<title><?= $this->e($title . ' - ' . $w_site_name); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="axessweb.io">
	<meta name="description" content="<?= $meta_description ?? ''; ?>">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?= $w_site_name; ?>">
	<meta property="og:title" content="<?= $this->e($title . ' - ' . $w_site_name); ?>">
	<?php if (isset($og_image) && !empty($og_image)) : ?>
		<meta property="og:image" content="<?= $protocol . '://' . $_SERVER['HTTP_HOST'] . $og_image; ?>">
		<meta property="og:image:url" content="<?= $protocol . '://' . $_SERVER['HTTP_HOST'] . $og_image; ?>">
	<?php endif; ?>
	<?php if (isset($og_url) && !empty($og_url)) : ?>
		<meta property="og:url" content="<?= $protocol . '://' . $_SERVER['HTTP_HOST'] . $og_url; ?>">
	<?php endif; ?>

	<link rel="dns-prefetch" href="//fonts.googleapis.com">
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<!-- CSS Local Library -->
	<link rel="stylesheet" href="<?= $this->assetUrl('libs/bootstrap-4.3.1-custom/css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="<?= $this->assetUrl('libs/bootstrap-sweetalert/sweetalert.min.css'); ?>">
	<link rel="stylesheet" href="<?= $this->assetUrl('fontawesome-pro-5.15.2-web/css/all.css'); ?>">
	<link rel="stylesheet" href="<?= $this->assetUrl('libs\EmojiPopper\dist\css\emojiPopper.min.css'); ?>">
	<!-- CSS CDN Library -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.css" integrity="sha256-M8o9uqnAVROBWo3/2ZHSIJG+ZHbaQdpljJLLvdpeKcI=" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster&display=swap">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Vibes&display=swap">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700,900&display=swap">
	<!-- CSS Internal -->
	<link rel="stylesheet" href="<?= $this->assetUrl('css/styles.css') . '?v=' . rand(); ?>">
	<?= $this->section('css'); ?>

</head>

<body>
	<header>
		<nav class="navbar navbar-expand-lg fixed-top">
			<div class="container">
				<div class="brand d-flex h-100">
					<a href="<?= $this->url('default_home'); ?>" class="brand-title" title="<?= $w_site_name; ?>">
						<span class="firstletter">bf</span><span>mania</span>
					</a>
				</div>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
					<?php if ($countUnreadMessage > 0) : ?>
						<span class="toggleMessage rounded-circle" title="<?= $countUnreadMessage; ?> <?= ($countUnreadMessage <= 1) ? 'nouveau message' : 'nouveaux messages'; ?>">
							<?php echo $countUnreadMessage; ?>
						</span>
					<?php endif ?>
				</button>
				<div class="collapse navbar-collapse bg-deepdark" id="navbarNavDropdown">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a href="<?= $this->url('default_home'); ?>" class="nav-link">Accueil</a>
						</li>
						<li class="nav-item dropdown bg-deepdark">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLinkGame" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Nos jeux
							</a>
							<div class="dropdown-menu bg-deepdark" aria-labelledby="navbarDropdownMenuLinkGame">
								<a href="<?= $this->url('default_play_game', ['action' => 'belote']); ?>" class="nav-link">Belote</a>
								<a href="<?= $this->url('default_play_game', ['action' => 'tarot']); ?>" class="nav-link">Tarot</a>
								<a href="<?= $this->url('default_play_game', ['action' => 'coinche']); ?>" class="nav-link">Coinche</a>
							</div>
						</li>
						<li class="nav-item">
							<a href="<?= $this->url('list_forum') ?>" class="nav-link">Forum</a>
						</li>
						<li class="nav-item dropdown bg-deepdark">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLinkUser" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fal fa-user-alt fa-fw"></i>
							</a>
							<?php if ($countUnreadMessage > 0) : ?>
								<span class="notifMessage rounded-circle" title="<?= $countUnreadMessage; ?> <?= ($countUnreadMessage <= 1) ? 'nouveau message' : 'nouveaux messages'; ?>">
									<?php echo $countUnreadMessage; ?>
								</span>
							<?php endif ?>
							<div class="dropdown-menu bg-deepdark" aria-labelledby="navbarDropdownMenuLinkUser">
								<?php if ($w_user) : ?>
									<?php (isset($countUnreadMessage)) ? $countUnreadMessage : $countUnreadMessage = 0 ?>
									<a href="<?= $this->url('users_account'); ?>" class="nav-link"><i class="fal fa-user-alt fa-fw"></i> <?= $w_user['username']; ?></a>
									<a href="<?= $this->url('messages_home'); ?>" id="navMsg" class="nav-link">
										<i class="fal fa-comment-alt fa-fw"></i> messagerie
										<?php if ($countUnreadMessage > 0) : ?>
											<span class="newMessage rounded-circle" title="<?= $countUnreadMessage; ?> <?= ($countUnreadMessage <= 1) ? 'nouveau message' : 'nouveaux messages'; ?>">
												<?php echo $countUnreadMessage; ?>
											</span>
										<?php endif ?>
									</a>
									<?php if ($w_user['role'] >= 30) : ?>
										<a href="<?= $this->url('back_dashboard'); ?>" class="nav-link">
											<i class="fal fa-shield-alt fa-fw"></i> console
										</a>
									<?php endif; ?>
									<a href="<?= $this->url('users_logout'); ?>" class="nav-link">
										<i class="fal fa-sign-out fa-fw"></i> déconnexion
									</a>
								<?php else : ?>
									<a href="<?= $this->url('users_login'); ?>" class="nav-link">
										<i class="fal fa-user-alt fa-fw"></i> connexion
									</a>
									<a href="<?= $this->url('users_signup'); ?>" class="nav-link">
										<i class="fal fa-sign-in fa-fw"></i> s'inscrire
									</a>
								<?php endif; ?>
							</div>
						</li>


					</ul>
				</div>
			</div>
		</nav>
	</header>

	<main>
		<?php if (!empty($w_flash_message) && isset($flash_custom) == false) : ?>
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-xxl-10">
						<?php foreach ($w_flash_message as $flash_msg) : ?>
							<div class="alert alert-<?= $flash_msg['level']; ?> alert-dismissible alert-custom" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
								<?= $flash_msg['message']; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>


		<?= $this->section('main_content') ?>
	</main>
	<footer id="footer">
		<div class="container h-100 w-100">
			<div class="row h-100 justify-content-between align-items-center">
				<div class="col-sm-4  col-md-3 d-flex flex-column h-100">
					<p class="copyright-brand"><?= $w_site_name; ?></p>
					<p class="copyright-baseline">
						&copy; 2009 - <?= date('Y'); ?>
						by <img src="<?= $this->assetUrl('img/copyright/axessweb_bottom.png'); ?>" alt="axessweb" title="axessweb" class="mt-n2"> & Co.
					</p>
				</div>
				<div id="newsletters-sub" class="d-none d-sm-block col-sm-5 col-md-3 px-5">
					<?= $this->insert('front/_partials/subscribe-newsletter') ?>
				</div>
				<div class="d-none d-sm-block col-sm-3 col-md-2 linkutil ">
					<div class="text-uppercase utilink d-inline-block">Liens utiles</div>
					<ul class="list-unstyled  mb-0 font13">
						<li class="pb-1">
							<a href="#" class="text-light">A propos</a>
						</li>
						<li class="pb-1">
							<a href="<?= $this->url('user_staff_show'); ?>" class="text-light">L'équipe BFmania</a>
						</li>
						<li class="pb-1">
							<a href="<?= $this->url('default_page', ['url' => 'mentions-legales']); ?>" class="text-light">Mentions légales</a>
						</li>
						<li class="pb-1">
							<a href="<?= $this->url('default_faq_home'); ?>" class="text-light">Foire aux questions</a>
						</li>
						<li class="pb-1">
							<a href="<?= $this->url('default_page', ['url' => 'conditions-generales']); ?>" class="text-light">Conditions d'utilisation</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<!-- Modal de signalement -->
	<div class="modal fade" id="report" tabindex="-1" role="dialog" aria-labelledby="modalReportLabel" aria-hidden="true">
		<div class="modal-dialog modal-report modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header ">
					<h5 class="modal-title" id="modalReportLabel">Signaler le message</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form>
						<div class="form-group">
							<h7>M'informer:</h7>
							<input type="radio" class="inform_report" name="inform_report" value="1" checked>
							<label class="mb-0" for="no">Oui</label>
							<input type="radio" class="inform_report" name="inform_report" value="0" checked>
							<label class="mb-0" for="yes">Non</label><br>
							<small class="text-muted">Vous informer quand votre signalement a été traité.</small>
						</div>
						<div class="form-group">
							<label for="report-message" class="col-form-label">Message:</label>
							<textarea id="report-message" value="" class="form-control "></textarea>
						</div>
					</form>
					<p class="text-center">Souhaitez-vous vraiment signaler le message de <span id="modal-username-from"></span> ? </p>
				</div>
				<div class="modal-footer">
					<button id="modal-report-type" value="" type="text" hidden>
						<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
						<button type="submit" value="" class="button-report btn btn-first text-light">Oui, signaler</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="unsubscribe" tabindex="-1" role="dialog" aria-labelledby="modalUnsubscribeLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header ">
					<h5 class="modal-title" >Se désinscire a la newsletters</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="alert"></div>
					<div id="form-newsletter-unsubscribe" action="<?= $this->url('newsletters_unsubscribe_submit'); ?>">
						<input type="login" name="email" id="email" class="form-control" placeholder="votre@email.fr">
					</div>
					<div class="modal-footer">
						<button type="text" hidden>
							<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
							<button id="unsubscribe-button" class="btn btn-first text-light">Oui, se désinscrire</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Js CDN Library -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js" integrity="sha256-bQTfUf1lSu0N421HV2ITHiSjpZ6/5aS6mUNlojIGGWg=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.2/socket.io.js"></script>
		<!-- Js Local Library -->
		<script src="<?= $this->assetUrl('libs/bootstrap-4.3.1-custom/js/bootstrap.bundle.min.js'); ?>"></script>
		<script src="<?= $this->assetUrl('libs/bootstrap-sweetalert/sweetalert.min.js'); ?>"></script>
		<script src="<?= $this->assetUrl('libs/EmojiPopper/dist/js/emojiPopper.js'); ?>" defer></script>

		<!-- Js Internal -->
		<script src="<?= $this->assetUrl('js/front/newsletters/subscribe.js'); ?>"></script>
		<script src="<?= $this->assetUrl('js/front/newsletters/unsubscribe.js'); ?>"></script>
		<script src="<?= $this->assetUrl('js/front/main.js'); ?>"></script>
		<?= $this->section('js'); ?>
		<script>
			var urlReport = '<?= $this->url('back_report_message'); ?>',
				urlBackMediasCkeditor = '<?= $this->url('back_medias_ckeditor'); ?>?action=ckeditor',
				urlReadUnread = '<?= $this->url('messages_read_message'); ?>',
				urlWriteMessage = '<?= $this->url('messages_write_message'); ?>',
				urlReplyAllMessage = '<?= $this->url('messages_reply_all_message'); ?>',
				urlReplyMessage = '<?= $this->url('messages_reply_message'); ?>',
				urlAutoCompleteMessage = '<?= $this->url('messages_autocomplete_message'); ?>',
				urlDeleteMessage = '<?= $this->url('messages_delete_message'); ?>'
		</script>
		<script src="<?= $this->assetUrl('js/front/report/report.js'); ?>"></script>
</body>

</html>