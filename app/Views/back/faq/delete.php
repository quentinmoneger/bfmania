<?php $this->layout('layout_back', ['title' => 'Suppression d\'une question']);?>

<?php $this->start('main_content');?>
	<h1 class="title font-weight-normal"><i class="fad fa-file-alt"></i> Suppression d'une question</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Suppression d'une question
				<span class="float-right">
					<a href="<?=$this->url('back_faq_list');?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-history"></i> Retour à la Foire Aux Questions
					</a>
				</span>
			</div>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="bg-white border p-4">
				<div class="row no-gutters justify-content-center">
					<div class="col-auto">
						<div class="alert alert-danger px-5" role="alert">
							<i class="fad fa-exclamation-triangle font26 float-left mr-3"></i>La suppression d'une question est <u>définitive</u>.
						</div>
					</div>
					<div class="w-100"></div>

					<div class="col-7">
						<form method="post" class="text-center pt-5">
								<p>Êtes-vous sûr de vouloir supprimer la question ci-dessous ?</p>

								<p class="mt-3 mb-5">
									<strong>Titre :</strong> &laquo; <?=$question['question'];?> &raquo;
								</p>

								<div>
									<a href="<?=$this->url('back_faq_list');?>" class="btn btn-secondary">Annuler</a>
									&nbsp; <button type="submit" name="delete" value="yes" class="btn btn-first">Oui, supprimer la question</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php $this->stop('main_content');?>