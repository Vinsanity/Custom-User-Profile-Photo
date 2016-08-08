<?php
require_once plugin_dir_path( __FILE__ ) . '/class-cupp-user-image.php';

/**
 * Class CUPP_User
 */
class CUPP_User {
	/**
	 * @var false|WP_User
	 */
	private $user;

	/**
	 * @var
	 */
	public $id;

	/**
	 * CUPP_User constructor.
	 *
	 * @param $identifier
	 */
	public function __construct( $identifier ) {
		$this->user  = $this->get_by_identifier( $identifier );
		$this->id    = $this->user ? $this->user->ID : 0;
	}

	/**
	 * @param $identifier
	 *
	 * @return false|WP_User
	 */
	private function get_by_identifier( $identifier ) {
		if ( is_numeric( $identifier ) ) {
			return get_user_by( 'id', (int) $identifier );
		}

		if ( property_exists( $identifier, 'user_id' ) ) {
			return get_user_by( 'id', (int) $identifier->user_id );
		}

		if ( is_a( $identifier, 'WP_User' ) ) {
			return $identifier;
		}

		$user = get_user_by( 'email', $identifier );

		return $user ? $user : false;
	}
}
