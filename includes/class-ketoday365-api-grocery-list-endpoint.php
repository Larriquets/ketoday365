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
class Api_Ketoday365_Grocery_List_Endpoint {

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
	public function ketoday365_grocery_list_api_constructor() {

		register_rest_route( 'keto-endpoint', 'grocery-list', array(
			'methods' => 'POST',
			'callback' => array( $this, 'ketoday365_phrase_grocery_list' ),
			'permission_callback' => '__return_true'
		   )
		);
	}


	function ketoday365_list_recipes($keys){
		foreach($keys as $key=>$days){
			$_days = $days;
			foreach($days as $key=>$meals){
				$_meals[] = $meals;
				foreach($meals as $key=>$id){
					$_ids[] = $id;
					$_recipes[] = $this->ketoday365_get_ingredients_gc_list($id);
				}
			}		
		}	
		return $_recipes;
	}


	function ketoday365_clean_by_taxonomy($keys){

		return $_recipes;
	}


	function ketoday365_add_recipes_duplicates($duplicates,$_ingredients_full ){
		foreach ($duplicates as $key => $val) {
			//	echo 'key ---'.$key;
			foreach( $_ingredients_full as $key_=> $ingredient ){	
				//echo ' $ingredient'.$ingredient['id_ingred'] ;
				if($ingredient['id_ingred'] == $val && $final_recipe_flag[$val]['flag'] != true){
					$final_recipe[$val]['text'] 		= 'add';
					$final_recipe[$val]['id_ingred'] 	= $ingredient['id_ingred'];
					$final_recipe[$val]['title_ingred'] = $ingredient['title_ingred'];
					$final_recipe[$val]['us_type']      = $ingredient['us_type'];
					$final_recipe[$val]['metric_type']  = $ingredient['metric_type'];
					$final_recipe[$val]['gl_category']  = array($ingredient['gl_category'][0]);

					$decimal_us = $this->is_decimal( $ingredient['us']);
					if ( $decimal_us ){
						$_dec_US = $ingredient['us'];
					}else{
						$_dec_US = $this->fractionToDecimal($ingredient['us']);
					}
					$final_recipe[$val]['us_desc']  += $_dec_US ;

		
					$decimal_metric = $this->is_decimal( $ingredient['metric']);
					if ( $decimal_metric ){
						$_dec_metric = $ingredient['metric'];
					}else{
						$_dec_metric = $this->fractionToDecimal($ingredient['metric']);
					}
				
					$final_recipe[$val]['metric_desc'] += $_dec_metric;				
					
				}
			}
			$final_recipe_flag[$val]['flag'] = true;
		    //	$final_recipe[$val]['us_fraccion'] = ;
			$decimal_us = $this->is_decimal( $final_recipe[$val]['us_desc'] );
			if ( $decimal_us ){
				$desc = $final_recipe[$val]['us_desc'];
				$round_us= ceil($desc * 4) / 4;
				$final_recipe[$val]['us_round']  = $round_us;
				$final_recipe[$val]['us']  = $this->decimalToFraction($round_us);
			}else{
				$final_recipe[$val]['us']  = $final_recipe[$val]['us_desc'];
			}

			$decimal_metric = $this->is_decimal( $final_recipe[$val]['metric_desc'] );
			if ( $decimal_metric ){
				$desc_m = $final_recipe[$val]['metric_desc'];
				$round_metric = ceil($desc_m * 4) / 4;
				$final_recipe[$val]['metric_round']  =$round_metric;
				$final_recipe[$val]['metric']  = $this->decimalToFraction($round_metric);
			}else{
				$final_recipe[$val]['metric']  = $final_recipe[$val]['metric_desc'];
			}


		}
		return $final_recipe;
	}

	//// ----  Custom Endpoint - Grocery List  (Print) ----  ////
	public function ketoday365_phrase_grocery_list(WP_REST_Request $request) {
		$keys = $request->get_json_params();	
		$_recipes = $this->ketoday365_list_recipes($keys);

		foreach( $_recipes as  $ingredients ){						
			foreach( $ingredients as $key => $ingredient ){
				$_gl_category = $ingredient['gl_category'];
				if(!empty($_gl_category)){	
					$_ingredients_full[] = $ingredient;
					$ingredient_id[] = $ingredient['id_ingred'];
				}
			}

		}

		$unique = array_unique($ingredient_id);
		$duplicates = array_diff_assoc($ingredient_id, $unique);
		$result = array_diff($unique, $duplicates);
		$unique_keys = array_keys($result);

		$final_recipe = $this->ketoday365_add_recipes_duplicates($duplicates,$_ingredients_full);

		// Filter			
		foreach($final_recipe as $key_=> $recipe){
			$fin_recipe[] = $recipe;
		}
	
        $merge_recipes = $this->ketoday365_merge_recipes($unique_keys ,$_ingredients_full,$fin_recipe);
		
		$final_recipes_by_tax = $this->ketoday365_order_recipes_by_taxonomy($merge_recipes);

		return $final_recipes_by_tax;


	}

