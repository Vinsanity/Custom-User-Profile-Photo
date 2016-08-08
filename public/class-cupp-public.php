<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Custom_User_Profile_Photo
 * @subpackage Custom_User_Profile_Photo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_User_Profile_Photo
 * @subpackage Custom_User_Profile_Photo/public
 * @author     Your Name <email@example.com>
 */
class Custom_User_Profile_Photo_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * WordPress Avatar Filter
	 *
	 * Replaces the WordPress avatar with your custom photo using the get_avatar hook.
	 *
	 * @param                   $avatar
	 * @param int|object|string $identifier
	 * @param                   $size
	 * @param                   $alt
	 *
	 * @return string
	 */
	public function cupp_avatar( $avatar, $identifier, $size = 96, $alt ) {
		$user = new Custom_User_Profile_Photo_User( $identifier );

		if ( $custom_avatar = $user->image->get_url( $size ) ) {
			if ( $user->image->get_upload_url() ) {
				return "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo'"
				       . "height='{$user->image->get_height()}' width='{$user->image->get_width()}' />";
			}

			return "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo'"
			       . "height='auto' width='{$size}' />";
		}

		return $avatar;
	}
}
