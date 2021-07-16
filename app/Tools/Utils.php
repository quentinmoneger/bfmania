<?php

/**
 * Méthodes variées et utiles
 */

namespace Tools;


use \DateTime;
use \Jenssegers\Date\Date;
use Jenssegers\Agent\Agent;
use \Respect\Validation\Validator as v;


class Utils
{

	/**
	 * Affiche la liste des mois 
	 * @todo Gestion multilingues
	 * @param bool $lower Les noms en minuscules
	 * @return array La liste des mois avec la clé correspond au numéro
	 */
	public static function listMonths($lower = false)
	{

		$months = [
			1 	=> 'Janvier',
			2 	=> 'Février',
			3 	=> 'Mars',
			4	=> 'Avril',
			5	=> 'Mai',
			6 	=> 'Juin',
			7 	=> 'Juillet',
			8 	=> 'Août',
			9 	=> 'Septembre',
			10 	=> 'Octobre',
			11 	=> 'Novembre',
			12 	=> 'Décembre',
		];

		return (!$lower) ? $months : array_map(function ($arr) {
			return mb_strtolower($arr, 'UTF-8');
		}, $months);
	}

	/**
	 * Obtient le nom du mois 
	 * @param int $number Le numéro du mois
	 * @param bool $lower Le nom du mois en minuscules
	 * @return mixed Le nom du mois ou false si inexistant
	 */
	public static function getNameOfMonth($number, $lower = false)
	{
		$month = self::listMonths($lower);

		// On supprime l'éventuel 0 initial
		$number = (int) $number;
		if (array_key_exists($number, $month)) {
			return $month[$number];
		}

		return false;
	}

	/**
	 * Affiche la liste des jour 
	 * @todo Gestion multilingues
	 * @param bool $lower Les noms en minuscules
	 * @return array La liste des jours avec la clé correspond au numéro
	 */
	public static function listDays($lower = false)
	{

		$days = [
			0 	=> 'Dimanche',
			1 	=> 'Lundi',
			2 	=> 'Mardi',
			3 	=> 'Mercredi',
			4	=> 'Jeudi',
			5	=> 'Vendredi',
			6 	=> 'Samedi',
			7 	=> 'Dimanche',
		];

		return (!$lower) ? $days : array_map(function ($arr) {
			return mb_strtolower($arr, 'UTF-8');
		}, $days);
	}

	/**
	 * Obtient le nom du jour 
	 * @param int $number Le numéro du jour
	 * @param bool $lower Le nom du jour en minuscules
	 * @return mixed Le nom du jour ou false si inexistant
	 */
	public static function getNameOfDay($number, $lower = false)
	{
		$day = self::listDays($lower);

		// On supprime l'éventuel 0 initial
		$number = (int) $number;
		if (array_key_exists($number, $day)) {
			return $day[$number];
		}

		return false;
	}


	/**
	 * Formatage de la date en français
	 * @param La date qu'on souhaite convertir
	 * @param Le format de sortie
	 * @see https://packagist.org/packages/jenssegers/date
	 */
	public static function dateFr($date_input, $format = 'd/m/Y')
	{
		$date_output = new Date($date_input);
		$date_output->setLocale('fr'); // On force la locale FR

		return $date_output->format($format);
	}


	/**
	 * Réordonne un array $_FILES
	 * @param array $files Devrait être $_FILES
	 */
	public static function orderMultipleFiles($files)
	{
		$result = [];

		foreach ($files as $key1 => $value1) {
			foreach ($value1 as $key2 => $value2) {
				$result[$key2][$key1] = $value2;
			}
		}

		return $result;
	}

