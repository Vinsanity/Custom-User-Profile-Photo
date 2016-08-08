<?php
/*
Plugin Name: Custom User Profile Photo
Plugin URI: http://3five.com
Description: A simple and effective custom WordPress user profile photo plugin. This plugin leverages the WordPress Media Uploader functionality. To use this plugin go to the users tab and select a user. The new field can be found below the password fields for that user.
Author: 3five
Author URI: http://3five.com
Text Domain: custom-user-profile-photo
Domain Path: /languages/
Version: 0.4
*/

// Abort if this file is called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * This program has been developed for use with the WordPress Software.
 *
 * It is distributed as free software with the intent that it will be
 * useful and does not ship with any WARRANTY.
 *
 * USAGE
 * // Default:
 * This will override the WordPress get_avatar hook
 *
 * // Custom placement:
 * <?php $imgURL = get_cupp_meta( $user_id, $size ); ?>
 * or
 * <img src="<?php echo get_cupp_meta( $user_id, $size ); ?>">
 *
 * Beginner WordPress template editing skill required. Place the above tag in your template and provide the two
 * parameters.
 *
 * @param WP_User|int $user_id Default: $post->post_author. Will accept any valid user ID passed into this parameter.
 * @param string      $size    Default: 'thumbnail'. Accepts all default WordPress sizes and any custom sizes made by the
 *                             add_image_size() function.
 *
 * @return {url}      Use this inside the src attribute of an image tag or where you need to call the image url.
 *
 * Inquiries, suggestions and feedback can be sent to support@3five.com
 *
 * This is plugin is intended for Author, Editor and Admin role post/page authors. Thank you for downloading our
 * plugin.
 *
 * We hope this WordPress plugin meets your needs.
 *
 * Happy coding!
 * - 3five
 *
 * Resources:
 *  • Steven Slack - http://s2web.staging.wpengine.com/226/
 *  • Pippin Williamson - https://gist.github.com/pippinsplugins/29bebb740e09e395dc06
 *  • Mike Jolley - https://gist.github.com/mikejolley/3a3b366cb62661727263#file-gistfile1-php
 */

require_once plugin_dir_path( __FILE__ ) . 'src/class-cupp.php';

$plugin = new CUPP( plugin_dir_path( __FILE__ ) );
$plugin->run();