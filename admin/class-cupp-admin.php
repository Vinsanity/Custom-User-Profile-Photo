<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CUPP
 * @subpackage CUPP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CUPP
 * @subpackage CUPP/admin
 * @author     Your Name <email@example.com>
 */
class CUPP_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CUPP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CUPP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/styles.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CUPP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CUPP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scripts.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Save the new user CUPP url.
	 *
	 * @param int $user_id
	 */
	public function save_user_meta( $user_id ) {
		if ( ! current_user_can( 'upload_files', $user_id ) ) {
			return;
		}

		$values = array(
			// String value. Empty in this case.
			'cupp_meta'             => filter_input( INPUT_POST, 'cupp_meta', FILTER_SANITIZE_STRING ),

			// File path, e.g., http://3five.dev/wp-content/plugins/custom-user-profile-photo/img/placeholder.gif
			'cupp_upload_meta'      => filter_input( INPUT_POST, 'cupp_upload_meta', FILTER_SANITIZE_URL ),

			// Edit path, e.g., /wp-admin/post.php?post=32&action=edit&image-editor,
			'cupp_upload_edit_meta' => filter_input( INPUT_POST, 'cupp_upload_edit_meta', FILTER_SANITIZE_URL ),
		);

		foreach ( $values as $key => $value ) {
			update_user_meta( $user_id, $key, $value ); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * @param $user_id
	 * @param $old_user_data
	 */
	public function change_image_on_profile_update( $user_id, $old_user_data ) {
		$image = new CUPP_User_Image( new CUPP_User( $user_id ) );

		if ( ! $image->get_upload_url() ) {
			return;
		}

		$image->maybe_attach_to_user_profile();
	}

	/**
	 * Show the new image field in the user profile page.
	 *
	 * Note: variables that appear "unused" are in fact used by the included template file.
	 *
	 * @param $user
	 */
	public function display_user_profile_settings( $user ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$image = new CUPP_User_Image( new CUPP_User( $user ) );

		// Custom image data
		$url             = $image->get_external_url();
		$upload_url      = $image->get_upload_url();
		$upload_edit_url = '';

		if ( $attachment_id = attachment_url_to_postid( $upload_url ) ) {
			$upload_edit_url = get_site_url() . '/wp-admin/post.php?post=' . $attachment_id . '&action=edit&image-editor';
		}

		// Classes for settings presentation
		$img_class   = 'cupp-current-img';
		$edit_class  = $upload_edit_url ? 'uploaded' : 'single';
		$button_text = $attachment_id ? 'Change Current Image' : 'Upload New Image';

		// Placeholder fallbacks if there is no custom image
		$current_url = $url ? $url : $upload_url;

		if ( ! $current_url ) {
			$current_url = plugins_url( 'custom-user-profile-photo/img/placeholder.gif' );
			$img_class .= ' placeholder';
			$edit_class = '';
		}

		include plugin_dir_path( __FILE__ ) . 'partials/cupp-user-settings-display.php';
	}
}
