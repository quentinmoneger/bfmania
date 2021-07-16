<?php

$title = 'Avertir un utilisateur';

?>

<?php $this->layout('layout_back', [
	'title' => $title,
]);?>

<?php $this->start('main_content');?>
<h1 class="title font-weight-normal"><i class="fad fa-user-alt"></i> <?=$title;?></h1>

<div class="row justify-content-center">
	<div class="col-12">
		<div class="alert alert-light border clearfix" role="alert">
			<?=$title;?>
			<span class="float-right">
				<a href="<?=$this->url('back_users_list');?>" class="btn btn-outline-dark btn-sm">
					<i class="far fa-sm fa-history"></i> Retour à la liste des utilisateurs
				</a>
			</span>
		</div>
	</div>
</div>

<div>
	<p>Envoi d'un avertissement pour : <?= $user['username'] ?></p>
	<?php if(!empty($errors)):?>
		<div class="alert alert-danger mb-3" role="alert">
			<?=implode('<br>', $errors);?>
		</div>
	<?php endif;?>
	<form id="form" method="post">
		<div class="no-gutters">
			<div class="col-6 my-2">
				<label>Veuillez sélectionner le motif de l'avertissement :</label>
				<select id="select" required name="motif" class="form-control">
					<option value="0" selected disabled>Sélectionnez</option>
					<option value="1">Parties</option>
					<option value="2">Provocations</option>
					<option value="3">Insultes</option>
					<option value="4">Flood</option>
					<option value="5">Publicité</option>
				</select>
			</div>
			<div class="col-6 my-2">
				<div class="d-flex flex-column">
					<label>Description de l'avertissement :</label>
					<textarea id="text" name="text" class="form-control" rows="5"></textarea>
				</div>
				<input class="my-2 btn-outline-dark btn-sm"  data-toggle="modal" type="submit" id="valid" name="valid" value="Valider">
			</div>
		</div>


	</form>



	<?php $this->stop('main_content');?>

	<?php $this->start('js');?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

	<!-- jQuery Modal -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
	<script src="<?= $this->assetUrl('js/back/users/warning.js'); ?>"></script>
	<?php $this->stop('js');?>

