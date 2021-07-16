<div id="newsletter-unsubscribe" class="h4 mb-2">Se désinscire a la newsletters</div>
<div id="form-newsletter-unsubscribe" class="input-group" action="<?= $this->url('newsletters_unsubscribe_submit'); ?>">
  <input type="login" name="email" id="email" class="form-control" placeholder="votre@email.fr" >
  <div class="input-group-append">
    <button id="unsubscribe-button" class="btn btn-danger " type="button"><i class="text-light fad fa-paper-plane"></i></button>
  </div> 
</div>
<a id="subscribe-newletters" type="button" class="text-light font10" data-toggle="collapse" data-target="#subscribe-newletters" aria-expanded="true" aria-controls="subscribe-newletters">
	Cliquez ici pour vous inscrire.
</a>	
