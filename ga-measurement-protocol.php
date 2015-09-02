<?php

/**
 * Google Analytics Measurement Protocol
 *
 * @package   Pilau_GA_Measurement_Protocol
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2013 Public Life
 *
 * @link	https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
 * @link	http://www.stumiller.me/implementing-google-analytics-measurement-protocol-in-php-and-wordpress/
 *
 * @wordpress-plugin
 * Plugin Name:			Pilau Google Analytics Measurement Protocol
 * Description:			Tools for interacting with the Google Analytics Measurement Protocol.
 * Version:				0.1.1
 * Author:				Steve Taylor
 * Text Domain:			ga-measurement-protocol-locale
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:			/lang
 * GitHub Plugin URI:	https://github.com/pilau/ga-measurement-protocol
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-ga-measurement-protocol.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Pilau_GA_Measurement_Protocol', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Pilau_GA_Measurement_Protocol', 'deactivate' ) );

Pilau_GA_Measurement_Protocol::get_instance();
