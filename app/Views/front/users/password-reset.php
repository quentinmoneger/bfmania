<?php $this->layout('layout', ['title' => 'Réinitialisation du mot de passe']); ?>


<?php $this->start('main_content'); ?>
<section class="pb-5 pt-3">
	<div class="container">
		<div class="col col-md-6 col-xl-5 col-xxl-4 mx-auto py-4">

			<div class="text-center mb-5">
				<h1 class="title-page with-underline">Réinitialisation du mot de passe</h1>
			</div>
			<div class="content">

				<?php if ($tokenExists == false) : ?>
					<div class="alert alert-danger mb-3 text-center" role="alert">
						Veuillez cliquer sur le lien qui vous a été envoyé par email
					</div>
				<?php elseif ($tokenExists) : ?>
					<?php if (count($errors) > 0) : ?>
						<div class="alert alert-danger mb-3" role="alert">
							<?= implode('<br>', $errors); ?>
						</div>
					<?php elseif ($success) : ?>
						<div class="alert alert-success mb-3" role="alert">
							Votre mot de passe a été modifié avec succès !
						</div>
					<?php endif; ?>
					<form method="post" class="py-3">
						<p class="font12">Saisissez votre nouveau mot de passe.</p>

						<div class="form-group mt-5">
							<label class="form-control-label sr-only" for="password">Mot de passe</label>
							<input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Nouveau mot de passe" required>
						</div>
						<div class="form-group">
							<label class="form-control-label sr-only" for="password_confirm">Confirmation de mot de passe</label>
							<input type="password" name="password_confirm" id="password_confirm" class="form-control form-control-lg" placeholder="Confirmez votre mot de passe" required>
						</div>

						<div class="form-row mt-4">
							<button type="submit" id="register" class="btn btn-block btn-color-1 btn-rounded py-2">Réinitialiser mon mot de passe</button>
						</div>

						<p class="text-center mt-5">
							<a href="<?= $this->url('users_login'); ?>" class="link-colored">
								<i class="fal fa-user-alt fa-fw"></i> Se connecter
							</a>
						</p>
						<hr class="mt-2 pb-0">
						<p class="text-center">
							<br>Vous n'avez pas encore de compte ? <a href="<?= $this->url('users_signup'); ?>" class="link-colored">S'inscrire</a>
						</p>
					</form>
				<?php endif; ?>

			</div>

		</div>
	</div>

</section>
<?php $this->stop('main_content'); ?>