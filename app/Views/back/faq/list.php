<?php $this->layout('layout_back', [
	'title' => 'Gestion de la FAQ',
]); ?>


<?php $this->start('main_content'); ?>
<h1 class="title font-weight-normal"><i class="fad fa-question-circle"></i> Gestion de la FAQ</h1>
<?php if ($w_user['role'] >= 70) : ?>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Liste des questions / réponses présentes dans la FAQ
				<span class="float-right">
					<a href="<?= $this->url('back_faq_add'); ?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-plus-circle"></i> Créer une nouvelle question
					</a>
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
								<th class="dropdown">Catégorie <span id="id_category"><?= $order['id_category'] ?></span>
									<i class="ml-2 sort fas fa-sort" data-toggle="dropdown" id="dropdownMenuLink"></i>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
										<a class="dropdown-item" href="<?= $this->url('back_faq_list'); ?>"><i class="mr-2 sort fad fa-stream"></i>Toutes les catégories</a>
										<?php foreach ($categories as $key => $value) : ?>
											<a class="dropdown-item" href="<?= $this->url('back_faq_list'); ?>?sort=category&id_category=<?= $key ?>"><i class="mr-2 sort fad <?= $value['icon'] ?>"></i><?= $value['title'] ?></a>
										<?php endforeach; ?>
									</div>
								</th>
								<th>Question</th>
								<th>Date de création
									<a href="<?= $this->url('back_faq_list'); ?>?sort=date_create&dir=<?= $order['date_create'] ?>&id_category=<?= $order['id_category'] ?>" class="text-dark"><i class="ml-2 sort fas fa-sort"></i>
									</a>
								</th>
								<th>Action</th>
							</tr>
						</thead>

						<?php foreach ($faqs as $item) : ?>

							<tbody>
								<tr>
									<td><?= $item['id']; ?></td>
									<td><?=\Tools\Utils::getCategoryName($item['id_category']); ?></td>
									<td><?= $item['question']; ?></td>
									<td><?= date('d/m/Y H:i', strtotime($item['date_create'])); ?></td>
									<td>
										<a href="<?= $this->url('back_faq_edit', ['id' => $item['id']]); ?>" class="grey-text text-darken-2"><i class="fas fa-sm fa-edit"></i>&nbsp;Modifier</a>
										&nbsp;
										<a href="<?= $this->url('back_faq_delete', ['id' => $item['id']]); ?>" class="red-text"><i class="fas fa-sm fa-times"></i>&nbsp;Supprimer</a>
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
	<?php $this->stop('main_content'); ?>