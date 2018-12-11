<?php

/**
 * CiviCRM Admin Utilities Multidomain Class.
 *
 * A class that encapsulates Multidomain admin functionality.
 *
 * @since 0.5.4
 */
class CiviCRM_Admin_Utilities_Multidomain {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.5.4
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * Multidomain Settings page reference.
	 *
	 * @since 0.5.4
	 * @access public
	 * @var array $multidomain_page The reference to the multidomain settings page.
	 */
	public $multidomain_page;



	/**
	 * Constructor.
	 *
	 * @since 0.5.4
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Store reference to plugin.
		$this->plugin = $plugin;

		// Initialise when plugin is loaded.
		add_action( 'civicrm_admin_utilities_loaded', array( $this, 'initialise' ) );

	}



	/**
	 * Initialise this object.
	 *
	 * @since 0.5.4
	 */
	public function initialise() {

		// Register hooks.
		$this->register_hooks();

	}



	/**
	 * Register hooks.
	 *
	 * @since 0.5.4
	 */
	public function register_hooks() {

		// Add Domain subpage to Single Site Settings menu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Add Domains AJAX handler.
		add_action( 'wp_ajax_cau_domains_get', array( $this, 'domains_get' ) );

		// Add Domain Groups AJAX handler.
		add_action( 'wp_ajax_cau_domain_groups_get', array( $this, 'domain_groups_get' ) );

		// Add Domain Orgs AJAX handler.
		add_action( 'wp_ajax_cau_domain_orgs_get', array( $this, 'domain_orgs_get' ) );

	}



	//##########################################################################




	/**
	 * Add admin menu item(s) for this plugin.
	 *
	 * @since 0.5.4
	 */
	public function admin_menu() {

		/**
		 * Set capability but allow overrides.
		 *
		 * @since 0.5.4
		 *
		 * @param str The default capability for access to domain page.
		 * @return str The modified capability for access to domain page.
		 */
		$capability = apply_filters( 'civicrm_admin_utilities_page_domain_cap', 'manage_options' );

		// Check user permissions.
		if ( ! current_user_can( $capability ) ) return;

		// Add Domain page.
		$this->multidomain_page = add_submenu_page(
			'civicrm_admin_utilities_parent', // Parent slug.
			__( 'CiviCRM Admin Utilities: Domain', 'civicrm-admin-utilities' ), // Page title.
			__( 'Domain', 'civicrm-admin-utilities' ), // Menu title.
			$capability, // Required caps.
			'civicrm_admin_utilities_multidomain', // Slug name.
			array( $this, 'page_multidomain' ) // Callback.
		);

		// Ensure correct menu item is highlighted.
		add_action( 'admin_head-' . $this->multidomain_page, array( $this->plugin->single, 'admin_menu_highlight' ), 50 );

		// Add help text.
		add_action( 'admin_head-' . $this->multidomain_page, array( $this, 'admin_head' ), 50 );

		// Add scripts and styles.
		add_action( 'admin_print_scripts-' . $this->multidomain_page, array( $this, 'page_multidomain_js' ) );
		add_action( 'admin_print_styles-' . $this->multidomain_page, array( $this, 'page_multidomain_css' ) );

		// Try and update options.
		$saved = $this->settings_update_router();

		// Filter the list of single site subpages and add multidomain page.
		add_filter( 'civicrm_admin_utilities_subpages', array( $this, 'admin_subpages_filter' ) );

		// Filter the list of single site page URLs and add multidomain page URL.
		add_filter( 'civicrm_admin_utilities_page_urls', array( $this, 'page_urls_filter' ) );

		// Filter the "show tabs" flag for setting templates.
		add_filter( 'civicrm_admin_utilities_show_tabs', array( $this, 'page_show_tabs' ) );

		// Add tab to setting templates.
		add_filter( 'civicrm_admin_utilities_settings_nav_tabs', array( $this, 'page_add_tab' ), 10, 2 );

	}



	/**
	 * Append the multidomain settings page to single site subpages.
	 *
	 * This ensures that the correct parent menu item is highlighted for our
	 * Multidomain subpage in Single Site installs.
	 *
	 * @since 0.5.4
	 *
	 * @param array $subpages The existing list of subpages.
	 * @return array $subpages The modified list of subpages.
	 */
	public function admin_subpages_filter( $subpages ) {

		// Add multidomain settings page.
		$subpages[] = 'civicrm_admin_utilities_multidomain';

		// --<
		return $subpages;

	}



	/**
	 * Initialise plugin help.
	 *
	 * @since 0.5.4
	 */
	public function admin_head() {

		// Get screen object.
		$screen = get_current_screen();

		// Pass to method in this class.
		$this->admin_help( $screen );

	}



