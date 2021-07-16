	<?php

$title = ($form == 'add') ? 'Ajout d\'un nouvel utilisateur' : 'Modification d\'un utilisateur';
$username	= $post['username'] ?? '';
$email  	= $post['email'] ?? '';
$role  		= $post['role'] ?? '';
$avatar 	= (!empty($post['avatar'])) ? $post['avatar'] : $this->assetUrl('/img/nophoto.jpg');
$facebook_id = $post['facebook_id'] ?? ''; 
$warning_date_expire = \Tools\Utils::dateFr(strtotime('+ 45 days'), 'd/m/Y \à H:i');
$count_banish = (count($banish));
$count_warning = (count($warning));
if($banish){
	$btn_banish = 'btn-warning';
	foreach ($banish as $value) {
		if (strtotime($value['date_expire']) > time()){
			$btn_banish = 'btn-danger';
		}
	}
} else {
	$btn_banish = 'btn-success';
}
if($warning){
	$btn_warning = 'btn-warning';
	foreach ($warning as $value) {
		if (strtotime($value['date_expire']) > time()){
			$btn_warning = 'btn-danger';
		}
	}
} else {
	$btn_warning = 'btn-success';
}

$this->layout('layout_back', ['title' => $title,]);
?>

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

<div class="row justify-content-center">
	<div class="col-12">
		<div class="bg-white border p-4">	
			<div class="row justify-content-center align-content-center">
				<!-- AVATAR  &  ACTIONS-->	
				<div class="col-md-6 col-lg-4 pr-4 ml-auto mb-3">
					<div class="user-image  p-3 ml-auto" style="width: 230px; height: 200px;">
						<img id="imgAvatar" src="<?= $avatar; ?>" class="card-img-top rounded-circle border border-secondary" alt="Votre avatar" style="width: 180px; height: 180px;">
					</div>

					<div class="bg-light px-2 py-1 mt-3  ml-auto rounded" style="max-width: 257px;">
						<?php if(in_array($w_user['role'], [70, 99])):?>
						<div class="text-center ">
							<?php if($w_user['id'] != $post['id']):?>
							<a class="btn btn-sm btn-outline-secondary py-0 btn-edit-user m-1" role="button" href="<?=$this->url('back_users_delete',['id'=>$post['id']])?>"><i class="text-danger fas fa-times mr-1" aria-hidden="true"></i> Supprimer</a>
							<?php endif;?>
							<button class="btn btn-sm py-0 btn-outline-secondary btn-edit-user m-1" type="button" data-toggle="collapse" data-target=".modify" aria-expanded="false" aria-controls="editUsername" aria-pressed="false" autocomplete="off"><i class="fas fa-edit mr-1" aria-hidden="true"></i> Modifier
							</button>
						</div>
						<?php endif;?>
						<?php if($w_user['id'] != $post['id']):?>
						<div class="text-center">
							<button id="btn-warning" class="btn btn-sm py-0 btn-outline-secondary btn-edit-user m-1" type="button" data-toggle="collapse" data-target=".warning, #bda" aria-expanded="false"><i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i> alerter
							</button>
							<button id="btn-banish" class="btn btn-sm py-0 btn-outline-secondary btn-edit-user m-1 <?=($btn_banish == 'btn-danger')?' disabled ' : ''?>" type="button" <?=($btn_banish == 'btn-danger')?'' : ' data-toggle="collapse" data-target=".banish, #bda" '?> aria-expanded="false"><i class="fas fa-radiation mr-1" aria-hidden="true"></i> Bannir
							</button>
						</div>
						<?php endif;?>
						<div class="text-center">
							<button id="btn-note" class="btn btn-sm py-0 btn-outline-secondary btn-edit-user m-1" type="button" data-toggle="collapse" data-target=".notes, #bda" aria-expanded="false"><i class="fad fa-sticky-note mr-1" aria-hidden="true"></i></i> Notes <small id="nbNote"></small>
							</button>
						</div>
					</div>
				</div>
				<!-- PROFIL -->
				<div class="col-md-6 col-lg-4 border-left border-right border-secondary mr-auto mb-2">

					<?php if(!empty($errors)):?>
						<div class="alert alert-danger mb-3" role="alert">
							<?=implode('<br>', $errors);?>
						</div>
					<?php endif;?>
					<!-- show profile -->
					<div class="collapse show modify">
						<div>
							<span class="text-muted ">Pseudo : </span><?=$post['username']?><small> [<span class="font12">UID:</span> <?=$post['id']?>] </small>
						</div>
						<div><span class="text-muted">Email : </span><?=$post['email']?></div>
						<?php if($post['facebook_id']) : ?>
						<div><i class="fab fa-facebook-square fa-lg text-muted"></i><span class="text-muted"> : </span><?=$post['facebook_id']?></div>
						<?php endif;?>
						<h3><span class="text-muted font16">Role : </span><span><?=\Tools\Utils::getHumanRole($post['role'],false,true)?></span></h3>
						<div class="font15"><span class="text-muted">Date d'incription :</span> <?=\Tools\Utils::dateFr($post['date_registered'], 'd/m/Y \à H:i');?></div>
						<div class="font15"><span class="text-muted">Dernière connexion :</span> <?=\Tools\Utils::dateFr($post['date_connect_prev'], 'd/m/Y \à H:i');?></div>
						<div class="my-2">
							<button <?=($btn_warning == 'btn-success')? '' : ' data-toggle="modal" data-target="#warningInfo" '?> role="button" id="warningBtn" class="btn btn-sm py-0 mr-2 <?=$btn_warning?>">Alerte : <span><?=$count_warning?></span></button>
							<button <?=($btn_banish == 'btn-success')? '' : ' data-toggle="modal" data-target="#banishInfo" '?> role="button" id="banishBtn" class="btn btn-sm py-0 mr-2 <?=$btn_banish?>">Bannisement : <span><?=$count_banish?></span></button>
						</div>&nbsp;
						<div><span class="text-muted">Adresse Ip : </span><?=$post['ip_address']?></div>
						<div class="my-2">
							<?=(count($multinick) < 2) ? '' : '<span class="text-muted">Multinick&nbsp;: </span>'?>
							<?php foreach ($multinick as  $user) :
								if ($user['id'] != $post['id']): ?>
								<a href="<?=$this->url('back_users_edit',['id'=>$user['id']])?>" role="button" class="btn btn-sm btn-outline-dark py-0 m-1"><?=$user['username']?><small>&nbsp;<span class="font12">UID:</span>'<?=$user['id']?>] </small></a>
							<?php endif; endforeach;?>
						</div>
					</div>
					<!-- edit profile -->
					<div class="collapse modify" id="editUser">
						<form method="post" runat="server" enctype="multipart/form-data">
							<div class="card card-body">
								<div class="form-group">
									<label for="firstname">Nom d'utilisateur</label>
									<input type="text" name="username" id="username" class="form-control form-control-sm" value="<?=$username;?>" placeholder="Jean" required>
								</div>

								<div class="form-group">
									<label for="email">Adresse email</label>
									<input type="email" name="email" id="email" class="form-control form-control-sm" value="<?=$email;?>" placeholder="email@example.org" required>
								</div>

								<div class="form-group">
									<label for="avatar">Facebook id</label>
									<input type="text" name="facebook_id" id="facebook_id" class="form-control form-control-sm" value="<?=$facebook_id;?>">
								</div>

								<div class="form-group">
									<label for="role">Rôle</label>
									<select name="role" id="role" class="form-control form-control-sm" required>
										<option>-- Sélectionnez --</option>
										<?php foreach(\Tools\Utils::listRoles() as $key => $value):?>
											<?php if(!in_array($key, [99])):?>
												<option value="<?=$key;?>" <?=($role == $key) ? 'selected' : '';?>><?=$value;?></option>
											<?php endif;?>
										<?php endforeach; ?>
									</select>
									<small class="form-text">
										<a href="#" data-toggle="modal" data-target="#usersRolesInfo" class="text-muted"><i class="fas fa-info-circle"></i> Informations sur les différents rôles utilisateurs</a>
									</small>
								</div>

								<div class="form-group mt-2">
									<div class="custom-file">
									  <input type="file" class="custom-file-input" id="avatar" accept="image/*">
									  <label class="custom-file-label" for="avatar" data-browse="Choisir" >Modifier la photo de profil</label>
									</div>
								
                                    <div id="upload-img" class="collapse mt-3">
                                    </div>
	                             	<input hidden type="text" name="resultCrop" id="resultCrop">
								</div>
								
								<div class="btn btn-sm btn-outline-secondary py-1 mb-3" data-toggle="collapse" data-target="#mdp">modifier le mot de passe</div>
								<div id="mdp" class="collapse">
									<div class="form-group">
										<label for="password">Mot de passe</label>
										<input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Mot de passe" autocomplete="new-password" <?=($form == 'add') ? 'required': '';?>>
										<small class="form-text text-muted">Le mot de passe doit comporter entre 8 et 20 caractères</small>
									</div>
									<div class="form-group">
										<label for="password_confirm">Confirmation mot de passe</label>
										<input type="password" name="password_confirm" id="password_confirm" class="form-control form-control-sm" placeholder="Confirmez votre mot de passe">
									</div>
								</div>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-first btn-sm px-5">Enregistrer</button>
								</div> 
							</div>
						</form>
					</div>
				</div>

				<!-- BANNISEMENT & AVERTISSEMENT FORM-->
				<div class="col-lg-4">
					<!-- BANNISEMENT -->
					<div class="collapse banish mb-2 mx-auto">
						<div class="card card-body" style="min-width: 400px">
							<div class="form-group">
								<label for="banishMotif">Motif du bannissement :</label>
								<select  id="banishMotif" name="banishMotif" motif" class="form-control form-control-sm" required>
									<option value="0" selected disabled>Sélectionnez</option>
									<option value="1">Parties</option>
									<option value="2">Provocations</option>
									<option value="3">Insultes</option>
									<option value="4">Flood</option>
									<option value="5">Publicité</option>
								</select>
							</div>

							<div class="form-group">
								<label for="textBanish">Message</label>
								<textarea class="form-control" id="textBanish" rows="5" name="textBanish" required data-placement="left" data-content="Veulliez remplir le message du bannissement !"></textarea>
								<div id="alert" class="alert alert-danger collapse" role="alert">
									Veulliez remplir le message du bannissement !
								</div>
							</div>

							<div class="form-group">
								<label for="date_expire">Jusqu'au</label>	
								<input type="date" id="date_expire" name="date_expire" min="<?=date('Y-m-d')?>" class="form-control form-control-sm" value="" required>
							</div>
							<div class="form-group text-right">
								<button id="banishSubmit" type="submit" class="btn btn-first btn-sm px-5">Bannir</button>
							</div> 
						</div>
					</div>
					<!-- AVERTISSEMENT -->
					<div class="collapse warning mb-2 mx-auto">
						<div class="card card-body" >
							<div class="form-group">
								<label for="warningMotif">Motif de l'avertissement :</label>
								<select name="warningMotif" id="warningMotif" class="form-control form-control-sm" required>
									<option value="0" selected disabled>Sélectionnez</option>
									<option value="1">Parties</option>
									<option value="2">Provocations</option>
									<option value="3">Insultes</option>
									<option value="4">Flood</option>
									<option value="5">Publicité</option>
								</select>
							</div>

							<div class="form-group">
								<label for="textWarning">Message</label>
								<textarea class="form-control" id="textWarning" rows="5" name="textWarning" required data-placement="left" data-content="Veulliez remplir le message d'avertissement' !"></textarea>
								<div id="alert" class="alert alert-danger collapse" role="alert">
									Veulliez remplir le message de l'avertissement !
								</div>
							</div>
							<div class="form-group text-right">
								<button id="warningSubmit" type="submit" class="btn btn-first btn-sm px-5" >Alerter</button>
							</div> 
						</div>
					</div>
				</div>

			</div>


				<!-- TABLE NOTES -->

				<div class="collapse notes mb-2 ml-n3 row">
					<div class="col-xl-4 text-right ">
						<button class="btn btn-first btn-sm mb-2 py-1 mr-xl-4" type="button" data-toggle="modal" data-target="#note" aria-expanded="false" style="width: 227px;"><i class="fas fa-pencil"></i> Ajouter une note
						</button>
						<div  id="alertNote" class="alert alert-danger collapse alert-dismissible alert-custom text-left" role="alert">
						</div>
						
					</div>
					<div class="col-xl-8 col-12 table-reponsive px-0">
						
						<table class="table table-hover table-sm">
							<thead id="table-note"class="bg-light">
								<th >#</th>
								<th>Notes</th>
								<th>Crée le</th>
								<th>ID auteur</th>
								<th>Action</th>
							</thead>
							<tbody id="tableNotes">
								
							</tbody>
						</table>
					</div>
				</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="warningInfo" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Alertes</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-sm border">
						<thead class="bg-light">
							<tr>
								<th>#</th>
								<th>Raison</th>
								<th>Depuis</th>
								<th>Expire</th>
								<th>Auteur</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($warning as $value): ?>
								<tr class="<?= (strtotime($value['date_expire']) > time())?' alert-danger ' : ' alert-warning '?>">
									<th class="font-weight-normal"><?=$value['id']?></th>
									<th class="font-weight-normal"><?=$value['reason']?></th>
									<th class="font-weight-normal"><?=\Tools\Utils::dateFr($value['date_create'], 'd/m/Y \à H:i');?></th>
									<th class="font-weight-normal"><?=\Tools\Utils::dateFr($value['date_expire'], 'd/m/Y \à H:i');?></th>
									<th class="font-weight-normal"><?= $value['author']; ?><small> [<span class="font10">UID:</span> <?=$value['id_author']?>] </small></th>
									<th><a href="<?= $this->url('back_warning_delete', ['id' => $value['id']]); ?>" class="red-text"><i class="fas fa-sm fa-times mr-1"></i><span class="font-weight-normal">Supprimer</span></a></th>

								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="banishInfo" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Bannissement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-sm border">
						<thead class="bg-light">
							<tr>
								<th>#</th>
								<th>Raison</th>
								<th>Depuis le</th>
								<th>Expire le</th>
								<th>Auteur</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($banish as $value): ?>
								<tr class="<?= (strtotime($value['date_expire']) > time())?' alert-danger ' : ' alert-warning '?>">
									<th class="font-weight-normal"><?=$value['id']?></th>
									<th class="font-weight-normal"><?=$value['reason']?></th>
									<th class="font-weight-normal"><?=\Tools\Utils::dateFr($value['date_create'], 'd/m/Y \à H:i');?></th>
									<th class="font-weight-normal"><?=\Tools\Utils::dateFr($value['date_expire'], 'd/m/Y \à H:i');?></th>
									<th class="font-weight-normal"><?= $value['author']; ?><small> [<span class="font10">UID:</span> <?=$value['id_author']?>] </small></th>
									<th><a href="<?= $this->url('back_banish_delete', ['id' => $value['id']]); ?>" class="red-text"><i class="fas fa-sm fa-times mr-1"></i><span class="font-weight-normal">Supprimer</span></a></th>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>	
