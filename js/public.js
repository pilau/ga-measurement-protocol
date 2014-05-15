/**
 * Download tracking based on @link http://www.blastam.com/blog/index.php/2013/09/howto-track-downloads-links-universalanalytics/
 */

/* Trigger when DOM has loaded */
jQuery( document ).ready( function( $ ) {
	var filetypes = new RegExp( '.(' + gamp.track_downloads.join( '|' ) + ')$', 'i' );
	var base_href = '';
	var base = $( 'base' );
	if ( base.attr('href') != undefined ) {
		base_href = base.attr('href');
	}
	var href_redirect = '';

	$( 'body' ).on( 'click', 'a', function( e ) {
		var el = $( this );
		var track = false;
		var ret = true;
		var href = ( typeof( el.attr( 'href' ) ) != 'undefined' ) ? el.attr( 'href' ) : '';
		var is_this_domain = href.match( document.domain.split( '.' ).reverse()[1] + '.' + document.domain.split( '.' ).reverse()[0] );

		if ( ! href.match( /^javascript:/i ) ) {
			var event = [];
			event.value = 0;
			event.non_i = false;

			if ( href.match( /^mailto\:/i ) ) {

				// Mailto link
				event.category = 'email';
				event.action = 'click';
				event.label = href.replace( /^mailto\:/i, '' );
				event.loc = href;
				track = true;

			} else if ( href.match( filetypes ) ) {

				// File download link
				var ext = ( /[.]/.exec( href ) ) ? /[^.]+$/.exec( href ) : undefined;
				event.category = 'download';
				event.action = 'click-' + ext[0];
				event.label = href.replace( / /g, '-' );
				event.loc = base_href + href;
				track = true;

			} else if ( href.match( /^https?\:/i ) && ! is_this_domain ) {

				// External link
				event.category = 'external';
				event.action = 'click';
				event.label = href.replace( /^https?\:\/\//i, '' );
				event.non_i = true;
				event.loc = href;
				track = true;

			} else if ( href.match( /^tel\:/i ) ) {

				// Telephone link
				event.category = 'telephone';
				event.action = 'click';
				event.label = href.replace( /^tel\:/i, '' );
				event.loc = href;
				track = true;

			}

			if ( track ) {

				if ( ( event.category == 'external' || event.category == 'download' ) && ( el.attr( 'target' ) == undefined || el.attr( 'target' ).toLowerCase() != '_blank' ) ) {
					href_redirect = event.loc;

					pilau_ga.event( event.category.toLowerCase(), event.action.toLowerCase(), event.label.toLowerCase(), event.value, {
						'nonInteraction':	event.non_i,
						'hitCallback':		pilau_ga_hit_callback_handler
					});

					ret = false;

				} else {

					pilau_ga.event( event.category.toLowerCase(), event.action.toLowerCase(), event.label.toLowerCase(), event.value, {
						'nonInteraction':	event.non_i
					});

				}

				return ret;
			}
		}
	});

	pilau_ga_hit_callback_handler = function() {
		window.location.href = href_redirect;
	}

});


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
	event: function( c, a, l, v, x ) {
		if ( typeof l == 'undefined' ) {
			l = null;
		}
		if ( typeof v == 'undefined' ) {
			v = null;
		}
		if ( typeof x == 'undefined' ) {
			x = null;
		}
		if ( pilau_ga.activated() ) {
			ga( 'send', 'event', c, a, l, v, x );
		}
	},

	// Is Analytics activated?
	activated: function() {
		return ( typeof ga != 'undefined' );
	}

};
