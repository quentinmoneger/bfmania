<?php $this->layout('layout', ['title' => 'Accueil']);?>

<?php $this->start('main_content');?>
<section id="cover">
	
		<div class="position-absolute h-100 w-100 d-flex">		
			<div class="m-auto">
				<h1>Jouez sur <?=$w_site_name;?></h1>
			</div>
		</div>
		<div class="btn-intro">
				<a href="#intro" id="to-intro" class="smooth-scroll"><i class="fad fa-chevron-double-down"></i></a>
		</div>
</section>

<section id="intro" class="bg-red-origin">
	<div class="container mt-3">

		<div class="row justify-content-around">
			
			<div class="text-center mt-md-0 mt-5">
				<h2 class="title-page with-underline">Comment ça marche ?</h2>
			</div>
			
			<div class="w-100 py-5"></div>

			<div class="col-md-4 px-4 mt-md-0 mt-0">
				<div class="item-home">
					<i class="fad fa-dice fa-4x"></i>
					<h4>Jouez gratuitement</h4>
					<p class="font14">Directement dans votre navigateur,
						<br>en ligne, et sans rien installer.</p>
				</div>
			</div>
			<div class="col-md-4 px-4 mt-md-0 mt-5">
				<div class="item-home">
					<i class="fad fa-user-friends fa-4x"></i>
					<h4>De vrais adversaires</h4>
					<p class="font14">Partagez la belote en ligne ou le tarot
						<br>avec de vrais joueurs !</p>
				</div>
			</div>
			<div class="col-md-4 px-4 mt-md-0 mt-5">
				<div class="item-home">
					<i class="fad fa-comments fa-4x"></i>
					<h4>Une communauté</h4>
					<p class="font14">Notre forum... Un lieu d'échanges et 
						<br>de discussions en tout genre.</p>
				</div>
			</div>
		</div>

		<div class="w-100 py-5"></div>

		
</section>
<section id="intro-cards">
	<div class="container">

		<div class="row justify-content-center">
			<div class="playing-cards">
				<div class="flip-card">

					<div class="card-front cf1">
						<div class="h-100 d-flex align-items-end">
							<div class="cf-menu">
								<h5 class="text-center text-dark">Belote</h5>
								<div class="text-center m-2">
									<a href="<?=$this->url('default_play_game', ['action' => 'belote']);?>" class="btn btn-play px-5">JOUER</a>
								</div>
								<div class="text-center m-2">
									<a href="#" class="btn btn-secondary">Règles du jeu</a>
								</div>
							</div>
						</div>
					</div>

					<div class="card-back">
						<h1 class="text-center">Belote</h1>
					</div>

				</div>
			</div>
			<div class="playing-cards">
				<div class="flip-card">
					<div class="card-front cf2">
						<div class="cf-menu">
							<h5 class="text-center text-dark">Tarot</h5>
							<div class="text-center m-2">
								<a href="<?=$this->url('default_play_game', ['action' => 'tarot']);?>" class="btn btn-play px-5">JOUER</a>
							</div>
							<div class="text-center m-2">
								<a href="#" class="btn btn-secondary">Règles du jeu</a>
							</div>
						</div>
					</div>
					<div class="card-back">
						<h1 class="text-center">Tarot</h1>
					</div>
				</div>
			</div>
			<div class="playing-cards">
				<div class="flip-card">
					<div class="card-front cf3">
						<div class="cf-menu">
							<h5 class="text-center text-dark">Coinche</h5>
							<div class="text-center m-2">
								<a href="<?=$this->url('default_play_game', ['action' => 'coinche']);?>" class="btn btn-play px-5">JOUER</a>
							</div>
							<div class="text-center m-2">
								<a href="#" class="btn btn-secondary">Règles du jeu</a>
							</div>
						</div>
					</div>
					<div class="card-back">
						<h1 class="text-center">Coinche</h1>
					</div>
				</div>
			</div>
		</div>		
	</div>
</section>
<input id="alertUnsubscribe" value="<?= $unsubscribe_alert ?? ''?>" hidden>
<?php $this->stop('main_content');?>