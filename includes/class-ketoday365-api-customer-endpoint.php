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
class Api_Ketoday365_New_Customer_Endpoint {

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
	public function ketoday365_custom_api_route_constructor() {
	    register_rest_route( 'keto-endpoint', 'new-order', array(
			'methods' => 'POST',
			'callback' => array( $this, 'ketoday365_custom_phrase' ),
			'permission_callback' => '__return_true'
		   )
		);
	}


	public function ketoday365_custom_phrase(WP_REST_Request $request) {

		$res  = array();
		$keys = $request->get_json_params();
		if (isset($keys['user']['name'])) {	
			$name = $keys['user']['name'];
			$surname = $keys['user']['surname'];
		}else{
			$name = 'New Customer';
			$surname = '';
		}

		$customer_id = wp_insert_post(array(
			'post_type' => 'customers',
			'post_title' => $name.' '.$surname,
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_content' => json_encode($keys)
		));
		$this->ketoday365_update_customer($keys,$customer_id);
	    $proteins = $keys['quiz']['protein'];
		$product_ID = $this->ketoday365_select_product( $proteins ,$keys['quiz']['restrictions'],$keys['plan']['plan_duration']);
		if(!empty($product_ID)){
			$product_link = get_permalink( $product_ID );
			$product_link = $product_link.'?mpl_customer='.$customer_id;		
			update_field('pdf_download', $product_link, $customer_id);
			update_field('associated_product', $product_ID, $customer_id);
			update_field('quiz', 'React App', $customer_id);
			$order_ID = $this->ketoday365_create_order_WC( $product_ID, $keys, $keys['customer_id'] , $customer_id );
	
	        if (isset($keys['subscription']['scp_status']) ){
				if($keys['subscription']['scp_status'] == 'active'){
					update_field('scp_status', $keys['subscription']['scp_status'] , $order_ID);
				}
			}
	
		}else{
			$product_link = 'error';
		}

		$res['wp_customer_id'] = $customer_id;
		$res['wp_order_id'] = $order_ID;
		$res['wp_ebook_link']= $product_link ;
		if (isset($keys['tracking']['cid'])) {	
			$cid = $keys['tracking']['cid'];
			$oid = $keys['tracking']['oid'];
			$postback = $this->ketoday365_postback_fm($cid,$oid, $order_ID );
			$res['fm_postback']= $postback ;		
		}


		return $res ;
	}