	/**
	 * Adds help copy to admin page.
	 *
	 * @since 0.5.4
	 *
	 * @param object $screen The existing WordPress screen object.
	 * @return object $screen The amended WordPress screen object.
	 */
	public function admin_help( $screen ) {

		// Init page IDs.
		$pages = array(
			$this->multidomain_page,
		);

		// Kick out if not our screen.
		if ( ! in_array( $screen->id, $pages ) ) return $screen;

		// Add a tab - we can add more later.
		$screen->add_help_tab( array(
			'id'      => 'civicrm_admin_utilities_multidomain',
			'title'   => __( 'CiviCRM Admin Utilities Domain', 'civicrm-admin-utilities' ),
			'content' => $this->admin_help_get(),
		));

		// --<
		return $screen;

	}



	/**
	 * Get help text.
	 *
	 * @since 0.5.4
	 *
	 * @return string $help The help text formatted as HTML.
	 */
	public function admin_help_get() {

		// Stub help text, to be developed further.
		$help = '<p>' . __( 'Domain Settings: For further information about using CiviCRM Admin Utilities, please refer to the readme.txt file that comes with this plugin.', 'civicrm-admin-utilities' ) . '</p>';

		// --<
		return $help;

	}



	//##########################################################################



	/**
	 * Show our multidomain settings page.
	 *
	 * @since 0.5.4
	 */
	public function page_multidomain() {

		/**
		 * Set capability but allow overrides.
		 *
		 * @since 0.5.4
		 *
		 * @param str The default capability for access to domain page.
		 * @return str The modified capability for access to domain page.
		 */
		$capability = apply_filters( 'civicrm_admin_utilities_page_domain_cap', 'manage_options' );

		// Check user permissions.
		if ( ! current_user_can( $capability ) ) return;

		// Bail if CiviCRM is not active.
		if ( ! $this->plugin->is_civicrm_initialised() ) return;

		// Get admin page URLs.
		$urls = $this->plugin->single->page_get_urls();

		// Get CiviCRM domain ID from config.
		$domain_id = CRM_Core_Config::domainID();

		// Get domain name.
		$domain_name = $this->domain_name_get( $domain_id );

		// Get CiviCRM domain group ID.
		$domain_group_id = defined( 'CIVICRM_DOMAIN_GROUP_ID' ) ? CIVICRM_DOMAIN_GROUP_ID : 0;

		// Get domain group name.
		$domain_group_name = $this->domain_group_name_get( $domain_group_id );

		// Get CiviCRM domain org ID.
		$domain_org_id = defined( 'CIVICRM_DOMAIN_ORG_ID' ) ? CIVICRM_DOMAIN_ORG_ID : 0;

		// Get domain org name.
		$domain_org_name = $this->domain_org_name_get( $domain_org_id );

		// Include template file.
		include( CIVICRM_ADMIN_UTILITIES_PATH . 'assets/templates/site-multidomain.php' );

	}



	/**
	 * Enqueue stylesheets for the Site Domain page.
	 *
	 * @since 0.6.2
	 */
	public function page_multidomain_css() {

		// Register Select2 styles.
		wp_register_style(
			'cau_site_domain_select2_css',
			set_url_scheme( 'http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' )
		);

		// Enqueue styles.
		wp_enqueue_style( 'cau_site_domain_select2_css' );

		// Add page-specific stylesheet.
		wp_enqueue_style(
			'cau_site_domain_css',
			plugins_url( 'assets/css/civicrm-admin-utilities-site-multidomain.css', CIVICRM_ADMIN_UTILITIES_FILE ),
			false,
			CIVICRM_ADMIN_UTILITIES_VERSION, // version
			'all' // media
		);

	}



	/**
	 * Enqueue Javascripts on the Site Domain page.
	 *
	 * @since 0.6.2
	 */
	public function page_multidomain_js() {

		// Register Select2.
		wp_register_script(
			'cau_site_domain_select2_js',
			set_url_scheme( 'http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js' ),
			array( 'jquery' )
		);

		// Enqueue Select2 script.
		wp_enqueue_script( 'cau_site_domain_select2_js' );

		// Enqueue our Javascript plus dependencies.
		wp_enqueue_script(
			'cau_site_domain_js',
			plugins_url( 'assets/js/civicrm-admin-utilities-site-multidomain.js', CIVICRM_ADMIN_UTILITIES_FILE ),
			array( 'jquery', 'cau_site_domain_select2_js' ),
			CIVICRM_ADMIN_UTILITIES_VERSION // version
		);

		// Localisation array.
		$vars = array(
			'localisation' => array(),
			'settings' => array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'blog_id' => get_current_blog_id(),
			),
		);

