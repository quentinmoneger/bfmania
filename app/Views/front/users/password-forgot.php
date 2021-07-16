<?php $this->layout('layout', ['title' => 'Mot de passe oublié ?']);?>


<?php $this->start('main_content');?>
<section class="pb-5 pt-3">
	<div class="container">
		<div class="col col-md-6 col-xl-5 col-xxl-4 mx-auto py-4">

			<div class="text-center mb-5">
				<h1 class="title-page with-underline">Mot de passe oublié ?</h1>
			</div>

			<div class="content">

				<?php if(count($errors) > 0):?>
					<div class="alert alert-warning mb-3" role="alert">
						<?=implode('<br>', $errors); ?>
					</div>
				<?php elseif($success):?>
					<div class="alert alert-success mb-3" role="alert">
						Un email vous a été envoyé !
					</div>	
				<?php endif;?>
				
				<form method="post" class="py-3">
					<p class="font12">Saisissez l'adresse mail associée à votre compte <?=$w_site_name;?>.
					<br>Nous allons envoyer à cette adresse un lien vous permettant de réinitialiser facilement votre mot de passe.</p>

					<div class="form-group mt-5">
						<label class="form-control-label sr-only" for="login">Identifiant</label>
						<input type="login" name="login" id="login" class="form-control form-control-lg" placeholder="Saisissez votre adresse email"  value="<?=$post['login'] ?? '';?>" required>
					</div>

					<div class="form-row mt-4">
						<button type="submit" id="register" class="btn btn-block btn-color-1 btn-rounded py-2">Demander un nouveau mot de passe</button>
					</div>

					<p class="text-center mt-5">
						<a href="<?=$this->url('users_login');?>" class="link-colored">
							<i class="fal fa-user-alt fa-fw"></i> Se connecter
						</a>
					</p>
					<hr class="mt-2 pb-0">
					<p class="text-center">
						<br>Vous n'avez pas encore de compte ? <a href="<?=$this->url('users_signup');?>" class="link-colored">S'inscrire</a>
					</p>
				</form>

			</div>

		</div>
	</div>

</section>
<?php $this->stop('main_content');?>