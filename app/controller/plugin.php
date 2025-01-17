<?php
namespace HubloyMembership\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;
use HubloyMembership\Core\Resource;
use HubloyMembership\Helper\Pages;
use HubloyMembership\Core\Util;
use HubloyMembership\Core\Admin;

class Plugin extends Controller {

	/**
	 * Base controller
	 *
	 * Set to true if its a base controllr
	 *
	 * @var bool
	 *
	 * @since 1.0.0
	 */
	protected $is_base = true;

	/**
	 * The admin core object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $admin = null;

	/**
	 * String translations
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $strings = array();


	/**
	 * Main plugin controller
	 *
	 * Loads all required plugin files and set up admin pages
	 */
	public function __construct() {
		$this->admin = new Admin();

		// Enqueue resources
		$this->add_action( 'wp_loaded', 'wp_loaded' );

		$this->add_action( 'admin_init', 'admin_init' );

		if ( is_multisite() ) {
			// Setup plugin admin UI.
			$this->add_action( 'network_admin_menu', 'network_add_menu_pages', 8 );
		} else {
			$this->add_action( 'admin_menu', 'add_menu_pages', 8 );
		}

		$this->add_action( 'rest_api_init', 'register_routes' );

		// Load scripts and styless
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_plugin_styles' );
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_plugin_scripts' );
		$this->add_action( 'wp_enqueue_scripts', 'enqueue_front_styles' );

		$this->add_filter( 'body_class', 'front_body_class' );

		$this->add_action( 'admin_bar_menu', 'admin_bar_menu', 999 );

		$this->add_filter( 'single_post_title', 'custom_page_titles', 10, 2 );

		$this->add_filter( 'hubloy_membership_admin_register_content_sub_page', 'register_content_page' );

		// Set up content protection
		// This checks if its enabled
		\HubloyMembership\Services\Protection::instance();
	}

	/**
	 * Get menu capability
	 *
	 * @return string
	 */
	public static function get_cap() {
		return apply_filters( 'hubloy_membership_admin_cap', 'manage_options' );
	}

	/**
	 * Load resources
	 *
	 * @since 1.0.0
	 */
	public function wp_loaded() {
		$this->init_variables();
		if ( is_admin() || is_network_admin() ) {
			Resource::register_admin_scripts();
			Resource::register_admin_styles();
		}
		Resource::register_front_scripts();
		Resource::register_front_styles();
	}

	/**
	 * Called when admin section has loaded
	 *
	 * @since 1.0.0
	 */
	public function admin_init() {
		$this->add_filter( 'display_post_states', 'post_states', 10, 2 );
	}

	/**
	 * This filter is called by WordPress when the page-listtable is created to
	 * display all available Posts/Pages. We use this filter to add a note
	 * to all pages that are special membership pages.
	 *
	 * @param  array   $states
	 * @param  WP_Post $post
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function post_states( $states, $post ) {
		if ( 'page' === $post->post_type ) {
			if ( Pages::is_membership_page( $post->ID ) ) {
				$url                         = admin_url( 'admin.php?page=hubloy_membership-settings#/' );
				$states['memberships-by-hubloy'] = sprintf(
					'<a style="%2$s" href="%3$s">%1$s</a>',
					__( 'Membership Page', 'memberships-by-hubloy' ),
					'background:#aaa;color:#fff;padding:1px 4px;border-radius:4px;font-size:0.8em',
					$url
				);
			}
		}
		return $states;
	}
	/**
	 * Admin menu pages
	 *
	 * @since 1.0.0
	 */
	public function add_menu_pages() {
		$cap = self::get_cap();

		add_menu_page(
			__( 'Memberships', 'memberships-by-hubloy' ),
			__( 'Memberships', 'memberships-by-hubloy' ),
			$cap,
			self::MENU_SLUG,
			null,
			'dashicons-lock',
			HUBMEMB_MENU_LOCATION
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Membership Content', 'memberships-by-hubloy' ),
			__( 'Membership Content', 'memberships-by-hubloy' ),
			$cap,
			self::MENU_SLUG,
			array( $this, 'render' )
		);

		$installed = Util::get_option( 'hubloy_membership_installed' );

		// Flag to check if wizard has run for first use
		if ( 1 == $installed ) {
			/**
			 * Action to set up additional admin pages
			 * This is called by the base controller
			 *
			 * @param boolean $is_network - if is network page
			 * @param string $slug - the menu slug
			 * @param string $cap - the menu capabilities
			 *
			 * @since 1.0.0
			 */
			do_action( 'hubloy_membership_admin_menu_page', self::MENU_SLUG, $cap );
		}
	}

