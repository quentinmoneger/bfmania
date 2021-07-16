<?php $this->layout('layout', ['title' => 'Profil de ' . $user['username']]);
$avatar 	= (!empty($user['avatar'])) ? $user['avatar'] : $this->assetUrl('/img/nophoto.jpg');
$month = date('n', $currentMonth);
$w_user['role'] = $w_user['role'] ?? 0;
?>


<?php $this->start('main_content'); ?>
<div class="container">
	<div class="row justify-content-center">
		<div class="col-xxl-10 text-center my-5">
			<h1 class="title-page with-underline text-center">Profil de <span class="text-color-1 font-weight-bold"><?= $user['username'] ?></span></h1>
		</div>
		<div class="col-md-6 col-lg-4 pr-4 ml-auto text-right mb-5">
			<div class="user-image  p-1 ml-auto" style="width: 150px; height: 150px;">
				<img id="imgAvatar" src="<?= $avatar; ?>" class="rounded-circle border border-secondary" alt="Votre avatar" style="width: 150px; height: 150px;">
				<?php if (in_array($w_user['role'], [50, 70, 99]) && $w_user['role'] > $user['role']) : ?>
					<a href="<?= $this->url('back_users_edit', ['id' => $user['id']]); ?>" target="_blank" class="btn btn-sm btn-outline-dark py-0 mt-3"><i class="fad fa-user-cog"></i> console</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-lg-7 border-left border-secondary pl-4 text-left">
			<!-- <h2 class="text-color-1 font-weight-bold mb-n1 ">< ?=$user['username']?></h2> -->

			<p class="text-muted">Rôle :</span> <?= \Tools\Utils::getHumanRole($user['role'], false, true); ?></p>

			<ul class="font15">
				<li>Inscrit le <?= \Tools\Utils::dateFr($user['date_registered'], 'd/m/Y \à H:i'); ?></li>
				<li>Dernière connexion <?= \Tools\Utils::timeAgo($user['date_connect_now']); ?></li>

				<?php if (in_array($w_user['role'], [50, 70, 90]) && $w_user['role'] > $user['role']) : ?>
					<li>Email : <?= $user['email'] ?></li>
				<?php endif; ?>

				<li>Forum : <?= $postNbr; ?> message<?= ($postNbr > 1) ? 's' : '' ?></li>
			</ul>

			<div class="mb-3">Changer de mois :

				<?php
					$list_months = \Tools\Utils::listMonths();
					$slice = array_slice($list_months, 0, $month);
				?>
				<?php foreach ($slice as $key => $value) : ?>
					<a href="#" data-month="<?= $key + 1 ?>" class="scoreMonth text-secondary <?= ($key == date('Y')) ? 'active' : ''; ?>">
						<i class="fad fa-calendar-alt mr-1"></i> <?= $value; ?>
					</a>
				<?php endforeach; ?>

			</div>
			<div class="font14 mb-3">
				<span class="rounded border border-secondary bg-light py-1 pl-2 font-weight-bold mr-3">Score Belote <span class="bg-color-1 text-white p-1 ml-1"><span class="myBelotePoint"><?= $belotePoint ?></span><sup class="font-weight-normal">pts</sup></span></span>
				<span class="rounded border border-secondary bg-light py-1 pl-2 font-weight-bold mr-3">Score Tarot <span class="bg-color-1 text-white p-1 ml-1"><span class="myTarotPoint"><?= $tarotPoint ?></span><sup class="font-weight-normal">pts</sup></span></span>
			</div>
		</div>


			<div class="col-xxl-8 col-xl-10 border-top border-secondary mb-5">
				<div class="row chartRow">
					<div id="piechart_3d" class="col-md-6" style="width: 500px; height: 300px;"></div>
					<div id="barchart" class="col-md-6" style="width: 550px; height: 300px;"></div>				
				</div>


				<div class="table-responsive rounded overflow-hidden my-2">
					<table class="table table-sm table-bordered  m-0 p-0">
						<thead class="bg-light text-dark">
							<tr id="detailBelote">									
							</tr>
						</thead>
						<tbody class="Bhtml">
							<?php if ($belotePlayed) : ?>
								<?php foreach ($scoresUser as  $score) : ?>
									<?php if (!in_array($score['type'], ['T3', 'T4', 'T5'])) : ?>
										<tr class="">
											<td class="px-3 <?= $score['type'] ?> d-flex justify-content-between align-items-center">
												<?= \Tools\Utils::getHumanGame($score['type']) ?>
												<small><?= \Tools\Utils::dateFr($score['date_create'], 'd/m/Y - H:i'); ?></small>
											</td>
											<td class="px-2">
												<a href="<?= $this->url('profile_show', ['username' => $score['username_1']]); ?>" class="text-dark"><?= $score['username_1'] ?></a> &
												<a href="<?= $this->url('profile_show', ['username' => $score['username_2']]); ?>" class="text-dark"><?= $score['username_2'] ?></a> :
												<small class="badge badge-light border border-secondary text-secondary ml-2"><?= $score['score_1'] ?> <sup>pts</sup></small>
											</td>
											<td class="px-2">
												<a href="<?= $this->url('profile_show', ['username' => $score['username_3']]); ?>" class="text-dark"><?= $score['username_3'] ?></a> & <a href="<?= $this->url('profile_show', ['username' => $score['username_4']]); ?>" class="text-dark"><?= $score['username_4'] ?></a> : <small class="badge badge-light border border-secondary text-secondary ml-2"><?= $score['score_3'] ?> <sup>pts</sup></small>
											</td>
											<td class="<?= (in_array($score['id'], $won)) ? 'bg-won' : 'bg-loose'; ?>" style="width: 15px;">
												&nbsp;
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<div class="table-responsive rounded overflow-hidden my-4">
					<table class="table table-sm table-bordered  m-0 p-0">
						<thead class="bg-light text-dark">
							<tr id="detailTarot">
							</tr>
						</thead>
						<tbody class="Thtml">
							<?php if ($tarotPlayed) : ?>
								<?php foreach ($scoresUser as  $score) : ?>
									<?php if (in_array($score['type'], ['T3', 'T4', 'T5'])) : ?>
										<tr>
											<td class="px-3 <?= $score['type'] ?> d-flex justify-content-between  align-items-center">
												<?= \Tools\Utils::getHumanGame($score['type']) ?> <small><?= \Tools\Utils::dateFr($score['date_create'], 'd/m/Y - H:i'); ?></small>
											</td>
											<td class="px-1">
												<a class="text-dark" href="<?= $this->url('profile_show', ['username' => $score['username_1']]); ?>"><?= $score['username_1'] ?></a><small class="badge badge-light border border-secondary text-secondary ml-2"><?= $score['score_1'] ?> <sup>pts</sup></small>
											</td>
											<td class="px-1">
												<a class="text-dark" href="<?= $this->url('profile_show', ['username' => $score['username_2']]); ?>"><?= $score['username_2'] ?></a><small class="badge badge-light border border-secondary text-secondary ml-2"><?= $score['score_2'] ?> <sup>pts</sup></small>
											</td>
											<td class="px-1">
												<a class="text-dark" href="<?= $this->url('profile_show', ['username' => $score['username_3']]); ?>"><?= $score['username_3'] ?></a><small class="badge badge-light border border-secondary text-secondary ml-2"><?= $score['score_3'] ?> <sup>pts</sup></small>
											</td>
											<td class="px-1">
												<?= ($score['score_4']) ? '<a class="text-dark" href="' . $this->url('profile_show', ['username' => $score['username_4']]) . '">' . $score['username_4'] . '</a><small class="badge badge-light border border-secondary text-secondary ml-2">' . $score['score_4'] . ' <sup>pts</sup></small>' : '' ?>
											</td>
											<td class="px-1">
												<?= ($score['score_5']) ? '<a class="text-dark" href="' . $this->url('profile_show', ['username' => $score['username_4']]) . '">' . $score['username_5'] . '</a><small class="badge badge-light border border-secondary text-secondary ml-2">' . $score['score_5'] . ' <sup>pts</sup></small>' : '' ?>
											</td>
											<td class="<?= (in_array($score['id'], $won)) ? 'bg-won' : 'bg-loose'; ?>" style="width: 15px;">
												&nbsp;
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
	</div>

	<input type="hidden" id="gamesCO" name="gamesCO" value="<?= $games['CO'] ?>">
	<input type="hidden" id="gamesCOA" name="gamesCOA" value="<?= $games['COA'] ?>">
	<input type="hidden" id="gamesC" name="gamesC" value="<?= $games['C'] ?>">
	<input type="hidden" id="gamesCA" name="gamesCA" value="<?= $games['CA'] ?>">
	<input type="hidden" id="gamesM" name="gamesM" value="<?= $games['M'] ?>">
	<input type="hidden" id="gamesMA" name="gamesMA" value="<?= $games['MA'] ?>">
	<input type="hidden" id="gamesT3" name="gamesT3" value="<?= $games['T3'] ?>">
	<input type="hidden" id="gamesT4" name="gamesT4" value="<?= $games['T4'] ?>">
	<input type="hidden" id="gamesT5" name="gamesT5" value="<?= $games['T5'] ?>">
	<input type="hidden" id="belotePlayed" name="belotePlayed" value="<?= $belotePlayed ?>">
	<input type="hidden" id="beloteWon" name="beloteWon" value="<?= $beloteWon ?>">
	<input type="hidden" id="belotePoint" name="belotePoint" value="<?= $belotePoint ?>">
	<input type="hidden" id="tarotPlayed" name="tarotPlayed" value="<?= $tarotPlayed ?>">
	<input type="hidden" id="tarotWon" name="tarotWon" value="<?= $tarotWon ?>">
	<input type="hidden" id="tarotPoint" name="tarotPoint" value="<?= $tarotPoint ?>">


</div>


<?php $this->stop('main_content'); ?>

<?php $this->start('js'); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
var userId = <?= $user['id']; ?>

var scoreUser = <?php echo json_encode($scoresUser); ?>,
    urlAjaxGetScore = '<?= $this->url('ajax_get_score') ?>'
</script>
<script src="<?= $this->assetUrl('js/front/profile/show.js'); ?>"></script>
<?php $this->stop('js'); ?>