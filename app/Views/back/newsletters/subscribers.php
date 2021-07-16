<?php $this->layout('layout_back', ['title' => 'Liste des inscrits']);?>

<?php $this->start('main_content');?>

	<h1 class="title font-weight-normal"><i class="fad fa-user-tag"></i> Newsletter</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Liste des inscrits à la newsletter
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
								<th>Email</th>
								<th>Date d'inscription</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($subscribers as $subscriber):?>
							<tr>
								<td><?=$subscriber['id'];?></td>
								<td><?=$subscriber['email'];?></td>
								<td><?=date('d/m/Y H:i', strtotime($subscriber['date_create']));?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content');?>