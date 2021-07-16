<?php $this->layout('layout_back', ['title' => 'Bibliothèque de médias']);?>

<?php $this->start('main_content');?>

	<h1 class="title font-weight-normal"><i class="fad fa-photo-video"></i> Bibliothèque de médias  </h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Bibliothèque de médias
				<span class="float-right">
					<a href="<?=$this->url($w_current_route, (!empty($action)) ? [] : ['action' => 'delete']);?>" class="btn btn-outline-dark btn-sm <?=($action == 'delete') ? 'active' : '';?>">
						<i class="far fa-sm fa-trash"></i> <?=($action == 'delete') ? ' Quitter le mode suppression ' : 'Entrer en mode suppression';?>
					</a>
				</span>
			</div>
		</div>
	</div>

	<div class="row justify-content-center">

		<div class="col-12">
			<div id="dropfile" class="d-flex justify-content-center align-items-center">
				<div>
					<p class="mb-2 font20">Glissez votre fichier dans cette zone pour les uploader</p>
					<p class="small text-gray mb-2">ou</p>

					<form method="post" enctype="multipart/form-data" class="d-none">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_size;?>">
						<input type="file" id="file" name="file">
					</form>
					<button type="button" name="open_file" class="btn btn-outline-secondary">Sélectionner un fichier</button>

					<br><br><small class="text-muted">Taille de fichier maximale : <?=\Tools\Utils::convertToReadableSize($max_size);?></small>

					<div class="progress mt-3">
						<div class="progress-bar progress-bar-striped bg-first" id="progressup" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
		 	</div>
		</div>

		<div class="w-100 my-3"></div>

		<div class="col-12">
			<div id="media-list" class="row no-gutters justify-content-start"></div>
		</div>

	</div>
</div>
<?php $this->stop('main_content');?>
<?php $this->start('js');?>
<script>
var MaxSize = <?=$max_size;?>,
	Action  = '<?=$action;?>'
	
</script>
<script src="<?= $this->assetUrl('js/back/medias/medias.js'); ?>"></script>
<?php $this->stop('js');?>