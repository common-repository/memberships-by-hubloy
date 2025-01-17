<?php
namespace HubloyMembership\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Primary HubloyMembership Class
 *
 * Note: Even all properties are marked private, they are made public via the
 * magic __get() function.
 *
 * @since 1.0.0
 *
 * @package JP
 */
class Plugin {

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Plugin
	 */
	private static $instance = null;


	/**
	 * Modifier values. Modifiers are similar to wp-config constants, but can be
	 * also changed via code.
	 *
	 * @since  1.0.0
	 *
	 * @var array
	 */
	private static $modifiers = array();


	/**
	 * The main controller of the plugin.
	 *
	 * @since  1.0.0
	 * @var   object
	 */
	private $controller;


	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();

			self::$instance = apply_filters(
				'hubloy_membership_base_plugin_instance',
				self::$instance
			);
		}

		return self::$instance;
	}


	/**
	 * Plugin constructor.
	 *
	 * Set properties, registers hooks and loads the plugin.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		// Do action before plugin is initialized
		do_action( 'hubloy_membership_base_plugin_init', $this );

		// Plugin activation Hook.
		register_activation_hook(
			HUBMEMB_PLUGIN_FILE,
			array( $this, 'plugin_activation' )
		);

		// Plugin deactivation hook
		register_deactivation_hook(
			HUBMEMB_PLUGIN_FILE,
			array( $this, 'plugin_deactivation' )
		);

		add_filter(
			'plugin_action_links_' . HUBMEMB_PLUGIN,
			array( $this, 'plugin_settings_link' )
		);

		add_filter(
			'network_admin_plugin_action_links_' . HUBMEMB_PLUGIN,
			array( $this, 'plugin_settings_link' )
		);

		add_action(
			'setup_theme',
			array( $this, 'setup_controller' )
		);

		add_action( 'plugins_loaded', array( $this, 'translate_plugin' ) );

		add_filter( 'cron_schedules', array( $this, 'cron_time_intervals' ) );

		$this->load_functions();

		// Grab instance of self.
		self::$instance = $this;

		// Handle the wrong actions
		add_action( 'hubloy_membership_doing_it_wrong', array( $this, 'doing_it_wrong' ) );

		// Do action afterp lugin is initialized
		do_action( 'hubloy_membership_base_plugin_end', $this );
	}


	/**
	 * Actions executed in plugin activation.
	 *
	 * @since  1.0.0
	 */
	public function plugin_activation() {

		\HubloyMembership\Core\Init::activate();

		do_action( 'hubloy_membership_plugin_activation', $this );
	}

	/**
	 * Actions executed in plugin deactivation
	 *
	 * @since 1.0.0
	 */
	public function plugin_deactivation() {
		\HubloyMembership\Core\Init::deactivate();

		do_action( 'hubloy_membership_plugin_deactivation', $this );
	}

	public function plugin_settings_link( $links ) {
		if ( ! is_network_admin() ) {
			$base_url      = 'admin.php?page=' . \HubloyMembership\Controller\Plugin::MENU_SLUG;
			$settings_link = apply_filters(
				'hubloy_membership_plugin_settings_link',
				sprintf( '<a href="%s">%s</a>', admin_url( $base_url ), __( 'Settings', 'memberships-by-hubloy' ) ),
				$this
			);
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Set up plugin
	 */
	public function setup_controller() {

		$this->controller = new \HubloyMembership\Controller\Plugin();
		\HubloyMembership\Core\Controller::load_controllers();
		\HubloyMembership\Core\Controller::load_routes();

		do_action( 'hubloy_membership_setup_controller' );
	}

	/**
	 * Handle plugin translations
	 *
	 * @since 1.0.0
	 */
	public function translate_plugin() {
		load_plugin_textdomain( 'memberships-by-hubloy', false, HUBMEMB_LANG_DIR );
	}

	/**
	 * Returns a modifier option.
	 * This is similar to a setting but more "advanced" in a way that there is
	 * no UI for it. A modifier can be set by the plugin (e.g. during Import
	 * the "no_messages" modifier is enabled) or via a const in wp-config.php
	 *
	 * A modifier is never saved in the database.
	 * It can be defined ONLY via MS_Plugin::set_modifier() or via wp-config.php
	 * The set_modifier() value will always take precedence over wp-config.php
	 * definitions.
	 *
	 * @since  1.0.0
	 * @api
	 *
	 * @param  string $key Name of the modifier.
	 * @return mixed The modifier value or null.
	 */
	public static function get_modifier( $key ) {
		$res = null;

		if ( isset( self::$modifiers[ $key ] ) ) {
			$res = self::$modifiers[ $key ];
		} elseif ( defined( $key ) ) {
			$res = constant( $key );
		}

		return $res;
	}

	/**
	 * Changes a modifier option.
	 *
	 * @see get_modifier() for more details.
	 * @since  1.0.0
	 * @api
	 *
	 * @param  string $key Name of the modifier.
	 * @param  mixed  $value Value of the modifier. `null` unsets the modifier.
	 */
	public static function set_modifier( $key, $value = null ) {
		if ( null === $value ) {
			unset( self::$modifiers[ $key ] );
		} else {
			self::$modifiers[ $key ] = $value;
		}
	}

	/**
	 * Set custom cron schedules
	 *
	 * @param array $schedules - current schedules
	 *
	 * @since 1.0.0
	 *
	 * @return array $schedules
	 */
	public function cron_time_intervals( $schedules ) {
		$schedules['weekly']     = array(
			'interval' => 604800,
			'display'  => __( 'Weekly', 'memberships-by-hubloy' ),
		);
		$schedules['monthly']    = array(
			'interval' => 2635200,
			'display'  => __( 'Monthly', 'memberships-by-hubloy' ),
		);
		$schedules['quarterly']  = array(
			'interval' => 3 * 2635200,
			'display'  => __( 'Every 3 Months', 'memberships-by-hubloy' ),
		);
		$schedules['biannually'] = array(
			'interval' => 6 * 2635200,
			'display'  => __( 'Every 6 Months', 'memberships-by-hubloy' ),
		);
		return $schedules;
	}

	/**
	 * Load function files
	 * These are used mainly within themes and external resources
	 *
	 * @since 1.0.0
	 */
	private function load_functions() {
		include_once HUBMEMB_FUNCTIONS_DIR . 'views.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'settings.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'account.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'protection.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'pages.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'conditionals.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'general.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'plans.php';
		include_once HUBMEMB_FUNCTIONS_DIR . 'transactions.php';
	}

	/**
	 * Doing it wrong handler
	 *
	 * @since 1.0.0
	 *
	 * @param string $function The function that was called.
	 * @param string $message  A message explaining what has been done incorrectly.
	 * @param string $version  The version of WordPress where the message was added.
	 */
	public function doing_it_wrong( $function, $message, $version ) {
		if ( is_ajax() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
	}

	/**
	 * Returns property associated with the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 * @param string $property The name of a property.
	 * @return mixed Returns mixed value of a property or NULL if a property doesn't exist.
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}
	}

	/**
	 * Check if property isset.
	 *
	 * @since  1.0.0
	 * @internal
	 *
	 * @param string $property The name of a property.
	 * @return mixed Returns true/false.
	 */
	public function __isset( $property ) {
		return isset( $this->$property );
	}
}

