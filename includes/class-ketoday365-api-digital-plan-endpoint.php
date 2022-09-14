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
class Api_Ketoday365_Digital_Plan_Endpoint {

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
	public function ketoday365_digital_plan_api_constructor() {
	    register_rest_route( 'keto-endpoint', 'digital-plan', array(
			'methods' => 'POST',
			'callback' => array( $this, 'ketoday365_phrase_digital_plan' ),
			'permission_callback' => '__return_true'
		   )
		);
	}



	public function ketoday365_phrase_digital_plan(WP_REST_Request $request) {
  
		$keys = $request->get_json_params();
		
		if (isset($keys['wp_customer_id'])) {	
			$wp_customer_id = $keys['wp_customer_id'];
			update_field('digital_plan_original', json_encode($keys) , $wp_customer_id);	
		}

		if (isset($keys['digital_plan_duration'])) {$digital_plan_duration = $keys['digital_plan_duration'];}

		if (isset($keys['start_day'])) {$start_day = $keys['start_day'];}else{$start_day = 1;}
	
		if (isset($keys['protein'])) {
			$proteins = $keys['protein'];
		}

		if (isset($keys['restrictions'])) {
			$restrictions = $keys['restrictions'];

		}


		$digital_plan = $this->ketoday365_create_digital_plan_init($start_day,$proteins ,$digital_plan_duration ,$restrictions );
		if( !empty($wp_customer_id)){
			update_field('digital_plan', json_encode($digital_plan) , $wp_customer_id);	
		}			
		return 	$digital_plan;

	}



	function ketoday365_create_digital_plan_init($start_day, $proteins, $digital_plan_duration ,$restrictions ){
	    if(!is_array( $restrictions) ){
			settype($restrictions, 'array');
		}
		if(!is_array( $proteins) ){
			settype($proteins, 'array');
		}
	 
	   	// -- Breakfast ---------- //
		$_breakfast = $this->ketoday365_get_meals_plans($digital_plan_duration, $restrictions,$proteins,'Breakfast');
		if(count($_breakfast) <= $digital_plan_duration ) {
			while (count($_breakfast) < $digital_plan_duration) {
				 $_breakfast[]  =$this->ketoday36_get_meals_plans_autocomplete(1, $restrictions,$proteins,'Breakfast');
			}
		}

		// -- Lunch ---------- //
		$_lunch  = $this->ketoday365_get_meals_plans($digital_plan_duration, $restrictions,$proteins,'Meals');
		if(count($_lunch) <= $digital_plan_duration ) {
			while (count($_lunch) < $digital_plan_duration) {
				 $_lunch[]  =$this->ketoday36_get_meals_plans_autocomplete(1, $restrictions,$proteins,'Meals');
			}
		}

		// -- Dinner ---------- //
		$_dinner = $this->ketoday365_get_meals_plans($digital_plan_duration, $restrictions,$proteins,'Meals');
		if(count($_dinner) <= $digital_plan_duration ) {
			while (count($_dinner) < $digital_plan_duration) {
				 $_dinner[]  =$this->ketoday36_get_meals_plans_autocomplete(1, $restrictions,$proteins,'Meals');
			}
		}

		// -- Snack ---------- //
		$_snack  = $this->ketoday365_get_meals_plans($digital_plan_duration, $restrictions,$proteins,'Snack');
		if(count($_snack) <= $digital_plan_duration ) {
			while (count($_snack) < $digital_plan_duration) {
				 $_snack[]  =$this->ketoday36_get_meals_plans_autocomplete(1, $restrictions,$proteins,'Snack');
			}
		}
		
	    // ------ Json Return ------ //
		$json_plan =  $this->ketoday365_create_digital_plan_week($start_day, 0, $digital_plan_duration, $_breakfast,$_lunch ,$_dinner, $_snack );
		return $json_plan;
	}


	function ketoday365_get_meals_plans($digital_plan_duration, $restrictions,$proteins, $meals){

		$proteins_exclude = $this->ketoday365_invert_proteins($proteins);		
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
				)
			);
		}else{
			$meta_query[] =  array(
					 'key'     => 'meals_checkbox',
					 'value'   => $meals,
					 'compare' => 'LIKE'
		    );
		}

		$posts = get_posts(array(
			'post_status' => 'publish',  
			'post__not_in' => $proteins_exclude ,
			'numberposts'	=>  $digital_plan_duration,
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


	function ketoday36_get_meals_plans_autocomplete($digital_plan_duration, $restrictions,$proteins, $meals){

		$proteins_exclude = $this->ketoday365_invert_proteins($proteins);		
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
				)
			);
		}else{
			$meta_query[] =  array(
					 'key'     => 'meals_checkbox',
					 'value'   => $meals,
					 'compare' => 'LIKE'
		    );
		}

		$posts = get_posts(array(
			'post_status' => 'publish',  
			'post__not_in' => $proteins_exclude ,
			'numberposts'	=>  $digital_plan_duration,
			'orderby' => 'rand',
			'post_type'		=>  'recipe',
			'meta_query' => $meta_query
		));
		$i = 1;
		if( ! empty( $posts ) ){
			foreach ( $posts as $p ){ 
				$i++;
				$_recipes=  array(
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

	
	function ketoday365_create_digital_plan_week($start_day, $from , $to, $_breakfast,$_lunch ,$_dinner, $_snack ){
		$day = array();
		for ($i = $from; $i < $to; $i++) {	
			$iday=  $i+ $start_day;
			
			$day['day_'.$iday]['breakfast'] = $_breakfast[$i];
			$day['day_'.$iday]['lunch'] = $_lunch[$i];
			$day['day_'.$iday]['snack'] = $_snack[$i];
			$day['day_'.$iday]['dinner'] = $_dinner[$i];
		}
		return $day;

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