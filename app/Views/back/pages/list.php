<?php $this->layout('layout_back', [
	'title' => 'Gestion des pages',
]); ?>


<?php $this->start('main_content');?>
	<h1 class="title font-weight-normal"><i class="fad fa-file-alt"></i> Gestion des pages</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Liste des pages présentes sur votre site web
				<span class="float-right">
					<a href="<?=$this->url('back_pages_choose_tpl');?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-plus-circle"></i> Créer une nouvelle page
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
								<th>Titre</th>
								<th>Publiée</th>
								<th>URL</th>
								<th>Date de création</th>
								<th>Action</th>
							</tr>
						</thead>

						<?php foreach($pages as $page):?>
						<tbody>
							<tr>
								<td><?=$page['id'];?></td>
								<td><?=$page['title'];?></td>
								<td><?=($page['status'] == 1) ? '<span class="text-success">Oui</span>' : '<span class="text-danger">Non</span>';?></td>
								<td>
									<a href="<?=$this->url('default_page', ['url' => $page['url']]);?>" target="_blank" title="Voir la page sur le site">
										/<?=$page['url'];?>
									</a>
								</td>
								<td><?=date('d/m/Y H:i', strtotime($page['date_create']));?></td>
								<td>
									<a href="<?=$this->url('back_pages_edit', ['template' => $page['template'], 'id' => $page['id']]);?>" class="grey-text text-darken-2">
										<i class="fas fa-sm fa-edit"></i> Modifier
									</a>
									&nbsp;
									<?php if($page['not_deletable'] != 1):?>
									<a href="<?=$this->url('back_pages_delete', ['id' => $page['id']]);?>" class="red-text">
										<i class="fas fa-sm fa-times"></i> Supprimer
									</a>
								<?php endif;?>
								</td>
							</tr>
						</tbody>
						<?php endforeach;?>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content');?>