<?php
namespace HubloyMembership\Emails\Member\Membership\Payment;

use HubloyMembership\Base\Email;

/**
 * User card expire
 */
class Failed extends Email {

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set up variables
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$type                = \HubloyMembership\Services\Emails::COMM_TYPE_FAILED_PAYMENT;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/payment/failed.php';
		$this->placeholders  = array(
			'{membership_name}' => '',
		);
	}

	/**
	 * Register defaults
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_defaults() {
		return array(
			'title'       => __( 'Payment failed', 'memberships-by-hubloy' ),
			'description' => __( 'Sent to a member when payment fails', 'memberships-by-hubloy' ),
			'heading'     => __( 'Payment failed', 'memberships-by-hubloy' ),
			'subject'     => sprintf( __( 'Your %s membership payment failed', 'memberships-by-hubloy' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

