<?php $this->layout('layout', ['title' => 'FAQ']); ?>

<?php $this->start('main_content'); ?>

<link rel="stylesheet" href="<?= $this->assetUrl('css/front/faq.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<section class="pb-5 pt-3">
    <div class="container">
        <div class="col col-md-10 mx-auto py-4">

            <div class="text-center my-5">
                <h1 class="title-page with-underline">Foire aux questions</h1>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="text-justify mb-5">
                        <p class="font18"><i class="fad fa-quote-left fa-pull-left mr-2 icon-color font24"></i> Vous recherchez une information ? Les questions les plus fréquemment posées sont recensées dans notre FAQ. Sélectionnez la catégorie qui vous intéresse.</p>
                    </div>
                </div>
                <div class="w-100 mt-4"></div>
            </div>

            <div class="row justify-content-around">
                <?php foreach ($categoriesList as $cat) : ?>

                    <a href="<?= $this->url('default_faq_category', ['category' => $cat['url']]); ?>" class="col-sm-12 col-md-5 col-xl-3 my-3 my-xl-0 bg-light faq border rounded-lg">
                        <div class="item-faq h-100 d-flex flex-column">
                            <div class="my-auto">
                                <i class="fad <?= $cat['icon'] ?>"></i>
                                <h5><?= $cat['name'] ?></h5>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<div class="bg-left">
    <div class="footer-faq"></div>
    <i class="fad fa-question-circle"></i>
</div>
<?php $this->stop('main_content'); ?>