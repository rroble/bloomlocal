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
$obj->new_version = '0.1.3';

// the url for the plugin homepage
$obj->url = 'http://bloomlocal.net';

//the download location for the plugin zip file (can be any internet host)
$obj->package = sprintf('https://github.com/rroble/bloomlocal/releases/download/v%s/bloomlocal-%s.zip', $obj->new_version, $obj->new_version);

switch ( $_POST['action'] ) {

case 'version':  
	echo serialize( $obj );
	break;  
case 'info':   
	$obj->requires = '5.4';  
	$obj->tested = '5.4';  
	$obj->downloaded = 12540;  
	$obj->last_updated = '2020-05-22';  
	$obj->sections = array(  
		'description' => 'The new version of the Bloomlocal plugin',  
	);
	$obj->download_link = $obj->package;  
	echo serialize($obj);  
case 'license':  
	echo serialize( $obj );  
	break;  
}
