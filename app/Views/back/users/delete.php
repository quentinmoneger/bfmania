<?php $this->layout('layout_back', ['title' => 'Suppression d\'un utilisateur']);
$avatar = (!empty($user['avatar'])) ? $user['avatar'] : $this->assetUrl('/img/nophoto.jpg');
?>

<?php $this->start('main_content'); ?>
<h1 class="title font-weight-normal"><i class="fad fa-user-alt"></i> Suppression d'un utilisateur</h1>


<div class="row justify-content-center">
	<div class="col-12">
		<div class="alert alert-light border clearfix" role="alert">
			Suppression d'un utilisateur
			<span class="float-right">
				<a href="<?= $this->url('back_users_list'); ?>" class="btn btn-outline-dark btn-sm">
					<i class="far fa-sm fa-history"></i> Retour à la liste des utilisateur
				</a>
			</span>
		</div>
	</div>
</div>


<div class="row justify-content-center">
	<div class="col-12">
		<div class="bg-white border p-4">
			<div class="row no-gutters justify-content-center">
				<div class="col-auto">
					<div class="alert alert-danger px-5 clearfix" role="alert">
						<i class="far fa-exclamation-triangle fa-2x float-left mr-5"></i> La suppression d'un utilisateur est <u>définitive</u>.
					</div>
					<form method="post" class="text-center pt-5">
						<p>Êtes-vous sûr de vouloir supprimer l' utilisateur ci-dessous ?</p>

						<div class="row justify-content-center align-items-end">
							<div class="col-5 pr-4 ">
								<div class="user-image  p-3 ml-auto" style="width: 190px; height: 190px;">
									<img id="imgAvatar" src="<?= $avatar; ?>" class="card-img-top rounded-circle border border-secondary" alt="Votre avatar" style="width: 170px; height: 170px;">
								</div>
							</div>
							<div class="col-7 border-left border-secondary pl-4 text-left">

								<div><span class="text-muted">Pseudo : </span><?= $user['username'] ?></div>
								<div><span class="text-muted">Email : </span><?= $user['email'] ?></div>
								<div><i class="fab fa-facebook-square fa-lg text-muted"></i><span class="text-muted"> : </span><?= $user['facebook_id'] ?></div>
								<h3><span class="text-muted font16">Role : </span><span><?= tools\Utils::getHumanRole($user['role'], false, true) ?></span></h3>
								<div class="font15"><span class="text-muted">Date d'incription :</span> <?= $user['date_registered']; ?></div>
								<div class="font15"><span class="text-muted">Dernière connexion :</span> <?= $user['date_connect_prev']; ?></div>
							</div>
							<div class="mt-5">
								<a href="<?= $this->url('back_users_list'); ?>" class="btn btn-secondary">Annuler</a>
								&nbsp; <button type="submit" name="delete" value="yes" class="btn btn-first">Oui, supprimer l' utilisateur</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

</div>

<?php $this->stop('main_content'); ?>