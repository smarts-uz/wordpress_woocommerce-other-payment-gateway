<?php

class WC_Other_Payment_Gateway extends WC_Payment_Gateway{
	private $order_status;

	public function __construct(){
		$this->id = 'other_payment';
		$this->method_title = "Your Hardcoded";
		$this->has_fields = true;
		$this->init_form_fields();
		$this->init_settings();
		$this->enabled = $this->get_option('enabled');
		$this->title = $this->calculate_installment_price();
		// $this->description = $this->get_option('description');
		$this->hide_text_box = $this->get_option('hide_text_box');
		$this->text_box_required = $this->get_option('text_box_required');
		$this->order_status = $this->get_option('order_status');


		add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_my_plugin_styles'));
		add_action('woocommerce_thankyou', array($this, 'display_order_data'), 20);
	}

	public function init_form_fields(){
		$this->form_fields = array(
				'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'woocommerce-other-payment-gateway' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable Custom Payment', 'woocommerce-other-payment-gateway' ),
				'default' 		=> 'yes'
			),
				'hide_text_box' => array(
				'title' 		=> __( 'Hide The Payment Field', 'woocommerce-other-payment-gateway' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Hide', 'woocommerce-other-payment-gateway' ),
				'default' 		=> 'no',
				'description' 	=> __( 'If you do not need to show the text box for customers at all, enable this option.', 'woocommerce-other-payment-gateway' ),
			),
				'order_status' => array(
				'title' => __( 'Order Status After The Checkout', 'woocommerce-other-payment-gateway' ),
				'type' => 'select',
				'options' => wc_get_order_statuses(),
				'default' => 'wc-on-hold',
				'description' 	=> __( 'The default order status if this gateway used in payment.', 'woocommerce-other-payment-gateway' ),
			), 
				'telegram_link' => array(
				'title' => __('Telegram Link', 'woocommerce-custom-payment-gateway'),
				'type' => 'text',
				'description' => __('Enter the Telegram link that will be shown to the customer after they place an order.', 'woocommerce-custom-payment-gateway'),
				'default' => '',
      ),
        'phone_number' => array(
				'title' => __('Phone Number', 'woocommerce-custom-payment-gateway'),
				'type' => 'text',
				'description' => __('Enter the phone number that will be shown to the customer after they place an order.', 'woocommerce-custom-payment-gateway'),
				'default' => '',
      ),
		);
	}
	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_options() {
		?>
		<h3><?php _e( 'Custom Payment Settings', 'woocommerce-other-payment-gateway' ); ?></h3>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<table class="form-table">
							<?php $this->generate_settings_html();?>
						</table><!--/.form-table-->
					</div>
					<div id="postbox-container-1" class="postbox-container">
	                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

     							<div class="postbox ">
	                                <h3 class="hndle"><span><i class="dashicons dashicons-update"></i>&nbsp;&nbsp;Upgrade to Pro</span></h3>
                                    <hr>
	                                <div class="inside">
	                                    <div class="support-widget">
	                                        <ul>
	                                            <li>» Full Form Builder</li>
	                                            <li>» Create Unlimited Custom Gateways</li>
	                                            <li>» Custom Gateway Icon</li>
	                                            <li>» Order Status After Checkout</li>
	                                            <li>» Custom API Requests</li>
	                                            <li>» Payment Information in Order’s Email</li>
	                                            <li>» Debugging Mode</li>
	                                            <li>» Auto Hassle-Free Updates</li>
	                                            <li>» High Priority Customer Support</li>
	                                        </ul>
											<a href="https://wpruby.com/plugin/woocommerce-custom-payment-gateway-pro/?utm_source=custom-payment-lite&utm_medium=widget&utm_campaign=freetopro" class="button wpruby_button" target="_blank"><span class="dashicons dashicons-star-filled"></span> Upgrade Now</a>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="postbox ">
	                                <h3 class="hndle"><span><i class="dashicons dashicons-editor-help"></i>&nbsp;&nbsp;Plugin Support</span></h3>
                                    <hr>
	                                <div class="inside">
	                                    <div class="support-widget">
	                                        <p>
	                                        <img style="width: 70%;margin: 0 auto;position: relative;display: inherit;" src="https://wpruby.com/wp-content/uploads/2016/03/wpruby_logo_with_ruby_color-300x88.png">
	                                        <br/>
	                                        Got a Question, Idea, Problem or Praise?</p>
	                                        <ul>
												<li>» Please leave us a <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-other-payment-gateway?filter=5#postform">★★★★★</a> rating.</li>
	                                            <li>» <a href="https://wpruby.com/submit-ticket/" target="_blank">Support Request</a></li>
	                                            <li>» <a href="https://wpruby.com/knowledgebase_category/woocommerce-custom-payment-gateway-pro/" target="_blank">Documentation and Common issues.</a></li>
	                                            <li>» <a href="https://wpruby.com/plugins/" target="_blank">Our Plugins Shop</a></li>
	                                        </ul>

	                                    </div>
	                                </div>
	                            </div>

	                            <div class="postbox rss-postbox">
	    								<h3 class="hndle"><span><i class="fa fa-wordpress"></i>&nbsp;&nbsp;WPRuby Blog</span></h3>
                                        <hr>
	    								<div class="inside">
											<div class="rss-widget">
												<?php
	    											wp_widget_rss_output(array(
	    													'url' => 'https://wpruby.com/feed/',
	    													'title' => 'WPRuby Blog',
	    													'items' => 3,
	    													'show_summary' => 0,
	    													'show_author' => 0,
	    													'show_date' => 1,
	    											));
	    										?>
	    									</div>
	    								</div>
	    						</div>

	                        </div>
	                    </div>
                    </div>
				</div>
				<div class="clear"></div>
				<style type="text/css">
				.wpruby_button{
					background-color:#4CAF50 !important;
					border-color:#4CAF50 !important;
					color:#ffffff !important;
					width:100%;
					text-align:center;
					height:35px !important;
					font-size:12pt !important;
				}
                .wpruby_button .dashicons {
                    padding-top: 5px;
                }
				</style>
				<?php
	}

	public function validate_fields() {
	  //   if($this->text_box_required === 'no'){
	  //       return true;
    //     }

	  //   $textbox_value = (isset($_POST['other_payment-admin-note']))? trim($_POST['other_payment-admin-note']): '';
		// if($textbox_value === ''){
		// 	wc_add_notice( __('Please, complete the payment information.','woocommerce-custom-payment-gateway'), 'error');
		// 	return false;
    //     }
		return true;
	}

	public function process_payment( $order_id ) {
    global $woocommerce;
    $order = new WC_Order( $order_id );

    // Get Telegram link and phone number
    $telegram_link = $this->get_option('telegram_link');
    $phone_number = $this->get_option('phone_number');

    // Add Telegram link and phone number to order notes
    $order->add_order_note("Telegram Link: " . $telegram_link);
    $order->add_order_note("Phone Number: " . $phone_number);

    // Mark as on-hold (we're awaiting the cheque)
    $order->update_status('on-hold', __( 'Awaiting cheque payment', 'woocommerce' ));

    // Reduce stock levels
    $order->reduce_order_stock();

    // Remove cart
    $woocommerce->cart->empty_cart();

    // Return thankyou redirect
    return array(
        'result' => 'success',
        'redirect' => $this->get_return_url( $order )
    );
	}

	public function payment_fields(){
	}

	public function enqueue_my_plugin_styles() {
    wp_enqueue_style( 'my-plugin-style', plugins_url( 'styles.css', __FILE__ ) );
	}

	public function calculate_installment_price() {
		if(isset(WC()->cart)) {
			$total_price = WC()->cart->total;
			// installment price per month in year is 35% of total price
			$installment_price = ($total_price + $total_price/100*35)/12;
			$formatted_price = number_format($installment_price, 0, '.', ' ');
			return "Installment plan <span class='seller-installment-price'>" . $formatted_price . "</span> UZS per month";
		} else {
			return "Installment plan";
		}
	}

	public function display_order_data($order_id) {
		$order = wc_get_order($order_id);
		$notes = $order->get_customer_order_notes();
		$telegram_link = '';
		$phone_number = '';

		foreach ($notes as $note) {
				if (strpos($note->comment_content, 'Telegram Link:') !== false) {
						$telegram_link = str_replace('Telegram Link: ', '', $note->comment_content);
				}
				if (strpos($note->comment_content, 'Phone Number:') !== false) {
						$phone_number = str_replace('Phone Number: ', '', $note->comment_content);
				}
		}

		if ($telegram_link || $phone_number) {
			echo '<h2>Additional Information</h2>';
			if ($telegram_link) {
				echo '<p>Telegram Link: ' . $telegram_link . '</p>';
			}
			if ($phone_number) {
				echo '<p>Phone Number: ' . $phone_number . '</p>';
			}
		}
  }
}
