<?php

/**
 * Class Custom_User_Profile_Photo_User_Image
 */
class Custom_User_Profile_Photo_User_Image {
	/**
	 * @var Custom_User_Profile_Photo_User $user
	 */
	private $user;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var string
	 */
	private $upload_url;

	/**
	 * Custom_User_Profile_Photo_User_Image constructor.
	 *
	 * @param Custom_User_Profile_Photo_User $user
	 */
	public function __construct( Custom_User_Profile_Photo_User $user ) {
		$this->user       = $user;
		$this->url        = get_the_author_meta( 'cupp_meta', $this->user->id );
		$this->upload_url = get_the_author_meta( 'cupp_upload_meta', $this->user->id );
	}

	/**
	 * @param string $size
	 *
	 * @return string
	 */
	public function get_url( $size = 'thumbnail' ) {
		// Check first for a custom uploaded image
		if ( $this->upload_url ) {
			$img = wp_get_attachment_image_src( attachment_url_to_postid( $this->upload_url ), $size );
			return $img[0];
		}

		return $this->url ? $this->url : '';
	}

	/**
	 * @return string
	 */
	public function get_upload_url() {
		return $this->upload_url;
	}

	/**
	 * @return string
	 */
	public function get_external_url() {
		return $this->url;
	}

	/**
	 * If the user has data in cupp_upload_meta but the image isn't visible to the media library, check if it's in the
	 * uploads directory and attach it to the user's profile.
	 */
	public function maybe_attach_to_user_profile() {
		if ( ! attachment_url_to_postid( $this->upload_url ) && $this->file_upload_exists() ) {

			if ( $this->copy_to_media_library() ) {
				update_user_meta( $this->user->id, 'cupp_upload_meta', $this->upload_url ); // @codingStandardsIgnoreLine
			}
		}
	}

	/**
	 * Check whether the file associated with the user's profile exists in the WordPress uploads directory. It's possible
	 * the file may exist in a custom path outside of what's visible to the Media Library.
	 *
	 * @return bool
	 */
	private function file_upload_exists() {
		$upload_dir      = wp_get_upload_dir();
		$upload_dir_url  = $upload_dir['baseurl'];
		$upload_dir_path = $upload_dir['basedir'];
		$path            = explode( $upload_dir_url, $this->upload_url ); // Split the path into two parts - upload directory and remaining file path

		// Provided path didn't match the upload directory, or we somehow got too many array indexes.
		if ( 2 !== count( $path ) ) {
			return false;
		}

		// Append the file path to the uploads base directory.
		$upload_dir_path .= '/' . $path[1];

		if ( file_exists( $upload_dir_path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Copy a file from a URL into the WordPress media library and return its new src path.
	 *
	 * @return array|false
	 */
	private function copy_to_media_library() {
		// WordPress hasn't loaded these necessary files yet, so we'll require them here.
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$new_img_id = attachment_url_to_postid( media_sideload_image( $this->upload_url, $this->user->id, null, 'src' ) );
		$new_img    = wp_get_attachment_image_src( $new_img_id, 'url' ); // We need the full size to get attachment_url_to_postid to work.

		if ( is_array( $new_img ) ) {
			$this->upload_url = $new_img[0];

			return $this->upload_url;
		}

		return '';
	}
}
