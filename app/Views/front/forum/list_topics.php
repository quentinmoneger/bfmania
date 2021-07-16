<?php $this->layout('layout', ['title' => 'liste des Sujets']); ?>

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

<section id="list-topic" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-9">

                <div class="text-right">
                    <a href="<?= $this->url('list_forum'); ?>" class="font14 text-dark"><i class="fad fa-undo icon-color"></i> Revenir aux Catégories</a>
                </div>
                <div class="categorie-forum d-flex justify-content-between">
                    <h4 class="categorie-title">
                        <span><?= $ctg['title'] ?></span>
                        <small class="categorie-description">Echanges à propos du site, annonces, maintenances, bugs... Vous pouvez également poster vos suggestions d'améliorations.</small>
                    </h4>

                    <div class="pt-3">
                        <a href="<?= $this->url('add_topic', ['category' => $ctg['id'], 'url' => $ctg['url']]); ?>" class="btn btn-danger btn-custom btn-sm">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp; Nouveau sujet
                        </a>
                    </div>
                </div>
                
                <div class="list-topics mt-0">
                    <?php foreach ($topics as $topic) : ?>
                        <?php $roles = json_decode($topic['auth'], true); ?>
                        <?php $myRole = (isset($w_user['role'])) ? $roles[$w_user['role']] : ''; ?>
                        <?php if (($myRole == 1) || ($roles['visitor'] == 1)) :?>

                            <?php if ($topic['pin'] == 1) : ?>
                                
                                <div class="row align-items-center has-topic">
                                    <div class="col-12 col-lg-5">
                                        <div class="row">
                                            <!-- horn/angledouble topic -->
                                            <div class="col-2 align-self-center">
                                                <?php if (in_array($w_user['role'], [70, 99])) : ?>
                                                    <div class="icon pin" data-toggle="tooltip" data-title="Enlever l'épingle" data-color="red">
                                                        <form method="post" action="<?= $this->url('pin_topic', ['url' => $ctg['url']]); ?>">
                                                            <input type="number" value="0" name="pin" hidden>
                                                            <input type="number" value="<?= $topic['id'] ?>" name="id_topic" hidden>
                                                        </form>
                                                        <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="icon">
                                                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                             <!-- Topic titre -->
                                            <div class="topic-row col-10">
                                                <div class="topic-name">
                                                    <a href="<?= $this->url('view_topic', ['id' => $topic['id'], 'page' => 1]); ?>">
                                                        <span class="badge badge-pill badge-danger">épinglé</span> <?= $topic['title'] ?>
                                                    </a>
                                                </div>
                                                <div class="topic-desc">
                                                    Posté par <a href="<?= $this->url('profile_show', ['username' => $topic['firstPostUsername']]); ?>"><?= $topic['firstPostUsername'] ?></a>, <?= \Tools\Utils::timeAgo($topic['date_create']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-4">   
                                        <div class="topic-delete topic-row d-flex justify-content-end align-items-center">
                                            <!-- Nb réponses -->
                                            <div class="topic-reply">
                                                <div class="nb-reply text-right">
                                                    <?= $topic['nbPosts'] -1 ?> réponse<?= $topic['nbPosts']-1 >=2 ? 's' : '' ;?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-3">    
                                        <!-- Dernière réponses par -->
                                        <div class="topic-row">
                                            <a href="<?= $this->url('view_topic', ['id' => $topic['id'], 'page' => 1]); ?>" class="view-last-post" title="Dernier message">
                                                <div class="topic-last-message h-100 d-flex justify-content-start align-items-center">
                                                    <div class="infos">
                                                        Dernière réponse par <?= $topic['lastPostUsername'] ?> <br><?= \Tools\Utils::timeAgo($topic['datetime_last_post']); ?>
                                                    </div>
                                                    <div class="arrow ml-auto">
                                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div><!-- /row -->
                                
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endforeach; ?>

                    <?php foreach ($topics as $topic) : ?>
                        <?php $roles = json_decode($topic['auth'], true); ?>
                        <?php $myRole = (isset($w_user['role'])) ? $roles[$w_user['role']] : ''; ?>
                        <?php if (($myRole == 1) || ($roles['visitor'] == 1)) :?>

                            <?php if ($topic['pin'] == 0) : ?>
                                <div class="row align-items-center has-topic">
                                    <div class="col-12 col-lg-5">
                                        <div class="row">
                                            <!-- horn/angledouble topic -->
                                            <div class="col-2 align-self-center">
                                                <?php if (in_array($w_user['role'], [70, 99])) : ?>
                                                    <div class="icon pin" data-toggle="tooltip" data-title="Epingler ce Sujet" data-color="red">
                                                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                                        <form method="post" action="<?= $this->url('pin_topic', ['url' => $ctg['url']]); ?>">
                                                            <input type="number" value="1" name="pin" hidden>
                                                            <input type="number" value="<?= $topic['id'] ?>" name="id_topic" hidden>
                                                        </form>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="icon">
                                                        <i class="fa fa-angle-double-right" aria-hidden="true"></i>                            
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Topic titre -->
                                            <div class="topic-row col-10">
                                                <div class="topic-name">
                                                    <a href="<?= $this->url('view_topic', ['id' => $topic['id'], 'page' => 1]); ?>">
                                                        <?= $topic['title'] ?>
                                                    </a>
                                                </div>
                                                <div class="topic-desc d-flex justify-content-between">
                                                    <span>Posté par <a href="<?= $this->url('profile_show', ['username' => $topic['firstPostUsername']]); ?>"><?= $topic['firstPostUsername'] ?></a>, <?= \Tools\Utils::timeAgo($topic['date_create']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-4">   
                                        <div class="topic-delete topic-row d-flex justify-content-between align-items-center">
                                            <!-- suppession -->
                                            <div class="topic-desc">
                                                <?php if ($w_user['id'] == $topic['id_author'] || in_array($w_user['role'], [50, 70, 99]) && $w_user['role'] > $topic['roleTopicCreator']) : ?>
                                                    <a href="<?= $this->url('delete_topic', ['id' => $topic['id'], 'author_role' => $topic['roleTopicCreator']]); ?>" class="falseLink red-text mr-2"><i class="fas fa-sm fa-times mr-1"></i><span class="font-weight-normal">Supprimer</span></a>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Nb réponses -->
                                            <div class="topic-reply">
                                                <div class="nb-reply pl-5">
                                                    <!-- "-1" car la première "réponse" affichée est le contenu du message lui-même -->
                                                    <?= $topic['nbPosts'] -1 ?> réponse<?= $topic['nbPosts']-1 >=2 ? 's' : '' ;?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-3">    
                                        <!-- Dernière réponses par -->
                                        <div class="topic-row">
                                            <a href="<?= $this->url('view_topic', ['id' => $topic['id'], 'page' => 1]); ?>" class="view-last-post" title="Dernier message">
                                                <div class="topic-last-message h-100 d-flex justify-content-start align-items-center">
                                                    <div class="infos">
                                                        Dernière réponse par <?= $topic['lastPostUsername'] ?> <br><?= \Tools\Utils::timeAgo($topic['datetime_last_post']); ?>
                                                    </div>
                                                    <div class="arrow ml-auto">
                                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                </div><!-- /row -->
                            <?php endif; ?>

                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-sm offset-lg-1 col-lg bg-gray ml-2 mt-3">
                <div class="topic-last-answers py-2">
                    <h4 class="lobster text-center fw-400">Les dernières réponses</h4>
                    <?php foreach ($topic_lasts as $topic_last) : ?>
                        <?php if($topic_last['message'] != null): ?>

                            <?php $roles = json_decode($topic_last['auth'], true); ?>
                            <?php $myRole = (isset($w_user['role'])) ? $roles[$w_user['role']] : ''; ?>
                            <?php if (($myRole == 1) || ($roles['visitor'] == 1)) :?>
                                <div class="last-answer-card my-2">
                                    <a href="<?= $this->url('view_topic', ['id' => $topic_last['id'], 'page' => 1]); ?>">
                                        <div class="pt-2">
                                            <h5 class="topic-title-radiant text-s p-2 mb-0"><i class="fa fa-bullhorn pr-1"></i><span class="pl-2"><?= $topic_last['title'] ?></h5>
                                        </div>
                                        <div class="grey-radiant text-secondary px-1 pt-2">
                                            <i class="fad fa-quote-left"></i>
                                            <i><?= strip_tags($topic_last['message']) ?></i>
                                            <div class="text-right"><i class="fad fa-quote-right"></i></div>
                                        </div>
                                    </a>
                                    <p class="author-post text-right px-2 pb-2 mb-0">Par : <span><a href="<?= $this->url('profile_show', ['username' => $topic_last['username']]); ?>"><?= $topic_last['username'] ?></a></span></p>                  
                                </div>                     
                            <?php endif; ?>

                        <?php endif; ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div><!-- /row -->
        
        <!-- Pagination -->
        <?php if($totalPage > 1) : ?>
            <div class="d-flex justify-content-center">
                <nav>
                    <ul class="pagination flex-wrap">
                        <li class="page-item <?= ($currentPage == 1) ? "disabled" : ""; ?>">
                            <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $currentPage - 1]) ?>" class="page-link">Précédente</a>
                        </li>

                        <?php if($totalPage < 5) : ?>
                            <?php for( $page = 1; $page <= $totalPage; $page++ ) : ?>
                            <li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
                                <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
                            </li>
                            <?php endfor ?>

                        <?php else : ?>
                            <!-- Les deux premières pages -->
                            <?php $page = 1; ?>
                            <li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
                                <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
                            </li>
                            <?php $page = 2; ?>
                            <li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
                                <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
                            </li>

                            <!-- ... ? ... -->
                            <?php if($currentPage == 3) : ?>
                                <li class="page-item <?= ($currentPage > 2 && $currentPage < $totalPage-1) ? "active" : ""; ?>">
                                    <span href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $page]) ?>" class="page-link"><?= $currentPage.' ...' ?></span>
                                </li>
                            <?php elseif($currentPage == $totalPage-2) : ?>
                                <li class="page-item <?= ($currentPage > 2 && $currentPage < $totalPage-1) ? "active" : ""; ?>">
                                    <span class="page-link">... <?= ($currentPage > 2 && $currentPage < $totalPage-1) ? $currentPage : "" ; ?></span>
                                </li>
                            <?php else : ?>
                                <li class="page-item <?= ($currentPage > 2 && $currentPage < $totalPage-1) ? "active" : ""; ?>">
                                <span class="page-link">... <?= ($currentPage > 2 && $currentPage < $totalPage-1) ? $currentPage.' ...' : "" ; ?></span>
                                </li>
                            <?php endif ?>

                            <!-- Les deux dernières pages -->
                            <?php $page = $totalPage-1; ?>
                            <li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
                                <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
                            </li>
                            <?php $page = $totalPage; ?>
                            <li class="page-item <?= ($currentPage == $page) ? "active" : ""; ?>">
                                <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $page]) ?>" class="page-link"><?= $page ?></a>
                            </li>
                            
                        <?php endif ?>
                        <li class="page-item <?= ($currentPage == $totalPage) ? "disabled" : ""; ?>">
                            <a href="<?= $this->url($w_current_route, ['url' => $url, 'page' => $currentPage + 1]) ?>" class="page-link">Suivante</a>
                        </li>
                    </ul>
                </nav>	
            </div>
            <?php endif; ?>
    </div><!-- /container -->
</section>
<?php $this->stop('main_content'); ?>
<?php $this->start('js') ?>
<script src="<?= $this->assetUrl('js/front/forum/list_topics.js'); ?>"></script>
<?php $this->stop('js') ?>