<?php $this->layout('layout_back', [
    'title' => 'Liste des catégories',
]); ?>

<?php $this->start('main_content'); ?>

<h1 class="title font-weight-normal"><i class="fad fa-sitemap"></i> Liste des catégories</h1>
<?php if ($w_user['role'] >= 70) : ?>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="alert alert-light border clearfix" role="alert">
                Liste des catégories présentes dans le forum
                <span class="float-right">
                    <a href="<?= $this->url('forum_add_categories'); ?>" class="btn btn-outline-dark btn-sm">
                        <i class="far fa-sm fa-plus-circle"></i> Créer une nouvelle catégorie
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="bg-white border p-4">
                <div class="table-responsive">
                    <table class="table table-hover mt-3">
                        <thead class="bg-light">
                            <tr>
                                <th>Ordre</th>
                                <th>Titre</th>
                                <th style="min-width: 300px;">Description</th>
                                <th>Url</th>
                                <th style="min-width: 150px;">Autorisation</th>
                                <th>Date de création</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <?php foreach ($ctgDb as $ctg) : ?>
                            <tbody>
                                <tr>
                                    <td><?= $ctg['position']; ?></td>
                                    <td><?= $ctg['title'] ?></td>
                                    <td style="max-width: 150px;" class="text-break"><?= $ctg['description']; ?></td>
                                    <td><a href="<?= $this->url('list_topics', ['url' => $ctg['url']]); ?>" target="_blank" title="Voir la page sur le site">
                                            /<?= $ctg['url']; ?>
                                        </a></td>
                                    <td>
                                        <ul class="list-unstyled">
                                            <?php foreach (json_decode($ctg['auth'], true) as $key => $value) : ?>
                                                <li>
                                                    <?= ($value == 1) ? '<i class="fas fa-check-square" style=" color: green"></i>' : '<i class="fas fa-times-square" style=" color: red"></i>'; ?>
                                                    <?= $rolesAvailable[str_replace('allow_', '', $key)]; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($ctg['date_create'])); ?></td>
                                    <td>
                                        <a href="<?= $this->url('forum_edit_categories', ['id' => $ctg['id']]); ?>" class="text-secondary"><i class="fas fa-sm fa-edit"></i>&nbsp;Modifier</a>
                                        &nbsp;<br>
                                        <a href="<?= $this->url('forum_delete_categories', ['id' => $ctg['id']]); ?>" class="red-text"><i class="fas fa-sm fa-times"></i>&nbsp;Supprimer</a>
                                    </td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php else :  ?>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="bg-white border p-4">
                <p>Seuls les administrateurs ont accès à la gestion des catégories.</p>
            </div>
        </div>
    </div>


<?php endif ?>
<?php $this->stop('main_content'); ?>