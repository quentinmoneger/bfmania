<?php
/**
 * Inclusions des fichiers
 */
require 'routes_front.php'; // Routes du front
require 'routes_back.php'; // Routes du back

/**
 * Ajout des préfixes Front / Back devant le nom des Controllers
 */
foreach($front_r as $r){
	$r[2] = 'Front\\'.ucfirst(str_replace('Front', '', $r[2]));
	$front_routes[] = $r;
}
foreach($back_r as $r){
	$r[2] = 'Back\\'.ucfirst(str_replace('Back', '', $r[2]));
	$back_routes[] = $r;
}
$w_routes = array_merge($front_routes, $back_routes);