<div class="modal fade" id="banishComfirm" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Confirmation du Bannissement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="rounded border border-secondary p-3 m-3">
					<p>Bonjour <?=$username?>,<br>
						Vous avez été bannis du site <?=$w_site_name?> jusqu'au <span id="confirmBanishDate"></span> par <?=$w_user['username']?> pour le motif suivant :</p>
					<p id="comfirmMotif" class="font-weight-bold" ></p>
					<p>Cordialement,<br>
						L'equipe Bfmania
					</p>
				</div>
				<div class="text-right">
					<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annuler</button>
					<form method="post" action="<?=$this->url('back_users_banish',['id'=>$post['id']])?>" class="d-inline">
						<input hidden type="text" id="username_author" name="username_author" value="<?=$w_user['username']?>">
						<input hidden type="text" name="hiddenDateExpire" id="hiddenDateExpire" class="form-control form-control-sm" required>
						<textarea hidden class="form-control" id="hiddenBanishMsg" rows="5" name="banishMsg"></textarea>
						<button type="submit" class="btn btn-first btn-sm">Confirmer</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="warningComfirm" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Confirmation de l'avertissement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="rounded border border-secondary p-3 m-3">
					<p>Bonjour <?=$username?>,<br>
						Vous avez été Avertis par <?=$w_user['username']?> pour le motif suivant :</p>
					<p id="comfirmWarningMotif"></p>
					<p>Cordialement,<br>
						L'equipe Bfmania
					</p>
				</div>
				<div class="text-right">
					<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annuler</button>
					<form method="post" action="<?=$this->url('back_users_warning',['id'=>$post['id']])?>" class="d-inline">
						<input hidden type="text" id="warning_author" name="warning_author" value="<?=$w_user['username']?>">
						<textarea hidden class="form-control" id="hiddenWarningMsg" rows="5" name="warningMsg"></textarea>
						<button type="submit" class="btn btn-first btn-sm">Confirmer</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="note" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Notes sur l'utilisateur</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				
					<div class="rounded border border-secondary p-3 m-3">
						<textarea style="resize : none;" id="noteText" name="note" class="w-100" rows="7" placeholder="Merci d'écrire une note"></textarea>
					</div>
					<div class="text-right mx-3">
						<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annuler</button>
						<button id="confirmNoteAdd" class="btn btn-first btn-sm" data-dismiss="modal">Confirmer</button>
					</div>
			
			</div>
		</div>
	</div>
</div>
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
<?php $this->stop('main_content');?>
<?php $this->start('js');?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js" integrity="sha256-bQTfUf1lSu0N421HV2ITHiSjpZ6/5aS6mUNlojIGGWg=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
	<script type="text/javascript">
		var  urlAjaxNotesList = '<?=$this->url('ajax_notes_list');?>',
		       urlAjaxNoteAdd = '<?=$this->url('ajax_note_add');?>',
		   urlAjaxNotesDelete ='<?=$this->url('ajax_notes_delete');?>',
		               postId = '<?=$post['id']?>'					
	</script>
	<script src="<?= $this->assetUrl('js/back/users/edit.js'); ?>"></script>
<?php $this->stop('js');?>