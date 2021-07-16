<?php $this->layout('layout', ['title' => 'L\'équipe']); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/staff.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section id="equipe">
	<div id="cover">
		<div class="position-absolute h-100 w-100 d-flex">		
			<div class="m-auto">
				<h1>L'équipe BFMania</h1>
			</div>
		</div>	
	</div>

	<div class="container text-center red-stripe py-5">
		<div class="row justify-content-center">
			<div class="col-lg-6 my-3">
				<div class="border shadow bg-light mx-2">
					<h5 class="my-3">Webmasters</h5>
					<hr class="w-50">
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled mx-0 px-0">
							<?php foreach ($webmasters as $webmaster) : ?>
								<?php $avatar = (!empty($webmaster['avatar'])) ? $webmaster['avatar'] : $this->assetUrl('/img/nophoto.jpg'); ?>
								<li class="d-flex align-items-center">
									<img src="<?= $avatar ?>" class="staffpicture mx-2 my-2">
									<a class="font18 link-unstyled mx-2 my-2" href="<?=$this->url('profile_show',['username'=>$webmaster['username']])?>"><?=$webmaster['username']?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div><!-- /row -->
		<div class="row justify-content-around">
			<div class="col-lg-6 my-3">
				<div class="border shadow bg-light mx-2 etiquette-left">
					<h5 class="my-3">Administrateurs</h5>
					<hr class="w-50 border-first">
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled mx-0 px-0">
							<?php foreach ($admins as $admin ) : ?>
								<?php $avatar = (!empty($admin['avatar'])) ? $admin['avatar'] : $this->assetUrl('/img/nophoto.jpg'); ?>
								<li class="d-flex align-items-center">
									<img src="<?= $avatar ?>" class="staffpicture mx-2 my-2">
									<a class="font22 link-unstyled mx-2 my-2" href="<?=$this->url('profile_show',['username'=>$admin['username']])?>" ><?=$admin['username']?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-lg-6 my-3">
				<div class="border shadow bg-light mx-2 mt-md-5 etiquette-right">
					<h5 class="my-3">Modérateurs</h5>
					<hr class="w-50">
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled mx-0 px-0">
							<?php foreach ($mods as $mod ) : ?>
								<?php $avatar = (!empty($mod['avatar'])) ? $mod['avatar'] : $this->assetUrl('/img/nophoto.jpg'); ?>
								<li class="d-flex align-items-center">
									<img src="<?= $avatar ?>" class="staffpicture mx-2 my-2">
									<a class="font22 link-unstyled mx-2 my-2" href="<?=$this->url('profile_show',['username'=>$mod['username']])?>" ><?=$mod['username']?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div><!-- /row -->
		<div class="row justify-content-center">
			<div class="col-xxl-4 col-lg-6 my-3">
				<div class="border shadow bg-light mx-2">
					<h5 class="my-3">Animateurs Radio</h5>
					<hr class="w-50">
					<div class="d-flex justify-content-center">
						<ul class="mx-0 px-0">
							<?php foreach ($radios as $radio ) : ?>
								<li class="d-flex align-items-center">
									<img src="<?=$radio['avatar']?>" class="staffpicture mx-2 my-2">
									<a class="font22 link-unstyled mx-2 my-2" href="<?=$this->url('profile_show',['username'=>$radio['username']])?>" ><?=$radio['username']?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="py-5"></div>
	</div>
</section>

<?php $this->stop('main_content'); ?>