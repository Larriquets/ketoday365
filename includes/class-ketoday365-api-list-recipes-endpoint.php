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
class Api_Ketoday365_List_Recipes_Endpoint {

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
	public function ketoday365_list_recipes_api_constructor() {
		register_rest_route( 'keto-endpoint', 'list-recipes', array(
			'methods' => 'POST',
			'callback' => array( $this, 'ketoday365_phrase_list_recipes' ),
			'permission_callback' => '__return_true'
		   )
		);
	}

    //// ----  Custom Endpoint - List Recipes (Print) ----  ////
	public function ketoday365_phrase_list_recipes(WP_REST_Request $request) {
		$keys = $request->get_json_params();
		if (isset($keys['recipes']))    { 
			$recipes = $keys['recipes'];
			if(is_array($recipes )){
				foreach($recipes as $key=>$id_recipe){
					$_recipes[] = $this->ketoday365_get_ingredients($id_recipe);				
				}
			}else{
				$_recipes = $this->ketoday365_get_ingredients($recipes);
			}
			return  $_recipes;
		}
	
		foreach($keys as $key=>$days){
			$_days = $days;
			foreach($days as $key=>$meals){
				$_meals[] = $meals;
				foreach($meals as $key=>$id){
					$_ids[] = $id;
					$_recipes[] = $this->ketoday365_get_ingredients($id);
				}
			}		
		}

		
		return $_recipes;



	}


	function ketoday365_get_ingredients($id_recipe){
		$recipe_object= array();
		$title = get_the_title($id_recipe);
		$recipe_object['title'] = $title;
		$id = $id_recipe;
		$recipe_object['id'] = $id;
		
		if( have_rows('add_ingredient',$id_recipe) ):
			$i=1;
			 while ( have_rows('add_ingredient',$id_recipe) ) : the_row();
		
			  $featured_posts = get_sub_field('ingredient',$id_recipe);     
			    if( $featured_posts ): 
					foreach( $featured_posts as  $post ): 
						setup_postdata($post);
						 $title_ingred[]= get_the_title($post->ID );
						 $us_type[]=  get_field( 'us_type',$post->ID ); 				
						 $metric_type[] = get_field( 'metric_type',$post->ID ); 
					endforeach; 
					wp_reset_postdata();
			    endif; 
			    
				$text[] = get_sub_field('text');   	      
				$us[]= get_sub_field('us') ;	
				$metric[]= get_sub_field('metric'); 
				//if( get_row_layout() == 'additional_title' ){
				 $additional_title[] = get_sub_field('additional_title');
				// } else{
				// 	$additional_title[] = 'null'	;
				// }
				
			    $i++;
			 endwhile;
		endif;


		foreach($title_ingred as $key=>$value){
			$ingredient [$key]['title_ingred'] = $title_ingred[$key] ;
			$ingredient [$key]['us_type'] = $us_type[$key] ;
			$ingredient [$key]['metric_type'] = $metric_type[$key] ;
	
		}

		foreach($text as $key=>$value){
			$res [$key]['text'] = $text[$key] ;
			$res [$key]['us'] = $us[$key] ;
			$res [$key]['metric'] = $metric[$key] ;
			$res [$key]['additional_title'] = $additional_title[$key] ;
			$res [$key]['ingredient'] = $ingredient[$key] ;
		}

		 $recipe_object['ingredients']= $res;	

		 $nutrients['calories']= get_field('calories',$id_recipe);
		 $nutrients['net_carbs']= get_field('net_carbs',$id_recipe);
		 $nutrients['fat']= get_field('fat',$id_recipe);
		 $nutrients['protein']= get_field('protein',$id_recipe);
 		 $recipe_object['nutrients']= $nutrients;

		if( have_rows('directions',$id_recipe) ):
			
			 while ( have_rows('directions',$id_recipe) ) : the_row();    
			   if( $featured_posts ): 
			
					$item_direction[] =  get_sub_field( 'item_direction',$post->ID ); 
				endif;
			 endwhile;
		endif;
		$recipe_object['direction']= $item_direction;
		$recipe_object['tips']= get_field('tips',$id_recipe);
		$recipe_object['image_desktop']= get_field('image_desktop',$id_recipe);
		// Return
		return $recipe_object;
	}

	
}