	function ketoday365_merge_recipes($unique_keys ,$_ingredients_full,$fin_recipe){
		if(empty($unique_keys)){
			$out =$fin_recipe;
		}else{
			foreach($unique_keys as $key_=> $posc){
				$final_unique[] = $_ingredients_full[$posc];
			}
			$out = array_merge($fin_recipe, $final_unique);
		}
		return $out;

	}

	function ketoday365_order_recipes_by_taxonomy($merge_recipes){
		foreach( $merge_recipes as $key=> $ingredient ){					
			$_gl_category = $ingredient['gl_category'];
			$_gl_taxonomy= implode(",", $_gl_category);
			$gl_taxonomy[$_gl_taxonomy][] = $ingredient;
		}
		return $gl_taxonomy;

	}

	// ---- Fractions ---- //
	function roundUpToAny($n,$x=5) {
		return (round($n)%$x === 0) ? round($n) : round(($n+$x/2)/$x)*$x;
	}

	function ketoday365_get_ingredients_gc_list($id_recipe){
		$recipe_object= array();
		$title = get_the_title($id_recipe);
		$recipe_object['title'] = $title;
		$id = $id_recipe;
		$recipe_object['id'] = $id;
		
		if( have_rows('add_ingredient',$id_recipe) ):
			$i=1;
			 while ( have_rows('add_ingredient',$id_recipe) ) : the_row();
			    if( get_row_layout() == 'additional_title' ){
				//	$additional_title[] = get_sub_field('additional_title');
				} else{
					//$additional_title[] = 'null'	;
					$featured_posts = get_sub_field('ingredient',$id_recipe);     
					if( $featured_posts ): 
						foreach( $featured_posts as  $post ): 
							setup_postdata($post);
							$id_ingred[] = $post->ID ;
							$title_ingred[]= get_the_title($post->ID );
							$us_type[]=  get_field( 'us_type',$post->ID ); 				
							$metric_type[] = get_field( 'metric_type',$post->ID ); 
							//Returns All Term Items for "my_taxonomy".
							$gl_category[] = wp_get_post_terms( $post->ID, 'gl_category', array( 'fields' => 'names' ) );
	
					
						endforeach; 
						wp_reset_postdata();
					endif; 
					
					$text[] = get_sub_field('text');   	      
					$us[]= get_sub_field('us') ;	
					$metric[]= get_sub_field('metric'); 
					$i++;
				}

			 endwhile;
		endif;


		
		
			foreach($title_ingred as $key=>$value){
			//	if(!empty($gl_category[$key])){
				$ingredient [$key]['text'] = $text[$key] ;
				$ingredient [$key]['us'] = $us[$key] ;
				$ingredient [$key]['metric'] = $metric[$key] ;
				$ingredient [$key]['id_ingred'] = $id_ingred[$key] ;
				$ingredient [$key]['title_ingred'] = $title_ingred[$key] ;
				$ingredient [$key]['us_type'] = $us_type[$key] ;
				$ingredient [$key]['metric_type'] = $metric_type[$key] ;
				$ingredient [$key]['gl_category'] = $gl_category[$key] ;
			//	}
			}

			// foreach($text as $key=>$value){
			// //	if(!empty($gl_category[$key])){
			// 	$res [$key]['text'] = $text[$key] ;
			// 	$res [$key]['us'] = $us[$key] ;
			// 	$res [$key]['metric'] = $metric[$key] ;
			// 	$res [$key]['ingredient'] = $ingredient[$key] ;
			// //	}
			// }

			$recipe_object['ingredients']= $res;	
	


		// Return
		return $ingredient;
	}

	function ketoday365_validate_array($_ingredients,$x){
		foreach ($_ingredients as $key => $value) {
			if (array_search($x, $value)) {
				return $key;
			}
		}
	}

