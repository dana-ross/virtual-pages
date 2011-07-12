<?php
/*
Plugin Name: Virtual Pages
Plugin URI: http://wordpress.org/#
Description:
Author: Dave Ross
Version: 0.4.1
Author URI: http://davidmichaelross.com
*/

/**
 * Copyright (c) 2010 Dave Ross <dave@csixty4.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL);

if(5.0 > floatval(phpversion())) {
	// Call the special error handler that displays an error
	add_action('admin_notices', 'virtual_pages_phpver_admin_notice');
}
else {
	
	// wp-settings.php hasn't run yet, so we need to
	// grab our own WP_Rewrite instance
	$wp_rewrite   =& new WP_Rewrite();
	if(!$wp_rewrite->using_permalinks()) {
		add_action('admin_notices', 'virtual_pages_permalink_admin_notice');
		
	}
	else {

		// Pre-2.6 compatibility
		// See http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
		if ( ! defined( 'WP_CONTENT_URL' ) )
		      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
		if ( ! defined( 'WP_CONTENT_DIR' ) )
		      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( ! defined( 'WP_PLUGIN_URL' ) )
		      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( ! defined( 'WP_PLUGIN_DIR' ) )
		      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	
		include_once("VirtualPages.php");
        new VirtualPages;
	}
}

function virtual_pages_phpver_admin_notice() {
	$alertMessage = __("Virtual Pages requires PHP 5.0 or higher");
	echo "<div class=\"updated\"><p><strong>$alertMessage</strong></p></div>";
}

function virtual_pages_permalink_admin_notice() {
	$alertMessage = __("The Virtual Pages plugin requires permalinks enabled");
	echo "<div class=\"updated\"><p><strong>$alertMessage</strong></p></div>";	
}