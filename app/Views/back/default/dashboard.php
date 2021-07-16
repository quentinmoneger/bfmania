<?php $this->layout('layout_back', [
	'title' => 'Tableau de bord'
]); ?>

<?php $this->start('css');?>
<style type="text/css">
	.user-agent li {
		font-size: .8rem;
		margin-bottom: 1rem;
	}
	.user-agent li .fa-li {
		color: #00008b;
		font-size: 1rem;
		margin-top: -2px;
	}
	.user-agent li .label {
		min-width: 100px;
		text-transform: uppercase;
		display: inline-block;
		color: #333;
		font-weight: 500!important;
	}
</style>
<?php $this->stop('css');?>

<?php $this->start('main_content') ?>
	<h1 class="title font-weight-normal"><i class="fad fa-tachometer-alt"></i> Tableau de bord</h1>
	

	<div class="row justify-content-between">
		<div class="col-sm-8">
			<div class="card rounded-0">
				<div class="card-body">
					<p>Bonjour <strong><?=$w_user['username'];?></strong>.
					<br>
					<br>Vous êtes connecté sur la console de gestion de <?=$w_site_name;?>.</p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<?php
				$platform_name = $agent->platform();
				$platform_ver = $agent->version($platform_name);

				$browser_name = $agent->browser();
				$browser_ver  = $agent->version($browser_name);
			?>
			<div class="card rounded-0">
				<div class="card-body">
					<h5 class="card-title mb-4">Informations système</h5>
					<ul class="fa-ul user-agent">
						<li><span class="fa-li"><i class="fal fa-fw fa-browser"></i></span> <span class="label">Système :</span> <?=$platform_name.' '.$platform_ver;?></li>
						<li><span class="fa-li"><i class="fal fa-fw fa-globe"></i></span> <span class="label">Navigateur :</span> <?=$browser_name.' '.$browser_ver;?></li>
						<li><span class="fa-li"><i class="fal fa-fw fa-desktop"></i></span> <span class="label">Résolution :</span> <span id="reso">détection en cours</span></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php $this->stop('main_content') ?>

