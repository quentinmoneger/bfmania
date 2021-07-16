<?php $this->layout('layout', ['title' => 'Mon compte']); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/faq.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section class="pb-5 pt-3">
    <div class="container">
        <div class="col col-md-10 mx-auto py-4">

            <div class="text-center my-5">
                <h1 class="title-page with-underline"><?= $categoriesList[$id_category]['title'] ?></h1>
            </div>
            <div class="text-right">
                <a href="<?= $this->url('default_faq_home'); ?>" class="font14 text-dark"><i class="fad fa-undo icon-color"></i> Revenir aux catégories</a>
            </div>
            <div class="content pb-5">
                <div class="row no-gutters">

                    <div class="w-100 <?= ($faqData[1]) ? 'accordion' : '' ?>" id="accordionExample">

                        <?php foreach ($faqData as $faq) : ?>

                            <div class="card-header btn-faq btn-btm" id="heading-<?= $faq['id'] ?>">
                                <h2 class="mb-0">
                                    <span class="btn w-100 upsideDown text-left font17" data-toggle="collapse" data-target="#collapse-<?= $faq['id'] ?>" aria-expanded="true" aria-controls="collapse-<?= $faq['id'] ?>">
                                        <?= $faq['question'] ?><i class="fad fa-sort-down upDown icon-color ml-2 font20"></i>
                                    </span>
                                </h2>
                            </div>
                            <div id="collapse-<?= $faq['id'] ?>" class="collapse" aria-labelledby="heading-<?= $faq['id'] ?>" data-parent="#accordionExample">

                                <div class="card-body body-card ml-5 my-3">
                                    <?= $faq['answer'] ?>
                                </div>

                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<?php $this->stop('main_content'); ?> <?php $this->start('js'); ?> 
<script src="<?= $this->assetUrl('js/front/faq/faq.js'); ?>"></script>
<?php $this->stop('js'); ?>