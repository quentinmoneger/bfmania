<div id="newsletter-subscribe" class="h4 mb-2">S'inscrire à la newsletter</div>
<div id="form-newsletter-subscribe" class="input-group" action="<?= $this->url('newsletters_subscribe'); ?>">
  <input type="login" name="email" id="email" class="form-control" placeholder="votre@email.fr" >
  <div class="input-group-append">
    <button id="subscribe-button" class="btn btn-danger " type="button"><i class="text-light fad fa-paper-plane"></i></button>
  </div> 
</div>
<a id="unsubscribe-newletters" data-toggle="modal" type="button" class="text-light font10" data-target="#unsubscribe" >
	Cliquez ici pour vous désinscrire.
</a>	