<?php

/**
 * Class Custom_User_Profile_Photo
 */
class Custom_User_Profile_Photo {
	/**
	 * @var
	 */
	public $plugin_path;

	/**
	 * @var $loader Custom_User_Profile_Photo_Loader
	 */
	public $loader;

	/**
	 * @var
	 */
	public $plugin_name = 'custom-user-profile-photo';

	/**
	 * @var
	 */
	public $version = '0.4';

	/**
	 * Custom_User_Profile_Photo constructor.
	 *
	 * @param $plugin_path
	 */
	public function __construct( $plugin_path ) {
		$this->plugin_path = $plugin_path;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once $this->plugin_path . '/src/class-cupp-user.php';

		/**
		 * Library of API methods available to theme developers
		 */
		require_once $this->plugin_path . '/includes/api-theme.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->plugin_path . '/src/class-cupp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'src/class-cupp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->plugin_path . '/admin/class-cupp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $this->plugin_path . '/public/class-cupp-public.php';

		$this->loader = new Custom_User_Profile_Photo_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Custom_User_Profile_Photo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Custom_User_Profile_Photo_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Custom_User_Profile_Photo_Admin( $this->get_plugin_name(), $this->get_version() );

		// Enqueue scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Update user meta on save
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'save_user_meta' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'save_user_meta' );
		$this->loader->add_action( 'profile_update', $plugin_admin, 'change_image_on_profile_update', 10, 2 );

		// User profile display
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'display_user_profile_settings' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'display_user_profile_settings' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Custom_User_Profile_Photo_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'get_avatar', $plugin_public, 'cupp_avatar', 1, 5 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Custom_User_Profile_Photo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