		// Localise with WordPress function.
		wp_localize_script(
			'cau_site_domain_js',
			'CAU_Site_Domain',
			$vars
		);

	}



	/**
	 * Append the multidomain settings page URL to single site subpage URLs.
	 *
	 * @since 0.5.4
	 *
	 * @param array $urls The existing list of URLs.
	 * @return array $urls The modified list of URLs.
	 */
	public function page_urls_filter( $urls ) {

		// Add multidomain settings page.
		$urls['multidomain'] = menu_page_url( 'civicrm_admin_utilities_multidomain', false );

		// --<
		return $urls;

	}



	/**
	 * Show subpage tabs on settings pages.
	 *
	 * @since 0.5.4
	 *
	 * @param bool $show_tabs True if tabs are shown, false otherwise.
	 * @return bool $show_tabs True if tabs are to be shown, false otherwise.
	 */
	public function page_show_tabs( $show_tabs ) {

		// Always show tabs.
		$show_tabs = true;

		// --<
		return $show_tabs;

	}



	/**
	 * Add subpage tab to tabs on settings pages.
	 *
	 * @since 0.5.4
	 *
	 * @param array $urls The array of subpage URLs.
	 * @param str The key of the active tab in the subpage URLs array.
	 */
	public function page_add_tab( $urls, $active_tab ) {

		// Define title.
		$title = __( 'Domain', 'civicrm-admin-utilities' );

		// Default to inactive.
		$active = '';

		// Make active if it's our subpage.
		if ( $active_tab === 'multidomain' ) {
			$active = ' nav-tab-active';
		}

		// Render tab.
		echo '<a href="' . $urls['multidomain'] . '" class="nav-tab' . $active . '">' . $title . '</a>' . "\n";

	}



	/**
	 * Get the URL for the form action.
	 *
	 * @since 0.5.4
	 *
	 * @return string $target_url The URL for the admin form action.
	 */
	public function page_submit_url_get() {

		// Sanitise admin page url.
		$target_url = $_SERVER['REQUEST_URI'];
		$url_array = explode( '&', $target_url );

		// Strip flag, if present, and rebuild.
		if ( ! empty( $url_array ) ) {
			$url_raw = str_replace( '&amp;updated=true', '', $url_array[0] );
			$target_url = htmlentities( $url_raw . '&updated=true' );
		}

		// --<
		return $target_url;

	}



	//##########################################################################



	/**
	 * Get the name of the domain for a given ID.
	 *
	 * @since 0.5.4
	 *
	 * @param int $domain_id The ID of the domain.
	 * @return str $name The name of the domain on success, error message otherwise.
	 */
	public function domain_name_get( $domain_id ) {

		// Get domain info.
		$domain_info = civicrm_api( 'domain', 'getsingle', array(
			'version' => 3,
			'id' => $domain_id,
		));

		// Assign error message or name depending on query success.
		if ( ! empty( $domain_info['is_error'] ) AND $domain_info['is_error'] == 1 ) {
			$name = $domain_info['error_message'];
		} else {
			$name = $domain_info['name'];
		}

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'domain_id' => $domain_id,
			'domain_info' => $domain_info,
			//'backtrace' => $trace,
		), true ) );
		*/

		// --<
		return $name;

	}



	/**
	 * Get the name of the domain group for a given ID.
	 *
	 * @since 0.5.4
	 *
	 * @param int $domain_group_id The ID of the domain group.
	 * @return str $name The name of the domain group on success, error message otherwise.
	 */
	public function domain_group_name_get( $domain_group_id ) {

		// Get domain group info.
		$domain_group_info = civicrm_api( 'group', 'getsingle', array(
			'version' => 3,
			'id' => $domain_group_id,
		));

		// Assign error message or name depending on query success.
		if ( ! empty( $domain_group_info['is_error'] ) AND $domain_group_info['is_error'] == 1 ) {
			$name = $domain_group_info['error_message'];
		} else {
			$name = $domain_group_info['title'];
		}

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'domain_group_id' => $domain_group_id,
			'domain_group_info' => $domain_group_info,
			//'backtrace' => $trace,
		), true ) );
		*/

		// --<
		return $name;

	}



	/**
	 * Get the name of the domain org for a given ID.
	 *
	 * @since 0.5.4
	 *
	 * @param int $domain_org_id The ID of the domain org.
	 * @return str $name The name of the domain org on success, error message otherwise.
	 */
	public function domain_org_name_get( $domain_org_id ) {

		// Get domain org info.
		$domain_org_info = civicrm_api( 'contact', 'getsingle', array(
			'version' => 3,
			'id' => $domain_org_id,
		));

		// Assign error message or name depending on query success.
		if ( ! empty( $domain_org_info['is_error'] ) AND $domain_org_info['is_error'] == 1 ) {
			$name = $domain_org_info['error_message'];
		} else {
			$name = $domain_org_info['display_name'];
		}

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'domain_org_id' => $domain_org_id,
			'domain_org_info' => $domain_org_info,
			//'backtrace' => $trace,
		), true ) );
		*/

		// --<
		return $name;

	}



	//##########################################################################



	/**
	 * Get the Domains registered in CiviCRM.
	 *
	 * @since 0.6.2
	 */
	public function domains_get() {

		// Bail if CiviCRM is not active.
		if ( ! $this->plugin->is_civicrm_initialised() ) return;

		// Init return.
		$json = array();

		// Sanitise search input.
		$search = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';

		// Get domains.
		$domains = civicrm_api( 'domain', 'get', array(
			'version' => 3,
			'name' => array( 'LIKE' => '%' . $search . '%' ),
		));

		// Sanity check.
		if ( ! empty( $domains['is_error'] ) AND $domains['is_error'] == 1 ) {
			return;
		}

		// Loop through our domains.
		foreach( $domains['values'] AS $domain ) {

			// Add domain data to output array.
			$json[] = array(
				'id' => $domain['id'],
				'name' => stripslashes( $domain['name'] ),
				'description' => $domain['description'],
			);

		}

		// Send data.
		$this->send_data( $json );

	}



	/**
	 * Get the Domain Groups registered in CiviCRM.
	 *
	 * @since 0.6.2
	 */
	public function domain_groups_get() {

		// Bail if CiviCRM is not active.
		if ( ! $this->plugin->is_civicrm_initialised() ) return;

		// Init return.
		$json = array();

		// Sanitise search input.
		$search = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';

		// Get domain groups.
		$groups = civicrm_api( 'group', 'get', array(
			'version' => 3,
			'visibility' => 'User and User Admin Only',
			'title' => array( 'LIKE' => '%' . $search . '%' ),
		));

		// Sanity check.
		if ( ! empty( $groups['is_error'] ) AND $groups['is_error'] == 1 ) {
			return;
		}

		// Loop through our groups.
		foreach( $groups['values'] AS $group ) {

			// Add group data to output array.
			$json[] = array(
				'id' => $group['id'],
				'name' => stripslashes( $group['title'] ),
				'description' => '',
			);

		}

		// Send data.
		$this->send_data( $json );

	}



	/**
	 * Get the Domain Orgs registered in CiviCRM.
	 *
	 * @since 0.6.2
	 */
	public function domain_orgs_get() {

		// Bail if CiviCRM is not active.
		if ( ! $this->plugin->is_civicrm_initialised() ) return;

		// Init return.
		$json = array();

		// Sanitise search input.
		$search = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';

		// Get domain orgs.
		$orgs = civicrm_api( 'contact', 'get', array(
			'version' => 3,
			'contact_type' => "Organization",
			'organization_name' => array( 'LIKE' => '%' . $search . '%' ),
		));

		// Sanity check.
		if ( ! empty( $orgs['is_error'] ) AND $orgs['is_error'] == 1 ) {
			return;
		}

		// Loop through our orgs.
		foreach( $orgs['values'] AS $org ) {

			// Add org data to output array.
			$json[] = array(
				'id' => $org['contact_id'],
				'name' => stripslashes( $org['display_name'] ),
				'description' => '',
			);

		}

		/*
		//$e = new Exception;
		//$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'orgs' => $orgs,
			'json' => $json,
			//'backtrace' => $trace,
		), true ) );
		*/

		// Send data.
		$this->send_data( $json );

	}



	/**
	 * Send JSON data to the browser.
	 *
	 * @since 0.6.2
	 *
	 * @param array $data The data to send.
	 */
	private function send_data( $data ) {

		// Bail if this not an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) OR ! DOING_AJAX ) {
			return;
		}

		// Set reasonable headers.
		header('Content-type: text/plain');
		header("Cache-Control: no-cache");
		header("Expires: -1");

		// Echo and die.
		echo json_encode( $data );
		exit();

	}

	//##########################################################################



	/**
	 * Route settings updates to relevant methods.
	 *
	 * @since 0.5.4
	 *
	 * @return bool $result True on success, false otherwise.
	 */
	public function settings_update_router() {

		// Init return.
		$result = false;

	 	// was the "Domain" form submitted?
		if ( isset( $_POST['civicrm_admin_utilities_multidomain_submit'] ) ) {
			return $this->settings_multidomain_update();
		}

		// --<
		return $result;

	}



	/**
	 * Update options supplied by our Multidomain admin page.
	 *
	 * @since 0.5.4
	 *
	 * @return bool True if successful, false otherwise (always true at present).
	 */
	public function settings_multidomain_update() {

		// Check that we trust the source of the data.
		check_admin_referer( 'civicrm_admin_utilities_multidomain_action', 'civicrm_admin_utilities_multidomain_nonce' );

		// TODO: Functional procedure here.

		// --<
		return true;

	}



} // Class ends.



