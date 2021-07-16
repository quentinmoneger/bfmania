<link rel="stylesheet" href="<?= $this->assetUrl('css/front/sidebar-account.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<aside class="col-12 m-2 col-xl-3 account-sidebar rounded border border-secondary">
	<div class="row">
		<div class="avatar col-5 m-auto col-xl-12">
			<div class="user-image">
				<?php $avatar = (!empty($w_user['avatar'])) ? $w_user['avatar'] : $this->assetUrl('/img/nophoto.jpg'); ?>
				<a href="<?= ($w_current_route === 'users_account_modified') ? '#uploadImageModal' : ''; ?>" role="button" data-toggle="modal">
					<abbr title="Changer votre avatar">
						<img id="imgside" src="<?= $avatar; ?>" class="card-img-top rounded-circle" alt="Votre avatar">
					</abbr>
				</a>				
			</div>
			<p class="card-text text-center text-light font18"><?= $w_user['username'] ?></p>
			
		</div>
		<ul class="user-nav col-12 col-md-7 col-xl-12 pl-3 pr-3 m-0 border-0">
			<li class="<?= ($w_current_route === 'users_account') ? 'active' : ''; ?>"><a href="<?= $this->url('users_account') ?>"><i class="fad fa-home"></i> Vue d'ensemble du compte</a></li>
			<li class="<?= ($w_current_route === 'users_account_modified') ? 'active' : ''; ?>"><a href="<?= $this->url('users_account_modified') ?>"><i class="fad fa-address-card"></i> Modifier mon profil</a></li>
			<li class="<?= ($w_current_route === 'users_account_password') ? 'active' : ''; ?>"><a href="<?= $this->url('users_account_password') ?>"><i class="fad fa-key"></i> Modifier mon mot de passe</a> </li>
			<li class="<?= ($w_current_route === 'messages_home' || $w_current_route === 'messages_write' || $w_current_route === 'messages_read') ? 'active' : ''; ?>"><a href="<?= $this->url('messages_home') ?>"><i class="fad fa-mask"></i> Messagerie privée </a></li>
		</ul>
	</div>
</aside>

<!-- Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="hidetitle" class="row justify-content-center pt-4">
					<h4 class="text-center">Cliquer sur votre avatar<br> pour en changer</h4>
				</div>
				<div class="row justify-content-center">
					<label id="imgpresent">
						<?php $avatar = (!empty($w_user['avatar'])) ? $w_user['avatar'] : $this->assetUrl('/img/nophoto.jpg'); ?>
						<img id="avatarimg" src="<?= $avatar; ?>" class="card-img-top rounded-circle p-2" alt="Votre avatar">
						<input style='display: none;' type="file" name="upload_image" id="upload_image" accept="image/*" />
					</label>
				</div>
				<div class="row justify-content-center">
					<div class="mt-4" id="image_demo" style="display: none;" style="width:350px; margin-top:30px"></div>
				</div>
				<div class="row justify-content-center">
					<button type="button" id="buttonupload" data-dismiss="modal" style="display: none;" class="btn btn-success crop_image">Enregistrer</button>
				</div>
			</div>

		</div>
	</div>
</div>