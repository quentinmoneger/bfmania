<?php $cache = rand();?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?=$this->e($title . ' - Administration ' . $w_site_name);?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- CSS Local Library -->
<link rel="stylesheet" href="<?=$this->assetUrl('libs/bootstrap-4.3.1-custom/css/bootstrap.min.css');?>">
<link rel="stylesheet" href="<?=$this->assetUrl('libs/bootstrap-sweetalert/sweetalert.min.css');?>">
<link rel="stylesheet" href="<?= $this->assetUrl('fontawesome-pro-5.15.2-web/css/all.css'); ?>">
<!-- CSS Internal -->
<link rel="stylesheet" href="<?=$this->assetUrl('css/back.css').'?v='.$cache;?>">
<!-- // Favicons -->
<?=$this->section('css');?>
</head>
<body class="pop">
	<div id="contentPop">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 pb-5 pt-3">
					<?php if(!empty($w_flash_message)): ?>
						<div class="alert alert-<?=$w_flash_message->level;?> alert-dismissible alert-custom" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
							<?=$w_flash_message->message;?>
						</div>
					<?php endif;?>
					<?=$this->section('main_content');?>
				</div>
			</div>
		</div>
	</div>

<!-- Js CDN Library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="crossorigin="anonymous"></script>
<!-- Js Local Library -->
<script src="<?=$this->assetUrl('libs/bootstrap-4.3.1-custom/js/bootstrap.bundle.min.js');?>"></script>
<script src="<?=$this->assetUrl('libs/bootstrap-sweetalert/sweetalert.min.js');?>"></script>
<script src="<?=$this->assetUrl('libs/ekko-lightbox/ekko-lightbox.min.js');?>"></script>
<script src="<?=$this->assetUrl('libs/bootstrap-awmedias/awmedias.js').'?v='.$cache;?>"></script>
<!-- Js Internal -->
<script src="<?=$this->assetUrl('js/back/back.js').'?v='.$cache;?>"></script>
<?=$this->section('js');?>
</body>
</html>