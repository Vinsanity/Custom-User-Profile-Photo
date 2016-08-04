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


/**
 * Enqueue scripts and styles
 */
function cupp_enqueue_scripts_styles() {
	// Register
	wp_register_style( 'cupp_admin_css', plugins_url( 'custom-user-profile-photo/css/styles.css' ), false, '1.0.0', 'all' );
	wp_register_script( 'cupp_admin_js', plugins_url( 'custom-user-profile-photo/js/scripts.js' ), array( 'jquery' ), '1.0.0', true );

	// Enqueue
	wp_enqueue_style( 'cupp_admin_css' );
	wp_enqueue_script( 'cupp_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'cupp_enqueue_scripts_styles' );


/**
 * Show the new image field in the user profile page.
 *
 * @param $user
 */
function cupp_profile_img_fields( $user ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		return;
	}

	// Custom image data
	$url             = get_the_author_meta( 'cupp_meta', $user->ID );
	$upload_url      = get_the_author_meta( 'cupp_upload_meta', $user->ID );
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
	?>

	<div id="cupp_container">
		<h3><?php _e( 'Custom User Profile Photo', 'custom-user-profile-photo' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="cupp_meta"><?php _e( 'Profile Photo', 'custom-user-profile-photo' ); ?></label></th>
				<td>
					<!-- Outputs the image after save -->
					<div id="current_img">
						<img class="<?php esc_attr_e( $img_class ); ?>" src="<?php echo esc_url( $current_url ); ?>"/>

						<?php if ( $edit_class ) : // Presentation for editing the image ?>
							<div class="edit_options <?php esc_attr_e( $edit_class ); ?>">
								<a class="remove_img">
									<span><?php _e( 'Remove', 'custom-user-profile-photo' ); ?></span>
								</a>

								<?php if ( $upload_edit_url ) : ?>
									<a class="edit_img" href="<?php echo esc_url( $upload_edit_url ); ?>" target="_blank">
										<span><?php _e( 'Edit', 'custom-user-profile-photo' ); ?></span>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<!-- Select an option: Upload to WPMU or External URL -->
					<div id="cupp_options">
						<input type="radio" id="upload_option" name="img_option" value="upload" class="tog" checked>
						<label for="upload_option"><?php _e( 'Upload New Image', 'custom-user-profile-photo' ); ?></label><br>

						<input type="radio" id="external_option" name="img_option" value="external" class="tog">
						<label for="external_option"><?php _e( 'Use External URL', 'custom-user-profile-photo' ); ?></label><br>
					</div>

					<!-- Hold the value here if this is a WPMU image -->
					<div id="cupp_upload">
						<input class="hidden" type="hidden" name="cupp_placeholder_meta" id="cupp_placeholder_meta"
						       value="<?php echo esc_url( plugins_url( 'custom-user-profile-photo/img/placeholder.gif' ) ); ?>" />
						<input class="hidden" type="hidden" name="cupp_upload_meta" id="cupp_upload_meta"
						       value="<?php echo esc_url_raw( $upload_url ); ?>" />
						<input class="hidden" type="hidden" name="cupp_upload_edit_meta" id="cupp_upload_edit_meta"
						       value="<?php echo esc_url_raw( $upload_edit_url ); ?>" />
						<input id="uploadimage" type='button' class="cupp_wpmu_button button-primary"
						       value="<?php _e( esc_attr( $button_text ), 'custom-user-profile-photo' ); ?>" />
						<br />
					</div>

					<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
					<div id="cupp_external">
						<input class="regular-text" type="text" name="cupp_meta" id="cupp_meta" value="<?php echo esc_url_raw( $url ); ?>"  />
					</div>

					<!-- Outputs the save button -->
					<span class="description">
						<?php
						_e(
							'Upload a custom photo for your user profile or use a URL to a pre-existing photo.',
							'custom-user-profile-photo'
						);
						?>
					</span>
					<p class="description">
						<?php _e( 'Update Profile to save your changes.', 'custom-user-profile-photo' ); ?>
					</p>
				</td>
			</tr>
		</table><!-- end form-table -->
	</div> <!-- end #cupp_container -->

	<?php
	// Enqueue the WordPress Media Uploader
	wp_enqueue_media();
}

add_action( 'show_user_profile', 'cupp_profile_img_fields' );
add_action( 'edit_user_profile', 'cupp_profile_img_fields' );


/**
 * Save the new user CUPP url.
 *
 * @param int $user_id
 */
function cupp_save_img_meta( $user_id ) {
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

	if ( count( $values ) !== count( array_filter( $values ) ) ) {
		$test = 'true';
		// do something
	}

	foreach ( $values as $key => $value ) {
		update_user_meta( $user_id, $key, $value ); // @codingStandardsIgnoreLine
	}
}

add_action( 'personal_options_update', 'cupp_save_img_meta' );
add_action( 'edit_user_profile_update', 'cupp_save_img_meta' );

/**
 * Retrieve the appropriate image size
 *
 * @param int $user_id Default: $post->post_author. Will accept any valid user ID passed into this parameter.
 * @param string      $size    Default: 'thumbnail'. Accepts all default WordPress sizes and any custom sizes made by
 *                             the add_image_size() function.
 *
 * @return string      (Url) Use this inside the src attribute of an image tag or where you need to call the image url.
 */
function get_cupp_meta( $user_id, $size = 'thumbnail' ) {
	global $post;

	if ( ! $user_id || ! is_numeric( $user_id ) ) {
		/*
		 * Here we're assuming that the avatar being called is the author of the post.
		 * The theory is that when a number is not supplied, this function is being used to
		 * get the avatar of a post author using get_avatar() and an email address is supplied
		 * for the $id_or_email parameter. We need an integer to get the custom image so we force that here.
		 * Also, many themes use get_avatar on the single post pages and pass it the author email address so this
		 * acts as a fall back.
		 */
		$user_id = $post->post_author;
	}

	// Check first for a custom uploaded image
	$attachment_upload_url = esc_url( get_the_author_meta( 'cupp_upload_meta', $user_id ) );

	if ( $attachment_upload_url ) {
		// grabs the id from the URL using the WordPress function attachment_url_to_postid @since 4.0.0
		$attachment_id = attachment_url_to_postid( $attachment_upload_url );

		// Retrieve the thumbnail size of our image. Should return an array with first index value containing the URL.
		$image_thumb = wp_get_attachment_image_src( $attachment_id, $size );

		return $attachment_upload_url;
	}

	// Finally, check for image from an external URL. If none exists, return an empty string.
	$attachment_ext_url = esc_url( get_the_author_meta( 'cupp_meta', $user_id ) );

	return $attachment_ext_url ? $attachment_ext_url : '';
}


/**
 * WordPress Avatar Filter
 *
 * Replaces the WordPress avatar with your custom photo using the get_avatar hook.
 *
 * @param $avatar
 * @param int|object|string $identifier
 * @param $size
 * @param $alt
 *
 * @return string
 */
function cupp_avatar( $avatar, $identifier, $size, $alt ) {
	if ( $user = cupp_get_user_by_id_or_email( $identifier ) ) {
		if ( $custom_avatar = get_cupp_meta( $user->ID, 'thumbnail' ) ) {
			return "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
		}
	}

	return $avatar;
}

add_filter( 'get_avatar', 'cupp_avatar', 1, 5 );

/**
 * Get a WordPress User by ID or email
 *
 * @param $identifier
 *
 * @return WP_User
 */
function cupp_get_user_by_id_or_email( $identifier ) {
	if ( is_numeric( $identifier ) ) {
		return get_user_by( 'id', (int) $identifier );
	}

	if ( property_exists( $identifier, 'user_id' ) ) {
		return get_user_by( 'id', (int) $identifier->user_id );
	}

	return get_user_by( 'email', $identifier );
}

/**
 * Hook: profile_update
 *
 * @param $user_id
 * @param $old_user_data
 */
function cupp_profile_update( $user_id, $old_user_data ) {
	if ( ! $user = cupp_get_user_by_id_or_email( $user_id ) ) {
		return;
	}

	foreach ( array( 'cupp_meta', 'cupp_upload_meta', 'cupp_edit_meta' ) as $meta ) {
		$data[ $meta ] = get_the_author_meta( $meta, $user_id ); // @codingStandardsIgnoreLine
	}

	if ( ! empty( $data['cupp_meta'] ) ) {
		$test = 'this is a test';
	}

	if ( ! empty( $data['cupp_upload_meta'] ) ) {
		// Upload isn't in the media library
		if ( ! attachment_url_to_postid( $data['cupp_upload_meta'] ) ) {
			$path = explode( '/uploads/', $data['cupp_upload_meta'] );
			$upload_path = wp_get_upload_dir();
			$file_path = $upload_path['basedir'];

			if ( 2 === count( $path ) ) {
				$file_path .= '/' . $path[1];
			}

			if ( file_exists( $file_path ) ) {
				if ( $new_src = cupp_copy_image_to_media_library( $data['cupp_upload_meta'], $user_id ) ) {
					update_user_meta( $user_id, 'cupp_upload_meta', $new_src );
				}
			}
		}
	}
}

add_action( 'profile_update', 'cupp_profile_update', 10, 2 );


/**
 * @param $image_url
 *
 * @return array|false
 */
function cupp_copy_image_to_media_library( $image_url, $user_id ) {
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Sideload image and return its source url
	$new_img_id = attachment_url_to_postid( media_sideload_image( $image_url, $user_id, null, 'src' ) );
	$new_img = wp_get_attachment_image_src( $new_img_id );

	if ( is_array( $new_img ) ) {
		return $new_img[0];
	}

	return '';
}
