<?php
/**
 * @var $img_class
 * @var $edit_class
 * @var $upload_edit_url
 * @var $upload_url
 * @var $button_text
 * @var $current_url
 */
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
