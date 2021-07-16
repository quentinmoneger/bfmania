<?php $this->layout('layout', ['title' => 'S\'inscrire']); ?>


<?php $this->start('main_content'); ?>
<section class="pb-5 pt-3">
	<div class="container">
		<div class="col col-md-6 col-xl-5 col-xxl-4 mx-auto py-4">

			<div class="text-center mb-5">
				<h1 class="title-page with-underline">Inscription</h1>
			</div>


			<div class="content">
				<h5 class="text-center mb-4 fw-300">Inscrivez-vous avec votre adresse email</h5>

				<?php if (isset($errors) && !empty($errors)) : ?>
					<div class="alert alert-danger mb-3 text-center" role="alert">
						<?= implode('<br>', $errors); ?>
					</div>
				<?php endif; ?>

				<form method="post" class="pb-3">
					<div class="form-group">
						<label class="form-control-label sr-only" for="login">Email</label>
						<input type="login" name="email" id="email" class="form-control" value="<?= $post['email'] ?? ''; ?>" placeholder="votre@email.fr" required>
					</div>

					<div class="form-group">
						<label class="form-control-label sr-only" for="username">Pseudo</label>
						<input type="text" name="username" id="username" class="form-control" value="<?= $post['username'] ?? ''; ?>" placeholder="Nom d'utilisateur (entre 3 et 15 caractères)" required>
					</div>

					<div class="form-group">
						<label class="form-control-label sr-only" for="password">Mot de passe</label>
						<input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe (minimum 8 caractères)" required>
					</div>
					<div class="form-group">
						<label class="form-control-label sr-only" for="password_confirm">Confirmation de mot de passe</label>
						<input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirmez votre mot de passe" required>
					</div>

					<div>
						<input type="checkbox" id="newsletter" name="newsletter" checked>
						<label for="newsletter">Inscription a la newsletter.</label>
					</div>

					<div class="d-flex justify-content-center pt-3">
						<script src="https://www.google.com/recaptcha/api.js" async defer></script>
						<div class="g-recaptcha" data-sitekey="6Ldss7oUAAAAANP4xVISlKFSFEBg6ce5EX2KQrt0"></div>
					</div>

					<div class="form-group mt-5">
						<p class="font12">En cliquant sur S'inscrire, vous acceptez les <a href="#" class="link-colored">Conditions d'utilisation</a> de <?= $w_site_name; ?>.</p>

						<button type="submit" id="register" class="btn btn-block btn-first btn-rounded py-3">S'inscrire</button>
					</div>

					<br>
					<hr>
					<p class="text-center">Vous avez déjà un compte ? <a href="<?= $this->url('users_login'); ?>" class="link-colored">Connexion</a></p>
				</form>

			</div>

		</div>
	</div>

</section>
<?php $this->stop('main_content'); ?>