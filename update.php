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
$obj->new_version = '0.1.2';

// the url for the plugin homepage
$obj->url = 'http://bloomlocal.net';

//the download location for the plugin zip file (can be any internet host)
$obj->package = sprintf('https://arcanys:FvoneOJHEO@bloomlocal.net/dev/bloomlocal-%s.zip', $obj->new_version);

switch ( $_POST['action'] ) {

case 'version':  
	echo serialize( $obj );
	break;  
case 'info':   
	$obj->requires = '4.0';  
	$obj->tested = '4.0';  
	$obj->downloaded = 12540;  
	$obj->last_updated = '2012-10-17';  
	$obj->sections = array(  
		'description' => 'The new version of the Bloomlocal plugin',  
		'another_section' => '',  
		'changelog' => 'Updates'  
	);
	$obj->download_link = $obj->package;  
	echo serialize($obj);  
case 'license':  
	echo serialize( $obj );  
	break;  
}
