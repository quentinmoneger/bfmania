<?php $this->layout('layout', ['title' => 'Se connecter']); ?>


<?php $this->start('main_content'); ?>
<section class="pb-5 pt-3">
	<div class="container">
		<div class="col col-md-6 col-xl-5 col-xxl-4 mx-auto py-4">

			<div class="text-center mb-5">
				<h1 class="title-page with-underline">Connexion</h1>
			</div>

			<div class="content">

				<?php if (isset($errors) && !empty($errors)) : ?>
					<div class="alert alert-danger mb-3" role="alert">
						<?= implode('<br>', $errors); ?>
					</div>
				<?php endif;?>

				<form method="post" class="pb-3">
					<div class="form-group">
						<label class="form-control-label sr-only" for="login">Identifiant</label>
						<input type="login" name="login" id="login" class="form-control form-control-lg" placeholder="Nom d'utilisateur ou adresse email" required>
					</div>

					<div class="form-group">
						<label class="form-control-label sr-only" for="password">Mot de passe</label>
						<input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Mot de passe" required>
					</div>

					<div class="form-group mt-4">
						<p class="font12 text-center">Vous avez oublié votre mot de passe ? <a href="<?= $this->url('users_forgot_password'); ?>" class="link-colored">Réinitialiser mon mot de passe</a></p>

						<button type="submit" id="register" class="btn btn-block btn-color-1 btn-rounded py-3">Se connecter</button>
					</div>

					<br>
					<p class="text-center">Vous n'avez pas encore de compte ? <a href="<?= $this->url('users_signup'); ?>" class="link-colored">S'inscrire</a></p>
				</form>

				<hr class="pb-4">
				<p class="font11">Si vous cliquez sur &laquo; Connexion avec Facebook &raquo; et que n'êtes pas encore utilisateur de <?= $w_site_name; ?>, vous serez inscrit, ce qui signifie que vous acceptez les <a href="#" class="link-colored">Conditions d'utilisation</a> de <?= $w_site_name; ?>.
			</div>

		</div>
	</div>

</section>
<?php $this->stop('main_content'); ?>