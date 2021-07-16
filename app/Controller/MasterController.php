<?php

namespace Controller;

use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;
use \Intervention\Image\ImageManagerStatic as Image;

class MasterController extends \W\Controller\Controller
{
	const ALLOW_CHARS = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØŒŠþÙÚÛÜÝŸàáâãäåæçèéêëìíîïðñòóôõöøœšÞùúûüýÿß%-_/&€$£¥';

	/**
	 * @var Le protocol HTTP
	 */
	protected $protocol;

	/**
	 * @var Le document root du site
	 */
	protected $document_root;

	/**
	 * @var Le chemin d'upload absolu
	 */
	protected $root_upload;
	/**
	 * @var Le chemin d'upload relatif (/assets/)
	 */
	protected $path_upload;

	/**
	 * @var Le nom du site
	 */
	protected $sitemane;


	/**
	 * Le constructreur
	 */
	public function __construct()
	{
		// On créer les répertoires d'uploads
		if (isset($_SERVER['W_BASE']) && !empty($_SERVER['W_BASE'])) {
			$this->document_root = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['W_BASE'];
		} else {
			$this->document_root = $_SERVER['DOCUMENT_ROOT'];
		}


		$this->path_upload = '/assets' . getApp()->getConfig('upload_dir');
		$this->root_upload = $this->document_root . $this->path_upload;

		$old = umask(0); // Pour un droit d'ecriture en 777
		if (!is_dir($this->root_upload)) {
			if (!mkdir($this->root_upload, 0777)) {
				die('Erreur lors de la création du répertoire upload principal');
			}
		}

		// ...et les sous-dossier a créer
		$folders = ['blog', 'pages', 'gallery', 'gallery/thumbs', 'newsletter', 'avatars'];
		foreach ($folders as $dir) {
			if (!is_dir($this->root_upload . $dir . '/')) {
				if (!mkdir($this->root_upload . $dir . '/', 0777)) {
					die('Erreur lors de la création du répertoire upload "' . $dir . '"');
				}
			}
		}
		umask($old);

		// Protocole HTTPS
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
			$_SERVER['HTTPS'] = 'https';
		}

