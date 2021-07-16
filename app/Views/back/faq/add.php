<?php $this->layout('layout_back', [
	'title' => 'Ajouter question',
]); ?>

<?php $this->start('main_content'); ?>
<h1 class="title font-weight-normal"><i class="fad fa-question-circle"></i> Ajout d'une question</h1>

<div class="row justify-content-center">
	<div class="col-12">
		<div class="alert alert-light border clearfix" role="alert">
			Création d'une question/réponse à rajouter dans la Foire Aux Questions
			<span class="float-right">
				<a href="<?= $this->url('back_faq_list'); ?>" class="btn btn-outline-dark btn-sm">
					<i class="far fa-sm fa-history"></i> Retour à la liste des question
				</a>
			</span>
		</div>
	</div>
</div>

<div class="row justify-content-center">
	<div class="col-12">
		<div class="bg-white border p-4">
			<div class="col-xxl-8 mx-auto ">
				<?php if (!empty($errors)) : ?>
					<div class="alert alert-danger mb-3" role="alert">
						<?= implode('<br>', $errors); ?>
					</div>
				<?php endif; ?>
				<form method="POST">
					<div class="input-group mb-3" style="max-width: 400px">
						<div class="input-group-prepend">
							<span class="input-group-text">Catégorie</span>
						</div>
						<select name="category" class="form-control">
							<option value="0" selected disabled>-- Sélectionnez --</option>
							<?php foreach ($categories as $key => $value) : ?>
								<option value="<?= $key; ?>"><?= str_replace('<br>', ' ', $value['name']); ?></option>
							<?php endforeach; ?>
						</select>

					</div>
					<div class="input-group mb-3">
						<input required name="question" id="question" class="form-control" value="<?php echo $post['question'] ?? '' ?>">
						<div class="input-group-append">
							<span class="input-group-text">Question</span>
						</div>
					</div>
					<div class="form-group">
						<label class="mb-n1 p-2 input-group-text border-bottom-0">Réponse :</label>
						<textarea contenteditable="true" name="answer" id="answer" class="form-control"><?php echo $post['answer'] ?? '' ?></textarea>
					</div>
					<div class="form-group text-center">
						<input type="submit" class="btn btn-first btn-sm px-5" name="submit" value="Créer Question/Réponse">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php $this->stop('main_content'); ?>


<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('libs/ckeditor/ckeditor.js'); ?>"></script>
<script src="<?=$this->assetUrl('js/back/faq/faq.js');?>"></script>
<?php $this->stop('js') ?>