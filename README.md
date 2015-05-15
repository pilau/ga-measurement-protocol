Pilau Google Analytics Measurement Protocol
=======================

A WordPress plugin for interacting with Google Analytics Measurement Protocol. Will also insert basic JavaScript tracking code if necessary.

**NOTE:** This plugin was developed because the Measurement Protocol requires Universal Analytics, which at the time wasn't supported by my preferred Analytics plugin by Yoast. The Yoast plugin now supports UA, but it's unclear if there's any kind of API for MP use. This plugin is a stopgap until it's clear that the Yoast plugin supports MP.

To use in your theme:

	$PGAMP = null;
	if ( class_exists( 'Pilau_GA_Measurement_Protocol' ) ) {
		$PGAMP = Pilau_GA_Measurement_Protocol::get_instance();
	}

	$PGAMP->build_hit( $method, $info );

Go to _Settings > Google Analytics Measurement Protocol_ to add your Analytics ID and adjust other settings.

Check the source and [this article](http://www.stumiller.me/implementing-google-analytics-measurement-protocol-in-php-and-wordpress/) for more details.