	/**
	 * Convertit un tableau multidimensionnel en tableau simple
	 * @param array $array un tableau multidimensionnel
	 */
	public static function arrayFlatten($array)
	{
		if (!is_array($array)) {
			return false;
		}
		$result = [];

		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = array_merge($result, self::arrayFlatten($value));
			} else {
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * Convertit un nombre au format français
	 * @param float $number le nombre à convertir
	 * @param int $decimal le nombre de chiffre après la virgule
	 */
	public static function numberFr($number, $decimal = 2)
	{
		return number_format($number, $decimal, ',', ' ');
	}


	/**
	 * Découpe une chaine de caractères
	 * @param string $text la chaine à découper
	 * @param int $length la longueur à afficher
	 * @return la chaine découpée
	 */
	public static function cutString($text, $length = 25)
	{

		if (strlen($text) > $length) {

			$text = substr($text, 0, $length);
			$lastSpace = strrpos($text, ' ');
			//$text = substr(substr($text, 0, $length -3), 0, $lastSpace).'...';
			$text = substr($text, 0, $length - 3) . '...';
		}

		return $text;
	}


	/** 
	 * Donne le temps passé depuis une date donnée
	 * @param $dateCompare une date au format YYYY-mm-dd H:i:s ou un objet DateTime
	 * @param $dateToday une date au format YYYY-mm-dd H:i:s ou un objet DateTime
	 * @param $outputFormat un format de sortie pour la date complète
	 * @return le temps passé sous forme de chaine : "il y a ..."
	 */
	public static function timeAgo($dateCompare, $dateToday = null, $outputFormat = 'd/m/Y \à H:i')
	{

		if (!$dateCompare instanceof DateTime) {
			$dateCompare = new DateTime($dateCompare);
		}

		if (!$dateToday instanceof DateTime) {
			$dateToday = new DateTime($dateToday);
		} elseif (empty($dateToday)) {
			$dateToday = new DateTime();
		}


		$diff = $dateCompare->diff($dateToday);

		if ($diff->days > 7) {
			$result = 'le ' . $dateCompare->format($outputFormat);
		} elseif ($diff->d <= 7 && $diff->d >= 1) {
			$result = 'il y a ' . $diff->d . ' jour';
			$result .= ($diff->d <= 1) ? '' : 's';
		} elseif ($diff->d < 1 && $diff->h >= 1) {
			$result = 'il y a ' . $diff->h . ' heure';
			$result .= ($diff->h <= 1) ? '' : 's';
		} elseif ($diff->h < 1 && $diff->i >= 1) {
			$result = 'il y a ' . $diff->i . ' minute';
			$result .= ($diff->i <= 1) ? '' : 's';
		} elseif ($diff->i < 1 && $diff->h == 0 && $diff->d == 0) {
			$result = 'il y a quelques secondes';
			//$result = 'il y a '.$diff->s.' seconde';
			//$result.= ($diff->s <= 1) ? '' : 's';
		}

		return (isset($result)) ? $result : false;
	}

	/** 
	 * Formatte les ms en decomptes textuel
	 * @param $countdown
	 * @return le temps passé sous forme de chaine : "En ..."
	 */
	public static function countdownFormat($countdown)
	{

		$countdown = intval($countdown);

	
        if ($countdown >= 0) 
		{

            $mi = floor($countdown / (60 * 1000));
            $ss = floor(($countdown - $mi * 60 * 1000) / 1000);
            $ms = $countdown - floor($countdown / 1000) * 1000;
            $timer = "";

			

            if( $mi > 0){
				if( $mi < 10){
					$timer .= '0';
				}
                $timer .= strval($mi).":";
            }
            if( $ss > 0){
				if( $ss < 10){
					$timer .= '0';
				}
                $timer .= strval($ss).".";
            }
            if( $ms > 0){
				if( $ms < 10){
					$timer .= '0';
				}
                $timer .= strval($ms)."";
            }
        };

        return $timer;
        
    }

	/**
	 * Génère un mot de passe aléatoire
	 * @param int $nbChars Le nombre de caractère du mot de passe
	 * @return string le mot de passe
	 */
	public static function randomPassword($nbChars = 12)
	{
		$password = '';

		$charsAvailable = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789+@!$%?&';
		$lengthCharsAvailable = strlen($charsAvailable);

		for ($i = 1; $i <= $nbChars; $i++) {
			$random = mt_rand(0, ($lengthCharsAvailable - 1));
			$password .= $charsAvailable[$random];
		}

		return $password;
	}

	/**
	 * Converti un email en caractères ASCII
	 * @return l'email encodée
	 */
	public static function encodeEmail($email)
	{
		$output = '';
		for ($i = 0; $i < strlen($email); $i++) {
			$output .= '&#' . ord($email[$i]) . ';';
		}
		return $output;
	}


	/**
	 * Retourne une erreur d'upload en français
	 * @param $code le code d'erreur retourné par $_FILES['file']['error']
	 */
	public static function getErrorCodeUpload($code)
	{
		switch ($code) {
			case 1:
			case UPLOAD_ERR_INI_SIZE:
				$error = 'Le fichier est trop lourd (ini)';
				break;

			case 2:
			case UPLOAD_ERR_FORM_SIZE:
				$error = 'Le fichier est trop lourd (form)';
				break;

			case 3:
			case UPLOAD_ERR_PARTIAL:
				$error = 'Le fichier n\'a été que partiellement uploadé';
				break;

			case 4:
			case UPLOAD_ERR_NO_FILE:
				$error = 'Aucun fichier n\'a été uploadé';
				break;

			case 6:
			case UPLOAD_ERR_NO_TMP_DIR:
				$error = 'Le dossier temporaire est introuvable';
				break;

			case 7:
			case UPLOAD_ERR_CANT_WRITE:
				$error = 'Erreur d\'écriture du fichier sur le serveur';
				break;

			case 8:
			case UPLOAD_ERR_EXTENSION:
				$error = 'Une extension a stopper l\'envoi du fichier';
				break;
		}

		return $error ?? $code;
	}

	/**
	 * @return Retourne l'OS du systeme
	 */
	public static function getSystemOS()
	{
		switch (true) {
			case stristr(PHP_OS, 'DAR'):
				$os = 'OS_OSX';
				break;

			case stristr(PHP_OS, 'WIN'):
				$os = 'OS_WIN';
				break;

			case stristr(PHP_OS, 'LINUX'):
				$os = 'OS_LINUX';
				break;

			default:
				$os = 'OS_UNKNOWN';
		}

		return $os;
	}

	/**
	 * Affiche la liste des rôles 
	 * @param bool $lower Les noms en minuscules
	 * @return array La liste des rôles avec la clé correspondante 
	 */
	public static function listRoles($lower = false)
	{

		$roles = [
			'0' 	=> 'Membre',
			'30'	=> 'Animateur Radio',
			'50'	=> 'Modérateur',
			'70'	=> 'Administrateur',
			'99'	=> 'Webmaster'
		];

		return (!$lower) ? $roles : array_map(function ($arr) {
			return mb_strtolower($arr, 'UTF-8');
		}, $roles);
	}

	/**
	 * Obtient le rôle de l'utilisateur admin
	 * @param int $role Le rôle de l'utilisateur
	 * @param bool $lower Le nom du mois en minuscules
	 * @param bool $colored Ajout un span de couleur autour
	 * @return mixed Le nom du mois ou false si inexistant
	 */
	public static function getHumanRole($role, $lower = false, $colored = false)
	{
		$listRoles = self::listRoles($lower);

		if (array_key_exists($role, $listRoles)) {

			switch ($role) {
				case '99':
					$css = 'black white-text';
					break;
				case '70':
					$css = 'red white-text';
					break;
				case '50':
					$css = 'indigo darken-2 white-text';
					break;
				case '30':
					$css = 'teal darken-2 white-text';
					//$css = 'teal-text text-darken-2';
					break;
				default:
					$css = '';
					break;
			}

			return ($colored) ? '<span class="badge ' . $css . '">' . $listRoles[$role] . '</span>' : $listRoles[$role];
		}

		return false;
	}


	public function getInfoUserAgent($agentuser, $return_type )
	{
		$agent = new Agent;

		$agent->setUserAgent($agentuser);

		$browser = $agent->browser();
		$version = $agent->version($browser);
		$browser_version = $browser.' '.$version;
		
		$platform = $agent->platform();
		$version = $agent->version($platform);
		$platform_version = $platform.' '.$version;


		if($return_type == 'array'){
			
			return [
				'browser' =>  $browser_version,
				'os'      =>  $platform_version
			];

		}
		elseif($return_type == 'html'){

			return [
				'browser' =>  $browser_version,
				'os'      =>  $platform_version
			];

		}

	}

	/**
	 * Trouver les coordonnées GPS en fonction d'une adresse
	 */
	public function getLatitudeLongitude($address = '', $zipcode = '', $city = '')
	{


		$maps_city = preg_replace('#\((.+)\)#U', '', $city);
		$maps_address = (!empty($address)) ? ucwords($address) . ', ' . $zipcode . ', ' . ucwords($maps_city) : $zipcode . ', ' . ucwords($maps_city);

		$maps_url = 'https://maps.googleapis.com/maps/api/geocode/json?key=' . getApp()->getConfig('google_api_key') . '&sensor=false&address=' . urlencode($maps_address . ', France');

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'https') {
			$maps_json = file_get_contents($maps_url);
		} else {
			$arrContextOptions = [
				'ssl' => [
					'verify_peer' 		=> false,
					'verify_peer_name' 	=> false,
				],
			];
			$maps_json = file_get_contents($maps_url, false, stream_context_create($arrContextOptions));
		}
		$json = json_decode($maps_json);

		if (!empty($json->results)) {
			$lng = $json->results[0]->geometry->location->lng;
			$lat = $json->results[0]->geometry->location->lat;
		} else {
			$geocoder = new \OpenCage\Geocoder\Geocoder(getApp()->getConfig('opencage_key'));
			$json = $geocoder->geocode($maps_address);
			$lat = $json['results'][0]['geometry']['lat'];
			$lng = $json['results'][0]['geometry']['lng'];
		}

		if (!empty($lat) && !empty($lng)) {
			return [
				'lat' => str_replace(',', '.', $lat),
				'lng' => str_replace(',', '.', $lng),
			];
		}

		return false;
	}


