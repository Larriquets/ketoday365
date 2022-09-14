<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://myplan.ketoday365.com/
 * @since      1.0.0
 *
 * @package    Ketoday365
 * @subpackage Ketoday365/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ketoday365
 * @subpackage Ketoday365/includes
 * @author     Ketoday365 <matiasl@healthyfitplan.com>
 */
class Ketoday365 {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ketoday365_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'KETODAY365_VERSION' ) ) {
			$this->version = KETODAY365_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ketoday365';
		$this->load_dependencies();
		$this->set_locale();
		$this->create_endpoint();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ketoday365_Loader. Orchestrates the hooks of the plugin.
	 * - Ketoday365_i18n. Defines internationalization functionality.
	 * - Ketoday365_Admin. Defines all hooks for the admin area.
	 * - Ketoday365_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-pdf_creator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-api-customer-endpoint.php';
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-api-digital-plan-endpoint.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-api-grocery-list-endpoint.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-api-digitalplan-base-endpoint.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-api-change-recipe-endpoint.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-api-list-recipes-endpoint.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-woo_extension.php';
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ketoday365-fractions.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ketoday365-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ketoday365-public.php';

		

		$this->loader = new Ketoday365_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ketoday365_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ketoday365_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


		/**
	 * Define the Custom Endpoint for API Boilerplate.
	 *
	 * Create the Route, Custom Endpoint & data for API Boilerplate.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function create_endpoint() {

		$plugin_endpoint = new Api_Ketoday365_New_Customer_Endpoint( $this->get_plugin_name(), $this->get_version());

		// Add Admin Notice if Below WordPress version 4.7 & WordPress API plugin is not installed
		$this->loader->add_action( 'admin_notices', $plugin_endpoint, 'api_boilerplate_nag_message' );

		// Construct Custom Endpoint
		$this->loader->add_action( 'rest_api_init', $plugin_endpoint, 'ketoday365_custom_api_route_constructor' );

		// $plugin_endpoint_plan = new Api_Ketoday365_Digital_Plan_Endpoint( $this->get_plugin_name(), $this->get_version());
		// $this->loader->add_action( 'rest_api_init', $plugin_endpoint_plan, 'ketoday365_digital_plan_api_constructor' );

		$plugin_endpoint_grocery = new Api_Ketoday365_Grocery_List_Endpoint( $this->get_plugin_name(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_endpoint_grocery, 'ketoday365_grocery_list_api_constructor' );

		
		$plugin_endpoint_dg_plan_base = new Api_Ketoday365_DigitalPlan_Base_Endpoint( $this->get_plugin_name(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_endpoint_dg_plan_base, 'ketoday365_digital_plan_base_api_constructor' );
	
			
		$plugin_endpoint_change_recipe = new Api_Ketoday365_Change_Recipe_Endpoint( $this->get_plugin_name(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_endpoint_change_recipe, 'ketoday365_change_recipe_api_constructor' );
	
		
					
		$plugin_endpoint_list_recipes = new Api_Ketoday365_List_Recipes_Endpoint( $this->get_plugin_name(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_endpoint_list_recipes, 'ketoday365_list_recipes_api_constructor' );
	
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ketoday365_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'ketoday365_custom_post_type',  10, 2 );
		$this->loader->add_action( 'admin_menu', $plugin_admin ,'ketoday365_rename_woocoomerce', 999 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ketoday365_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter('query_vars',$plugin_public, 'add_query_vars');
		$this->loader->add_filter('rewrite_rules_array',$plugin_public, 'add_rewrite_rules');
		$this->loader->add_filter('query_vars',$plugin_public, 'add_query_var_customer');
		$this->loader->add_filter('rewrite_rules_array',$plugin_public, 'add_rewrite_rules_customer');
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$plugin_woo_extension = new Ketodat365_Woo_Extension( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_woo_extension , 'ketoday365_custom_override_checkout_fields' );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_woo_extension , 'ketoday365_editable_order_meta_general' );	
		$this->loader->add_filter( 'woocommerce_checkout_get_value',$plugin_woo_extension , 'ketoday365_checkout_fields', 10, 2 );
		$this->loader->add_action( 'woocommerce_after_checkout_billing_form',$plugin_woo_extension,  'ketoday365_select_field' );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta',$plugin_woo_extension , 'ketoday365_save_customer_id' );

	    // $plugin_fractions = new Fraction(1,2);

		
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
	 * @return    Ketoday365_Loader    Orchestrates the hooks of the plugin.
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
