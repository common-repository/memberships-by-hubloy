<?php
namespace HubloyMembership\Addon\Mailchimp;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * MailChimp API
 *
 * @since 1.0.0
 */
class Api {

	private $_api_key;
	private $_data_center;
	private $_user;

	/**
	 * The endpoint for the supported version
	 */
	private $_endpoint = 'https://<dc>.api.mailchimp.com/3.0/';

	/**
	 * Constructs class with required data
	 *
	 * Api constructor.
	 *
	 * @param $api_key
	 */
	public function __construct( $api_key, $data_center ) {
		$this->_api_key     = $api_key;
		$this->_data_center = $data_center;
		$this->_endpoint    = str_replace( '<dc>', $data_center, $this->_endpoint );
		$this->_user        = is_user_logged_in() ? wp_get_current_user()->display_name : get_bloginfo( 'name' );
	}

	/**
	 * Sends request to the endpoint url with the provided $action
	 *
	 * @param string $verb
	 * @param string $action rest action
	 * @param array  $args
	 * @return object|WP_Error
	 */
	private function _request( $verb = 'GET', $action, $args = array() ) {
		$url = trailingslashit( $this->_endpoint ) . $action;

		$_args = array(
			'method'  => $verb,
			'headers' => array(
				'Authorization' => 'apikey ' . $this->_api_key,
				'Content-Type'  => 'application/json;charset=utf-8',
			),
		);

		if ( 'GET' === $verb ) {
			$url .= ( '?' . http_build_query( $args ) );
		} else {
			$_args['body'] = wp_json_encode( $args['body'] );
		}

		$res = wp_remote_request( $url, $_args );

		if ( ! is_wp_error( $res ) && is_array( $res ) ) {
			if ( $res['response']['code'] <= 204 ) {
				return json_decode( wp_remote_retrieve_body( $res ) );
			}

			$err = new WP_Error();
			$err->add( $res['response']['code'], $res['response']['message'] );
			return $err;
		}

		return $res;
	}

	/**
	 * Sends rest GET request
	 *
	 * @param $action
	 * @param array  $args
	 * @return array|mixed|object|WP_Error
	 */
	private function _get( $action, $args = array() ) {
		return $this->_request( 'GET', $action, $args );
	}

	/**
	 * Sends rest POST request
	 *
	 * @param $action
	 * @param array  $args
	 * @return array|mixed|object|WP_Error
	 */
	private function _post( $action, $args = array() ) {
		return $this->_request( 'POST', $action, $args );
	}

	 /**
	  * Sends rest PUT request
	  *
	  * @param $action
	  * @param array  $args
	  * @return array|mixed|object|WP_Error
	  */
	private function _put( $action, $args = array() ) {
		return $this->_request( 'PUT', $action, $args );
	}

	/**
	 * Gets all the lists
	 *
	 * @param $count - current total lists to show
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_lists( $count = 0 ) {
		return $this->_get(
			'lists',
			array(
				'user'  => $this->_user . ':' . $this->_api_key,
				'count' => ( $count > 0 ) ? ( $count * 20 ) : 20,
			)
		);
	}

	/**
	 * Gets all the groups under a list
	 *
	 * @param $list_id
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_interest_categories( $list_id, $total = 10 ) {
		return $this->_get(
			'lists/' . $list_id . '/interest-categories',
			array(
				'user'  => $this->_user . ':' . $this->_api_key,
				'count' => $total,
			)
		);
	}

	/**
	 * Gets all the interests under a group list
	 *
	 * @param $list_id
	 * @param $category_id
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_interests( $list_id, $category_id, $total = 10 ) {
		return $this->_get(
			'lists/' . $list_id . '/interest-categories/' . $category_id . '/interests',
			array(
				'user'  => $this->_user . ':' . $this->_api_key,
				'count' => $total,
			)
		);
	}

	/**
	 * Check member email address if already existing
	 *
	 * @param $list_id
	 * @param $email
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function check_email( $list_id, $email ) {
		$md5_email = md5( strtolower( $email ) );
		return $this->_get(
			'lists/' . $list_id . '/members/' . $md5_email,
			array(
				'user' => $this->_user . ':' . $this->_api_key,
			)
		);
	}


	/**
	 * Delete detail of member
	 *
	 * @param $list_id
	 * @param $email
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function delete_email( $list_id, $email ) {
		$md5_email = md5( strtolower( $email ) );
		$this->update_subscription_patch( $list_id, $email, array( 'status' => 'unsubscribed' ) );
		return $this->_delete( 'lists/' . $list_id . '/members/' . $md5_email );
	}

	/**
	 * Add custom field for list
	 *
	 * @param $list_id
	 * @param $field_data
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function add_custom_field( $list_id, $field_data ) {
		return $this->_post(
			'lists/' . $list_id . '/merge-fields',
			array(
				'body' => $field_data,
			)
		);
	}

	/**
	 * Add new subscriber
	 *
	 * @param $list_id
	 * @param $data
	 * @return array|mixed|object|WP_Error
	 */
	public function subscribe( $list_id, $data ) {
		$res = $this->_post(
			'lists/' . $list_id . '/members',
			array(
				'body' => $data,
			)
		);

		$error = __( 'Something went wrong, please compare your Opt-in fields with MailChimp fields and add any missing fields.', 'memberships-by-hubloy' );

		if ( ! is_wp_error( $res ) ) {
			return __( 'Successful subscription', 'memberships-by-hubloy' );
		} else {
			throw new \Exception( $error );
		}
	}

	/**
	 * Update subscription
	 *
	 * @param $list_id - the list id
	 * @param $email - the email
	 * @param $data - array
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function update_subscription( $list_id, $email, $data ) {
		$md5_email = md5( strtolower( $email ) );
		$resp      = $this->_put(
			'lists/' . $list_id . '/members/' . $md5_email,
			array(
				'body' => $data,
			)
		);
		$error     = __( 'This email address has already subscribed', 'memberships-by-hubloy' );

		if ( ! is_wp_error( $res ) ) {
			return __( 'You have been added to the new group', 'memberships-by-hubloy' );
		} else {
			throw new \Exception( $error );
		}
	}


	/**
	 * Update subscription
	 *
	 * @param $list_id - the list id
	 * @param $email - the email
	 * @param $data - array
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function update_subscription_patch( $list_id, $email, $data ) {
		$md5_email = md5( strtolower( $email ) );
		if ( ! empty( $data['tags'] ) && is_array( $data['tags'] ) ) {
			foreach ( $data['tags'] as $tag_id ) {
				$res = $this->_post(
					'lists/' . $list_id . '/segments/' . $tag_id . '/members/',
					array(
						'body' => array(
							'email_address' => strtolower( $email ),
						),
					)
				);
			}
			unset( $data['tags'] );
		}
		$res = $this->_patch(
			'lists/' . $list_id . '/members/' . $md5_email,
			array(
				'body' => $data,
			)
		);

		$error = __( "Couldn't update the user", 'memberships-by-hubloy' );
		if ( ! is_wp_error( $res ) ) {
			return __( 'User updated', 'memberships-by-hubloy' );
		} else {
			throw new Exception( $error );
		}
	}
}
