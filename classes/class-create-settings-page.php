<?php
/**
 * Build a settings page for the plugin.
 */
namespace Organixx\Create_Settings_Page;

$init_settings_page = new Create_Settings_Page();

/**
 * Class Create Settings Page
 */
class Create_Settings_Page {

	/**
	 * Class Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_menu', array( $this, 'options_page' ) );
	}

	/**
	 * Setttings Init
	 *
	 * Register settings for our custom plugin page
	 * @return void
	 */
	public function settings_init() {
		// register a new setting for "sync_header_footer" page
		register_setting( 'sync_header_footer', 'sync_header_footer_options' );

		// register a new section in the "sync_header_footer" page
		add_settings_section(
			'sync_header_footer_dev_section',
			__( '', 'sync_header_footer' ),
			array( $this, 'dev_callback' ),
			'sync_header_footer'
		);

		// register a new field in the "sync_header_footer_dev_section" section, inside the "sync_header_footer" page
		add_settings_field(
			'sync_header_footer_field', // as of WP 4.6 this value is used only internally

			// use $args' label_for to populate the id inside the callback
			__( '', 'sync_header_footer' ),
			array( $this, 'user_input_callback' ),
			'sync_header_footer',
			'sync_header_footer_dev_section',
			[
				'custom_data' => 'custom',
			]
		);
	}

	/**
	 * Dev callback
	 *
	 * @param array
	 * @return void
	 */
	public function dev_callback( $args ) {

	}

	/**
	 * field callbacks can accept an $args parameter, which is an array.
	 * $args is defined at the add_settings_field() function.
	 * wordpress has magic interaction with the following keys: label_for, class.
	 * the "label_for" key value is used for the "for" attribute of the <label>.
	 * the "class" key value is used for the "class" attribute of the <tr> containing the field.
	 * you can add custom key value pairs to be used inside your callbacks.
	 *
	 * @param array
	 * @return void
	 */
	public function user_input_callback( $args ) {
		// get the value of the setting we've registered with register_setting()
		// $options  = get_option( 'sync_header_footer_options' );
		// $password = isset( $options['password'] ) ? trim( $options['password'] ) : '';
		// $key      = isset( $options['key'] ) ? trim( $options['key'] ) : '';
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function options_page() {
		// add top level menu page
		add_menu_page(
			'Shopify Sync',
			'Sync Shopify Header and Footer',
			'manage_options',
			'sync_header_footer',
			array( $this, 'shopify_sync_page_html' )
		);
	}

	/**
	 * Shopify sync page html
	 *
	 * @return void
	 */
	public function shopify_sync_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 * add error/update messages
		 * check if the user have submitted the settings
		 * wordpress will add the "settings-updated" $_GET parameter to the url
		 */
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'sync_header_footer_messages', 'sync_header_footer_message', __( 'Shopify Header/Footer Syncd!', 'sync_header_footer' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'sync_header_footer_messages' );

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2>Sync the Shopify site header and footer to organixx.com.</h2>
			<h3>Class Properties in /classes/class-build-header-footer.php</h3>
			<ul>
				<li>$footer_path string</li>
				<li>$header_path string</li>
				<li>$committer array</li>
				<li>$github_oath_token string</li>
				<li>$oxdev string</li>
				<li>$develop string</li>
			</ul>
			<h3>Misc. items to update in /classes/class-build-header-footer.php</h3>
			<ul>
				<li>footer.liquid schema in method get_footer_html()</li>
				<li>change branch to commit to in method update_shopify_files() -> update_liquid_file( $this->oxdev )</li>
			</ul>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "sync_header_footer"
				settings_fields( 'sync_header_footer' );

				// output setting sections and their fields
				// (sections are registered for "sync_header_footer", each field is registered to a specific section)
				do_settings_sections( 'sync_header_footer' );

				// output save settings button
				submit_button( 'Sync Header and Footer' );
				?>
			</form>
		</div>
		<?php
	}
}