	function ketoday365_postback_fm($cid,$oid,$order_ID){
		$url = 'http://rdldtrk.com/p.ashx';//http://larevistademibarrio.com.ar/2017/drupal_test/wp-remote-receiver.php'; // ?cid=27983&refid = {{su_orden. id_or_transaction.id_here}} & clickid = {{click.id}}
		$payout = '';
		$txid = $order_ID;
        $cid = $cid;
     
        $response = wp_remote_post( $url, array(
            'body'        => array(   
                "r" 		=> $cid,
				"o" 		=> $oid,
				"f" 		=> 'pb',
                // "payout"	=> $payout,
                // "txid"  	=> $txid,
            ), )
        );
   
        if ( is_wp_error( $response ) ) {
           $res = 'error';
        }else {
			$res = 'ok';
        }
         return $res;

	}

	
	public function ketoday365_update_customer($keys,$customer_id){
	
		if (isset($keys['user']['email'])) {	
			update_field('email',$keys['user']['email'] , $customer_id);
		}
		if (isset($keys['quiz']['protein'])) {	
			update_field('protein',$keys['quiz']['protein'] , $customer_id);
		}
		if (isset($keys['user']['name'])) {	
			update_field('name',$keys['user']['name'] , $customer_id);
		}
		if (isset($keys['user']['surname'])) {	
			update_field('last_name',$keys['user']['surname'] , $customer_id);
		}
		if (isset($keys['quiz']['goals'])) {	
			update_field('goals',$keys['quiz']['goals'] , $customer_id);
		}
		if (isset($keys['quiz']['motivated'])) {	
			update_field('motivated',$keys['quiz']['motivated'] , $customer_id);
		}
		if (isset($keys['quiz']['health'])) {	
			update_field('health',$keys['quiz']['health'] , $customer_id);
		}
		if (isset($keys['quiz']['diets'])) {	
			update_field('diets_tried',$keys['quiz']['diets'] , $customer_id);
		}
		if (isset($keys['quiz']['habits'])) {	
			update_field('habits',$keys['quiz']['habits'] , $customer_id);
		}
		if (isset($keys['quiz']['exercise'])) {	
			update_field('exercise',$keys['quiz']['exercise'] , $customer_id);
		}
		if (isset($keys['quiz']['sleep'])) {	
			update_field('sleep',$keys['quiz']['sleep'] , $customer_id);
		}
		if (isset($keys['quiz']['restrictions'])) {	
			update_field('restrictions',$keys['quiz']['restrictions'] , $customer_id);
		}
		if (isset($keys['quiz']['gender'])) {	
			update_field('gender',$keys['quiz']['gender'] , $customer_id);
		}
		if (isset($keys['quiz']['age'])) {	
			update_field('age',$keys['quiz']['age'] , $customer_id);
		}
		if (isset($keys['quiz']['height_feet'])) {	
			update_field('height_feet',$keys['quiz']['height_feet'] , $customer_id);
		}
		if (isset($keys['quiz']['height_in'])) {	
			update_field('height_in',$keys['quiz']['height_in'] , $customer_id);
		}
		if (isset($keys['quiz']['height_cm'])) {	
			update_field('height_cm',$keys['quiz']['height_cm'] , $customer_id);
		}
		if (isset($keys['quiz']['weight_to_lose_lb'])) {	
			update_field('weight_to_lose_lb',$keys['quiz']['weight_to_lose_lb'] , $customer_id);
		}
		if (isset($keys['quiz']['weight_to_lose_kg'])) {	
			update_field('weight_to_lose_kg',$keys['quiz']['weight_to_lose_kg'] , $customer_id);
		}
		if (isset($keys['quiz']['current_weight_lb'])) {	
			update_field('current_weight_lb',$keys['quiz']['current_weight_lb'] , $customer_id);
		}
		if (isset($keys['quiz']['current_weight_kg'])) {	
			update_field('current_weight_kg',$keys['quiz']['current_weight_kg'] , $customer_id);
		}
	
		if (isset($keys['quiz']['desired_weight_lb'])) {	
			update_field('desired_weight_imperial',$keys['quiz']['desired_weight_lb'] , $customer_id);
		}
		if (isset($keys['quiz']['desired_weight_kg'])) {	
			update_field('desired_weight_mt',$keys['quiz']['desired_weight_kg'] , $customer_id);
		}
		if (isset($keys['user']['country'])) {	
			update_field('country',$keys['user']['country'] , $customer_id);
		}
		if (isset($keys['subscription']['scp_status'])) {	
			update_field('subscription',$keys['subscription']['scp_status'] , $customer_id);
		}
		if (isset($keys['tracking']['utm_site_source'])) {	
			update_field('utm_site_source',$keys['tracking']['utm_site_source'] , $customer_id);
		}
		if (isset($keys['tracking']['utm_campaign_name'])) {	
			update_field('utm_campaign_name',$keys['tracking']['utm_campaign_name'] , $customer_id);
		}
		if (isset($keys['tracking']['cid'])) {	
			update_field('cid',$keys['tracking']['cid'] , $customer_id);
		}
		if (isset($keys['tracking']['sub_id'])) {	
			update_field('subid',$keys['tracking']['sub_id'] , $customer_id);
		}
		if (isset($keys['tracking']['oid'])) {	
			update_field('oid',$keys['tracking']['oid'] , $customer_id);
		}
		
		if (isset($keys['subscription']['paypal_subscription_id'])) {	
			update_field('payer_id',$keys['subscription']['paypal_subscription_id'] , $customer_id);
		}
		if (isset($keys['subscription']['stripe_order_id'])) {	
			update_field('payment_id',$keys['subscription']['stripe_order_id'] , $customer_id);
		}
		if (isset($keys['payment']['stripe_subscription_id'])) {	
			update_field('sub_id',$keys['payment']['stripe_subscription_id'] , $customer_id);
		}
	
	}
	

