<?php $this->layout('layout_back', [
	'title' => 'Ajout d\'une nouvelle page',
]); ?>

<?php $this->start('main_content');?>
	<h1 class="title font-weight-normal"><i class="fad fa-file-alt"></i> Ajout d'une nouvelle page</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Choix du template de nouvelle page
				<span class="float-right">					
					<a href="<?=$this->url('back_pages_list');?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-history"></i> Retour à la liste des pages
					</a>
				</span>
			</div>
		</div>
	</div>


	<div class="row ">
		<div class="col-12">
			<div class="bg-white border p-4">
				<div class="row justify-content-center">
					<div class="col-10">
						<div class="row">
						<?php foreach($templates_availables as $key => $value):?>
							<div class="col-3 py-3 px-4">
								<a href="<?=$this->url('back_pages_add', ['template' => $key]);?>" class="d-block text-decoration-none">
									<div class="row no-gutters is-template border border-secondary">
									<?php
										$rows = explode('|', $value);
										foreach($rows as $row){
											$cols = explode(',', $row);
											$i=1;
											foreach($cols as $col){
												echo '<div class="col-'.$col.' border border-secondary">&nbsp;</div>';
											}
										}
									?>
									</div>
								</a>
							</div>
						<?php endforeach;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content');?>