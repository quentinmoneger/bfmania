<?php

namespace Controller\Back;

use \Respect\Validation\Validator as v;
use \Behat\Transliterator\Transliterator as tr;
use Exception;

class MediasController extends MasterBackController
{

	const PATH_VIEWS = 'back/medias';

	/**
	 * @var le chemin complet du dossier
	 */
	public $root_media_directory;

	/**
	 * @var le chemin relatif du dossier depuis "assets"
	 */
	public $path_media_directory;

	/**
	 * @var le nom du dossier dans /uploads/
	 */
	public $out_dir = 'medias/';

	/**
	 * @var La taille maxi
	 */
	public $max_size = 5 * 1000 * 1000;

	/**
	 * @var mimes types acceptés
	 */
	public $allow_mimes = [
		'application/pdf', 	// PDF ou AI
		'application/zip', 	// ZIP
		'application/rtf', 	// RTF
		'application/x-rar',// RAR
		'application/x-7z-compressed', // 7z

		'image/png', 	// PNG
		'image/jpg', 	// JPG
		'image/jpeg', 	// JPEG
		'image/gif', 	// GIF
		'image/webp', 	// WEBP
		'image/x-icon', // ICO
		'image/vnd.adobe.photoshop', // PSD

		'text/rtf', 	// RTF
		'text/csv', 	// CSV
		'text/plain', 	// TXT

		// Powerpoint
		'application/vnd.ms-powerpoint', // PPT
		'application/vnd.oasis.opendocument.presentation', // ODP
		'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PPTX

		// Excel
		'application/vnd.ms-excel', // XLS
		'application/vnd.oasis.opendocument.spreadsheet', // ODS
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // XLSX
		// Word
		'application/msword', // DOC
		'application/vnd.oasis.opendocument.text', // ODT
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX

	];


	public function __construct()
	{
		parent::__construct();

		$old = umask(0); // Pour un droit d'ecriture en 777

		$this->root_media_directory = str_replace('//', '/', $this->root_upload.$this->out_dir);
		$this->path_media_directory = str_replace('//', '/', $this->path_upload.$this->out_dir);


		// on créer le dossier
		if(!is_dir($this->root_media_directory)){
			if(!mkdir($this->root_media_directory, 0777)){
				die('Erreur lors de la création du répertoire upload "'.$this->root_media_directory.'"');
			}
		}
		// ...et les sous-dossier a créer
		$folders = ['.crop', '.thumb'];
		foreach($folders as $dir){	
			if(!is_dir($this->root_media_directory.$dir.'/')){
				if(!mkdir($this->root_media_directory.$dir.'/', 0777)){
					die('Erreur lors de la création du répertoire upload "'.$dir.'"');
				}
			}
		}
		umask($old);


		/*
		 * Lit la taille maxi autorisé d'upload dans la configuration de PHP
		 * Redefinit si nécessaire
		 */
		$max_size_ini = (int) ini_get('upload_max_filesize');
		$max_size_ini = $max_size_ini * 1000 * 1000; // Conversion en octets 
		if($max_size_ini < $this->max_size){
			$this->max_size = $max_size_ini;
		}
		$this->allowTo(array_keys(\Tools\Utils::listRoles()));
	}


	/**
	 * Galerie de medias
	 */
	public function home($action = null)
	{
		// SUPPRESSION
		if(!empty($_GET)){
			$get = array_map('trim', array_map('strip_tags', $_GET));

			if(!empty($get['file']) && !empty($action) && $action == 'delete'){
				$unlink = @unlink($this->root_media_directory.'/'.$get['file']);
				if($unlink){
					$this->flash('Le fichier a bien été supprimé', 'success');
					$this->redirectToRoute('back_medias', ['action' => $action]);
				}
			}
		}

		$this->render(self::PATH_VIEWS.'/home', [
			'max_size' 	=> $this->max_size,
			'action'	=> $action,
		]);
	}

	/**
	 * Galerie de medias
	 * Zone de drop des fichiers
	 */
	public function ckeditor()
	{
		$this->render(self::PATH_VIEWS.'/ckeditor', [
			'max_size' 	=> $this->max_size,
			'action'	=> $action ?? $_GET['action'] ?? '',
		]);
	}

