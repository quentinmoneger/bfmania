<?php $this->layout('layout', ['title' => 'Messagerie']); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/messaging.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section class="">
	<div class="container">
		<div class="text-center mt-5">
			<h1 class="title-page with-underline">Messagerie</h1>
		</div>
		<div class="row content pb-2  no-gutters justify-content-center">
			<?= $this->fetch('front/_partials/sidebar-account'); ?>
			<div class="m-2 col-12 col-xl-8 account-overview">
				<div class="desktop row bg-secondary m-0 box rounded-top p-3">
					<button type="button" id="btnNew" class="btn btn-sm btn-outline-light mx-2 px-3 py-0 font14">Nouveau Message</button>
					<span type="button"><i class="btn_none far fa-plus-circle postion-absolute addMessage text-white bg-secondary "></i></span>
					<button type="button" id="btnReceive" class="btn btn-sm btn-outline-light mx-2 px-3 py-0 font14">Boite de reception</button>
					<button type="button" id="btnSend" src="" class=" btn btn-sm btn-outline-light mx-2 px-3 py-0 font14">Messages envoyés</button>
					<a href="<?= $this->url('messages_home'); ?>" class="float-right refresh btn  btn-sm btn-outline-light mx-2 px-3 mt-1 py-0 font14"><i class=" fas fa-redo-alt "></i></a>
				</div>
				<div class="mobile row bg-secondary m-0 box rounded-top p-3">
					<button type="button" id="btnNewMobile" class="btn btn-sm btn-outline-light mx-2 px-3 py-0 font14"><i class="fas fa-paper-plane"></i></button>
					<button type="button" id="btnReceiveMobile" class="btn btn-sm btn-outline-light mx-2 px-3 py-0 font14"><i class="fas fa-envelope"></i></button>
					<button type="button" id="btnSendMobile" class=" btn btn-sm btn-outline-light mx-2 px-3 py-0 font14"><i class=" fas fa-inbox-out"></i></button>
					<a href="<?= $this->url('messages_home'); ?>" class="float-right refresh btn  btn-sm btn-outline-light mx-2 px-2 mt-1 py-0 font14"><i class=" fas fa-redo-alt "></i></a>
				</div>
				<div class="listMessages rounded-bottom border">

					<div id="get-box" class="bg-light collapse show h-100">
						<?php if ($discussion) : ?>

							<?php foreach ($discussion as $message) : ?>
								<!-- LIST MESSAGES-->
								<div id="list-<?= $message['id'] ?>" class="collapse <?= ($message['id_from'] == $w_user['id'] && !isset($message['bis'])) ? 'send-box ' : ' show get-box' ?>">
									<div class="row px-2 py-1 mx-0 text-color-2 mpline border-bottom align-items-center
									<?= (strstr($message['read_message'], ',' . $w_user['id']) || !strstr(',' . $w_user['id'], $message['id_to'])) ? '' : 'bg-white' ?> ">
										<div class="col-md-4 col-12 px-1 border-right p-0 text-truncate">
											<?php if (strpos($message['id_to'], (',' . $w_user['id'])) !== false) : ?>
												<span>
													<i class="font13 envelope border-right pr-1 fas fa-envelope<?= (strstr($message['read_message'], $w_user['id'])) ? '-open " data-open=1"' : '" data-open=0' ?>" data-toggle=" tooltip" data-container=".listMessages"></i>
													<input hidden type="text" value="<?= $message['id'] ?>" name="id_post" class="message-id">
												</span>											
											<?php endif; ?>

											<a class="<?= ($message['id_from'] == $w_user['id'] && !isset($message['bis'])) ? '' : ' read-mail ' ?> link-colored text-secondary m-0 align-middle" data-toggle="collapse" data-target="#read-<?= $message['id'] ?>, <?= ($message['id_from'] == $w_user['id'] && !isset($message['bis'])) ? '.send-box ' : '.get-box' ?>">
												<?php if (strpos($message['id_to'], (',' . $w_user['id'])) !== false) : ?>
													<div class="d-inline-block mr-2 align-middle ">
														<small>de :</small>
														<?= $message['username_from'] ?>
														<small><?= (isset($message['answer'])) ? '[' . (count($message['answer']) + 1) . ']' : '' ?></small>
													</div>

												<?php else : ?>
													<div class=" d-inline-block mr-2 align-middle text-truncate">
														<i class="border-right pr-1 fas fa-inbox-out font13"></i>
														<small>à :</small>
														<?= $message['username_copy']  ?>
														<?php $user_to = ($message['username_from'] == $w_user['username']) ? 'moi' : $message['username_from'] ?>
														<?= ($message['id_message_parent']) ? ', ' . $user_to : ''; ?>
														<small><?= (isset($message['answer'])) ? '[' . (count($message['answer']) + 1) . ']' : '' ?></small>
													</div>
												<?php endif; ?>
											</a>
										</div>
										<div class="col-md-5 col-12 px-1 text-truncate border-right p-0 read-mail" data-toggle="collapse" data-target="#read-<?= $message['id'] ?>, <?= ($message['id_from'] == $w_user['id'] && !isset($message['bis'])) ? '.send-box ' : '.get-box' ?>">
											<?= \Tools\Utils::cutString($message['title'], $length = 39); ?>
											<span class="text-muted font14 pl-2">
												- <?= $message['message'] ?>
											</span>
										</div>
										<div class="col-md-3 col-12 px-1 mp-date d-flex justify-content-between align-items-center p-0 read-mail">

											<div class="mr-1 band">
												<div class="mail-date" data-toggle="collapse" data-target="#read-<?= $message['id'] ?>, <?= ($message['id_from'] == $w_user['id'] && !isset($message['bis'])) ? '.send-box ' : '.get-box' ?>"><?= \Tools\Utils::timeAgo($message['date'], null, 'd/m/Y'); ?></div>
												<div class="collapse mpline-show action-date">
													<?php if (strpos($message['id_to'], (',' . $w_user['id'])) !== false) : ?>
														<input hidden type="text" value="<?= $message['id'] ?>" name="id_post">
														<i class="fa fa-reply-all border-right  px-1" data-toggle="collapse" data-target="#replyall-<?= $message['id'] ?>, #read-<?= $message['id'] ?>, .get-box"></i>
														<i class="fa fa-reply transfer border-right px-1" data-toggle="collapse" data-target="#reply-<?= $message['id'] ?>, #read-<?= $message['id'] ?>, .get-box"></i>
														<i class="fa fa-arrow-alt-right border-right px-1" data-toggle="collapse" data-title="<?= $message['title'] ?>" data-message="<?= $message['message'] ?>"></i>
														<i class="fas fa-trash  border-right px-1" data-toggle="modal" data-target="#delete-<?= $message['id'] ?>"></i>
													<?php else : ?>
														<input hidden type="text" value="<?= $message['id'] ?>" name="id_post">
														<i class="fa fa-arrow-alt-right border-right px-1" data-toggle="collapse" data-title="<?= $message['title'] ?>" data-message="<?= $message['message'] ?>"></i>
														<i class="fas fa-trash border-right px-1" data-toggle="modal" data-target="#delete-<?= $message['id'] ?>"></i>
													<?php endif; ?>
												</div>
											</div>
											<div class="arrow ml-auto">
												<i class="fa fa-chevron-right fa-lg" data-toggle="collapse" data-target="#read-<?= $message['id'] ?>, <?= ($message['id_from'] == $w_user['id'] && !isset($message['bis'])) ? '.send-box ' : '.get-box' ?>" aria-hidden="true"></i>
											</div>
										</div>
									</div>
								</div>
								<!-- READ MESSAGE-->
								<div id="read-<?= $message['id'] ?>" class="bg-white collapse mail px-4 pb-5 h-100">
									<div class="text-right">
										<?php if (strpos($message['id_to'], (',' . $w_user['id'])) !== false) : ?>
											<a href="" data-toggle="collapse" data-target="#read-<?= $message['id'] ?>, .get-box" class="text-secondary font14"><i class="fa fa-undo mr-2"></i>retour boite de réception</a>
										<?php else : ?>
											<a href="" data-toggle="collapse" data-target="#read-<?= $message['id'] ?>, .send-box" class="text-secondary font14"><i class="fa fa-undo mr-2"></i>retour messages envoyés</a>
										<?php endif ?>
										<hr class="mt-1 mx-n4">
									</div>

									<?php if (strpos($message['id_to'], (',' . $w_user['id'])) !== false) : ?>
										<!-- REPLY ALL-->
										<div id="replyall-<?= $message['id'] ?>" class="collapse mail my-4 p-4 border rounded bg-white" style="border-color: #CCC">
											<div class="row">
												<div class="col-12 col-md-8">
													<div class="row">
														<div class="mr-4">
															<img id="imgAvatar" src="<?= $w_user['avatar']; ?>" class="rounded-circle border border-secondary" alt="Avatar de l'expediteur">
														</div>
														<div>
															<small>à : </small><?= $message['username_from']  ?>
															<?php if (!empty($message['username_copy'])) : ?>
																<br><small>en copie : </small><?= $message['username_copy']  ?>
															<?php endif; ?>
															<br><small>objet : </small>re: <?= $message['title']; ?>
														</div>
													</div>
												</div>
												<div class="col-12 col-md-3 mt-2 text-right align-items-end">
													<button class="reply-send-all btn btn-danger btn-sm"><i class="mr-2 text-light fad fa-paper-plane"></i> Envoyer </button>
												</div>
											</div>
											<hr>

											<div>
												<input hidden type="text" name="id_to" class="form-control form-control-sm font16" value="<?= $message['id_to'] ?? ''; ?>" required>
												<input hidden type="text" name="id_from" class="form-control form-control-sm font16" value="<?= $message['id_from'] ?? ''; ?>" required>
												<input hidden type="text" name="title-all" class="form-control form-control-sm font16" value="re: <?= $message['title'] ?? ''; ?>" required>
												<input hidden type="number" name="id_parent" class="form-control form-control-sm font16" value="<?= ($message['id_message_parent']) ? $message['id_message_parent'] : $message['id']; ?>" required>
												<div class="form-group">
													<textarea name="message-reply-all" id="message-reply-all" class="form-control" rows="6" placeholder="Votre Message" required><?= $post['message'] ?? ''; ?></textarea>
												</div>
												<div class="annex">
													<button type="button" class="btn emoji-reply-all" data-toggle="emojiPopper" data-target="#message-reply-all">&#x1F642;</button>
												</div>
												<div class="float-right">
													<button id="reply-send-all-mobil" class="d-none btn btn-danger btn-sm "><i class="text-light fad fa-paper-plane"></i> Envoyer</button>
												</div>
											</div>
										</div>
										<!-- REPLY -->
										<div id="reply-<?= $message['id'] ?>" class="collapse mail my-4 p-4 border rounded bg-white" style="border-color: #CCC">
											<div class="row">
												<div class="col-12 col-md-8">
													<div class="row">
														<div class="mr-4">
															<img id="imgAvatar" src="<?= $w_user['avatar']; ?>" class="rounded-circle border border-secondary" alt="Avatar de l'expediteur">
														</div>
														<div>
															<small>à : </small><?= $message['username_from']  ?>
															<br><small>objet : </small>re: <?= $message['title']; ?>
														</div>
													</div>
												</div>
												<div class="col-12 col-md-3 mt-2 text-right align-items-end">
													<button class="reply-send btn btn-danger btn-sm"><i class="mr-2 text-light fad fa-paper-plane"></i> Envoyer</button>
												</div>
											</div>
											<hr>

											<div>
												<input hidden type="text" name="id_to" class="form-control form-control-sm font16" value="<?= $message['id_to'] ?? ''; ?>" required>
												<input hidden type="text" name="id_from" class="form-control form-control-sm font16" value="<?= $message['id_from'] ?? ''; ?>" required>
												<input hidden type="text" name="title" class="form-control form-control-sm font16" value="re: <?= $message['title'] ?? ''; ?>" required>
												<input hidden type="number" name="id_parent" class="form-control form-control-sm font16" value="<?= ($message['id_message_parent']) ? $message['id_message_parent'] : $message['id']; ?>" required>
												<div class="form-group">
													<textarea name="message-reply" id="message-reply" class="form-control" rows="6" placeholder="Votre Message" required><?= $post['message'] ?? ''; ?></textarea>
												</div>
												<div class="annex">
													<button type="button" class="btn emoji-reply" data-toggle="emojiPopper" data-target="#message-reply">&#x1F642;</button>
												</div>
												<div class="float-right">
													<button id="reply-send-mobil" class="d-none btn btn-danger btn-sm "><i class="text-light fad fa-paper-plane"></i> Envoyer</button>
												</div>
											</div>
										</div>
									<?php endif ?>
									<div>
										<div class="row">
											<div class="col-md-8 col-12">
												<div class="row">
													<div class="mr-4">
														<img id="imgAvatar" src="<?= $message['avatar_from']; ?>" class="rounded-circle border border-secondary" alt="Avatar de l'expediteur">
													</div>
													<div class="text-truncate">
														<?php if ($message['id_from'] == $w_user['id']) : ?>
															<small>à : </small><?= $message['username_copy']; ?>
														<?php else : ?>
															<small>de : </small><?= $message['username_from']; ?>
															<?php if (!empty($message['username_copy'])) : ?>
																<br><small>en copie : </small><?= $message['username_copy']  ?>
															<?php endif; ?>
														<?php endif; ?>
														<br><small>objet : </small><?= $message['title']; ?>
													</div>
												</div>
											</div>
											<div class="col-md-4 col-12 mt-2 text-right">
												<div>
													<?php if ($message['id_from'] == $w_user['id']) : ?>
													<small>Envoyé le: </small>
													<?php else : ?>
													<small>Reçu le: </small>
													<?php endif; ?>
													<span class="font14"><?= \Tools\Utils::dateFr($message['date'], 'd/m/Y \à H:i'); ?></span>
												</div>
												<div class="icon p-1 d-inline-block font18">
													<?php if (strpos($message['id_to'], (',' . $w_user['id'])) !== false) : ?>
														<span class="text-light">|</span>
														<i class="fa fa-reply-all mx-1" data-toggle="collapse" data-target="#replyall-<?= $message['id'] ?>"></i>
														<i class="fa fa-reply mx-1" data-toggle="collapse" data-target="#reply-<?= $message['id'] ?>"></i>
														<i class="fa fa-arrow-alt-right mx-1" data-toggle="collapse" data-title="<?= $message['title'] ?>" data-message="<?= $message['message'] ?>"></i>
														<i class="fa fa-comment-alt-exclamation px-1" data-toggle="modal" data-report-type="messagerie" data-username-from="<?= $message['username_from'] ?>" data-id="<?= $message['id'] ?>" data-target="#report"></i>
													<?php endif ?>
													<span class="text-light">|</span>
													<i class="fas fa-trash mx-1" data-toggle="modal" data-target="#delete-<?= $message['id'] ?>"></i>
													<span class="text-light">|</span>
												</div>
											</div>
										</div>
										<hr class="">
										<div class="mb-4">
											<?= nl2br($message['message']); ?>
										</div>
									</div>
									<!-- DISCUSSIONS -->
									<?php
									$ml = 0;
									if (isset($message['answer'])) :
										foreach ($message['answer'] as $message_parent) :
											$ml += 10;
									?>
											<div class="p-2" style="background-color: #fff; margin-left:<?= $ml ?>px; border-left:2px solid #ddd">

												<div class="d-flex justify-content-start align-items-end">
													<div><small>de :</small> <?= ($message_parent['username_from'] == $w_user['username']) ? 'moi' : $message_parent['username_from']; ?></div>
													<div class="font12 mx-2"><?= \Tools\Utils::dateFr($message_parent['date'], 'd/m/Y \à H:i'); ?></div>
												</div>
												<hr class="my-1">
												<div class="font14" id="collapse<?= $message_parent['id'] ?>" aria-labelledby="heading<?= $message_parent['id'] ?>">
													Message : <?php echo nl2br($message_parent['message']); ?>
												</div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
								<!-- MODAL -->

								<!-- Modal de suppresion -->
								<div class="modal fade" id="delete-<?= $message['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="modalDeleteLabel">Confirmer la suppression</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												Souhaitez-vous vraiment supprimer le message de <?= $message['username_from'] ?> ?
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-color-2" data-dismiss="modal">Non</button>
												<button value="<?= $message['id'] ?>" type="submit" class="button-delete btn btn-first text-light">Oui, supprimer</button>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif ?>
					</div>
					<!-- NEW MESSAGE -->
					<div id="write-message" class="write-message collapse position-relative mx-4 mt-4">
						<div class="ml-0 row mb-3 justify-content-between">
							<select class="to autocomplete form-control col-7 col-lg-10" id="to" name="to[]" multiple>
							</select>
							<div class="col-4 col-lg-2 write-send text-right">
								<button id="write-send" class="btn btn-danger btn-sm "><i class="text-light fad fa-paper-plane"></i> Envoyer</button>
							</div>

						</div>
						<div class="input-group bg-white rounded">
							<label class="mx-2 my-auto" for="title">Objet :</label>
							<input type="text" name="title" id="title" class="shadow-none form-control form-control-sm font16" value="<?= $post['title'] ?? ''; ?>" required>
						</div>

						<div class="form-group">
							<textarea name="message" id="message" class="shadow-none form-control" rows="10" placeholder="Votre Message" required><?= $post['message'] ?? ''; ?></textarea>
						</div>
						<div class="annex">
							<button type="button" class="btn emoji-new" data-toggle="emojiPopper" data-target="#message">&#x1F642;</button>							
						</div>
						<div class="float-right">
							<button id="write-send-mobil" class="d-none btn btn-danger btn-sm "><i class="text-light fad fa-paper-plane"></i> Envoyer</button>
						</div>						
					</div>
				</div>
			</div>
		</div>
</section>
<?php $this->stop('main_content'); ?>
<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('js/front/messaging/messaging.js'); ?>"></script>
<?php $this->stop('js') ?>