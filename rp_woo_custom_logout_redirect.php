<?php
/*
Plugin Name: RP Woo Custome Logout Redirect
Author: RP
Description: To redirect to custom url or page when logout through woocommerce within site domain.
Text Domain: rp-woo-custom-logout
Version: 1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
	add_action('admin_menu', 'register_rp_custom_woo_logout_submenu_page',99);
	add_action( 'template_redirect', 'rp_wc_bypass_logout_confirmation' );
}



function register_rp_custom_woo_logout_submenu_page() {
    add_submenu_page( 'woocommerce', 'Custom Logout Page', 'Custom Logout', 'manage_options', 'rp-custom-woo-logout-page', 'rp_custom_woo_logout_submenu_page_callback' ); 
}

function rp_custom_woo_logout_submenu_page_callback() {
	rp_custom_woo_logout_submenu_page_save();
    rp_custom_woo_logout_submenu_page_view();

}

function rp_custom_woo_logout_submenu_page_view()
{
?>
	<h3>Custom WC Logout Redirect</h3>
	<form method="post" action="">
		<table>
			<tbody>

				<tr>
					<td><label for="rp_custom_woo_logout">Custom Redirect Url</label></td>
					<td>
						<?php $rp_custom_woo_logout=esc_url(get_option('rp_custom_woo_logout'))?esc_url(get_option('rp_custom_woo_logout')):'';?>
		<input type="text" name="rp_custom_woo_logout" id="rp_custom_woo_logout" class="regular-text form-control"
		value="<?php echo 
		$rp_custom_woo_logout;?>">

					</td>
				</tr>
				<tr>
					<td>
						<?php submit_button(); ?>
					</td>
				</tr>
				<tr style="margin-top: 50px;"><td colspan="2" ><b>Note:</b> Redirect only with-in the site domain.</td></tr>
				<tr><td colspan="2"><b>Example:</b> <?php echo home_url().'/abc'?></td></tr>
			</tbody>
		</table>		
	</form>
<?php
}


function rp_custom_woo_logout_submenu_page_save(){
	if ( isset($_POST['submit'] ) ) {	
		$validate=rp_custom_woo_logout_submenu_page_validate($_POST['rp_custom_woo_logout']);	
		if($validate!=""){
			if(!$validate){
				return;
			}	
		}		
		update_option( "rp_custom_woo_logout", $validate);
		echo "Succeccfully Saved!";
	}
}

function rp_custom_woo_logout_submenu_page_validate($url){
	
	$rp_errors = array();

	// Remove all illegal characters from a url
	

	if ( trim($url)!='') {
		$url = filter_var($url, FILTER_SANITIZE_URL);
	    if (!filter_var($url, FILTER_VALIDATE_URL)) {	    
	        $rp_errors[]='Not a valid URL';
	    }else{
	    	$url=esc_url($url);
	    }	    
	}else{
		$url="";
	}

	if (!empty($rp_errors) ) {
 			
	    foreach ( $rp_errors as $error ) {
	      
	        echo '<div class="error" style="line-height: 30px;">';
	        echo '<strong>ERROR</strong>:';
	        echo $error . '<br/>';
	        echo '</div>';
	         
	    }
 		return false;
	}
	return $url;

}


function rp_wc_bypass_logout_confirmation() {
   global $wp, $wp_query;

	$rp_custom_woo_logout=esc_url(get_option('rp_custom_woo_logout')) ?
	esc_url(get_option('rp_custom_woo_logout')) :wc_get_page_permalink( 'myaccount' ) ;

if ( isset( $wp->query_vars['customer-logout'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'customer-logout' ) ) { 

		// WPCS: input var ok, CSRF ok.

		// Logout.
		wp_safe_redirect( str_replace( '&amp;', '&', wp_logout_url( $rp_custom_woo_logout ) ) );
		exit;

	}
}

