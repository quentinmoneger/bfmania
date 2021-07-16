<?php $this->layout('layout', [
    'title' => 'Forum de discussion',
    'flash_custom' => true,
]); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/forum/forum.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section id="cover">
    <div class="position-absolute h-100 w-100 d-flex">
        <div class="m-auto">
            <h1>Forum</h1>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="row mt-4">
            <div class="col-12 col-lg-9"> 
                <div class="section-chat">                 
                    <?=$this->insert('/front/_partials/chat.html')?>
                </div>
                <div class="section-forum">
                    <div class="list-categories mt-0">
                    
                        <?= $this->fetch('front/_partials/flash-message', ['flash_custom' => true]); ?>
                        
                        <?php foreach ($categories as $forum) : ?>

                            <?php $roles = json_decode($forum['auth'], true); ?>
                            <?php $myRole = (isset($w_user['role'])) ? $roles[$w_user['role']] : ''; ?>

                            <?php if (($myRole == 1) || ($roles['visitor'] == 1)) :?>
                                <div class="has-cat">
                                    <!-- <div class="d-flex justify-content-start flex-nowrap"> -->
                                    <div class="row">
                                        <div class="col-md-8 m-auto">
                                            <div class="row">
                                                <div class="col-2 col-xl-1">                          
                                                    <div class="icon">
                                                        <i class="fas fa-comment" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                                <div class="col-10 col-xl-11">
                                                    <div class="cat-name">
                                                        <a href="<?= $this->url('list_topics', ['url' => $forum['url']]); ?>">
                                                            <?= $forum['title'] ?>
                                                        </a>
                                                    </div>
                                                    <div class="cat-desc text-break"><?= $forum['description'] ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 margin-at-768">
                                            <?php if(!empty($forum['id_topic'])) : ?>
                                            <a href="<?= $this->url('view_topic', ['id' => $forum['id_topic'], 'page' => 1]); ?>" class="view-last-post" title="Le Nouveau Site">
                                                <div class="cat-last-message h-100 d-flex justify-content-start align-items-center">
                                                    <div class="infos">
                                                        <span class="title-post"><?=($forum['topic_title'])?$forum['topic_title']:'Aucun topic'?></span>
                                                        <br><?=($forum['username'])? 'par '. $forum['username']: ''?>
                                                        <br>Dernière activité <?=($forum['datetime_last_post'])? \Tools\Utils::timeAgo($forum['datetime_last_post']): 'il y a quelques secondes';?>
                                                    </div>
                                                    <div class="arrow ml-auto">
                                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php else : ?>
                                                <div class="cat-last-message h-100 d-flex justify-content-start align-items-center">
                                                    <span>Aucun topic</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div><!-- /row -->
                                </div><!-- /has-cat -->
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
            <div class="col-md col-lg bg-gray ml-2 mt-3">
            <?php //dd($forum_lasts); ?>
                <div class="py-2 list-last-answers">
                    <h4 class="lobster text-center fw-400">Les dernières réponses</h4>
                    <?php foreach ($forum_lasts as $forum_last) : ?>
                        <?php if($forum_last['message'] != null): ?>

                            <?php $roles = json_decode($forum_last['auth'], true); ?>
                            <?php $myRole = (isset($w_user['role'])) ? $roles[$w_user['role']] : ''; ?>
                            <?php if (($myRole == 1) || ($roles['visitor'] == 1)) :?>
                                <div class="my-3"> 
                                    <a href="<?= $this->url('list_topics', ['url' => $forum_last['forum_url']]); ?>">
                                        <h4 class="red-radiant text-white text-center py-2 mb-0"><span"><?= $forum_last['forum_title'] ?></span></h4>
                                    </a>
                                    <a href="<?= $this->url('view_topic', ['id' => $forum_last['topic_id'], 'page' => 1]); ?>">    
                                        <div class="grey-radiant p-1">
                                        <h5 class="px-1 pt-1"><i class="fa fa-bullhorn pr-1"></i><span class="pl-2"><?= $forum_last['topic_title'] ?></span></h5>
                                            <i class="fad fa-quote-left"></i>
                                            <i class="text-secondary px-1"><?= strip_tags($forum_last['message']) ?></i>
                                            <div class="text-right"><i class="fad fa-quote-right"></i></div>
                                        </div>
                                    </a>
                                    <p class="author-post px-2 text-right my-0 pb-2"><span><a href="<?= $this->url('profile_show', ['username' => $forum_last['username']]); ?>"><?= $forum_last['username'] ?></a></span></p>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>
                    <?php endforeach ?>
                </div>
            </div><!-- /col -->
        </div><!-- /row -->
    </div><!-- /container -->

</section>
<?php $this->stop('main_content'); ?>
<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('js/front/chat/chat-client.js'); ?>"></script>
<?php $this->stop('js') ?>