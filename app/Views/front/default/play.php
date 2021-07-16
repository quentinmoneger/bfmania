<?php $this->layout('layout', [
	'title' => $action. ' en ligne gratuitement',
]); ?>

<?php $this->start('main_content'); ?>
<section class="pb-5 pt-3">
	<div class="container">
		<div class="col col-md-10 mx-auto py-4">
			<div class="text-center my-5">
				<h1 class="title-page with-underline"><?=ucwords($action);?></h1>
			</div>

			<div class="text-center pt-5">
			
				<a href="javascript:window.open('http://188.165.122.98:8080/', '_blank');"class="btn btn-outline-color-1">Jouez !</a>
			</div>

		</div>
	</div>
</section>
<?php $this->stop('main_content'); ?>