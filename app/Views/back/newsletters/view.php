<?php $this->layout('layout_back', [
	'title' => 'Visualisation d\'une newsletter',
]);?>

<?php $this->start('css');?>
<style>
#editor img, .cke_textarea_inline img {
	width: auto;
	max-width: 556px !important;
	height: auto !important;
}
</style>
<?php $this->stop('css');?>

<?php $this->start('main_content');?>

	<h1 class="title font-weight-normal"><i class="fad fa-at"></i> Visualisation d'une newsletter</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Visualisation d'une newsletter
				<?php if($news['is_draft']): ?>
					enregistrée comme brouillon
				<?php else:?>
					envoyée le <?=\DateTime::createFromFormat('Y-m-d H:i:s', $news['date_send'])->format('d/m/Y \à H:i'); ?>
				<?php endif; ?>
				<span class="float-right">					
					<a href="<?=$this->url('back_newsletters_list');?>" class="btn btn-outline-dark btn-sm">
						<i class="far fa-sm fa-history"></i> Retour à la liste des newsletters
					</a>
				</span>
			</div>
		</div>
	</div>

	<br>
	<div class="row justify-content-center">
		<div class="col-lg-8">

			<form method="post">
				<div class="form-group row">
					<label for="subject" class="col-sm-3 col-form-label">Objet de la newsletter</label>
					<div class="col-sm-7">
						<input type="text" name="subject" id="subject" class="form-control-plaintext" value="<?=$news['subject'];?>" readonly>
					</div>
				</div>

				<div class="form-group row">
					<label for="recipients" class="col-sm-3 col-form-label">Destinataires</label>
					<div class="col-sm-7">
						<?php 
						if(!empty($news['emails_to'])){
							$recipients = 'Envoi TEST';
						}
						else {
							$recipients = 'Utilisateurs inscrits';
						}
						?>
						<input type="text" name="recipients" id="recipients" class="form-control-plaintext" value="<?=$recipients;?>" readonly>
					</div>
				</div>
				
				<div class="form-group row">
					<label for="content" class="col-sm-3 col-form-label">Contenu</label>
				</div>

				<div class="form-group row" style="background:#ddd">
					<div class="mx-auto" style="width:600px;">
						<br>
						<table class="body-wrap" style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: 600px; margin: auto; padding: 20px;background-color: #fff">
							<tr style="margin: 0; padding: 0;">
								<td style="margin: 0; padding: 0;"></td>
								<td class="container" style="clear: both !important; display: block !important; max-width: 600px !important; margin: 0 auto; padding: 20px; border: 1px solid #f0f0f0;">

									<div class="content" style="display: block; max-width: 600px; margin: 0 auto; padding: 0;">
										<table style="width: 100%">
											<tr>
												<td>
													<div class="text-center">
														<img src="<?=$this->assetUrl('img/logo-email.png');?>" id="logoPreview" alt="%sitename%" style="width: auto; max-width: 100%; margin: 0; padding: 0;">
													</div>

													<br>
													<div id="editor">
														<?php echo $news['content']; ?>
													</div>
												</td>
											</tr>
										</table>
									</div>
								</td>
								<td style="margin: 0; padding: 0;"></td>
							</tr>
						</table>
						<br>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php $this->stop('main_content');?>