		// Protocole HTTPS
		$this->protocol = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			$this->protocol = 'https';
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
			$this->protocol = 'https';
		}

		$this->sitename = getApp()->getConfig('sitename');

		// Ajout des données additionnelles à toutes les vues
		$this->addDataViews = array_merge($this->addDataViews, [
			'protocol' => $this->protocol
		]);

		$forbiddens = (new \Model\BanishModel)->banish();
		$ip_address = \Tools\Utils::getIpAddress();
			// foreach ($forbiddens as $forbidden) {
			// 	if ($forbidden['id_user'] == $_SESSION['user']['id'] || $forbidden['ip_address'] == $ip_address){
			// 		$this->showForbidden();
			// 	}
			// }
	}


	/** 
	 * Upload d'une image
	 * @param string $file_name L'attribut name de $_FILES
	 * @param string $folder_output Le dossier dans le quel on veut sauvegarder : Doit être rajouté dans le constructeur du MasterController
	 * @param int $width_output La largeur de sortie, automatique si non renseigné mais $height_output présent
	 * @param int $height_output La hauteur de sortie, auto si non-renseigné mais $width_output présent
	 * @param string $name_output Le nom de l'image sans extension (.jpg etc)
	 * @param int $max_size_input Le poid maxi de l'image en MP
	 * @param int $width_minimal_input La largeur minimale de l'image d'entrée
	 * @param int $height_minimal_input La hauteur minimale de l'image d'entrée
	 * @param int $quality La qualité de l'image, uniquement pour les JPG
	 * @param bool $fake_file Si true, la variable $_FILES ne sera pas prise en compte, mais le tableau donné en $file_name
	 */
	public function uploadImage($file_name = 'picture', $folder_output = '', $width_output = null, $height_output = null, $name_output = null, $max_size_input = 3, $width_minimal_input = null, $height_minimal_input = null, $quality = 70, $fake_file = false)
	{
		$allowMimeTypes = ['image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'];
		$maxSize =  $max_size_input ?? 3; // 3 Mo
		$maxSize = 1024 * 1024 * (int) $maxSize;

		$old = umask(0); // Pour un droit d'ecriture en 777
		if (!is_dir($this->root_upload . $folder_output . '/')) {
			if (!mkdir($this->root_upload . $folder_output . '/', 0777)) {
				die('Erreur lors de la création du répertoire upload "' . $folder_output . '"');
			}
		}
		umask($old);

		if ($fake_file) {
			// C'est quand même un peu dégeu de réecrire une superglobale
			$_FILES['fake'] = $file_name;
		}

		if (!empty($_FILES)) {
			$file = ($fake_file) ? $_FILES['fake'] : $_FILES[$file_name];

			// Redéfini les paramètres si faut fichier
			if ($fake_file) {
				$file['error'] = (int) $file['error'];

				if (!file_exists($file['tmp_name'])) {
					$file['tmp_name'] = $this->document_root . $file['tmp_name'];
				}
			}

			if ($file['error'] === UPLOAD_ERR_OK) {
				$img = Image::make($file['tmp_name']);

				// On check le poids de l'image
				if ($img->filesize() > $maxSize && !$fake_file) {
					// ERREUR
					return [
						'status' => false,
						'msg'	 => 'Image trop lourde : ' . $max_size_input . ' Mo maximum',
					];
				} else {
					// On check le mime-type
					$mime = $img->mime();

					switch ($mime) {
						case 'image/jpg':
						case 'image/jpeg':
						case 'image/pjpeg':
							$ext = '.jpg';
							break;

						case 'image/png':
							$ext = '.png';
							break;

						case 'image/gif':
							$ext = '.gif';
							break;
					}

					if (!in_array($mime, $allowMimeTypes)) {
						// ERREUR
						return [
							'status' => false,
							'msg'	 => 'Format d\'image non supporté',
						];
					} elseif ((!empty($width_minimal_input) && $width_minimal_input > $img->width()) || (!empty($height_minimal_input) && $height_minimal_input > $img->height())) {
						// ERREUR 
						return [
							'status' => false,
							'msg'	 => 'La taille de l\'image est invalide, taille minimum : ' . $width_minimal_input . 'px * ' . $height_minimal_input . 'px. Taille actuelle : '.$img->width() .'px * '.$img->height().'px',
						];
					} else {
						if (!empty($width_output) && !empty($height_output)) {
							$img->fit($width_output, $height_output, function ($constraint) {
								$constraint->upsize();
							});
						} elseif (!empty($width_output) || !empty($height_output)) {
							$img->resize($width_output, $height_output, function ($constraint) {
								$constraint->aspectRatio();
								$constraint->upsize();
							});
						}


						$name_save = (empty($name_output)) ? substr(pathinfo($file['name'], PATHINFO_FILENAME), 0, 50) . '-' . uniqid(time()) : $name_output;
						$filename = tr::transliterate($name_save);
						$filename = $filename . $ext;


						if (!empty($folder_output)) {
							$folder_output = ltrim($folder_output, '/');
							$output = '/' . $folder_output . '/' . $filename;
						} else {
							$output = '/' . $filename;
						}

						$save = $img->save(str_replace('//', '/', $this->root_upload . $output), $quality);

						if ($save) {
							return [
								'status' => true,
								'msg'	 => str_replace('//', '/', $this->path_upload . $output),
							];
						}
					}
				}
			} else {
				// ERREUR
				return [
					'status' => false,
					'msg'	 => \Tools\Utils::getErrorCodeUpload($file['error']),
				];
			}
		}
	}


	/**
	 * Envoi un email hors newsletter
	 * @param string $subject Le sujet
	 * @param string $messageHTML Le message
	 * @param mixed $address_to un ou plusieurs destinataire (array)
	 * @param string $file Chemin absolu vers le fichier en PJ
	 */
	protected function sendEmail($subject, $messageHTML, $address_to, $reply_to = null, $file = null)
	{
		$app = getApp();
		if ($app->getConfig('email_enable') === true) {

			// 
			$sitename = $app->getConfig('site_name');
			$domain = explode('@', $app->getConfig('email_notif'));
			$domain = $domain[1]; // Utile pour les vérifications dkim

			// L'endroit ou se trouve nos fichiers emails
			$path_email = realpath($this->document_root . '/../app/Emails/');

			// On remplace les variables dans le message en paramètre			
			$messageVars = [
				'search' => ['%sitename%', '%host%', '%logo%', '%subject%'],
				'replace' => [$sitename, $app->getConfig('email_host'), $app->getConfig('email_logo'), $subject],
			];
			$message = str_replace($messageVars['search'], $messageVars['replace'], $messageHTML);

			// On charge le template 
			$templateEmail = file_get_contents($path_email . '/Message.html');
			$templateVars = [
				'search' => ['%sitename%', '%host%', '%logo%', '%subject%', '%content%'],
				'replace' => [$sitename, $app->getConfig('email_host'), $app->getConfig('email_logo'), $subject, $message],
			];
			$contentEmail = str_replace($templateVars['search'], $templateVars['replace'], $templateEmail);

			if (empty($address_to)) {
				die('Pas de destinataire saisi !');
			}

			// Si on a saisi qu'une adresse email, on force un tableau pour le foreach
			if (!is_array($address_to)) {
				$address_to = [$address_to];
			}

			foreach ($address_to as $to) {
				if (v::notEmpty()->email()->validate($to)) {

					$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
					try {
						// Si on envoi le mail en SMTP
						if ($app->getConfig('email_by_smtp') === true) {
							$mail->isSMTP();
							$mail->Host 	= $app->getConfig('email_smtp_host');
							$mail->SMTPAuth = true;
							$mail->Port 	= $app->getConfig('email_smtp_port');
							$mail->Username = $app->getConfig('email_smtp_username');
							$mail->Password = $app->getConfig('email_smtp_password');

							if (!empty($app->getConfig('email_smtp_protocol'))) {
								$mail->SMTPSecure = $app->getConfig('email_smtp_protocol');
							}
						}

						$mail->CharSet = 'UTF-8';
						$mail->setFrom($app->getConfig('email_notif'));
						$mail->addAddress(mb_strtolower($to, 'UTF-8'));
						if (!empty($reply_to) && v::email()->validate($reply_to)) {
							$mail->addReplyTo(mb_strtolower($to, 'UTF-8'));
						}
						if (!empty($file) && file_exists($file)) {
							$mail->addAttachment($file);
						}
						$mail->isHTML(true);

						// DKIM
						if ($app->getConfig('email_dkim') === true) {
							$mail->DKIM_domain 		= $domain;
							$mail->DKIM_private 	= realpath(__DIR__) . '/../../dkim_keys/privatekey.txt';
							$mail->DKIM_selector 	= 'mailing';
							$mail->DKIM_passphrase 	= '';
							$mail->DKIM_identity 	= $mail->From;
						}

						$mail->Subject = $subject;
						$mail->msgHTML($contentEmail);
						$mail->send();
					} catch (Exception $e) {
						die('Erreur lors de l\'envoi du message. Erreur : ' . $mail->ErrorInfo);
					}
				}
			}
		}
	}



	/**
	 * Construit une URL unique à partir du titre
	 * @param string $title Le titre de l'article
	 * @return un slug
	 */
	protected function buildUrl($title, $nameModel)
	{

		if (empty($nameModel)) {
			return false;
		}

		$url = tr::transliterate(trim(strip_tags($title)));

		$modelFullName = '\Model\\' . $nameModel;

		$myModel = new $modelFullName();

		if ($myModel->urlExist($url)) {
			$url = $this->buildUrl($title . '-' . mt_rand(1, 1000), $nameModel);
		}
		return $url;
	}
}
