<?php

/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Pilau_GA_Measurement_Protocol
 * @author    Steve Taylor
 * @license   GPL-2.0+
 * @copyright 2013 Public Life
 */

?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( isset( $_GET['done'] ) ) { ?>
		<div class="updated"><p><strong><?php _e( 'Settings updated successfully.' ); ?></strong></p></div>
	<?php } ?>

	<form method="post" action="">

		<?php wp_nonce_field( $this->plugin_slug . '_settings', $this->plugin_slug . '_settings_admin_nonce' ); ?>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="<?php echo $this->plugin_slug . '-ga-id'; ?>"><?php _e( 'Google Analytics ID' ); ?></label></th>
					<td><input type="text" name="ga-id" id="<?php echo $this->plugin_slug . '-ga-id'; ?>" value="<?php esc_attr_e( $this->settings['ga-id'] ); ?>" class="regular-text" placeholder="UA-XXXXXXXX-X"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="<?php echo $this->plugin_slug . '-insert-tracking-code'; ?>"><?php _e( 'Insert JavaScript tracking code?' ); ?></label></th>
					<td><input type="checkbox" name="insert-tracking-code" id="<?php echo $this->plugin_slug . '-insert-tracking-code'; ?>" value="1"<?php checked( $this->settings['insert-tracking-code'] ); ?>></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="<?php echo $this->plugin_slug . '-exclude-user-capability'; ?>"><?php _e( 'Exclude users with this capability' ); ?></label></th>
					<td>
						<input type="text" name="exclude-user-capability" id="<?php echo $this->plugin_slug . '-exclude-user-capability'; ?>" value="<?php esc_attr_e( $this->settings['exclude-user-capability'] ); ?>" class="regular-text" placeholder="e.g. edit_posts">
						<p class="description"><?php _e( 'Leave blank to track all users', $this->plugin_slug ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="<?php echo $this->plugin_slug . '-track-downloads'; ?>"><?php _e( 'Track downloads' ); ?></label></th>
					<td>
						<input type="text" name="track-downloads" id="<?php echo $this->plugin_slug . '-track-downloads'; ?>" value="<?php esc_attr_e( $this->settings['track-downloads'] ); ?>" class="regular-text" placeholder="e.g. pdf,doc,docx,zip">
						<p class="description"><?php _e( 'Enter file types, comma-separated; leave blank to not track downloads', $this->plugin_slug ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="<?php echo $this->plugin_slug . '-enhanced-link-attribution'; ?>"><?php _e( 'Enhanced link attribution?' ); ?></label></th>
					<td>
						<input type="checkbox" name="enhanced-link-attribution" id="<?php echo $this->plugin_slug . '-enhanced-link-attribution'; ?>" value="1"<?php checked( $this->settings['enhanced-link-attribution'] ); ?>>
						<p class="description"><a href="https://support.google.com/analytics/answer/2558867" target="_blank"><?php _e( 'Details on enhanced link attribution', $this->plugin_slug ); ?></a></p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save settings"></p>

	</form>

</div>
