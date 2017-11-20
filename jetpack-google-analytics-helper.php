<?php
/*
Plugin Name: Jetpack Google Analytics Helper
Plugin URI: https://github.com/Automattic/jetpack-google-analytics-helper
Description: A tool to help with testing of the Jetpack Google Analytics module
Version: 1.0
Author: Automattic
Author https://automattic.com
License: GPL2
*/

class Jetpack_Google_Analytics_Helper {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function get_form_fields() {
		return array(
			array(
				'label' => __( 'Google Analytics Tracking/Property ID', 'jpgah' ),
				'key' => 'code',
				'type' => 'text',
			),
			array(
				'label' => __( 'Anonymize IP Addresses', 'jpgah' ),
				'key' => 'anonymize_ip',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Purchase Transactions', 'jpgah' ),
				'key' => 'ec_track_purchases',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Add to Cart Events', 'jpgah' ),
				'key' => 'ec_track_add_to_cart',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Enable Enhanced eCommerce', 'jpgah' ),
				'key' => 'enh_ec_tracking',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Remove From Cart Events (Enh eComm only)', 'jpgah' ),
				'key' => 'enh_ec_track_remove_from_cart',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Product Impressions on Listing Pages (Enh eComm only)', 'jpgah' ),
				'key' => 'enh_ec_track_prod_impression',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Product Clicks on Listing Pages (Enh eComm only)', 'jpgah' ),
				'key' => 'enh_ec_track_prod_click',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Product Detail Views (Enh eComm only)', 'jpgah' ),
				'key' => 'enh_ec_track_prod_detail_view',
				'type' => 'checkbox',
			),
			array(
				'label' => __( 'Track Checkout Process Initiated (Enh eComm only)', 'jpgah' ),
				'key' => 'enh_ec_track_checkout_started',
				'type' => 'checkbox',
			)
		);
	}

	public function admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Jetpack Google Analytics Helper', 'jpgah' ),
			__( 'Jetpack Google Analytics Helper', 'jpgah' ),
			'manage_options',
			'jpgah',
			array( $this, 'echo_form_html' )
		);
	}

	protected function echo_form_table_rows_html() {
		$jetpack_wga_option = get_option( 'jetpack_wga', array() );

		foreach ( $this->get_form_fields() as $form_field ) {
			extract( $form_field );
			$value = array_key_exists( $key, $jetpack_wga_option ) ? $jetpack_wga_option[ $key ] : '';
			?>
			<tr>
				<th><?php echo esc_html( $label ); ?></th>
				<td>
					<?php
						if ( 'text' === $type ) {
							?>
							<input name="<?php esc_attr_e( $key ); ?>"
								type="text"
								id="<?php esc_attr_e( $key ); ?>"
								value="<?php esc_attr_e( $value ); ?>"
							/>
							<?php
						} else if ( 'checkbox' === $type ) {
							?>
							<input name="<?php esc_attr_e( $key ); ?>"
								type="checkbox"
								id="<?php esc_attr_e( $key ); ?>"
								value="<?php esc_attr_e( $key ); ?>"
								<?php checked( $value ); ?>
							/>
							<?php
						}
					?>
				</td>
			</tr>
			<?php
		}
	}

	public function save_form() {
		$jetpack_wga_option = get_option( 'jetpack_wga', array() );

		foreach ( $this->get_form_fields() as $form_field ) {
			extract( $form_field );
			if ( isset( $_POST[ $key ] ) ) {
				if ( 'text' === $type ) {
					$new_value = sanitize_text_field( $_POST[ $key ] );
					$jetpack_wga_option[ $key ] = $new_value;
				} else if ( 'checkbox' === $type ) {
					$jetpack_wga_option[ $key ] = true;
				}
			} else {
				// Clear unsupplied checkboxes
				if ( 'checkbox' === $type ) {
					$jetpack_wga_option[ $key ] = false;
				}
			}
		}

		update_option( 'jetpack_wga', $jetpack_wga_option );
	}

	public function echo_form_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST[ 'jpgah_nonce' ] ) ) {
			if ( ! wp_verify_nonce( $_POST[ 'jpgah_nonce' ], 'jpgah_action' ) ) {
				wp_die( __( 'Unauthorized', 'jpgah' ) );
			}

			$this->save_form();

			?>
				<div class="updated">
					<p><?php esc_html_e( 'Changes saved', 'jpgah' ); ?></p>
				</div>
			<?php
		}

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Jetpack Google Analytics Helper', 'jpgah' ); ?></h1>
			<form method="post" action="">
				<table class="form-table">
					<?php $this->echo_form_table_rows_html(); ?>
				</table>
				<?php
					wp_nonce_field( 'jpgah_action', 'jpgah_nonce' );
				?>
				<input
					class="button button-primary"
					type="submit"
					value="<?php esc_attr_e( __( 'Save Changes', 'jpgah' ) ) ?>"
				>
			</form>
		</div>
		<?php
	}
}

new Jetpack_Google_Analytics_Helper();

