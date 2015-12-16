<?php
/**
 * Google Analytics Measurement Protocol
 *
 * @package   Pilau_GA_Measurement_Protocol
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2013 Public Life
 */

/**
 * Plugin class
 *
 * @package Pilau_GA_Measurement_Protocol
 * @author  Steve Taylor
 */
class Pilau_GA_Measurement_Protocol {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1
	 *
	 * @var     string
	 */
	protected $version = '0.1.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ga-measurement-protocol';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * The plugin's settings.
	 *
	 * @since    0.1
	 *
	 * @var      array
	 */
	protected $settings = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1
	 */
	private function __construct() {

		// Set the settings
		$this->settings = $this->get_settings();

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the settings page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'process_plugin_admin_settings' ) );

		// Load admin styles and scripts
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing scripts and styles
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Define custom functionality.
		add_action( 'wp_head', array( $this, 'insert_tracking_code' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1
	 */
	public function enqueue_scripts() {

		if ( $this->settings['insert-tracking-code'] && $this->analytics_applicable() ) {

			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
			// Use localize to pass settings
			wp_localize_script( $this->plugin_slug . '-plugin-script', 'gamp', array(
				'track_downloads'	=> explode( ',', $this->settings['track-downloads'] )
			));

		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Google Analytics Measurement Protocol', $this->plugin_slug ),
			__( 'Google Analytics Measurement Protocol', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Get the plugin's settings
	 *
	 * @since    0.1
	 */
	public function get_settings() {

		$settings = get_option( $this->plugin_slug . '_settings' );
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		// Defaults
		$defaults = array(
			'ga-id'						=> '',
			'insert-tracking-code'		=> '',
			'exclude-user-capability'	=> '',
			'enhanced-link-attribution'	=> '',
			'allow-anchor-parameters'	=> '',
			'track-downloads'			=> 'pdf,doc,docx,zip',
		);
		$settings = array_merge( $defaults, $settings );

		return $settings;
	}

	/**
	 * Set the plugin's settings
	 *
	 * @since    0.1
	 */
	public function set_settings( $settings ) {
		return update_option( $this->plugin_slug . '_settings', $settings );
	}

	/**
	 * Process the settings page for this plugin.
	 *
	 * @since    0.1
	 */
	public function process_plugin_admin_settings() {

		// Submitted?
		if ( isset( $_POST[ $this->plugin_slug . '_settings_admin_nonce' ] ) && check_admin_referer( $this->plugin_slug . '_settings', $this->plugin_slug . '_settings_admin_nonce' ) ) {

			// Gather into array
			$settings = array();
			$settings['ga-id'] = preg_replace( '/[^UA\-0-9]/', '', $_POST['ga-id'] );
			$settings['insert-tracking-code'] = isset( $_POST['insert-tracking-code'] ) ? 1 : 0;
			$settings['exclude-user-capability'] = preg_replace( '/[^a-z_]/', '', $_POST['exclude-user-capability'] );
			$settings['track-downloads'] = preg_replace( '/[^a-zA-Z0-9,]/', '', $_POST['track-downloads'] );
			$settings['enhanced-link-attribution'] = isset( $_POST['enhanced-link-attribution'] ) ? 1 : 0;
			$settings['allow-anchor-parameters'] = isset( $_POST['allow-anchor-parameters'] ) ? 1 : 0;

			// Save as option
			$this->set_settings( $settings );

			// Redirect
			wp_redirect( admin_url( 'options-general.php?page=' . $this->plugin_slug . '&done=1' ) );

		}

	}

	/**
	 * Check if Analytics is currently applicable
	 *
	 * @since    0.1
	 */
	private function analytics_applicable() {
		return (
			// Applicable if there's no exclude capability set
			( ! isset( $this->settings['exclude-user-capability'] ) || ! $this->settings['exclude-user-capability'] ) ||
			// OR, the user isn't logged in, OR the logged-in user doesn't have the excluded capability
			( ! is_user_logged_in() || ! current_user_can( $this->settings['exclude-user-capability'] ) )
		);
	}

	/**
	 * Insert the JavaScript tracking code in the header
	 *
	 * @since    0.1
	 */
	public function insert_tracking_code() {

		/*
		 * Only include if:
		 * - We've got a tracking code
		 * - The flag is set to include the tracking code
		 * - There's no exclude capability, or the user isn't logged in, or the user doesn't have the specified capability
		 */
		if (	isset( $this->settings['ga-id'] ) &&
				( isset( $this->settings['insert-tracking-code'] ) && $this->settings['insert-tracking-code'] ) &&
				$this->analytics_applicable()
		) {

			$domain = $_SERVER['HTTP_HOST'];
			if ( substr( $domain, 0, 4 ) == 'www.' ) {
				$domain = substr( $domain, 4 );
			}

			// Fields object
			$fieldsObject = array(
				'allowAnchor'		=> $this->settings['enhanced-link-attribution']
			);

			?>

			<!-- Google Analytics tracking inserted by Measurement Protocol plugin -->
			<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
				ga( 'create', '<?php echo $this->settings['ga-id']; ?>', '<?php echo $domain; ?>', { <?php
					foreach ( $fieldsObject as $fo_key => $fo_value ) { ?>'<?php echo $fo_key ?>': <?php echo $fo_value ? 'true' : 'false'; ?>,
					<?php } ?>
				} );
				<?php if ( $this->settings['enhanced-link-attribution'] ) { ?>
					ga( 'require', 'linkid', 'linkid.js' );
				<?php } ?>
				ga( 'send', 'pageview' );
			</script>

			<?php
		}

	}

	/**
	 * Handle the parsing of the _ga cookie or setting it to a unique identifier
	 *
	 * @since	0.1
	 * @return	string
	 */
	public function parse_cookie() {

		if ( isset( $_COOKIE['_ga'] ) ) {

			list( $version, $domain_depth, $cid1, $cid2 ) = preg_split( '[\.]', $_COOKIE["_ga"] , 4 );
			$contents = array(
				'version'		=> $version,
				'domainDepth'	=> $domain_depth,
				'cid'			=> $cid1 . '.' . $cid2
			);
			$cid = $contents['cid'];

		} else {

			$cid = $this->generate_uuid();

		}

		return $cid;
	}

	/**
	 * Generate UUID v4 function - needed to generate a CID when one isn't available
	 *
	 * @since	0.1
	 * @return	string
	 */
	public function generate_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

	/**
	 * Fire hit
	 *
	 * @since	0.1
	 * @link	https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
	 * @param	array	$data
	 * @return	mixed
	 */
	public function fire_hit( $data = null ) {

		if ( $data && $this->analytics_applicable() ) {

			$get_string = 'https://ssl.google-analytics.com/collect';
			$get_string .= '?payload_data&';
			$get_string .= http_build_query( $data );
			$result = wp_remote_get( $get_string );

			#$sendlog = error_log( $get_string, 1, "me@company.com"); // comment this in and change your email to get an log sent to your email

			return $result;
		}

		return false;
	}

	/**
	 * Build hit
	 *
	 * @since	0.1
	 * @link	https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
	 * @param	string	$method		'pageview' | 'ecommerce' | 'event'
	 * @param	array	$info		Check each method below, and the above dev guide, for details of what to include here
	 * @return	mixed
	 */
	public function build_hit( $method = null, $info = null ) {

		if ( $method && $info && isset( $this->settings['ga-id'] ) && $this->analytics_applicable() ) {

			// Standard params
			$standard_params = array(
				'v'		=> 1,
				'tid'	=> $this->settings['ga-id'],
				'cid'	=> $this->parse_cookie(),
			);

			if ( $method === 'pageview' ) {

				// Send PageView hit
				$data = array_merge( $standard_params, array(
					't'		=> 'pageview',
					'dt'	=> $info['title'],
					'dp'	=> $info['slug']
				));

				$this->fire_hit( $data );

			} else if ( $method === 'ecommerce' ) {

				// Register an ECOMMERCE TRANSACTION (and an associated ITEM)

				// Set up Transaction params
				$ti = uniqid(); // Transaction ID
				$ta = 'SI';
				$tr = $info['price']; // transaction value (native currency)
				$cu = $info['cc']; // currency code

				// Send Transaction hit
				$data = array_merge( $standard_params, array(
					't'		=> 'transaction',
					'ti'	=> $ti,
					'ta'	=> $ta,
					'tr'	=> $tr,
					'cu'	=> $cu
				));

				$this->fire_hit( $data );

				// Set up Item params
				$in = urlencode( $info['info']->product_name ); // item name
				$ip = $tr;
				$iq = 1;
				$ic = urlencode( $info['info']->product_id ); // item SKU
				$iv = urlencode( 'SI' ); // Product Category - we use 'SI' in all cases, you may not want to

				// Send Item hit
				$data = array_merge( $standard_params, array(
					't'		=> 'item',
					'ti'	=> $ti,
					'in'	=> $in,
					'ip'	=> $ip,
					'iq'	=> $iq,
					'ic'	=> $ic,
					'iv'	=> $iv,
					'cu'	=> $cu
				));

				$this->fire_hit( $data );

			} else if ( $method === 'event' ) {

				// Send Event tracking hit
				$event_params = array(
					't'		=> 'event',
					'ec'	=> $info['category'],
					'ea'	=> $info['action']
				);
				if ( isset( $info['label'] ) && $info['label'] ) {
					$event_params['el'] = $info['label'];
				}
				if ( isset( $info['value'] ) && $info['value'] ) {
					$event_params['ev'] = $info['value'];
				}

				$data = array_merge( $standard_params, $event_params );
				$this->fire_hit( $data );

			}

		}

	}

}