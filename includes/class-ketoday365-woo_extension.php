<?php
/**
 * WooCommerce Extension Boilerplate - functions and filters.
 *
 * @class 	Ketodat365_Woo_Extension
 * @version 0.1.0
 * @since   0.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ketodat365_Woo_Extension {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}


	function ketoday365_custom_override_checkout_fields( $fields ) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_1']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_postcode']);
		unset($fields['billing']['billing_country']);
		unset($fields['billing']['billing_state']);
		unset($fields['billing']['billing_phone']);
		unset($fields['order']['order_comments']);
		unset($fields['account']['account_username']);
		unset($fields['account']['account_password']);
		unset($fields['account']['account_password-2']);
		return $fields;
	}


	function ketoday365_editable_order_meta_general( $order ){  ?>
 
		<br class="clear" />
		<h4>HFP - Additional Information </h4>
        <?php 
            $customer_id = get_post_meta( $order->get_id(), 'customer_id', true );
            $downloads = $order->get_downloadable_items();
            $download_prod = array();	
            foreach ( $downloads as $download ) :
                $download_prod = $download;
			endforeach;
			$link = admin_url( 'post.php?post=' . absint( $customer_id ) . '&action=edit' );
			//$country = $this->get_user_geo_country();
			$subscription = get_post_meta( $order->get_id(), 'subscription', true );
			$pass = get_post_meta( $order->get_id(), 'pass', true );
		?>
		<div class="">
	
		<p> <strong>Tracking / Plan :</strong>  <a target="_blank" ><?php echo $subscription ?> </a></p> 
		<p> <strong>Pass / Plan :</strong> <input type="password"  name="" value="<?php echo $pass ?>"> </p> 
            <p> <strong>Customer ID :</strong> <a target="_blank" href="<?php echo esc_url($link) ?>"><?php echo $customer_id ?> </a></p>  
			<p> <strong>Download PDF :</strong> <a href="<?php echo esc_url( get_permalink( $download_prod['product_id'] ).'?mpl_customer='.$customer_id); ?>"> PDF</a> </p> 
		</div>		
	<?php }
	

	function ketoday365_checkout_fields ( $value, $input ) {

		if(isset($_POST['email']) && ! empty($_POST['email']) ){

			$email = esc_attr( $_POST['email'] );

			$checkout_fields = array(
				'billing_first_name'    => '',
				'billing_last_name'     => '',
				'billing_email'         => $email ,
				// 'customer_id'           => $customer_id ,  
			);

			foreach( $checkout_fields as $key_field => $field_value ){
				if( $input == $key_field && ! empty( $field_value ) ){
					$value = $field_value;
				}
			}
			return $value;
		}
	}


	function ketoday365_select_field( $checkout ){
    	woocommerce_form_field( 'customer_id', array( 'type'  => 'hidden' ), $checkout->get_value( 'customer_id' ) );
	}


	function ketoday365_save_customer_id( $order_id ){
        if( !empty( $_POST['customer_id'] ) ){
			update_post_meta( $order_id, 'customer_id', $_POST['customer_id']  ); 
		}else{
			update_post_meta( $order_id, 'customer_id','1111'  ); 
		}

	}

} 