	function ketoday365_sumar_array($old_ingredient, $new_ingredient){
		$final_array['text'] = 'add';
		$final_array['id_ingred'] = $new_ingredient['id_ingred'];
		$final_array['title_ingred'] = $new_ingredient['title_ingred'];

		// --------  US ----------- //
		$decimal_old_us = $this->is_decimal( $old_ingredient['us']);
		if ( $decimal_old_us ){
			$old_dec_US = $old_ingredient['us'];
		}else{
			$old_dec_US = $this->fractionToDecimal($old_ingredient['us']);
		}

		$decimal_new_us = $this->is_decimal( $new_ingredient['us']);
		if ($decimal_new_us ){
			$new_dec_US = $new_ingredient['us'];
		}else{
			$new_dec_US = $this->fractionToDecimal($new_ingredient['us']);
		}
		$add_dec_US = $new_dec_US + $old_dec_US;
		$final_array['us_desc'] = $add_dec_US ;

		// --------  METRIC ----------- //
		$decimal_old_metric = $this->is_decimal( $old_ingredient['metric']);
		if ( $decimal_old_metric ){
			$old_dec_metric = $old_ingredient['metric'];
		}else{
			$old_dec_metric = $this->fractionToDecimal($old_ingredient['metric']);
		}

		$decimal_new_metric = $this->is_decimal( $new_ingredient['metric']);
		if ($decimal_new_metric ){
			$new_dec_metric = $new_ingredient['metric'];
		}else{
			$new_dec_metric = $this->fractionToDecimal($new_ingredient['metric']);
		}
		$add_dec_metric = $new_dec_metric + $old_dec_metric;
		$final_array['metric_desc'] = $add_dec_metric ;

		$final_array['new_ingredient'] = $new_dec_US;
		$final_array['old_ingredient'] = $old_dec_US ;


		$final_array['us_type'] = $new_ingredient['us_type'];
		$final_array['metric_type'] = $new_ingredient['metric_type'];
		
		$final_array['gl_category'] = array($new_ingredient['gl_category'][0]);
        return $final_array;
		
	}

	function is_decimal( $val ){
		return is_numeric( $val ) && floor( $val ) != $val;
	}

	function decimalToFraction($decimal) {
		// Determine decimal precision and extrapolate multiplier required to convert to integer
		$precision = strpos(strrev($decimal), '.') ?: 0;
		$multiplier = pow(10, $precision);

		// Calculate initial numerator and denominator
		$numerator = $decimal * $multiplier;
		$denominator = 1 * $multiplier;

		// Extract whole number from numerator
		$whole = floor($numerator / $denominator);
		$numerator = $numerator % $denominator;

		// Find greatest common divisor between numerator and denominator and reduce accordingly
		$factor = gmp_intval(gmp_gcd($numerator, $denominator));
		$numerator /= $factor;
		$denominator /= $factor;

		// Create fraction value
		$fraction = [];
		$whole && $fraction[] = $whole;
		$numerator && $fraction[] = "{$numerator}/{$denominator}";

		return implode(' ', $fraction);
	}

	function fractionToDecimal($fraction) {
		// Split fraction into whole number and fraction components
		preg_match('/^(?P<whole>\d+)?\s?((?P<numerator>\d+)\/(?P<denominator>\d+))?$/', $fraction, $components);

		// Extract whole number, numerator, and denominator components
		$whole = $components['whole'] ?: 0;
		$numerator = $components['numerator'] ?: 0;
		$denominator = $components['denominator'] ?: 0;

		// Create decimal value
		$decimal = $whole;
		$numerator && $denominator && $decimal += ($numerator/$denominator);

		return $decimal;
	}

	function ketoday365_fractions_add($a_, $b_){
			$a = new Fraction(1,2);
			// Convert a well formed string to a fraction
			$d = Fractions::fromString($a_);
			$e = Fractions::fromString($b_);
			// Because the Fraction constructor only accepts a numerator, and denominator, you can use this method to convert a mixed number to a Fraction object 
			$b = Fractions::fromArray(array(1,1,2)); 
			// $plugin_fractions = new Fraction(1,2);
			$sum = Fractions::add($d, $e); 
			// Fraction::toString() is dumb, and just prints the numerator/denominator
			return	$sum->toString(); // 4/2
			// Fractions::toString() prints in lowest terms, and mixed numbers
			//echo	return  Fractions::toString($sum); // 2
	}
	
}