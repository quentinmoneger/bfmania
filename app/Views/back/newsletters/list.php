<?php $this->layout('layout_back', ['title' => 'Gestion des newsletters']);?>

<?php $this->start('main_content');?>

	<h1 class="title font-weight-normal"><i class="fad fa-at"></i> Gestion des newsletters</h1>


	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Liste des newsletters
				<span class="float-right">
					<a href="<?=$this->url('back_newsletters_add');?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-plus-circle"></i> Créer une newsletter
					</a>
				</span>
			</div>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="bg-white border p-4">
				<div class="table-responsive">
					<table class="table">
						<thead class="bg-light">
							<tr>
								<th>#</th>
								<th>Sujet</th>
								<th>Type</th>
								<th>Statut</th>
								<th>Programmée le</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($newsletters as $newsletter):?>
								<tr>
									<td><?=$newsletter['id'];?></td>
									<td><?=$newsletter['subject'];?></td>
									<td>
										<?php if(!empty($newsletter['emails_to'])):?>
											<span class="badge bg-secondary text-white">test</span>
										<?php else:?>
											<span class="badge bg-success text-white">tous</span>
										<?php endif;?>
									</td>
									<td>
										<?php 
											if($newsletter['is_draft']){
												$edit = true;
												echo '<span class="text-danger">Brouillon</span>';
											}
											elseif(!empty($newsletter['emails_to'])){
												$edit = true;
												echo '<span class="text-primary">Envoi test</span>';
											}
											else {

												if(strtotime($newsletter['date_send']) > time()){
													$edit = true;
													echo '<span class="text-success">En attente d\'envoi</span>';
												}
												else {
													$edit = false;
													echo '<span class="text-success">Envoyée</span>';
												}
											}
										?>
									</td>
									<td><?=($newsletter['is_draft'] == 0) ? date('d/m/Y H:i', strtotime($newsletter['date_send'])) : '---';?></td>
									<td>
										<a href="<?=$this->url('back_newsletters_view', ['id' => $newsletter['id']]);?>" class="blue-grey-text">
											<i class="fas fa-sm fa-cog"></i> Détails
										</a>
										&nbsp;
										<?php if($edit): ?>
											<a href="<?=$this->url('back_newsletters_edit', ['id' => $newsletter['id']]);?>" class="grey-text text-darken-2">
												<i class="fas fa-sm fa-edit"></i> Modifier
											</a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content');?>