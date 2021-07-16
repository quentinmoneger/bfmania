<?php $this->layout('layout', [
	'title' 			=> $page['title'], 
	'meta_description'  => $page['meta_description']
]) ?>

<?php $this->start('main_content');?>
<section id="cover-page" style="background-image:url('<?=$page['picture_cover'];?>');"></section>
<section class="pb-5 pt-3">
	
	<div class="container">

		<div class="row justify-content-center">
			<div class="col-12 col-xxl-10">
				<div class="text-center mb-5">
					<h2 class="title-page with-underline"><?=$page['title'];?></h2>
				</div>

				<div id="content-page">
					<?php 
					// Template de page
					$i=1;
					$content = json_decode($page['content'], true);
					$rows = explode('|', $template);
					foreach($rows as $row){
						$cols = explode(',', $row);
						echo '<div id= class="row">'.PHP_EOL;
						foreach($cols as $col){
							echo '<div class="col-'.$col.'">'.PHP_EOL;
							echo $content[$i].PHP_EOL;
							echo '</div>'.PHP_EOL;
							$i++;
						}
						echo '</div>'.PHP_EOL;
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php $this->stop('main_content');?>
