<?php

/**
 * The woocommerce-specific functionality of the plugin.
 *
 * @link       https://boms-it.pl/boxofhope
 * @since      1.0.0
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/woocommerce
 */

/**
 * The woocommerce-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/woocommerce
 * @author     Leonid Moshko <leomoshko@gmail.com>
 */
class Boxofhope_Return_Label_Plugin_Woocomerce {

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

	}

	public function woocomerceOrderBoxBoxOfHope() {
		global $wpdb;
		$order_id = filter_input(INPUT_GET, 'post');
		$existing_record = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "boh_return_label WHERE order_id = " . $order_id);
		include 'partials/boxofhope-return-label-plugin-woocommerce-display.php';
	}

    public function BohReturnLabelPluginOrderViewBox()
	{
		add_meta_box(
			'boh_return_plugin_order_conten_box',
	        __( 'Collaboration with BoxOfHope - Return Label', 'boxofhope-return-label-plugin' ),
	        [ &$this, 'woocomerceOrderBoxBoxOfHope' ],
	        'shop_order',
	        'normal',
	        'low'
	    );
    }

	public function BohReturnLabelPluginGeneratLabelActions($actions)
	{
		$actions['boh_return_plugin_generate_label_action'] = __('Generate BoxOfHope label', 'boxofhope-return-label-plugin');

		global $wpdb;
		$order_id = filter_input(INPUT_GET, 'post');
		$existing_record = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "boh_return_label WHERE order_id = " . $order_id);
		if ($existing_record) {
			unset($actions['boh_return_plugin_generate_label_action']);
		}

		return $actions;
	}

	public function BohReturnLabelPluginDownloadLabelActions($actions)
	{
		$actions['boh_return_plugin_download_label_action'] = __('Download BoxOfHope label', 'boxofhope-return-label-plugin');

		global $wpdb;
		$order_id = filter_input(INPUT_GET, 'post');
		$existing_record = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "boh_return_label WHERE order_id = " . $order_id);
		if (!$existing_record) {
			unset($actions['boh_return_plugin_download_label_action']);
		}

		return $actions;
	}

	function BohReturnLabelPluginGenerateLabelActionsExecute($order)
	{
		global $wpdb;

		if (filter_input(INPUT_POST, 'wc_order_action') !== 'boh_return_plugin_generate_label_action') {
			return;
		}

		if ( ! function_exists( 'is_plugin_active' ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			die();
		}

		[ &$this, 'removeAllBohNotification'];

		if ($wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "boh_return_label WHERE order_id = " . $order->get_id())) {
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		}

		$api_key = get_option( 'boh_return_label_plugin_api_key' );

		if (!$api_key) {
			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_generate_label_api_key_error',
				__('Incorrect BoxOfHope Return Label Plugin configuration, verify the setting', 'boxofhope-return-label-plugin')
			);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		}

		$header = ["Content-Type" => "application/json"];
		$url = $this->getBoxOfHopeUrl();
		$returnData = json_encode($this->createRequestDataForGenerateDelivery($order->get_id()));

		$response = wp_remote_post($url . 'api-delivery-ecosystem/v.0.1/delivery/business/api-key/' . $api_key , [
			'headers' => $header,
            'body' => $returnData,
        ]);

		if ( is_wp_error( $response ) OR $response['response']['code'] == 404 ) {
			$responseBody = json_decode(wp_remote_retrieve_body( $response ), true);
			if ($responseBody['error'] && $responseBody['error'] === 'No such api key') {
				WC_Admin_Notices::add_custom_notice(
					'boh_return_label_plugin_generate_label_host_error',
					 sprintf( __( 'Sorry, an error occurred - %d - %s. Please generate new key in BoxOfHope.pl dashboard.', 'boxofhope-return-label-plugin' ), $responseBody['statusCode'], $responseBody['error'] )
				);
				wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
				exit;
			}

			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_generate_label_host_error',
				 sprintf( __( 'Sorry, an error occurred - %d - %s', 'boxofhope-return-label-plugin' ), $response['response']['code'], $response['response']['message'] )
			);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		} elseif ($response['response']['code'] == 400) {
			$responseBody = json_decode(wp_remote_retrieve_body( $response ), true);
			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_generate_label_host_error',
				 sprintf( __( 'Sorry, an error occurred - %d - %s', 'boxofhope-return-label-plugin' ), $responseBody['statusCode'], $responseBody['error'][0]['message'] )
			);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		} else {
			$responseArray = json_decode(wp_remote_retrieve_body( $response ), true);
			$packageBody = array(
				'order_id' => $order->get_id(),
				'delivery_id' => $responseArray['package_id'],
				'return_code' =>  $responseArray['reference_code']
				);

			$result = $wpdb->insert( $wpdb->prefix . 'boh_return_label', $packageBody );

			if ( $result === false ) {
				WC_Admin_Notices::add_custom_notice(
					'boh_return_label_plugin_generate_label_api_db_safe',
					__('We apologize for the inconvenience, but there seems to be an issue with setting up your environment. Please do not hesitate to contact us at <a href="mailto:support_plugins@boxofhope.pl">support_plugins@boxofhope.pl</a> for further assistance.', 'boxofhope-return-label-plugin')
				);
				wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
				exit;

			} else {
				WC_Admin_Notices::add_custom_notice(
					'boh_return_label_plugin_generate_label_api_sucess',
					__('The request for BoxOfHope label has been successfully submitted', 'boxofhope-return-label-plugin')
				);

				$order->add_order_note(__('The request for BoxOfHope label has been successfully submitted', 'boh_return_plugin_generate_label_action'));
			}
		}
	}

	function BohReturnLabelPluginDownloadLabelActionsExecute($order)
	{
		global $wpdb;

		if (filter_input(INPUT_POST, 'wc_order_action') !== 'boh_return_plugin_download_label_action') {
			return;
		}

		[ &$this, 'removeAllBohNotification'];

		$bohDeliveryLabelData = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "boh_return_label WHERE order_id = " . $order->get_id());

		if (!$bohDeliveryLabelData) {
			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_generate_label_generate_label_error',
				__('We apologize for the inconvenience, but there seems to be an issue with setting up your environment. Please do not hesitate to contact us at <a href="mailto:support_plugins@boxofhope.pl">support_plugins@boxofhope.pl</a> for further assistance.', 'boxofhope-return-label-plugin')
			);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		}

		$api_key = get_option( 'boh_return_label_plugin_api_key' );

		if (!$api_key) {
			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_generate_label_api_key_error',
				__('Incorrect BoxOfHope Return Label Plugin configuration, verify the setting', 'boxofhope-return-label-plugin')
			);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		}

		$deliveryId = $bohDeliveryLabelData->delivery_id;
		$url = $this->getBoxOfHopeUrl();

		$response = wp_remote_post($url . 'api-delivery-ecosystem/v.0.1/delivery/business/label/'. $deliveryId . '/api-key/' . $api_key);
		$responseArray = json_decode(wp_remote_retrieve_body( $response ), true);

		if ( is_wp_error( $response ) OR $response['response']['code'] == 404 ) {
			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_generate_label_host_error',
				 sprintf( __( 'Sorry, an error occurred - %d - %d', 'truemisha' ), $response['response']['code'], $response['response']['message'] )
				);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		} elseif ($responseArray['status'] !== 'queueing'){

			$label_url = $responseArray['url'];
			$file_name = 'return_label.pdf'; // Nazwa pliku, możesz dostosować do swoich potrzeb
			$file_content = file_get_contents($label_url);

			if ($file_content !== false) {
				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $file_name . '"');
				echo $file_content;
 	 			exit;
			} else {
				WC_Admin_Notices::add_custom_notice(
					'boh_return_label_plugin_download_label_file_error',
					__('Error downloading return label file.', 'boxofhope-return-label-plugin')
				);
				wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
				exit;
			}
		} else {
			WC_Admin_Notices::add_custom_notice(
				'boh_return_label_plugin_download_label_not_ready',
				__('I’m sorry, but your package is still in the preparation stage.', 'boxofhope-return-label-plugin')
			);
			wp_redirect(admin_url('post.php?post=' .  $order->get_id() . '&action=edit'));
			exit;
		}
	}

    public function cheduleRemoveAllBohNotification() {
		$delay_seconds = 30;
		$timestamp = time() + $delay_seconds;

		wp_schedule_single_event( $timestamp, 'removeAllBohNotificationEvent' );
	}

	public function removeAllBohNotification() {
		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_generate_label_api_key_error'
		);

		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_generate_label_host_error'
		);

		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_generate_label_api_sucess'
		);

		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_generate_label_api_db_safe'
		);

		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_download_label_not_ready'
		);

		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_download_label_file_error'
		);

		WC_Admin_Notices::remove_notice(
			'boh_return_label_plugin_generate_label_generate_label_error'
		);
	}

    private function createRequestDataForGenerateDelivery(int $order_id) {
		$order = new WC_Order( $order_id );
		$email = get_post_meta( $order_id, '_shipping_email', true ) ?: $order->get_billing_email();

		return [
			"pickup" => [
				"name" => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                "street" => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
                "postcode" => $order->get_shipping_postcode(),
                "city" => $order->get_shipping_city(),
                "email" => $email,
                "phone" => $order->get_billing_phone(),
                "company" => $order->get_shipping_company()
            ],
            "parcels" => [
				"width" => 10,
                "depth" => 10,
                "height" => 10,
                "weight" => 2,
                "value" => 100,
                "description" => "test"
			]
		];
	}

	private function getBoxOfHopeUrl(): string
	{
		$isProdMode = get_option( 'boh_return_label_plugin_is_production' );
		return $isProdMode ? 'https://boxofhope.pl/' : 'https://boh-stage.lessclub.dev/';
	}
}
 