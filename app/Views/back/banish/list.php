<?php $this->layout('layout_back', [
	'title' => 'Gestion des bannis',
]); 
?>


<?php $this->start('main_content'); ?>
<h1 class="title font-weight-normal"><i class="fad fa-radiation"></i> Gestion des bannis</h1>
<?php if ($w_user['role'] >= 50) : ?>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Liste des bannis
				<span class="float-right">
					<!-- <a href="<?= $this->url('back_faq_add'); ?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-plus-circle"></i> Créer une nouvelle question
					</a> -->
				</span>
			</div>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="bg-white border p-4">
				<div class="table-responsive">
					<table class="table table-hover mt-3">
						<thead class="bg-light">
							<tr>
								<th>#</th>
								<th>Utilisateur
									<a href="<?= $this->url('back_banish_list');?>?sort=username&dir=<?=$order['username']?>" class="text-dark"><i class="ml-2 sort fas fa-sort"></i>
									</a>
								</th>
								<th>Raison</th>
								<th>Depuis le 
									<a href="<?= $this->url('back_banish_list');?>?sort=date_create&dir=<?=$order['date_create']?>" class="text-dark"><i class="ml-2 sort fas fa-sort"></i>
									</a>
								</th>
								<th>Expire le
									<a href="<?= $this->url('back_banish_list');?>?sort=date_expire&dir=<?=$order['date_expire']?>" class="text-dark"><i class="ml-2 sort fas fa-sort"></i>
									</a>
								</th>
								<th>Auteur
									<a href="<?= $this->url('back_banish_list');?>?sort=author&dir=<?=$order['author']?>" class="text-dark"><i class="ml-2 sort fas fa-sort"></i>
									</a>
								</th>
								<th>Action</th>
							</tr>
						</thead>

						<?php foreach ($banish as $item) : ?>

							<tbody>
								<tr>
									<td><?= $item['id']; ?></td>
									<td><a href="<?= $this->url('back_users_edit', ['id' => $item['id_user']]); ?>" class="text-dark"><?= $item['username']; ?><small class="text-muted"> [<span class="font10">UID:</span> <?=$item['id_user']?>] </small></a></td>
									<td><?= $item['reason']; ?></td>
									<td><?= date('d/m/Y H:i', strtotime($item['date_create'])); ?></td>
									<td><?= date('d/m/Y H:i', strtotime($item['date_expire'])); ?></td>
									<td><?= $item['author']; ?><small class="text-muted"> [<span class="font10">UID:</span> <?=$item['id_author']?>] </small></td>
									<td>
										<a href="<?= $this->url('back_banish_delete', ['id' => $item['id']]); ?>" class="red-text"><i class="fas fa-sm fa-times mr-1"></i>Supprimer</a>
									</td>
								</tr>
							</tbody>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
		<?php else :  ?>
			<div class="row justify-content-center">
				<div class="col-12">
					<div class="bg-white border p-4">
						<p>Seuls les administrateurs ont accès à la gestion de la Foire Aux Questions.</p>
					</div>
				</div>
			</div>
		<?php endif ?>
	</div>


<?php $this->stop('main_content'); ?>