	/**
	 * Fabrique un hash à partir des données envoyées
	 * @param $datas Les données pour builder le hash
	 */
	public static function createtHash(array $datas)
	{
		if (!is_array($datas)) {
			return false;
		}

		return sha1(implode('+', array_filter($datas)));
	}


	/**
	 * Rend une taille en octet lisible
	 * @param $size la taille en octet
	 */
	public static function convertToReadableSize($size)
	{
		$base = log($size) / log(1000);
		$suffix = ['', 'Ko', 'Mo', 'Go', 'To'];
		$f_base = floor($base);

		return round(pow(1000, $base - floor($base)), 1) . ' ' . $suffix[$f_base];
	}

	/**
	 *
	 *
	 */
	public static function dateIncrement($date, $increment = '2 days', $format = 'd-m-Y')
	{
		$dateCreated = date_create($date);
		date_add($dateCreated, date_interval_create_from_date_string($increment));

		return date_format($dateCreated, $format);
	}

	public static function getCategoryName($category)
	{
		$categories = [
			'1' => 'Mon compte',
			'2'	=> 'Jouer sur BFmania',
			'3'	=> 'Utiliser le forum'
		];

		if (array_key_exists($category, $categories)) {

			switch ($category) {
				case '1':
					$category = $categories[1];
					break;
				case '2':
					$category = $categories[2];
					break;
				case '3':
					$category = $categories[3];
					break;
			}

			return $category;
		}

		return false;
	}


