<?php

// https://github.com/omarabid/Self-Hosted-WordPress-Plugin-repository

/**
 * The remote host file to process update requests
 *
 */
if ( !isset( $_POST['action'] ) ) {
	echo '0';
	exit;
}

//set up the properties common to both requests 
$obj = new stdClass();
$obj->slug = 'bloomlocal';  
$obj->name = 'Bloomlocal';
$obj->plugin_name = 'bloomlocal.php';
$obj->new_version = getVersion('0.1.3');

// the url for the plugin homepage
$obj->url = 'http://bloomlocal.net';

//the download location for the plugin zip file (can be any internet host)
$obj->package = sprintf('https://github.com/rroble/bloomlocal/releases/download/v%s/bloomlocal-%s.zip', $obj->new_version, $obj->new_version);

switch ( $_POST['action'] ) {

case 'version':  
	echo serialize( $obj );
	break;  
case 'info':   
	$obj->requires = '5.4.1';  
	$obj->tested = '5.4.1';  
	$obj->last_updated = '2020-05-22';  
	$obj->sections = array(  
		'description' => 'Latest version <a href="https://github.com/rroble/bloomlocal/releases/latest">here.</a>',  
	);
	$obj->download_link = $obj->package;  
	echo serialize($obj);  
case 'license':  
	echo serialize( $obj );  
	break;  
}

function getVersion($default = '0.1.3') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://github.com/rroble/bloomlocal/releases/latest');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch); 
	$tag = explode('tag/v', $output);
	if (isset($tag[1])) {
		$vers = explode('"', $tag[1]);
		if (isset($vers[0])) {
			return $vers[0];
		}
	}

	return $default;
}
