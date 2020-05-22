<?php

function bl_check_update($current_version, $plugin_slug) {
    list ($t1, $t2) = explode( '/', $plugin_slug );
    $slug = str_replace( '.php', '', $t2 );		

    $obj = new stdClass();
    $obj->slug = $slug;  
    $obj->name = ucwords($slug);
    $obj->plugin_name = ucwords($slug);
    $obj->plugin_slug = $plugin_slug;
    $obj->url = 'http://bloomlocal.net';
    $obj->new_version = bl_get_version('0.1.3');
    $obj->package = sprintf('https://github.com/rroble/bloomlocal/releases/download/v%s/bloomlocal-%s.zip', $obj->new_version, $obj->new_version);

    add_filter('pre_set_site_transient_update_plugins', function ($transient) use ($plugin_slug, $obj) {
        if (empty($transient->checked)) {
			return $transient;
        }

		// If a newer version is available, add the update
		if (version_compare($current_version, $obj->new_version, '<')) {
			$transient->response[$plugin_slug] = $obj;
        }

		return $transient;
    });

    add_filter('plugins_api', function ($api, $action, $arg) use ($slug, $obj) {
        if (($action == 'query_plugins' || $action == 'plugin_information') && isset($arg->slug) && $arg->slug === $slug) {
            $obj->requires = '5.4';  
            $obj->tested = '5.4';  
            $obj->last_updated = '2020-05-22';  
            $obj->sections = array(  
                'description' => sprintf('Latest version: <a href="https://github.com/rroble/bloomlocal/releases/latest">%s</a>', $obj->new_version),
            );
            $obj->download_link = $obj->package;

            return $obj;
		}
		
		return $api;
    }, 10, 3);
}

function bl_get_version($default = '0.1.3') {
    if( false !== ($version = get_transient('bloomlocal_latest_version'))) {
        return $version;
    }

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://github.com/rroble/bloomlocal/releases/latest');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch); 
	$tag = explode('tag/v', $output);
	if (isset($tag[1])) {
		$vers = explode('"', $tag[1]);
		if (isset($vers[0])) {
            set_transient('bloomlocal_latest_version', $vers[0], 43200); // 12 hours cache
            return $vers[0];
		}
	}

	return $default;
}
