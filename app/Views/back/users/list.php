<?php $this->layout('layout_back', ['title' => 'Gestion des utilisateurs']);?>

<?php $this->start('main_content');
?>

	<h1 class="title font-weight-normal"><i class="fad fa-user-alt"></i> Gestion des utilisateurs</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border border-bottom-0 mb-0 clearfix" role="alert">
				Liste des utilisateurs 
				<span class="float-right">
					<a href="<?=$this->url('back_users_add');?>" class="btn  btn-outline-dark btn-sm">
						<i class="far fa-sm fa-plus-circle"></i> Ajouter un nouvel utilisateur
					</a>
				</span>
			</div>
		</div>
	</div>

	<div class="row justify-content-center ">
		<div class="col-12">
			<div class="bg-white border border-top-0 p-4">
						

				<div class="table-responsive">
					<form id="sort" class="form-inline justify-content-end mr-4 pr-2" method="get">
						<button id="multinickButton" class="btn btn-sm btn-outline-dark mr-3" type="button" data-toggle="collapse" data-target="#collapseMultinick" aria-expanded="true" aria-controls="collapseOne">
								  Multinick
						</button>

						<div class="form-group my-1 ml-2">
							<label class="d-none d-sm-block" for="role">Roles :</label>
							<select class="form-control form-control-sm ml-2" id="role" name="role">
								<option value="100" selected>Tous</option>
							    <option value="0">Membre</option>
							    <option value="30">Animateur</option>
							    <option value="50">Moderateur</option>
							    <option value="70">Administrateur</option>
							    <option value="99">Webmaster</option>
						    </select>
						</div>

						<div class="form-group my-1 ml-2">
							<select class="ml-2 form-control form-control-sm" id="nb_page" name="nb_users">
								<option>5</option>
							    <option>10</option>
							    <option>25</option>
							    <option selected>50</option>
							    <option>100</option>
						    </select>
						</div>

						<div class="form-group" class="my-1">	
							<div class="input-group">
								<input id="search" type="search" name="search" class="form-control ml-5" placeholder="rechercher" style="max-width: 300px;">
								<div class="input-group-prepend">
									<button class="btn btn-first" type="submit"><i class="far fa-search font16 line-height-1"></i></button>
								</div>
							</div>
						</div> 		 	
					</form>
					<div id="collapseMultinick" class="collapse mt-4" aria-labelledby="collapseMultinick">
						<div class="">
							<table class="table table-sm table-hover border">
								<thead class="bg-light">
									<tr>
										<th>#</th>
										<th>Nb utilisateurs</th>
										<th>Adresse IP</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($multinick as $key => $value): ?>
										<tr>
											<th class="font-weight-normal"><?=$key+1?></th>
											<th class="font-weight-normal"><?=$value['multinick']?></th>
											<th class="font-weight-normal"><?=$value['ip_address']?></th>
											<th class="font-weight-normal multinickView"><a href="#" class="grey-text text-darken-2" value="<?=$value['ip_address']?>"><i class="fas fa-sm fa-eye"></i> Voir les utilisateurs</a></th>
										</tr>
									<?php endforeach;?>
								</tbody>
							</table>	
						</div>
					</div>


					<table class="table table-hover mt-3">
						<thead class="bg-light">
							<tr>
								<th>#</th>
								<th>Username <i id="sortUsername" class="ml-2 sort fas fa-sort"></i></th>
								<th>Email <i id="sortEmail" class="ml-2 sort fas fa-sort"></i></th>
								<th>Rôle <i id="sortRole" class="ml-2 sort fas fa-sort"></th>
								<th>Date d'incription <i id="sortDateRegistered" class="ml-2 sort fas fa-sort"></i></th>
								<th>Dernière visite <i id="sortDateLast" class="ml-2 sort fas fa-sort"></i></th>
								<th>User agent <i id="sortAgent" class="ml-2 sort fas fa-sort"></i></th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="tableBody">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<input id="w_user_role" value="<?= $w_user['role'] ?>"type="text" hidden>
	<input id="w_user_id" value="<?= $w_user['id'] ?>"type="text" hidden>
<?php $this->stop('main_content');?>
<?php $this->start('js');?>
<script src="<?=$this->assetUrl('js/back/users/list_users.js');?>"></script>
<?php $this->stop('js');?>