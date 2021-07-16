<?php
$company_name 	= $post['company_name'] ?? '';
$company_street = $post['company_street'] ?? '';
$company_zipcode= $post['company_zipcode'] ?? '';
$company_city 	= $post['company_city'] ?? '';
$company_country= $post['company_country'] ?? '';
$company_siret 	= $post['company_siret'] ?? '';
$company_capital= $post['company_capital'] ?? '';
$company_phone 	= $post['company_phone'] ?? '';
$company_email 	= $post['company_email'] ?? '';

?>
<?php $this->layout('layout_back', ['title' => 'Paramètres du site']);?>

<?php $this->start('main_content');?>

	<h1 class="title font-weight-normal"><i class="fad fa-cog"></i> Paramètres du site</h1>

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="alert alert-light border clearfix" role="alert">
				Paramètres du site
			</div>
		</div>
	</div>

	<br>
	<div class="row justify-content-center">
		<div class="col-12">
			<div class="bg-white border p-4">
				<div class="row justify-content-center">
					<div class="col-lg-5">
						<?php if(!empty($errors)):?>
						<div class="alert alert-danger mb-3" role="alert">
							<?=implode('<br>', $errors);?>
						</div>
						<?php endif;?>

						<form method="post">

							<fieldset>
								<legend class="text-first font18">Informations générales</legend>

								<div class="form-group row">
									<label for="company_name" class="col-sm-5 col-form-label col-form-label-sm">Dénomination commerciale <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_name" id="company_name" class="form-control form-control-sm" value="<?=$company_name ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_street" class="col-sm-5 col-form-label col-form-label-sm">Adresse <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_street" id="company_street" class="form-control form-control-sm" value="<?=$company_street ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_zipcode" class="col-sm-5 col-form-label col-form-label-sm">Code postal <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_zipcode" id="company_zipcode" class="form-control form-control-sm" value="<?=$company_zipcode ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_city" class="col-sm-5 col-form-label col-form-label-sm">Ville <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_city" id="company_city" class="form-control form-control-sm" value="<?=$company_city ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_country" class="col-sm-5 col-form-label col-form-label-sm">Pays <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_country" id="company_country" class="form-control form-control-sm" value="<?=$company_country ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_siret" class="col-sm-5 col-form-label col-form-label-sm">N&deg; de SIRET <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_siret" id="company_siret" class="form-control form-control-sm" value="<?=$company_siret ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_capital" class="col-sm-5 col-form-label col-form-label-sm">Capital</label>
									<div class="col-sm-7">
										<input type="text" name="company_capital" id="company_capital" class="form-control form-control-sm" value="<?=$company_capital ?? '';?>">
									</div>
								</div>

								<div class="form-group row">
									<label for="company_phone" class="col-sm-5 col-form-label col-form-label-sm">Téléphone <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_phone" id="company_phone" class="form-control form-control-sm" value="<?=$company_phone ?? '';?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label for="company_email" class="col-sm-5 col-form-label col-form-label-sm">Email de contact <span class="required">*</span></label>
									<div class="col-sm-7">
										<input type="text" name="company_email" id="company_email" class="form-control form-control-sm" value="<?=$company_email ?? '';?>" required>
									</div>
								</div>
							</fieldset>

							<div class="form-group">
								<div class="text-center mx-auto pt-3">
									<button type="submit" class="btn btn-first px-5">Enregistrer</button>
								</div>
							</div>
							
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php $this->stop('main_content');?>
