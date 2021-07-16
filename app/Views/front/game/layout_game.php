<?php $this->layout('layout', ['title' => ucfirst($game_name)]); ?>

<?php $this->start('main_content'); ?>
<link rel="stylesheet" href="<?= $this->assetUrl('css/front/game/' . $game_name . '.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>
<div class="row">
    <section class="col-2 text-center theme-light">
        <table class=" table">
            <thead>
                <tr>
                    <th scope="col"><i class="fas fa-users"></i> Meilleurs joueurs &#127942</th>
                </tr>
            </thead>
            <tbody id="bestPlayers">
            </tbody>
        </table>
    </section>
    <section class="col-8">
        <div id="container" >
            <?= $this->insert('/front/game/'. $game_name) ?>
        </div>
    </section>
    <section class="col-2 text-center theme-light">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"><i class="fas fa-user"></i> Votre meilleur score &#127941</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="bestresult" class="p-1"></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th scope="col"> Vos derniers Scores <i class="far fa-clock"></i></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="result1" class="p-1"></td>
                </tr>
                <tr>
                    <td id="result2" class="p-1"></td>
                </tr>
                <tr>
                    <td id="result3" class="p-1"></td>
                </tr>
                <tr>
                    <td id="result4" class="p-1"></td>
                </tr>
                <tr>
                    <td id="result5" class="p-1"></td>
                </tr>
            </tbody>
        </table>
    </section>
</div>
<?php $this->stop('main_content'); ?>
<?php $this->start('js'); ?>
<script>
    var game_name = '<?= $game_name ?>'
        urlScore = '<?= $this->url('game_score'); ?>',
        urlLoadScore = '<?= $this->url('game_load_score'); ?>'
</script>
<script src="<?= $this->assetUrl('js/front/game/' . $game_name . '.js'); ?>"></script>
<script src="<?= $this->assetUrl('js/front/game/game_score.js'); ?>"></script>
<?php $this->stop('js'); ?>