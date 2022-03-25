<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://organixx.com
 * @since             1.0.0
 * @package           Organixx\Header_Footer_Sync
 *
 * @wordpress-plugin
 * Plugin Name:       Organixx Header Footer Sync
 * Plugin URI:        https://github.com/epigenetic-labs-llc/oxng/wp-content/plugins/organixx-header-footer-sync
 * Description:       Sync the shopify header and footer to WordPress site.
 * Version:           1.0.0
 * Author:            Organixx
 * Author URI:        https://organixx.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       organixx-header-footer-sync
 * Domain Path:       /languages
 */
namespace Organixx\Header_Footer_Sync;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217?' );
}

$plugin_url = plugin_dir_url( __FILE__ );
define( 'HEADER_FOOTER_SYNC_URL', $plugin_url );
define( 'HEADER_FOOTER_SYNC_DIR', plugin_dir_path( __DIR__ ) );
define( 'HEADER_FOOTER_SYNC_VER', '1.0.0' );
define( 'HEADER_FOOTER_SYNC_API', '' );
define( 'HEADER_FOOTER_SYNC_PW', '' );

new Init_Plugin();

/**
 * Class Init_Plugin
 */
class Init_Plugin {

	/**
	 * Construct function
	 *
	 * @return void
	 */
	public function __construct() {
		// Only load script if in admin.
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		register_activation_hook( __FILE__, array( __CLASS__, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate_plugin' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall_plugin' ) );

		$this->init_autoloader();
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_scripts() {
		//wp_enqueue_media();
		wp_enqueue_script( 'organixx-sync-script', HEADER_FOOTER_SYNC_URL . 'assets/organixx-sync-script.js', 'jquery', HEADER_FOOTER_SYNC_VER, true );
	}

	/**
	 * Plugin activation handler
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate_plugin() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		flush_rewrite_rules();
	}

	/**
	 * The plugin is deactivating. Delete out the rewrite rules option.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate_plugin() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Uninstall plugin handler
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function uninstall_plugin() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		check_admin_referer( 'bulk-plugins' );

		// Important: Check if the file is the one
		// that was registered during the uninstall hook.
		if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
			return;
		}
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Kick off the plugin by initializing the plugin files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_autoloader() {
		require_once 'vendor/autoload.php';
		require_once 'classes/class-create-settings-page.php';
		require_once 'classes/class-build-header-footer.php';
	}
}