	/**
	 * Liste des médias 
	 * @package ajax
	 */
	public function listAll()
	{
		$files = [];

		foreach(scandir($this->root_media_directory) as $file) {
			if(substr($file, 0, 1) !== '.'){
				$root_file = $this->root_media_directory.$file;
				$files[filemtime($root_file).'_'.uniqid()] = [
					$file,
					date('Y-m-d H:i:s', filemtime($root_file)),
					mime_content_type($root_file),
					filesize($root_file)
				];
			}
		}

		// Tri du plus récent au plus ancien
		krsort($files);

		// En mode suppression
		$delete = ($_GET['action'] == 'delete') ? 'delete' : '';
		if(isset($_GET['action']) && $_GET['action'] == 'file'){
			$select = 'selectable';
		}
		elseif(isset($_GET['action']) && $_GET['action'] == 'ckeditor'){
			$select = 'selectable-editor';
		}
		else {
			$select = '';
		}


		$html = '';
		foreach($files as $key => $value) {

			// Exclusion des fichiers non image
			if(isset($_GET['action']) && ($_GET['action'] == 'file' || $_GET['action'] == 'ckeditor')){
				if(!in_array($value[2], ['image/png','image/jpg','image/jpeg','image/gif'])){
					continue;
				}
			}
			
			$ext = pathinfo($this->path_media_directory.$value[0], PATHINFO_EXTENSION);
			$image_thumb = '';
			switch (strtolower($ext)){
				case 'zip':
				case 'rar':
				case '7z':
					$image_thumb = 'archive.png';
				break;
				
				case 'rtf':
				case 'txt':
					$image_thumb = 'text.png';
				break;
				
				case 'ppt':
				case 'odp':
				case 'pptx':
					$image_thumb = 'interactive.png';
				break;
				
				case 'xls':
				case 'ods':
				case 'xlsx':
				case 'csv':
					$image_thumb = 'spreadsheet.png';
				break;

				case 'doc':
				case 'odt':
				case 'docx':
				case 'pdf':
					$image_thumb = 'document.png';
				break;
				
				case 'psd':
				case 'ai':
					$image_thumb = 'default.png';
				break;
				
				default:
					//$image_thumb = 'default.png';
				break;
			}

			if(isset($image_thumb) && !empty($image_thumb)){
				$thumb = $this->assetUrl('img/medias/'.$image_thumb);
				$is_downloadable = true;
			}
			else {
				$thumb = $this->path_media_directory.$value[0];
				$is_downloadable = false;
			}


			$title = ($delete) ? 'Supprimer ce fichier' : (($select) ? 'Choisir ce fichier' : strtoupper($ext));

			$html.= '<div class="col-auto">';
			$html.= '<div class="m-2">';
			if($delete){
				$html.= '<a href="'.$this->generateUrl('back_medias', ['action' => 'delete']).'?file='.$value[0].'" class="d-block">';
			}
			elseif(empty($select)) {
				$html.= '<a href="'.$this->path_media_directory.$value[0].'" class="d-block" '.((!$is_downloadable) ? 'data-toggle="lightbox" data-gallery="medias"' : '').'>';
			}
			$html.= '<figure class="img-thumbnail is-media '.$delete.' '.$select.'" title="'.$title.'"  
						data-awmedia-name="'.$value[0].'"
						data-awmedia-type="'.$value[2].'"
						data-awmedia-size="'.$value[3].'""
						data-awmedia-tmp-name="'.$this->path_media_directory.$value[0].'">
							<img src="'.$thumb.'" class="img-fluid" alt="'.$value[0].'">
							<figcaption class="border-top text-truncate">'.$value[0].'</figcaption>
					</figure>';
			if($delete || empty($select)){
				$html.= '</a>';
			}
			$html.= '</div>';
			$html.= '</div>';
		}

		$this->json($html);
	}

