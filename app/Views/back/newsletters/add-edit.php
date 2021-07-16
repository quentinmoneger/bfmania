<?php

$page_title = ($form == 'add') ? 'Création d\'une newsletter' : 'Modification d\'une newsletter';
$subject 	= $post['subject'] ?? '';
$content 	= $post['content'] ?? '';
$date_send	= $post['date_send'] ?? '';

?>
<?php $this->layout('layout_back', [
	'title' => $page_title,
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

	<h1 class="title font-weight-normal"><i class="fad fa-at"></i> <?=$page_title;?></h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				<?=$page_title;?>
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
		<div class="col-12">
			<div class="bg-white border p-4">

				<div class="row justify-content-center">
					<div class="col-lg-8">

						<?php if(!empty($errors)):?>
						<div class="alert alert-danger mb-3" role="alert"><?=implode('<br>', $errors);?></div>
						<?php endif;?>


						<form method="post">
							<div class="form-group row">
								<label for="subject" class="col-sm-3 col-form-label">Objet de la newsletter</label>
								<div class="col-sm-7">
									<input type="text" name="subject" id="subject" class="form-control" value="<?=$subject;?>" placeholder="Sujet de votre lettre d'informations" required>
								</div>
							</div>

							<div class="form-group row">
								<label for="date_send" class="col-sm-3 col-form-label">Date d'envoi</label>
								<div class="col-sm-3">
									<input type="date" id="date_send" name="date_send" class="form-control form-control-auto" min="<?=date('Y-m-d');?>" max="<?=date('Y-m-d', strtotime('+6 months'));?>" value="<?=$date_send;?>">
								</div>
								<div class="col-sm-4">
									<select name="hour_send" class="form-control d-inline-block" style="width:auto">
										<option value="" selected disabled>HH</option>
										<?php for($i=0;$i<=23;$i++):?>
											<option value="<?=($i <10) ? '0'.$i : $i;?>"><?=($i <10) ? '0'.$i : $i;?></option>
										<?php endfor;?>
									</select>
									:
									<select name="min_send" class="form-control d-inline-block" style="width:auto">
										<option value="" selected disabled>mm</option>
										<option value="01">00</option>
										<option value="15">15</option>
										<option value="30">30</option>
										<option value="45">45</option>
									</select>
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
																<textarea id="editor" contenteditable="true" name="content">
																<?php if(empty($content)): ?>
																	<p>Bonjour,
																	<br>
																	<br>Donec ullamcorper, risus tortor, pretium porttitor. Morbi quam quis lectus non leo.
																	<br>Integer faucibus scelerisque. Proin faucibus at, aliquet vulputate, odio at eros. Fusce gravida, erat vitae augue. Fusce urna fringilla gravida.
																	<br><br>In hac habitasse platea dictumst. Praesent wisi accumsan sit amet nibh. Maecenas orci luctus a, lacinia quam sem, posuere commodo, odio condimentum tempor, pede semper risus. Suspendisse pede. In hac habitasse platea dictumst. Nam sed laoreet sit amet erat. Integer.
																	</p>
																<?php else: ?>
																	<?php echo $content; ?>
																<?php endif; ?>
																</textarea>
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

							<div class="form-group row justify-content-center">
								<div class="col-4 text-center">
									<button type="button" name="opendiv" class="btn btn-warning" id="testNews">
										<i class="far fa-user-cog"></i> Envoyer un test
									</button>

									<div class="bg-light p-2 text-left mt-3" data-target="testNews" style="display:none;">
										<span class="fw-500">Destinataires</span>
										<textarea name="emails" placeholder="Une adresse email par ligne" class="form-control form-control-sm mt-2"></textarea>

										<button type="submit" name="submit" value="test" class="btn btn-secondary btn-sm mt-2">Envoyer</button>
									</div>
								</div>

								<div class="col-4 text-center">
									<button type="submit" name="submit" value="save" class="btn btn-danger" id="saveNews">
										<i class="far fa-save fa-fw"></i> Enregistrer un brouillon
									</button>
								</div>
								
								<div class="col-4 text-center">
									<button type="submit" name="submit" value="send" class="btn btn-primary" id="submitNews">
										<i class="fa fa-paper-plane fa-fw"></i> Envoyer la newsletter
									</button>
								</div>

							</div>

							<p class="text-muted mt-5">
								<i class="fas fa-info-circle fa-fw mr-2"></i><b>Informations utiles :</b>
								<br> &bull; En enregistrant une newsletter comme brouillon, celle-ci ne sera pas envoyée. La date et l'heure ne sont alors pas obligatoire. 
								<br> &bull; Un envoi test permet de simuler un envoi afin de pouvoir visualiser le rendu dans votre boite de réception.
								<br> &bull; En envoyant la newsletter, l'envoi de celle-ci débutera à la date et l'heure indiquée.
							</p>
						</form>

					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content');?>

<?php $this->start('js');?>
<script>
	var urlBackUploadCkeditor = '<?=$this->url('back_medias_ckeditor', ['folder_output' => 'newsletter', 'max_width' => 556, 'full_url' => true]);?>'
</script> 
<script src="<?=$this->assetUrl('js/back/newsletters/add-edit.js');?>"></script>
<script src="<?=$this->assetUrl('libs/ckeditor/ckeditor.js');?>"></script>
<?php $this->stop('js'); ?>