<?php
/**
 * Build header and footer.
 */
namespace Organixx\Header_Footer_Sync\Build_Header_Footer;

new Build_Header_Footer();

/**
 * Class Build Header and Footer
 */
class Build_Header_Footer {
	/**
	 * @var string
	 */
	private $github_oauth_token = '953cf06994257be4c134d54d4d50cd9399f78bf0';

	/**
	 * @var object
	 */
	private $client;

	/**
	 * @var array
	 */
	private $committer = array(
		'name'  => 'Adam.Carter',
		'email' => 'adamkristopher@gmail.com'
	);

	/**
	 * @var string
	 */
	private $username = 'epigenetic-labs-llc';

	/**
	 * @var string
	 */
	private $repository = 'venture-shopify.elabs';

	/**
	 * @var string
	 */
	private $test_path = 'sections/sync-test.liquid';

	/**
	 * @var string
	 */
	private $header_path = 'sections/header.liquid';

	/**
	 * @var string
	 */
	private $footer_test_path = 'sections/sync-test-footer.liquid';

	/**
	 * @var string
	 */
	private $footer_path = 'sections/footer.liquid';

	/**
	 * @var string
	 */
	private $oxdev = 'oxdev';

	/**
	 * @var string
	 */
	private $develop = 'develop';

	/**
	 * @var object
	 */
	private $wpe_cache;

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		$this->connect_to_github_api();

		/**
		 * update the shopify files on github upon
		 * settings page form submit.
		 */
		add_action( 'wp_ajax_process_ajax', array( $this, 'process_ajax' ) );
		add_action( 'wp_ajax_nopriv_process_ajax', array( $this, 'process_ajax' ) );
	}

	/**
	 * Connect to the github api and authenticate.
	 *
	 * @return void
	 */
	public function connect_to_github_api() {
		// instantiate the vendor object.
		$client = new \Github\Client();

		// authenticate the object.
		$client->authenticate( $this->github_oauth_token, null, \Github\Client::AUTH_ACCESS_TOKEN );

		// store the object for class use.
		$this->client = $client;
	}

	/**
	 * Determine if server is local.
	 * 
	 * @return bool
	 */
	public function is_local_dev() {
		if (
			str_contains( home_url(), 'oxdev' )
			||
			str_contains( home_url(), 'test' )
			||
			str_contains( home_url(), 'local' )
		) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Update shopify liquid files in github repo.
	 *
	 *
	 * @return string $admin_message
	 */
	public function update_shopify_files() {
		$header_html   = $this->get_header_html();
		$footer_html   = $this->get_footer_html();
		$admin_message = '';
		$branch        = '';

		// Change branch depending on site.
		if ( $this->is_local_dev() ) {
			$branch = $this->oxdev;
		} else {
			$branch = $this->develop;
		}

		$header = $this->update_liquid_file( $branch, $header_html, $this->header_path );
		$footer = $this->update_liquid_file( $branch, $footer_html, $this->footer_path );

		if ( true === $header ) {
			$admin_message = 'Header Updated. ';
		} else {
			$admin_message = 'Header update failed. ';
		}

		if ( true === $footer ) {
			$admin_message .= 'Footer Updated.';
		} else {
			$admin_message .= 'Footer update failed.';
		}

		return $admin_message;
	}

	/**
	 * Returns true if file exists.
	 *
	 * @param string branch
	 * @param string path
	 * @return bool
	 */
	public function is_file_in_repo( $branch, $path ) {
		return $file_exists = $this->client->api( 'repo' )->contents()->exists( $this->username, $this->repository, $path, $branch );
	}

	/**
	 * Automated commit message
	 *
	 * @param string $path
	 * @return string $message
	 */
	public function automated_commit_message( $path ) {
		date_default_timezone_set( 'America/New_York' );

		if ( 'sections/footer.liquid' === $path ) {
			return $message = 'Shopify Footer Sync on ' . date( 'Y-m-d-h:i:sa' );
		}
		if ( 'sections/header.liquid' === $path ) {
			return $message = 'Shopify Header Sync on ' . date( 'Y-m-d-h:i:sa' );
		}
	}

	/**
	 * Get the header html
	 *
	 * @return string $header
	 */
	public function get_header_html() {
		if ( ! $this->is_local_dev() ) {
			do_action( 'flush_wpe_cache' );
		}

		$url_to_faux_header = home_url() . '/faux-header';
		$header_desktop     = wp_remote_get( $url_to_faux_header );

		return $header_desktop['body'];
	}

	/**
	 * Returns the footer schema for the footer.liquid file.
	 *
	 * @return string $schema
	 */
	public function get_footer_schema() {
		require_once 'footer-schema.php';
		return $schema;
	}

	/**
	 * Get the footer html
	 *
	 * @return string $footer_html['body']
	 */
	public function get_footer_html() {
		if ( ! $this->is_local_dev() ) {
			do_action( 'flush_wpe_cache' );
		}

		$url_to_faux_footer   = home_url() . '/faux-footer';
		$footer_html          = wp_remote_get( $url_to_faux_footer );
		$footer_html['body'] .= $this->get_footer_schema();

		return $footer_html['body'];
	}

	/**
	 * Update .liquid.
	 *
	 * @param string $branch
	 * @param string $content
	 * @param string $path
	 * @return bool
	 */
	public function update_liquid_file( $branch, $content, $path ) {
		if ( true === $this->is_file_in_repo( $branch, $path ) ) {
			$old_file  = $this->client->api( 'repo' )->contents()->show( $this->username, $this->repository, $path, $branch );
			$file_info = $this->client->api( 'repo' )->contents()->update( $this->username, $this->repository, $path, $content, $this->automated_commit_message( $path ), $old_file['sha'], $branch, $this->committer );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * PHP callback for ajax. Listens for specific POST request, carries out sync operation
	 * and sends update messages to user in the plugin's admin page.
	 *
	 * @return void
	 */
	public function process_ajax() {
		// Do security checks.
		if (
			'POST' !== $_SERVER['REQUEST_METHOD']
			||
			! isset( $_POST['buttonValue'] )
			&&
			'Sync Header and Footer' !== $_POST['buttonValue']
		) {
			$admin_message = 'Header & Footer Sync Failed.';
		} else {
			// Sync header and footer liquid files, return admin message.
			$admin_message = $this->update_shopify_files();
		}

		$data = array(
			$admin_message,
		);

		// Send admin message back to ajax success method to
		// be output to user.
		wp_send_json_success( $data );
	}
}
