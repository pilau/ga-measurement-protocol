
/**
 * Custom JS Analytics
 *
 * @link	https://developers.google.com/analytics/devguides/collection/analyticsjs/
 *
 * Usage:
 *
 * Pageview:
 * pilau_ga.pageview( '/page/path', 'Page title' );
 *
 * Event:
 * pilau_ga.event( category, action, opt_label, opt_value );
 * e.g. pilau_ga.event( 'PledgeForm', 'share', 'facebook', 23 );
 */
var pilau_ga = {

	// Track a page view (a page that can be accessed via a URL, but which is viewed via AJAX)
	pageview: function( p, t ) {
		if ( pilau_ga.activated() ) {
			ga( 'send', 'pageview', { 'page': p, 'title': t } );
		}
	},

	// Track an event (actions without corresponding pages)
	event: function( c, a, l, v ) {
		if ( typeof l == 'undefined' ) {
			l = null;
		}
		if ( typeof v == 'undefined' ) {
			v = null;
		}
		if ( pilau_ga.activated() ) {
			ga( 'send', 'event', c, a, l, v );
		}
	},

	// Is Analytics activated?
	activated: function() {
		return ( typeof ga != 'undefined' );
	}

};
