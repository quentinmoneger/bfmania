<?php $this->layout('layout', ['title' => 'Forum de discussion']); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/forum/forum.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xxl-8 my-3">

				<div class="bg-color-2 border p-4 rounded">
					<div class="mx-auto ">
						<?php if (!empty($errors)) : ?>
							<div class="alert alert-danger mb-3" role="alert">
								<?= implode('<br>', $errors); ?>
							</div>
						<?php endif; ?>
						<form method="POST" action="<?= $this->url('add_topic', ['category' => $category, 'url' => $url]); ?>">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">Sujet :</span>
								</div>
								<input required name="topic_title" id="topic_title" class="form-control" value="">
								
							</div>
							<div class="form-group">
								<label class="mb-n1 p-2 input-group-text border-bottom-0">Message :</label>
								<textarea contenteditable="true" name="topic_msg" id="topic_msg" class="form-control"></textarea>
							</div>
							<div class="form-group text-center">
								<button type="submit" class="btn btn-first btn-sm px-5"><i class="fad fa-file-alt"></i> Publier</button>
							</div>
						</form>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</section>

<?php $this->stop('main_content'); ?>
<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('libs/ckeditor/ckeditor.js'); ?>"></script>
<script src="<?= $this->assetUrl('js/front/forum/add_topic.js'); ?>"></script>
<?php $this->stop('js') ?>