	/**
	 * Network admin page setup
	 *
	 * @since 1.0.0
	 */
	public function network_add_menu_pages() {
		$cap = self::get_cap();

		add_menu_page(
			__( 'Memberships', 'memberships-by-hubloy' ),
			__( 'Memberships', 'memberships-by-hubloy' ),
			$cap,
			self::MENU_SLUG,
			null,
			'dashicons-lock',
			HUBMEMB_MENU_LOCATION
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Membership Content', 'memberships-by-hubloy' ),
			__( 'Membership Content', 'memberships-by-hubloy' ),
			$cap,
			self::MENU_SLUG,
			array( $this, 'network_render' )
		);

		$installed = Util::get_option( 'hubloy_membership_installed' );

		// Flag to check if wizard has run for first use
		if ( 1 == $installed ) {
			/**
			 * Action to set up additional admin pages
			 * This is called by the base controller
			 *
			 * @param boolean $is_network - if is network page
			 * @param string $slug - the menu slug
			 * @param string $cap - the menu capabilities
			 *
			 * @since 1.0.0
			 */
			do_action( 'hubloy_membership_network_admin_menu_page', self::MENU_SLUG, $cap );
		}
	}

	/**
	 * Get the content sub-pages
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function content_pages() {
		return apply_filters( 'hubloy_membership_admin_register_content_sub_page', $this->admin->get_content_pages() );
	}

	/**
	 * Load JavasSript for admin
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_plugin_styles( $hook ) {
		$screen         = get_current_screen();
		$load_resources = apply_filters( 'hubloy_membership_load_admin_resouces', strpos( $screen->id, self::MENU_SLUG ) );
		if ( false !== $load_resources ) {
			wp_enqueue_style( 'hubloy_membership-uikit' );
			wp_enqueue_style( 'hubloy_membership-tiptip' );
			wp_enqueue_style( 'hubloy_membership-jquery-ui' );
			wp_enqueue_style( 'hubloy_membership-jquery-tags' );
			wp_enqueue_style( 'hubloy_membership-styled-notifications' );
			wp_enqueue_style( 'hubloy_membership-select2' );
			wp_enqueue_style( 'hubloy_membership-admin' );

			$enabled_text = __( 'Enabled', 'memberships-by-hubloy' );
			$custom_css   = "
				.hubloy_membership-input .switch-checkbox .switch .knobs::after {
                    content: '{$enabled_text}';
                }";
			wp_add_inline_style( 'hubloy_membership-admin', $custom_css );

			// Add body classes
			add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		}
	}


	/**
	 * Add body class
	 *
	 * @param string $classes - array of body classes
	 *
	 * @since 1.0.0
	 *
	 * @return string $classes
	 */
	public function add_body_class( $admin_body_classes ) {
		return "$admin_body_classes hubloy_membership-dashboard";
	}

	/**
	 * Load Styles for admin
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function enqueue_plugin_scripts( $hook ) {
		$screen         = get_current_screen();
		$load_resources = apply_filters( 'hubloy_membership_load_admin_resouces', strpos( $screen->id, self::MENU_SLUG ) );
		if ( $load_resources !== false ) {
			wp_enqueue_script( 'hubloy_membership-uikit' );
			wp_enqueue_script( 'hubloy_membership-uikit-icons' );
			wp_enqueue_script( 'hubloy_membership-tiptip' );
			wp_enqueue_script( 'hubloy_membership-sweetalert' );
			// Date picker
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'hubloy_membership-jquery-tags' );
			wp_enqueue_script( 'hubloy_membership-styled-notifications' );
			wp_enqueue_script( 'hubloy_membership-select2' );
			wp_enqueue_script( 'hubloy_membership-admin' );
			wp_enqueue_script( 'hubloy_membership-admin-react' );
			wp_enqueue_script( 'hubloy_membership-wizard-react' );
			do_action( 'hubloy_membership_controller_scripts' );
		}
	}


	/**
	 * Front styles
	 */
	public function enqueue_front_styles() {
		wp_enqueue_style( 'hubloy_membership-front' );
		wp_enqueue_script( 'hubloy_membership-sweetalert' );
		wp_enqueue_script( 'hubloy_membership-front' );
	}

