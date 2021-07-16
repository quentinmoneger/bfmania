<?php $this->layout('layout_back', [
	'title' => 'Gestion des signalements',
]);
?>


<?php $this->start('main_content'); ?>
<h1 class="title font-weight-normal"><i class="fad fa-exclamation-triangle"></i> Gestion des signalements</h1>
<?php if ($w_user['role'] >= 50) : ?>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Liste des signalements
			</div>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="bg-white border p-4">
				<div class="">
					<table class="table table-hover mt-3">
						<thead class="bg-light">
							<h1 class="bg-light p-3 h5 text-uppercase">Signalements</h1>
						</thead>
						<?php foreach ($reports as $report) : ?>
							<div id="list-<?= $report['id'] ?>">
								<div class="row">
									<div class="col-md-2 col-sm-12">
										<span class="text-uppercase"><?= $report['type'] ?></span><br>
										<?php if ($report['state'] == false ) : ?>
											<span class="text-success">OUVERT</span>
										<?php else : ?>
											<span class="text-danger">FERME</span>
										<?php endif; ?>
									</div>
									<div class="col-md-8 col-sm-12 text-break">
										<span class="class-report text-secondary" data-id="<?= $report['id'] ?>" data-date="<?= $report['date'] ?>" id="date-report-insert-<?= $report['id'] ?>" class="m-1 "></span><span class="text-secondary"> par <?= $report['username'] ?></span><br>
										<span class="mt-3 font14"><?= $report['report_message'] ?></span>
									</div>
									<div class="col-md-2 col-sm-12 font12 text-center mt-4 mt-md-0">
										<?php if( $report['delete'] != 1 || $report['state'] != 1): ?>
											<?php if( $report['type'] == 'forum'): ?>
												<a target="_blank" href="<?= $this->url('back_report_view' , ['id_post' => $report['id_message'] ]) ?>"  type="button" class=" text-primary"><i class="m-1 fas fa-external-link-alt"></i>Visualiser</a><br>
											<?php else: ?>
												<span type="button" data-toggle="modal" data-type="<?= $report['type'] ?>" data-username="<?= $report['username_from'] ?>" data-title="<?= $report['content_title'] ?>" data-message="<?= $report['content'] ?>" data-target="#message" class="modal-message text-primary"><i class="m-1 fas fa-external-link-alt"></i>Visualiser</span><br>
											<?php endif ; ?>
										<?php endif ; ?>
										<?php if( $report['state'] != 1 ): ?>														
											<span type="button" data-toggle="modal" data-username="<?= $report['username_from'] ?>" data-id="<?= $report['id'] ?>" data-target="#close" class="modal-close"><i class=" m-1 fas fa  fa-times"></i>Cloturer</span><br>
										<?php endif ; ?>
										<?php if( $report['delete'] != 1 ): ?>	
											<span type="button" data-toggle="modal" data-id="<?= $report['id'] ?>" data-target="#delete" class="modal-delete text-danger"><i class="m-1 fas fa fa-times"></i>Supprimer</span>
										<?php endif ; ?>
									</div>
								</div>
								<div class="m-1 border"></div>
							</div>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	<?php else :  ?>
		<div class="row justify-content-center">
			<div class="col-12">
				<div class="bg-white border p-4">
					<p>Seuls les administrateurs ont accès à la gestion des avertissements.</p>
				</div>
			</div>
		</div>
	<?php endif ?>
	</div>

	<!-- MODAL -->

	<!-- Modal de suppresion -->
	<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalDeleteLabel">Confirmer la suppression</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Souhaitez-vous vraiment supprimer le signalement ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
					<form action="<?=$this->url('back_report_delete');?>" method="post">
						<button name="id_report" value="" type="submit" class=" btn-delete btn btn-first text-light">Oui, supprimer</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal de visualisation -->
	<div class="modal fade" id="message" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Signalement de <span id="modal-visual-username"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="text-break modal-body">
					<h5 id="modal-type"></h5><br>
					<div>" <span id="modal-title"></span> "</div><br>
					<h5>Message :</h5><br>
					<div>" <span id="modal-message"></span> "</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal de cloture -->
	<div class="modal fade" id="close" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Signalement de <span id="modal-close-username"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Voulez-vous vraiment cloturer le signalement ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
					<form action="<?=$this->url('back_report_close');?>" method="post">
						<button name="id_report" value="" type="submit" class=" btn-close btn btn-first text-light">Oui, cloturer</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content'); ?>
<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('/js/back/report/admin_report.js'); ?>"></script>
<?php $this->stop('js') ?>