	/**
	 * Upload
	 * @package ajax
	 */
	public function upload()
	{
		$result = [];
		// From upload drag & drop
		if(!empty($_FILES)){
			$file = $_FILES['file'];
			
			if($file['error'] === UPLOAD_ERR_OK){
				$finfo = new \finfo();
				$mime = $finfo->file($file['tmp_name'], FILEINFO_MIME_TYPE);

				if(!in_array($mime, $this->allow_mimes)){
					$json = [
						'status' => false,
						'msg' 	 => 'Ce type de fichier n\'est pas accepté : '.$mime,
					];
				}
				elseif($file['size'] > $this->max_size){
					$json = [
						'status' => false,
						'msg' 	 => 'Le poids du fichier dépasse la taille maximum autorisée : '.$file['size'],
					];
				}
				else {

					$filename = $this->buildFilename($file['name']);
					// upload
					if(move_uploaded_file($file['tmp_name'], str_replace('//', '/', $this->root_media_directory.$filename))){
						$json = [
							'status' => true,
							'msg'	 => str_replace('//', '/', $this->path_media_directory.$filename),
							'type'	 => [$mime, $file['type']],
						];
					}
					else {
						$json = [
							'status' => false,
							'msg' 	 => 'Une erreur est survenue lors de l\'envoi du fichier',
						];
					}
				}
			}
			else {
				$json = [
					'status' => false,
					'msg' 	 => \Tools\Utils::getErrorCodeUpload($file['error']),
				];
			}
		}
		// From CROP
		elseif(!empty($_POST)){
			$post = array_map('trim', array_map('strip_tags', $_POST));

			if(isset($post['img_crop_base64'])){

				if(preg_match('/^data:image\/(\w+);base64,/', $post['img_crop_base64'], $type)){
					$data = substr($post['img_crop_base64'], strpos($post['img_crop_base64'], ',') + 1);
					$ext = strtolower($type[1]); // l'extension de l'image : jpg, png, gif
					

					$base64 = base64_decode($data);
					if($base64 !== false) {

						$filename = $this->buildFilename(pathinfo($post['filename'], PATHINFO_FILENAME).'.jpg', '.crop');
						$tmp_img = imagecreatefromstring($base64);
						if($tmp_img === false){
							$json = [
								'status' => false, 
								'msg'	 => 'Une erreur est survenue lors de la sauvegarde de l\'image',
							];
						}
						else {
							// On converti le transparent en blanc
							list($width, $height) = getimagesizefromstring($base64);
							$output_img = imagecreatetruecolor($width, $height);
							$white = imagecolorallocate($output_img,  255, 255, 255);
							imagefilledrectangle($output_img, 0, 0, $width, $height, $white);
							imagecopy($output_img, $tmp_img, 0, 0, 0, 0, $width, $height);

							// On créer l'image et la sauvegarde
							imagejpeg($output_img, str_replace('//', '/', $this->root_media_directory.'/.crop/'.$filename), 80);
							// On libère la mémoire
							imagedestroy($tmp_img); 
							imagedestroy($output_img);

							$root_file = str_replace('//', '/', $this->root_media_directory.'/.crop/'.$filename);

							$json = [
								'status' => true,
								'msg'	 => str_replace('//', '/', $this->path_media_directory.'/.crop/'.$filename),
								'type'	 => ['image/jpg', 'image/jpg'],
								'medias' => [
									'size' 		=> filesize($root_file),
									'type' 		=> 'image/jpg',
									'tmp_name' 	=> str_replace('//', '/', $this->path_media_directory.'/.crop/'.$filename),
									'name'		=> $filename,
								],
							];
						}
					}
					else {
						$json = [
							'status' => false, 
							'msg'	 => 'Impossible de décoder l\'image',
						];
						
					}
				}
				else {
					// N'est pas une image
					$json = [
						'status' => false, 
						'msg'	 => 'base64 image data invalide',
					];
				}

			}
		}

		return $this->json($json);
	}


	/**
	 * Créer le nom d'un fichier
	 * @param string $file_name Le nom d'origine du fichier
	 * @param string $directory Le repertoire
	 * @return string $final_filename Le nom du fichier final
	 */
	protected function buildFilename($file_name, $directory = '')
	{
		$ext = pathinfo($file_name);
		$new_filename = tr::transliterate(trim(strip_tags($ext['filename'])));
		// Nom du fichier avec son extension
		$final_filename = $new_filename.'.'.$ext['extension'];


		if($this->checkFilenameExist($final_filename, $directory)){
			$final_filename = $this->buildFilename($new_filename.'-'.mt_rand(1,1000).'.'.$ext['extension'], $directory);
		}

		return $final_filename;
	}

	/**
	 * Vérifie si un fichier existe
	 * @param string $check_name Le nom du fichier qu'on veut vérifier
	 * @param string $directory Le repertoire
	 * @return bool
	 */
	protected function checkFilenameExist($check_name, $directory = '')
	{

		if(empty($check_name)){
			throw new Exception('Nom de fichier non fourni');
		}

		return file_exists(str_replace('//', '/', $this->root_media_directory.'/'.$directory.'/'.$check_name));
	}



}
