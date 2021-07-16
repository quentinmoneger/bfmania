<?php

$title = ($form == 'add') ? 'Ajout d\'un nouvel utilisateur' : 'Modification d\'un utilisateur';
$avatar 	=  $this->assetUrl('/img/nophoto.jpg');
$this->layout('layout_back', ['title' => $title,]);
?>

<?php $this->start('main_content'); ?>

<h1 class="title font-weight-normal"><i class="fad fa-user-alt"></i> <?= $title; ?></h1>

<div class="row justify-content-center">
	<div class="col-12">
		<div class="alert alert-light border clearfix" role="alert">
			<?= $title; ?>
			<span class="float-right">
				<a href="<?= $this->url('back_users_list'); ?>" class="btn btn-outline-dark btn-sm">
					<i class="far fa-sm fa-history"></i> Retour à la liste des utilisateurs
				</a>
			</span>
		</div>
	</div>
</div>

<br>
<div class="row justify-content-center">
	<div class="col-12">
		<div class="bg-white border p-4">

			<div class="row justify-content-center">
				<div class="col-auto pr-4 ml-lg-auto mb-3">

					<div class="user-image  p-3" style="width: 230px; height: 200px;">
						<img id="imgAvatar" src="<?= $avatar; ?>" class="card-img-top rounded-circle border border-secondary" alt="Votre avatar" style="width: 180px; height: 180px;">
					</div>
				</div>
				<div class="col-lg-4 border-left border-secondary mr-auto">

					<?php if (!empty($errors)) : ?>
						<div class="alert alert-danger mb-3" role="alert">
							<?= implode('<br>', $errors); ?>
						</div>

					<?php endif; ?>

					<div class="">

						<form method="post" runat="server" enctype="multipart/form-data">
							<div class="container mt-2">
								<div class=" form-group">
									<label for="firstname">Nom d'utilisateur</label>
									<input type="text" name="username" id="username" class="form-control form-control-sm" value="" placeholder="Jean" required>
								</div>

								<div class="form-group">
									<label for="email">Adresse email</label>
									<input type="email" name="email" id="email" class="form-control form-control-sm" value="" placeholder="email@example.org" required>
								</div>

								<div class="form-group">
									<label for="avatar">Facebook id</label>
									<input type="text" name="facebook_id" id="facebook_id" class="form-control form-control-sm" value="">
								</div>

								<div class="form-group">
									<label for="role">Rôle</label>
									<select name="role" id="role" class="form-control form-control-sm" required>
										<option>-- Sélectionnez --</option>
										<?php foreach (\Tools\Utils::listRoles() as $key => $value) : ?>
											<option value="<?= $key; ?>"><?= $value; ?></option>
										<?php endforeach; ?>
									</select>
									<small class="form-text">
										<a href="#" data-toggle="modal" data-target="#usersRolesInfo" class="text-muted"><i class="fas fa-info-circle"></i> Informations sur les différents rôles utilisateurs</a>
									</small>
								</div>

								<div class="form-group mt-2">
									<div class="custom-file">
									  <input type="file" class="custom-file-input" id="avatar" accept="image/*">
									  <label class="custom-file-label" for="avatar" data-browse="Choisir" >Photo de profil</label>
									</div>
								
                                    <div id="upload-img" class="collapse mt-3">
                                    </div>
	                             	<input hidden type="text" name="resultCrop" id="resultCrop">
								</div>

								<div class="form-group">
									<label for="password">Mot de passe</label>
									<input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Mot de passe" required>
									<small class="form-text text-muted">Le mot de passe doit comporter entre 8 et 20 caractères</small>
								</div>
								<div class="form-group">
									<label for="password_confirm">Confirmation mot de passe</label>
									<input type="password" name="password_confirm" id="password_confirm" class="form-control form-control-sm" placeholder="Confirmez votre mot de passe" required>
								</div>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-first btn-sm px-5">Enregistrer</button>
								</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
</div>
</div>


<!-- Modal -->
<!-- @todo: remplir l'explication des rôles -->
<div class="modal fade" id="usersRolesInfo" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Rôles utilisateurs</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="">
					<ul class="font13">
						<li><strong>Administrateur : </strong><i>Lorem ipsum dolor sit amet</i> </li>
						<li><strong>Manager : </strong><i>Lorem ipsum dolor sit amet</i> </li>
						<li><strong>Salarié : </strong> <i>Lorem ipsum dolor sit amet</i> </li>
						<li><strong>Editeur : </strong> <i>Lorem ipsum dolor sit amet</i> </li>
					</ul>
				</div>
				<div class="text-right">
					<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->stop('main_content'); ?>
<?php $this->start('js'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js" integrity="sha256-bQTfUf1lSu0N421HV2ITHiSjpZ6/5aS6mUNlojIGGWg=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script src="<?= $this->assetUrl('js/back/users/add.js') ?>"></script>
<?php $this->stop('js'); ?>