	/**
	 * Get current user IP Address.
	 * @return string
	 */
	public static function getIpAddress()
	{
		// if (isset($_SERVER['HTTP_X_REAL_IP'])) {
		// 	if(v::ip()->validate($_SERVER['HTTP_X_REAL_IP'])){
		// 		return $_SERVER['HTTP_X_REAL_IP'];
		// 	}	
		// } 
		// elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		// 	// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
		// 	// Make sure we always only send through the first IP in the list which should always be the client IP.
		// 	$ipClient = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
		// 	if (v::ip()->validate($ipClient)){
		// 		return $ipClient;
		// 	}
		// }

		// elseif (isset($_SERVER['REMOTE_ADDR'])) {
		// 	if(v::ip()->validate($_SERVER['REMOTE_ADDR'])){
		// 		return $_SERVER['REMOTE_ADDR'];
		// 	}
		// }

		foreach (array('HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        	if (array_key_exists($key, $_SERVER) === true){
	            foreach (explode(',', $_SERVER[$key]) as $ip){
	                $ip = trim($ip); // just to be safe

	                if (v::ip()->validate($ip)){
	                    return $ip;
	                }
	            }
	        } 
	    }

		return '';
	}


	/**
	 * Affiche la liste des jeux de cartes 
	 * @param bool $lower Les noms en minuscules
	 * @return array La liste des jeux avec la clé correspondante 
	 */
	public static function listGames($lower = false)
	{

		$games = [
			'CO' 	=> 'Coinche',
			'COA'	=> 'Coinche avec annonces',
			'C'		=> 'Belote Classique',
			'CA'	=> 'Belote Classique avec annonces',
			'M'		=> 'Belote Moderne',
			'MA'	=> 'Belote Moderne avec annonces',
			'T3'	=> 'Tarot 3 joueurs',
			'T4'	=> 'Tarot 4 joueurs',
			'T5'	=> 'Tarot 5 joueurs'
		];

		return (!$lower) ? $games : array_map(function ($arr) {
			return mb_strtolower($arr, 'UTF-8');
		}, $games);
	}

	/**
	 * Obtient le nom du jeu de carte
	 * @param String $game Le jeu de carte correspondant
	 * @param bool $lower Le nom du jeu en minuscules
	 * @param bool $colored Ajout un span de couleur autour
	 * @return mixed Le nom du jeu ou false si inexistant
	 */
	public static function getHumanGame($game, $lower = false, $colored = false)
	{
		$listGames = self::listGames($lower);

		if (array_key_exists($game, $listGames)) {

			switch ($game) {
				case 'CO':
					$css = 'black white-text';
					break;
				case 'COA':
					$css = 'red white-text';
					break;
				case 'C':
					$css = 'indigo darken-2 white-text';
					break;
				case 'CA':
					$css = 'teal darken-2 white-text';
					break;
				case 'M':
					$css = 'black white-text';
					break;
				case 'MA':
					$css = 'red white-text';
					break;
				case 'T3':
					$css = 'indigo darken-2 white-text';
					break;
				case 'T4':
					$css = 'teal darken-2 white-text';
					break;
				case 'T5':
					$css = 'teal darken-2 white-text';
					break;
				default:
					$css = '';
					break;
			}

			return ($colored) ? '<span class="badge ' . $css . '">' . $listGames[$game] . '</span>' : $listGames[$game];
		}

		return false;
	}
	
	

}
