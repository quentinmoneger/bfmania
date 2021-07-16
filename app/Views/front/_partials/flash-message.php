<?php if (!empty($w_flash_message) && isset($flash_custom)): ?>
	<?php foreach($w_flash_message as $flash_msg):?>
	<div class="alert alert-<?=$flash_msg['level'];?> alert-dismissible alert-custom" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
		<?=$flash_msg['message'];?>
	</div>
	<?php endforeach;?>
<?php endif; ?>