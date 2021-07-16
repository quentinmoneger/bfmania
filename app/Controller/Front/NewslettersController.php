<?php

namespace Controller\Front;

use \Model\NewslettersModel;
use \Model\NewsletterUsersModel;

use \Respect\Validation\Validator as v;

class NewslettersController extends MasterFrontController
{


	public function __construct()
	{
		parent::__construct();
		$this->newslettersDb = new NewslettersModel();
		$this->newslettersUsersDb = new NewsletterUsersModel();
	}

        /**
	 * Envoi d'email pour la désincription a la newsletter
	 * @method AJAX
	 */
	public function unsubscribeSubmit()
	{
        if (!empty($_POST)) {
    
            $safe = array_map('trim', array_map('strip_tags', $_POST));

            if (!v::email()->validate($safe['email'])) {
                $alert = 'Le format de l\'email n\'est pas valide.';
            }

            if (!isset($alert)) {

                $issubscribe = $this->newslettersUsersDb->emailExist($safe['email']);

                if(isset($issubscribe)){
                    $newsletterUser = $this->newslettersUsersDb->findBy('email' ,$safe['email']);
                }
                
                if(isset($newsletterUser['active']) & true){
                    
                    $md5 = md5('bf2021'.$newsletterUser['email']);

                    $link =  'http://bfmania.loc'.$this->generateUrl('newsletters_unsubscribe').'?eid='.$newsletterUser['id'].'&token='.$md5;

                    $subject = '[Contact du site] Votre lien de désinscription a la newsletters ';

                    $message = '<h3>Vous avez demandé a vous désincrire de la newsletters';
                    $message .= '<p>Cliquez sur le lien ci dessous :';
                    $message .= '<br>';
                    $message .= '<a href="'.$link.'">Désincription du compte '.$safe['email'].'.</a>';
    
                    $receiver = $safe['email'];

                    $this->sendEmail($subject, $message, $receiver);

                    $alert = 'Un email vous a été envoyé pour confirmation!';
    
                    $json= ['alert'=>$alert, 'success'=>true];
                    $this->showJson($json);

                }else{
                    $alert= 'Vous n\'êtes pas inscrit a la newsletter !';
                 

                    $json= ['alert'=>$alert, 'success'=>false];
                    $this->showJson($json);
                }
    
            } else {

					$json= ['alert'=>$alert, 'success'=>false];
					$this->showJson($json);
			}
        }
	}

	/**
	 * Souscription a la newsletter
	 * @method AJAX
	 */
	public function subscribe()
	{
        if (!empty($_POST)) {
    
            $safe = array_map('trim', array_map('strip_tags', $_POST));

            if (!v::email()->validate($safe['email'])) {
                $alert = 'Le format de l\'email n\'est pas valide.';
            }

            if (!isset($alert)) {
                $emailExist = $this->newslettersUsersDb->emailExist($safe['email']);
                $notactive = false ;

                if($emailExist){
                    $newsletterUser = $this->newslettersUsersDb->findBy('email' ,$safe['email']);
                    if($newsletterUser['active'] == false){
                        $notactive = true; 
                    }                                  
                }

                if(!$emailExist || $notactive == true){
                    
                    if(!$emailExist){
                        $newsletterNewUser = [
                            'email' => $safe['email'],
                            'date_create' => date('Y-m-d H:i:s'),
                            'active' => 1
                        ];
    
                        $this->newslettersUsersDb->insert($newsletterNewUser);
                    }

                    if(isset($newsletterUser['active'])){

                        $data = [
                            'active' => 1
                        ];
  
                        $this->newslettersUsersDb->update($data , $newsletterUser['id'] , true );
                    }

                    $alert = '
                    votre email a bien été enregistré !
                    ';
    
                    $json= ['alert'=>$alert, 'success'=>true];
                    $this->showJson($json);

                }else{
                    $alert = 'Vous étes déjà inscrit !';


                    $json= ['alert'=>$alert, 'success'=>false];
                    $this->showJson($json);
                }
            } else {

					$json= ['alert'=>$alert, 'success'=>false];
					$this->showJson($json);
			}
        }
	}

    	/**
	 * Souscription a la newsletter
	 * @method AJAX
	 */
	public function unsubscribe()
	{
        if (!empty($_GET)) {
    
            $safe = array_map('trim', array_map('strip_tags', $_GET));

            $newsletterUser = $this->newslettersUsersDb->find($safe['eid']);

            $md5 = md5('bf2021'.$newsletterUser['email']);

                if($this->newslettersUsersDb->find($safe['eid']) & ($safe['token'] == $md5)){
                    
                    $this->newslettersUsersDb->update(['active' => 0 ], $safe['eid'] );
    
                    $unsubscribe = 'valid'; 

                    $this->redirectToRoute('default_unsubscribe_home', ['unsubscribe' => $unsubscribe]);

                }else{

                    $unsubscribe = 'error'; 

                    $this->redirectToRoute('default_unsubscribe_home', ['unsubscribe' => $unsubscribe]);
                }

        }
	}
}