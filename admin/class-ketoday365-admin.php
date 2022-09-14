<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://myplan.ketoday365.com/
 * @since      1.0.0
 *
 * @package    Ketoday365
 * @subpackage Ketoday365/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ketoday365
 * @subpackage Ketoday365/admin
 * @author     Ketoday365 <matiasl@healthyfitplan.com>
 */
class Ketoday365_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		//add_action('admin_menu', 'ab_stripe_settings_setup');
		//add_action('admin_init', 'ab_stripe_register_settings');
		// add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);   
		// add_action('admin_init', array( $this, 'registerAndBuildFields' )); 
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ketoday365_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ketoday365_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ketoday365-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ketoday365_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ketoday365_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ketoday365-admin.js', array( 'jquery' ), $this->version, false );

	}


	function ketoday365_custom_post_type(){

		// ----------------  Customer  -----------------  //
		$labels = array(
			'name' => _x( 'KetoDay365 - IPN Notifications', 'post type general name' ),
			'singular_name' => _x( 'IPN Notification', 'post type singular name' )
		);
		$args = array( 'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			// 'show_in_graphql' => true,
			// 'graphql_single_name' => 'custommerql',
      		// 'graphql_plural_name' => 'customersql'
		);
		register_post_type( 'notifications', $args ); 


		// ----------------  Customer  -----------------  //
		$labels = array(
			'name' => _x( 'KetoDay365 - Customers', 'post type general name' ),
			'singular_name' => _x( 'Customer KetoDay365', 'post type singular name' )
		);
		$args = array( 'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'show_in_graphql' => true,
			'graphql_single_name' => 'custommerql',
      		'graphql_plural_name' => 'customersql'
		);
		register_post_type( 'customers', $args ); 



		// ----------------  Recipe  -----------------  //
		$labels = array(
			'name' => _x( 'KetoDay365 - Recipes', 'post type general name' ),
			'singular_name' => _x( 'Recipe', 'post type singular name' )
		);
	 
		$args = array( 'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'show_in_graphql' => true,
			'graphql_single_name' => 'recipeql',
      		'graphql_plural_name' => 'recipesql'
		);
	 
		register_post_type( 'recipe', $args ); 
	
	

		// ----------------  Ingredients  -----------------  //
		$labels = array(
			'name' => _x( 'KetoDay365 - Ingredients', 'post type general name' ),
			'singular_name' => _x( 'KetoDay365 Ingredient', 'post type singular name' )
		);

		$args = array( 'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'show_in_graphql' => true,
			'graphql_single_name' => 'ingredientql',
      		'graphql_plural_name' => 'ingredientsql'
		);
	 
		register_post_type( 'ingredients', $args ); 


        // ----------------  Taxonomy  -----------------  //
		$labels = array(
			'name'             => _x( 'GL Category', 'taxonomy general name' ),
			'singular_name'    => _x( 'GL Categorys', 'taxonomy singular name' ),
			'search_items'     =>  __( 'Find by Category' ),
			'all_items'        => __( 'All Categorys' ),
			'parent_item'      => __( 'Category padre' ),
			'parent_item_colon'=> __( 'Category padre:' ),
			'edit_item'        => __( 'Edit Category' ),
			'update_item'      => __( 'Update Category' ),
			'add_new_item'     => __( 'Add new Category' ),
			'new_item_name'    => __( 'Name new Category' ),
		  );


		  
		 register_taxonomy( 'gl_category', array( 'ingredients' ), array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gl_category' ),
		  ));
		  // ---
		  
		$labels = array(
			'name'             => _x( 'Proteins Category', 'taxonomy general name' ),
			'singular_name'    => _x( 'Proteins Categorys', 'taxonomy singular name' ),
			'search_items'     =>  __( 'Find by Proteins' ),
			'all_items'        => __( 'All Proteins' ),
			'parent_item'      => __( 'Proteins padre' ),
			'parent_item_colon'=> __( 'Proteins padre:' ),
			'edit_item'        => __( 'Edit Proteins' ),
			'update_item'      => __( 'Update Category' ),
			'add_new_item'     => __( 'Add new Proteins' ),
			'new_item_name'    => __( 'Name new Proteins' ),
		  );


		  
		 register_taxonomy( 'gl_proteins', array( 'ingredients' ), array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gl_proteins' ),
		  ));

		  // ---

	
		$labels = array(
			'name'             => _x( 'Proteins', 'taxonomy general name' ),
			'singular_name'    => _x( 'Protein', 'taxonomy singular name' ),
			'search_items'     =>  __( 'Find by Protein' ),
			'all_items'        => __( 'All Proteins' ),
			'parent_item'      => __( 'Protein padre' ),
			'parent_item_colon'=> __( 'Protein padre:' ),
			'edit_item'        => __( 'Edit Protein' ),
			'update_item'      => __( 'Update Protein' ),
			'add_new_item'     => __( 'Add new Protein' ),
			'new_item_name'    => __( 'Name new Protein' ),
		  );


		  
		 register_taxonomy( 'protein', array( 'recipe' ), array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'protein' ),
		  ));
		  
			// Other categories
			$labels = array(
				'name'             => _x( 'Other categories', 'taxonomy general name' ),
				'singular_name'    => _x( 'Other categorie', 'taxonomy singular name' ),
				'search_items'     =>  __( 'Find by Other categorie' ),
				'all_items'        => __( 'All Other categories' ),
				'parent_item'      => __( 'Other categories padre' ),
				'parent_item_colon'=> __( 'Other categories padre:' ),
				'edit_item'        => __( 'Edit Other categories' ),
				'update_item'      => __( 'Update Other categories' ),
				'add_new_item'     => __( 'Add new Other categories' ),
				'new_item_name'    => __( 'Name new Other categories' ),
			);
			
			// register_taxonomy( 'other_categories', array( 'recipe' ), array(
			// 	'hierarchical'       => true,
			// 	'labels'             => $labels,
			// 	'show_ui'            => true,
			// 	'query_var'          => true,
			// 	'rewrite'            => array( 'slug' => 'other_categories' ),
			// ));
			

			// Meals - 
			$labels = array(
				'name'             => _x( 'Meals', 'taxonomy general name' ),
				'singular_name'    => _x( 'Meal', 'taxonomy singular name' ),
				'search_items'     =>  __( 'Find by Meal' ),
				'all_items'        => __( 'All Meals' ),
				'parent_item'      => __( 'Meals padre' ),
				'parent_item_colon'=> __( 'Meals padre:' ),
				'edit_item'        => __( 'Edit Meals' ),
				'update_item'      => __( 'Update Meals' ),
				'add_new_item'     => __( 'Add new Meals' ),
				'new_item_name'    => __( 'Name new Meals' ),
			);
			
			// register_taxonomy( 'meals', array( 'recipe' ), array(
			// 	'hierarchical'       => true,
			// 	'labels'             => $labels,
			// 	'show_ui'            => true,
			// 	'query_var'          => true,
			// 	'rewrite'            => array( 'slug' => 'meals' ),
			// ));


			// Budget - 
			$labels = array(
				'name'             => _x( 'Budget', 'taxonomy general name' ),
				'singular_name'    => _x( 'Budget', 'taxonomy singular name' ),
				'search_items'     =>  __( 'Find by MBudgeteal' ),
				'all_items'        => __( 'All Budget' ),
				'parent_item'      => __( 'Budget padre' ),
				'parent_item_colon'=> __( 'Budget padre:' ),
				'edit_item'        => __( 'Edit Budget' ),
				'update_item'      => __( 'Update Budget' ),
				'add_new_item'     => __( 'Add new Budget' ),
				'new_item_name'    => __( 'Name new Budget' ),
			);
			
			// register_taxonomy( 'budget', array( 'recipe' ), array(
			// 	'hierarchical'       => true,
			// 	'labels'             => $labels,
			// 	'show_ui'            => true,
			// 	'query_var'          => true,
			// 	'rewrite'            => array( 'slug' => 'budget' ),
			// ));


			// Type of dish - 
			$labels = array(
				'name'             => _x( 'Type of dish', 'taxonomy general name' ),
				'singular_name'    => _x( 'Type of dish', 'taxonomy singular name' ),
				'search_items'     =>  __( 'Find by Type of dish' ),
				'all_items'        => __( 'All Type of dish' ),
				'parent_item'      => __( 'Type of dish padre' ),
				'parent_item_colon'=> __( 'Type of dish padre:' ),
				'edit_item'        => __( 'Edit Type of dish' ),
				'update_item'      => __( 'Update Type of dish' ),
				'add_new_item'     => __( 'Add new Type of dish' ),
				'new_item_name'    => __( 'Name new Type of dish' ),
			);

			// register_taxonomy( 'type_of_dish', array( 'recipe' ), array(
			// 	'hierarchical'       => true,
			// 	'labels'             => $labels,
			// 	'show_ui'            => true,
			// 	'query_var'          => true,
			// 	'rewrite'            => array( 'slug' => 'type_of_dish' ),
			// ));


			// Vegetables 
			$labels = array(
				'name'             => _x( 'Vegetables', 'taxonomy general name' ),
				'singular_name'    => _x( 'Vegetables', 'taxonomy singular name' ),
				'search_items'     =>  __( 'Find by Vegetables' ),
				'all_items'        => __( 'All Vegetables' ),
				'parent_item'      => __( 'Vegetables padre' ),
				'parent_item_colon'=> __( 'Vegetables padre:' ),
				'edit_item'        => __( 'Edit Vegetables' ),
				'update_item'      => __( 'Update Vegetables' ),
				'add_new_item'     => __( 'Add new Vegetables' ),
				'new_item_name'    => __( 'Name new Vegetables' ),
			);

			// register_taxonomy( 'vegetables', array( 'recipe' ), array(
			// 	'hierarchical'       => true,
			// 	'labels'             => $labels,
			// 	'show_ui'            => true,
			// 	'query_var'          => true,
			// 	'rewrite'            => array( 'slug' => 'vegetables' ),
			// ));
	}


	function ketoday365_rename_woocoomerce(){
		global $menu;
		$woo = $this->rename_woocommerce_name( 'Products', $menu );
		// Validate
		if( !$woo ) return;
		$menu[$woo][0] = 'KetoDay365 - Products';
	}

	 function rename_woocommerce_name( $needle, $haystack ){
		foreach( $haystack as $key => $value ){
			$current_key = $key;
			if($needle === $value
				OR (
					is_array( $value )
					&& $this->rename_woocommerce_name( $needle, $value ) !== false
				)
			)
			{ return $current_key; }
		}
		return false;
	}


	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(  $this->plugin_name, 'Settings Page', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-chart-area', 26 );
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( $this->plugin_name, 'Settings Page Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
	}
	public function displayPluginAdminDashboard() {
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
  }
	public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
				add_action('admin_notices', array($this,'settingsPageSettingsMessages'));
				do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
	}
	public function settingsPageSettingsMessages($error_message){
		switch ($error_message) {
				case '1':
						$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 $err_code = esc_attr( 'settings_page_example_setting' );                 $setting_field = 'settings_page_example_setting';                 
						break;
		}
		$type = 'error';
		add_settings_error(
					$setting_field,
					$err_code,
					$message,
					$type
			);
	}
	public function registerAndBuildFields() {
			/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */     
		add_settings_section(
			// ID used to identify this section and with which to register options
			'settings_page_general_section', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
				array( $this, 'settings_page_display_general_account' ),    
			// Page on which to add this section of options
			'settings_page_general_settings'                   
		);
		unset($args);
		$args = array (
							'type'      => 'input',
							'subtype'   => 'text',
							'id'    => 'settings_page_example_setting',
							'name'      => 'settings_page_example_setting',
							'required' => 'true',
							'get_options_list' => '',
							'value_type'=>'normal',
							'wp_data' => 'option'
					);
		add_settings_field(
			'settings_page_example_setting',
			'Example Setting',
			array( $this, 'settings_page_render_settings_field' ),
			'settings_page_general_settings',
			'settings_page_general_section',
			$args
		);


		register_setting(
						'settings_page_general_settings',
						'settings_page_example_setting'
						);

	}
	public function settings_page_display_general_account() {
		echo '<p>These settings apply to all Plugin Name functionality.</p>';
	} 
	public function settings_page_render_settings_field($args) {
			/* EXAMPLE INPUT
								'type'      => 'input',
								'subtype'   => '',
								'id'    => $this->plugin_name.'_example_setting',
								'name'      => $this->plugin_name.'_example_setting',
								'required' => 'required="required"',
								'get_option_list' => "",
									'value_type' = serialized OR normal,
			'wp_data'=>(option or post_meta),
			'post_id' =>
			*/     
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {

			case 'input':
					$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
					if($args['subtype'] != 'checkbox'){
							$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
							$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
							$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
							$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
							$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
							if(isset($args['disabled'])){
									// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
									echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
							} else {
									echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
							}
							/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/

					} else {
							$checked = ($value) ? 'checked' : '';
							echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
					}
					break;
			default:
					# code...
					break;
		}
	}








}
