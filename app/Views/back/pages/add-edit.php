<?php

$page_title 		= ($form == 'add') ? 'Ajout d\'une nouvelle page' : 'Modification d\'une page';
$title				= $post['title'] ?? '';
$content			= $post['content'] ?? '';
$status				= $post['status'] ?? 1;
$picture_cover		= $post['picture_cover']['tmp_name'] ?? $post['picture_cover'] ?? '';
$meta_description	= $post['meta_description'] ?? '';

?>

<?php $this->layout('layout_back', [
	'title' => $page_title,
]); ?>

<?php $this->start('main_content');?>
	<h1 class="title font-weight-normal"><i class="fad fa-file-alt"></i> <?=$page_title;?></h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				<?=$page_title;?>
				<span class="float-right">
					<a href="<?=$this->url('back_pages_list');?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-history"></i> Retour à la liste des pages
					</a>
				</span>
			</div>
		</div>
	</div>

	<br>
	<div class="row justify-content-center">
		<div class="col-12">
			<div class="bg-white border p-4">


				<?php if(!empty($errors)):?>
				<div class="alert alert-danger mb-3" role="alert">
					<?=implode('<br>', $errors);?>
				</div>
				<?php endif;?>

				<form method="post" enctype="multipart/form-data">
					<div class="form-group row">
						<label for="title" class="col-sm-3 col-form-label">Titre</label>
						<div class="col-sm-4">
							<input type="text" name="title" id="title" class="form-control" value="<?=$title;?>" placeholder="Lorem ipsum dolor sit amet..." required>
						</div>
						<div class="col-sm-4">
							<div class="form-check pt-2">
								<input type="checkbox" name="status" id="status" class="form-check-input" <?=($status == 0) ? '' : 'checked';?>>
								<label class="form-check-label" for="status">Page publiée</label>
							</div>
						</div>
					</div>

					<?php if($enable_meta_description):?>
						<div class="form-group row">
							<label for="meta_description" class="col-sm-3 col-form-label">Meta description</label>
							<div class="col-sm-6">
								<input type="text" name="meta_description" id="meta_description" class="form-control" value="<?=$meta_description;?>" placeholder="Lorem ipsum dolor sit amet..." required>
							</div>
						</div>
					<?php endif;?>

					<?php if($enable_picture_cover):?>
						<div class="form-group row">
							<label for="picture_cover" class="col-sm-3 col-form-label">Photo de couverture</label>
							<div class="col-sm-5">
								<button type="button" name="picture_cover" id="picture_cover" class="btn btn-outline-first btn-sm awmedias mr-2" data-width="1920" data-height="200">
									Choisir un fichier
								</button>
							</div>
							<div class="w-100 mb-2"></div>
							<div class="col-sm-3 offset-3">
								<?php $currentImg = (!empty($picture_cover)) ? $picture_cover : 'https://via.placeholder.com/375x50&text=1920x200';?>
								<a href="<?=$currentImg;?>" title="" data-toggle="lightbox" data-gallery="article" data-type="image" id="picture_cover_preview">
									<img src="<?=$currentImg;?>" class="img-fluid img-thumbnail" title="Fichier actuel">
								</a>
							</div>
						</div>
					<?php endif;?>


					<div class="form-group row">
						<label for="content" class="col-sm-12 col-form-label">Contenu de la page</label>
						<div class="col-sm-12">
							<div class="border p-2">
								<div class="row" style="min-height: 500px;">
									<?php
									$i=1;
									$rows = explode('|', $template);
									foreach($rows as $row){
										$cols = explode(',', $row);
										foreach($cols as $col){
											echo '<div class="col-'.$col.' content-page">'.PHP_EOL;

											if(empty($content[$i])){
												$current_content = '<h3>Exemple de titre</h3><p>Donec ullamcorper, risus tortor, pretium porttitor. Morbi quam quis lectus non leo.
												<br>Integer faucibus scelerisque. Proin faucibus at, aliquet vulputate, odio at eros. Fusce gravida, erat vitae augue. Fusce urna fringilla gravida.
												<br><br>In hac habitasse platea dictumst. Praesent wisi accumsan sit amet nibh. Maecenas orci luctus a, lacinia quam sem, posuere commodo, odio condimentum tempor, pede semper risus. Suspendisse pede. In hac habitasse platea dictumst. Nam sed laoreet sit amet erat. Integer.</p>';
											}
											else {
												$current_content = $content[$i];
											}

											echo '<textarea contenteditable="true" name="content['.$i.']">'.$current_content.'</textarea>'.PHP_EOL;
											echo '</div>'.PHP_EOL;
											$i++;
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group pt-4 text-center">
						<button type="submit" class="btn btn-first px-5">Enregistrer</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php $this->stop('main_content');?>

<?php $this->start('js') ?>
<script src="<?=$this->assetUrl('libs/ckeditor/ckeditor.js');?>"></script>
<script src="<?= $this->assetUrl('js/pages/pages.js'); ?>"></script>
<?php $this->stop('js') ?>