	/**
	 * Add front body class
	 *
	 * @param array $classes - the current classes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function front_body_class( $classes ) {
		if ( hubloy_membership_is_account_page() ) {
			return array_merge( $classes, array( 'memberships-by-hubloy-account-page' ) );
		} elseif ( hubloy_membership_is_membership_page() ) {
			return array_merge( $classes, array( 'memberships-by-hubloy-membership-page' ) );
		} elseif ( hubloy_membership_is_protected_content_page() ) {
			return array_merge( $classes, array( 'memberships-by-hubloy-protected-content-page' ) );
		} elseif ( hubloy_membership_is_registration_page() ) {
			return array_merge( $classes, array( 'memberships-by-hubloy-registration-page' ) );
		} elseif ( hubloy_membership_is_thank_you_page() ) {
			return array_merge( $classes, array( 'memberships-by-hubloy-thank-you-page' ) );
		}
		return $classes;
	}

	/**
	 * Admin bar menu
	 *
	 * @since 1.0.0
	 */
	public function admin_bar_menu( $admin_bar ) {
		if ( ! is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// Show only when the user is a member of this site, or they're a super admin.
		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
			return;
		}

		if ( ! defined( 'HUBMEMB_HIDE_TOP_BAR' ) ) {
			$args = array(
				'id'    => 'memberships-by-hubloy',
				'title' => __( 'Memberships', 'memberships-by-hubloy' ),
				'href'  => is_multisite() ? esc_url( network_admin_url( 'admin.php?page=memberships-by-hubloy' ) ) : esc_url( admin_url( 'admin.php?page=memberships-by-hubloy' ) ),
			);
			$admin_bar->add_node( $args );
		}

		$admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'view-account',
				'title'  => __( 'View Account', 'memberships-by-hubloy' ),
				'href'   => hubloy_membership_get_account_page_links(),
			)
		);
		$admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'view-memberships',
				'title'  => __( 'View Memberships', 'memberships-by-hubloy' ),
				'href'   => hubloy_membership_get_page_permalink( 'membership_list' ),
			)
		);
	}

	/**
	 * Handle custom page titles
	 * This checks the query args and sets the appropriate page title
	 *
	 * @param array $title - the current title
	 *
	 * @since 1.0.0
	 */
	public function custom_page_titles( $title, $post ) {
		global $wp;

		if ( hubloy_membership_is_account_page() ) {
			$endpoint = hubloy_membership()->get_query()->get_current_endpoint();
			$sep      = apply_filters( 'hubloy_membership_account_title_separator', '|' );
			switch ( $endpoint ) {
				case 'view-plan':
					$plan_id = $wp->query_vars['view-plan'];
					if ( ! empty( $plan_id ) ) {
						$membership = hubloy_membership_get_plan_by_id( $plan_id );
						if ( $membership ) {
							$title = sprintf( __( 'Membership Plan %1$s  %2$s', 'memberships-by-hubloy' ), $sep, $membership->name );
						}
					}

					break;

				case 'edit-account':
					$title .= sprintf( __( ' %s details', 'memberships-by-hubloy' ), $sep );
					break;

				case 'transactions':
					$title .= sprintf( __( ' %s Transactions', 'memberships-by-hubloy' ), $sep );
					break;

				case 'subscriptions':
					$title .= sprintf( __( ' %s Subscriptions', 'memberships-by-hubloy' ), $sep );
					break;
			}
		}
		return $title;
	}

	/**
	 * Register content sub page
	 *
	 * @param array $args
	 *
	 * @since 1.0.0
	 */
	public function register_content_page( $args ) {
		return $this->admin->register_content_sub_page( $args );
	}


	/**
	 * Render view
	 * By default it will return the base view
	 *
	 * @return String
	 */
	public function render() {
		$installed = Util::get_option( 'hubloy_membership_installed' );
		// Flag to check if wizard has run for first use
		if ( 1 == $installed ) {
			?>
			<div id="hubloy_membership-admin-container"></div>
			<?php
		} else {
			$this->show_wizard_page();
		}
	}


	/**
	 * Render admin page
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function network_render() {
		$installed = Util::get_option( 'hubloy_membership_installed' );

		// Flag to check if wizard has run for first use
		if ( 1 == $installed ) {
			?>
			<div id="hubloy_membership-admin-container"></div>
			<?php
		} else {
			$this->show_wizard_page();
		}
	}

	/**
	 * Register rest routes
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		do_action( 'hubloy_membership_register_rest_route' );
	}

	/**
	 * Show the wizard page if first use
	 *
	 * @since 1.0.0
	 */
	public function show_wizard_page() {
		?>
		<div id="hubloy_membership-wizard-container"></div>
		<?php
	}

	/**
	 * Set up admin js variables
	 *
	 * @param array $vars
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function admin_js_vars( $vars ) {
		if ( $this->is_page( 'memberships-by-hubloy' ) ) {
			$vars['common']['string']['title'] = __( 'Dashboard', 'memberships-by-hubloy' );
			$vars['active_page']               = 'dashboard';
			$vars['strings']['dashboard']      = $this->get_strings();
		}
		return $vars;
	}

	/**
	 * Get the strings
	 * This sets the strings if not defined
	 *
	 * @since 1.0.0
	 */
	private function get_strings() {
		if ( empty( $this->strings ) ) {
			$this->strings = include HUBMEMB_LOCALE_DIR . '/site/dashboard.php';
		}
		return $this->strings;
	}
}
