<?php $this->layout('layout', ['title' => 'liste des membres']);?>

<?php $this->start('main_content');?>
<div class="container">
	<div class="row ">
		<div  class="col-12 text-center">
			<h1 class="title-page text-center with-underline mt-5 mb-3">Les membres</h1>
			<div class="form-group">	
				<div class="input-group justify-content-center">
					<input id="search" type="search" name="search" class="form-control" placeholder="rechercher" style="max-width: 300px;">
					<div class="input-group-prepend">
						<button id="submit" class="btn btn-first" type="submit"><i class="far fa-search font16 line-height-1"></i></button>
					</div>
				</div>
			</div> 
			<div id="members">
			</div>
		</div>
	</div>
</div>

<?php $this->stop('main_content');?>
<?php $this->start('js'); ?>
<script>
var urlSearchProfile = '<?php echo $this->url('profile_search');?>?search='
</script>
<script src="<?= $this->assetUrl('js/front/profile/list.js'); ?>"></script>
<?php $this->stop('js'); ?>