	public function ketoday365_create_order_WC($product_id ,$data_post,$id_firestore,$customer_id  ) {
	
		global $woocommerce;
		$address = array(
			'first_name' =>  $data_post['user']['name'],
			'last_name'  =>  $data_post['user']['surname'],
			'email'      =>  $data_post['user']['email'],
		);
	  
		$new_product_price = $data_post['plan']['plan_price'];
		$order = wc_create_order();
		$product = wc_get_product( $product_id );
		$product->set_price( $new_product_price );
		
		$order->add_product( $product , 1); 
		$order->set_address( $address, 'billing' );
		if ( $data_post['subscription']['paypal_order_id'] != null){
			$order->set_payment_method( 'Paypal Order ID : '.$data_post['subscription']['paypal_order_id']);
			$note = 'Paypal Order ID :'.$data_post['subscription']['paypal_order_id'].'</br>Firestore ID : '.$id_firestore ;
		}else{
			$order->set_payment_method( 'Stripe Order ID : '. $data_post['subscription']['stripe_order_id']);
			$note = 'Stripe Order ID :'.$data_post['subscription']['stripe_order_id'].'</br>Firestore ID : '.$id_firestore ;
		}

		$order->add_order_note( $note );
		$order->calculate_totals();
		$order_id = $order->get_id();
		update_post_meta( $order_id, 'customer_id', $customer_id );	
	
		if($data_post['tracking']['subscription']  ){
			update_post_meta( $order_id, 'subscription', 'avocado_scp' );		
		}
		if($data_post['tracking']['unique_subscription'] ){
			update_post_meta( $order_id, 'subscription', 'avocado_dig' );
		}
		if(empty( $data_post['tracking']['subscription']))
		{
			update_post_meta( $order_id, 'subscription', 'avocado' );
		}
		if($data_post['subscription']['checkout'] == true ){
			update_post_meta( $order_id, 'subscription', 'checkout' );		
		}
		if($data_post['subscription']['checkout_fr'] == true ){
			update_post_meta( $order_id, 'subscription', 'checkout_fr' );		
		}
		if($data_post['subscription']['checkout_se'] == true ){
			update_post_meta( $order_id, 'subscription', 'checkout_se' );		
		}
		if (isset($data_post['pass'])) {	
			update_post_meta( $order_id, 'pass',$data_post['pass'] ); 
		}
		if (isset($data_post['user']['pass'])) {	
			update_post_meta( $order_id, 'pass',$data_post['user']['pass'] ); 
		}
        // if($data_post['subscription']['scp_status'] =="active" ){
		//    update_post_meta( $order_id, 'subscription', 'active' );		
		// }else{
		// 	update_post_meta( $order_id, 'subscription', 'none' );
		// }
		$order->update_status( 'completed' );
		$order->save();
	
		return $order_id;
	}
	

	public function ketoday365_select_product($proteins ,$restrictions,$plan_duration){
		$id_plan_product = 1046;

		if ( $restrictions == 'Lactose Intolerant') {
			$restrictions = 'lactose';
			$id_plan_product = 1059;
			if ($plan_duration =='28d'){$id_plan_product =1059; }
			if ($plan_duration =='60d'){$id_plan_product = 1059; }
			if ($plan_duration =='90d'){ $id_plan_product = 1059;}
			//$id_plan_product =  keto_get_product_by_restrictions($restrictions,$plan_duration);
		}else{
			if ($proteins  == 'None') {	
				$id_plan_product = 1002;
				if ($restrictions == 'Vegan' ) {
					if ($plan_duration =='28d'){$id_plan_product =1000; }
					if ($plan_duration =='60d'){ $id_plan_product =1000; }
					if ($plan_duration =='90d'){ $id_plan_product =1000; }
				}
				if (  $restrictions == 'Vegetarian') {
					if ($plan_duration =='28d'){$id_plan_product =1002; }
					if ($plan_duration =='60d'){$id_plan_product =6309; }
					if ($plan_duration =='90d'){$id_plan_product =7032;}
				}

			} else {
				$id_plan_product =  $this->ketoday365_get_product_by_proteins($proteins,$plan_duration);
			}
		}
		return $id_plan_product;
	}


	public function ketoday365_get_product_by_proteins ($array_proteins, $plan_duration_select){
		global $woocommerce;
		$sortcolumn = 'ID';
		$product_args = array(
			'numberposts' => -1,
			'post_status' => array('publish'),
			'post_type' => array('product'), 
			'orderby' => $sortcolumn,
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key'     => 'plan_duration',
					'value'   => $plan_duration_select,
					'compare' => 'LIKE'
				)
			)	
		);
		
		$products = get_posts($product_args);
		$product_id_res = 1046; //Default
		$trimmed_array = array_map('trim', $array_proteins);
		foreach($products as $product){
			if( have_rows('proteins',$product->ID) ){
				while( have_rows('proteins',$product->ID) ) : the_row();
					$proteins = get_sub_field('protein',$product->ID);
					if ($trimmed_array === $proteins){
						$product_id_res= $product->ID; 						
						return $product_id_res;        
					}            			
				endwhile;
			}		
		
		}	
		return $product_id_res;
	}
	
}
