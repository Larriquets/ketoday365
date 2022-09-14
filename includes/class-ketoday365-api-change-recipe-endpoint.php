<?php

/**
 * The Custom Endpoint
 *
 * Create the custom endpoint and add the data for API Boilerplate.
 *
 * @link       https://myplan.ketoday365.com/
 * @since      1.0.0
 *
 * @package    Ketoday365
 * @subpackage Ketoday365/includes
 *
 */

/**
 * Define the custom endpoint content.
 *
 * Add the route for the API Boilerplate Custom Endpoint and generate
 * the necessary data for the frontend.
 *
 * @since      1.0.0
 * @package    Ketoday365
 * @subpackage Ketoday365/includes
 * @author     Ketoday365 <matiasl@healthyfitplan.com>
 */
class Api_Ketoday365_Change_Recipe_Endpoint {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The options name prefix for API Boilerplate
	 *
	 * @since  	0.1
	 * @access 	private
	 * @var  		string 		$option_name 	Option name prefix for API Boilerplate
	 */
	private $option_name;
	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param 	 string 	$plugin_name 		  The name of this plugin.
	 * @param    string    	$version    		  The version of this plugin.
	 * @param    string    	$option_name   		  The option prefix for this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name        = $plugin_name;
		$this->version            = $version;


	}

	/**
	 * Admin nag message is WP API not enabled.
	 *
	 * @since    0.1.0
	 */
	public function api_boilerplate_nag_message() {

		global $wp_version;

		// WP v4.7 was the first WP version with the API fully baked in :)
		if ( $wp_version >= 4.7 ) {

			return;

		} elseif ( is_plugin_active( 'WP-API-develop/plugin.php' ) || is_plugin_active( 'rest-api/plugin.php' )  || is_plugin_active( 'WP-API/plugin.php' ) ) {

				return;

		} else { ?>

			<div class="update-nag notice">
				<p>
					<?php __( 'To use <strong>API Boilerplate</strong>, you need to update to the latest version of WordPress (version 4.7 or above). To use an older version of WordPress, you can install the <a href="https://wordpress.org/plugins/rest-api/">WP API Plugin</a> plugin. However, we&apos;d strongly advise youto update WordPress.', 'api-boilerplate' ); ?>
				</p>
			</div>
		<?php
		}

	}

	/**
	 * API Route Constructor.
	 *
	 * @since    0.1.0
	 */
	public function ketoday365_change_recipe_api_constructor() {

		register_rest_route( 'keto-endpoint', 'change-recipe', array(
			'methods' => 'POST',
			'callback' => array( $this, 'ketoday365_phrase_change_recipes' ),
			'permission_callback' => '__return_true'
		   )
		);
	}

	public function ketoday365_phrase_change_recipes(WP_REST_Request $request) {
		$keys = $request->get_json_params();
		$proteins_exclude = array();
		if (isset($keys['recipe_id']))    { 
			$recipe_id = $keys['recipe_id'];
			//array_push($proteins_exclude, $recipe_id);
			
		}
		if (isset($keys['min_calories'])) {	$min_calories = $keys['min_calories'];	}
		if (isset($keys['max_calories'])) { $max_calories= $keys['max_calories'];}
		if (isset($keys['meal'])) {
			$meals = $keys['meal'];
			if($meals == "Dinner" || $meals == 'Lunch'){
				$meals = "Meals" ;
			}		
		}
		if (isset($keys['quantity']))     { $quantity = $keys['quantity'];}
		if (isset($keys['protein']))      { $proteins = $keys['protein'];}
		if (isset($keys['restrictions'])) { $restrictions = $keys['restrictions'];}
		if(!is_array( $proteins) ){
			settype($proteins, 'array');
		}
		if(!is_array( $restrictions) ){
			settype($restrictions, 'array');
		}

		$proteins_exclude = $this->ketoday365_invert_proteins($proteins);	
		if($proteins_exclude != null){
			array_push($proteins_exclude, $recipe_id ); 
		}else{
			$proteins_exclude[0] = $recipe_id ;
		}	
		
		if(in_array("Lactose Intolerant",$restrictions)){	 	
	     	$_lactose_free = $this->ketoday365_getID_lactose_free(); 
			$meta_query[] = array(
			   'relation'		=> 'AND',
				array(
					'key'     => 'lactose_free',
					'value'   => '1',
				),
				array(
					'key'     => 'meals_checkbox',
					'value'   => $meals,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'calories',
					'value' => array($min_calories, $max_calories),
					'compare' => 'BETWEEN',
					'type' => 'NUMERIC'
				)
			);
		}else{
			$meta_query[] =  array(
			    'relation'		=> 'AND',
				array(
					'key'     => 'calories',
					'value' => array($min_calories, $max_calories),
					'compare' => 'BETWEEN',
					'type' => 'NUMERIC'
				),
				array(
					'key'     => 'meals_checkbox',
					'value'   => $meals,
					'compare' => 'LIKE'
				)
		    );
		}

		$posts = get_posts(array(
			'post_status' => 'publish',  
			'post__not_in' => $proteins_exclude ,
			'numberposts'	=>  $quantity,
			'orderby' => 'rand',
			'post_type'		=>  'recipe',
			'meta_query' => $meta_query
		));
		$_recipes = array( );
		$i = 1;
		if( ! empty( $posts ) ){
			foreach ( $posts as $p ){ 
				$i++;
				$_recipes[]=  array(
					'id' => $p->ID,
					'title' => $p->post_title,
					'image_desktop' => get_field('image_desktop',$p->ID),
					'mobile_image' => get_field('mobile_image',$p->ID),
					'calories' =>  get_field('calories',$p->ID),
					'net_carbs' =>  get_field('net_carbs',$p->ID),
					'fat' =>  get_field('fat',$p->ID),
					'protein' =>  get_field('protein',$p->ID),
					
				);
			}       
		} 
		// if(count($data) >= $digital_plan_duration){

		// 	echo 'epa';

		// }
         return $_recipes;


	}


	function ketoday365_invert_proteins($proteins){
		$ids_proteins_excl = array();
		$turkey = '';
		$fish = '';
		$chicken = '';
		$beef = '';
		$pork = '';
		$none = '';
		$_proteins = array() ;
	
		if( ! empty($proteins)){
			if (in_array("Turkey", $proteins)) { }else{$_proteins[] = 'turkey' ; }
			if (in_array("Fish", $proteins)) { }else{ $_proteins[] = 'fish'; }
			if (in_array("Chicken", $proteins)){ }else{$_proteins[] = 'chicken' ;}
			if (in_array("Beef", $proteins)) { }else{$_proteins[] = 'beef' ;}
			if (in_array("Pork", $proteins)) {}else{$_proteins[] = 'pork' ;}
			// if (in_array("None", $proteins)) {}else{$_proteins[] = 'none' ;}
		}else{ $_proteins[] = 'none' ;
		}

		if( ! empty( $_proteins ) ){
			$ids_proteins_excl =  $this->ketoday365_get_recipes_excl_proteins ($_proteins);         
		}else{
			$ids_proteins_excl =null ;
		}
	    return $ids_proteins_excl;

	}

	function ketoday365_get_recipes_excl_proteins ($proteins){

		$meta_query = array('relation' => 'OR');
		foreach( $proteins as $item ){
			$meta_query[] = array(
				'key'     => 'protein_selection',
				'value'   => $item,
				'compare' => 'LIKE',
			);
		}

		// args
		$args = array(
			'numberposts'	=> -1,
			'post_type'		=>  'recipe',
			'meta_query' => $meta_query,
		);

		$my_posts = get_posts( $args );
		$ids_proteins = array();
		$i=1;
		if( ! empty( $my_posts ) ){
			foreach ( $my_posts as $p ){
				$i++;
				$ids_proteins[] = $p->ID;
			}       
		}
		return $ids_proteins;
	}

	function ketoday365_getID_lactose_free(){

		$args = array(
			'numberposts'	=> -1,
			'post_type'		=>  'recipe',
			'meta_query' => 	array(
				'key'     => 'lactose_free',
				'value'   => '1',
			)
		);

		$my_posts = get_posts( $args );
		$ids_proteins = array();
		$i=1;
		if( ! empty( $my_posts ) ){
			foreach ( $my_posts as $p ){
				$i++;
				$ids_proteins[] = $p->ID;
			}       
		}
		return $ids_proteins;
	}
	
}