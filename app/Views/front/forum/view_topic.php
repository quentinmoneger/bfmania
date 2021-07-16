<?php $this->layout('layout', ['title' => 'vue des sujets']); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/forum/forum.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section class="pb-5 mt-4">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-12 col-lg-9">
			
				<div class="topic-forum d-flex justify-content-between">
					<h4 class="topic-title">
						<span><?= $topic['title'] ?></span>
					</h4>
					<div class="text-right">
						<a href="<?= $this->url('list_topics', ['url' => $topic['url']]); ?>" class="font14 text-dark mr-4"><i class="fad fa-undo icon-color"></i> Revenir aux Sujets</a>
					</div>
				</div>
				
				<div class="list-posts mt-0">
					
					<?php foreach ($paginates as $post) : ?>
						
						<?php $roles = json_decode($post['auth'], true); ?>
                        <?php $myRole = (isset($w_user['role'])) ? $roles[$w_user['role']] : ''; ?>

                        <?php if (($myRole == 1) || ($roles['visitor'] == 1)) :?>

							<div class="has-post row" id="p<?= $post['id'] ?>">
								<div class="col-md-3 post-user text-center">
									<a href="/membre/<?= $post['username'] ?>">
										<img src="<?= ($post['avatar']) ? $post['avatar'] : $this->assetUrl('/img/nophoto.jpg') ?>" width="80" height="80" class="rounded-circle author_avatar">
										<p class="author_username">"<?= $post['username'] ?>"</p>
									</a>
									<div class="mt-n2"><span><?= \Tools\Utils::getHumanRole($post['role'], false, true) ?></span></div>
								</div>
								<div class="col-md-9 post-message">
									<div id="msg_<?= $post['id'] ?>" class="collapse show pb-4 mb-md-0 mb-5">
										<?= $post['message'] ?>
									</div>
	
									<?php if ($w_user['id'] == $post['id_author'] || in_array($w_user['role'], [50, 70, 99]) && $w_user['role'] > $post['role']): ?>
										<div id="modify_<?= $post['id'] ?>" class="collapse">
											<form method="POST" action="<?= $this->url('update_post', [
																					'id_post' => $post['id'],
																					'id_topic' => $topic['id'],
																					'author_role' => $post['role'],
																				]); ?>">

												<div class="form-group">
													<textarea modifyContent="true" name="post_msg" id="post_msg" class="form-control" rows="7"><?= $post['message'] ?></textarea>
												</div>
												<div class="form-group text-center pt-2 pb-5">
													<span class="btn btn-danger btn-sm btn-rounded py-0 px-4 mx-2" data-toggle="collapse" data-target="#modify_<?= $post['id'] ?>, #msg_<?= $post['id'] ?>"><i class="fad fa-times"></i> Annuler</span>
													<button type="submit" class="btn btn-danger btn-sm btn-rounded py-0 px-4 mx-2"><i class="fad fa-file-alt"></i> Modifier</button>
												</div>
											</form>
										</div>
										
										<div id="delete_<?= $post['id'] ?>" class="collapse pb-5">
											<form method="POST" action="<?=  $this->url('delete_post', [
																					'id_post' => $post['id'],
																					'id_topic' => $topic['id'],
																					'author_role' => $post['role'],
																				]); ?>">

												<div class="alert alert-danger">
													La suppression du message est définitive, êtes vous sur de bien vouloir le supprimer ?
												</div>
												<input hidden type="text" value="delete" name="delete">
												<div class="form-group text-center">
													<span class="btn btn-danger btn-sm btn-rounded py-0 px-4 mx-2" data-toggle="collapse" data-target="#delete_<?= $post['id'] ?>, #msg_<?= $post['id'] ?>"><i class="fad fa-times"></i> Annuler</span>
													<button type="submit" class="btn btn-danger btn-sm btn-rounded py-0 px-4 mx-2"><i class="fad fa-trash-alt"></i> Supprimer </button>
												</div>
											</form>
										</div>
									<?php endif; ?>
									
									<div class="info-message row justify-content-center">
										<?php if ($w_user['id'] == $post['id_author'] || in_array($w_user['role'], [50, 70, 99]) && $w_user['role'] > $post['role']): ?>
											<div>
												<a href="" class="mr-3 text-secondary" data-toggle="collapse" data-target="#modify_<?= $post['id'] ?>, #msg_<?= $post['id'] ?>"><i class="fad fa-eraser"></i> Editer</a>
												<a href="" class="mr-3 text-danger" data-toggle="collapse" data-target="#delete_<?= $post['id'] ?>, #msg_<?= $post['id'] ?>"><i class="fad fa-trash"></i> Supprimer</a>
											</div>
										<?php endif; ?>
										<div>
											<span data-toggle="tooltip" data-placement="top" title="<?= \Tools\Utils::dateFr($post['date_create'], 'd/m/Y H:i'); ?>">Posté <?= \Tools\Utils::timeAgo($post['date_create']); ?></span>
											<i type="button" class="fal chat-report fa-comment-alt-exclamation px-1" data-toggle="modal" data-report-type="forum" data-username-from="<?= $post['username'] ?>" data-id="<?= $post['id'] ?>"  data-target="#report"></i>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
						
					<?php endforeach; ?>
					
					<!-- Pagination -->
					<?php if(ceil($count[0]['nbPosts']/$limit) > 1) : ?>
					<div class="d-flex justify-content-center">
						<nav>
							<ul class="pagination flex-wrap">
								<li class="page-item <?= ($currentPage == 1) ? "disabled" : ""; ?>">
									<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $currentPage - 1]) ?>" class="page-link">Précédente</a>
								</li>

								<?php if((ceil($count[0]['nbPosts']/$limit))<5) : ?>

									<?php for( $page = 1; $page <= (ceil($count[0]['nbPosts']/$limit)); $page++ ) : ?>
									<li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
										<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
									</li>
									<?php endfor ?>

								<?php else : ?>
									<!-- Les deux premières pages -->
									<?php $page = 1; ?>
									<li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
										<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
									</li>
									<?php $page = 2; ?>
									<li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
										<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
									</li>

									<!-- ... ? ... -->
									<?php if($currentPage == 3) : ?>
										<li class="page-item <?= ($currentPage > 2 && $currentPage < ceil($count[0]['nbPosts']/$limit)-1) ? "active" : ""; ?>">
											<span class="page-link"><?= ($currentPage > 2 && $currentPage < ceil($count[0]['nbPosts']/$limit)-1) ? $currentPage.' ...' : "" ; ?></span>
										</li>
									<?php elseif($currentPage == ceil($count[0]['nbPosts']/$limit)-2) : ?>
										<li class="page-item <?= ($currentPage > 2 && $currentPage < ceil($count[0]['nbPosts']/$limit)-1) ? "active" : ""; ?>">
											<span class="page-link">... <?= ($currentPage > 2 && $currentPage < ceil($count[0]['nbPosts']/$limit)-1) ? $currentPage : "" ; ?></span>
										</li>
									<?php else : ?>
										<li class="page-item <?= ($currentPage > 2 && $currentPage < ceil($count[0]['nbPosts']/$limit)-1) ? "active" : ""; ?>">
											<span class="page-link">... <?= ($currentPage > 2 && $currentPage < ceil($count[0]['nbPosts']/$limit)-1) ? $currentPage.' ...' : "" ; ?></span>
										</li>
									<?php endif ?>

									<!-- Les deux dernières pages -->
									<?php $page = ceil($count[0]['nbPosts']/$limit)-1 ; ?>
									<li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
										<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
									</li>
									<?php $page = ceil($count[0]['nbPosts']/$limit); ?>
									<li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
										<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
									</li>
								<?php endif ?>
								<li class="page-item <?= ($currentPage == (ceil($count[0]['nbPosts']/$limit))) ? "disabled" : ""; ?>">
									<a href="<?= $this->url($w_current_route, ['id' => $id_topic, 'page' => $currentPage + 1]) ?>" class="page-link">Suivante</a>
								</li>
							</ul>
						</nav>	
					</div>
					<?php endif; ?>

					<?php if (!empty($w_user)) : ?>
						<div class="text-center">
							<button class="btn btn-danger btn-custom btn-sm" data-toggle="collapse" data-target="#reply">Répondre</button>
						</div>
						<div id="reply" class="row justify-content-center collapse">
							<div class="col m-3">
								<div class="bg-color-2 border p-4 rounded">
									<div class="mx-auto">
									
										<form method="POST" action="<?= $this->url('add_post', ['id_topic' => $topic['id']]); ?>">

											<div class="form-group">
												<label class="mb-n1 p-2 input-group-text border-bottom-0">Réponse :</label>
												<textarea contenteditable="true" name="post_msg" id="post_msg" class="form-control" rows="7"></textarea>
											</div>
											<div class="form-group text-center">
												<button type="submit" class="btn btn-first btn-sm px-5"><i class="fad fa-file-alt"></i> Publier</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>

			</div><!-- /col -->
		</div><!-- /row -->
	</div><!-- /container -->
</section>

<?php $this->stop('main_content'); ?>
<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('libs/ckeditor/ckeditor.js'); ?>"></script>
<script src="<?= $this->assetUrl('js/front/forum/view_topic.js'); ?>"></script>
<?php $this->stop('js') ?>