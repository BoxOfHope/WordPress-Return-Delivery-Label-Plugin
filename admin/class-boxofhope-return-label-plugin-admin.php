<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://boms-it.pl/boxofhope
 * @since      1.0.0
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/admin
 * @author     Leonid Moshko <leomoshko@gmail.com>
 */
class Boxofhope_Return_Label_Plugin_Admin {

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
		 * defined in Boxofhope_Return_Label_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Boxofhope_Return_Label_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/boxofhope-return-label-plugin-admin.css', array(), $this->version, 'all' );

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
		 * defined in Boxofhope_Return_Label_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Boxofhope_Return_Label_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/boxofhope-return-label-plugin-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function adminBoxOfHope() {
		include 'partials/boxofhope-return-label-plugin-admin-display.php';
	}
	
	public function registerBoxOfHopeMenu() {
		add_menu_page(
			__('BoxOfHope Return Label Plugin Configuration', 'boxofhope-return-label-plugin'),
			'BoxOfHope',
			'manage_options',
			'boh_return_plugin_configuration',
			[ &$this, 'adminBoxOfHope' ],
			 plugin_dir_url( __FILE__ ) . '../img/ocon.png'
		);
	}

	public function boxOfHopePluginConfigurationFields() {
		register_setting(
			'boh_return_label_plugin_configuration',
			'boh_return_label_plugin_api_key',
			array(
				'sanitize_callback' => [ &$this, 'bohReturnLabelPluginApiKeyValidate' ]
			)

		);

		register_setting(
			'boh_return_label_plugin_configuration',
			'boh_return_label_plugin_is_production',
			array(
				'sanitize_callback' => [ &$this, 'BohReturnLabelPluginSanitizeCheckbox' ]
			)
		);

		add_settings_section(
			'boh_return_label_plugin_configuration_section_id',
			'',
			'',
			'boh_return_label_plugin'
		);

		add_settings_field(
			'boh_return_label_plugin_api_key',
			__('BoxOfHope API-KEY', 'boxofhope-return-label-plugin'),
			[ &$this, 'getBohReturnLabelPluginApiKeyField'],
			'boh_return_label_plugin',
			'boh_return_label_plugin_configuration_section_id',
			array(
				'label_for' => 'boh_return_label_plugin_api_key',
				'class' => 'boh-configuration-class',
				'name' => 'boh_return_label_plugin_api_key',
			)
		);

		add_settings_field(
			'boh_return_label_plugin_is_production',
			__('Is Production Mode', 'boxofhope-return-label-plugin'),
			[ &$this, 'getBohReturnLabelPluginIsProductionField'],
			'boh_return_label_plugin',
			'boh_return_label_plugin_configuration_section_id',
			array(
				'name' => 'boh_return_label_plugin_is_production',
				'label_text' => ''
			)
		);
	}

	public function BohReturnLabelPluginSanitizeCheckbox( $value ) {
		return ( 'on' == $value ) ? 'yes' : 'no';
	}

	public function getBohReturnLabelPluginApiKeyField($args) {
		printf(
			'<input type="text" id="%s" name="%s" value="%s" />',
			esc_attr( $args[ 'name' ] ),
			esc_attr( $args[ 'name' ] ),
			esc_attr( get_option( $args[ 'name' ] ) )
		);
	}

	public function getBohReturnLabelPluginIsProductionField($args) {
		printf(
			'<label for="%s-id"><input type="checkbox" name="%s" id="%s-id" %s> %s</label>',
			esc_attr($args[ 'name' ]),
			esc_attr($args[ 'name' ]),
			esc_attr($args[ 'name' ]),
			checked( get_option( esc_attr($args[ 'name' ]) ), 'yes', false ),
			esc_attr($args[ 'label_text' ])
		);
	}

	public function bohReturnLabelPluginApiKeyValidate($input) {
		$input = sanitize_text_field($input);

		if($input === '' OR $input === null OR strlen($input) < 10) {
			add_settings_error(
				'boh_return_label_plugin_configuration_errors',
				'not-empty',
				 __('The field cannot be empty and must contain at least 10 characters', 'boxofhope-return-label-plugin')
			);

			$input = get_option( 'boh_return_label_plugin_api_key' );
		}

		return $input;
	}

	public function BohReturnLabelPluginConfigurationSaveSuccess() {
		$settings_errors = get_settings_errors( 'boh_return_label_plugin_configuration_errors' );

		if ( ! empty( $settings_errors ) ) {
			return;
		}

		if(
			isset( $_GET[ 'page' ] )
			&& 'boh_return_plugin_configuration' == $_GET[ 'page' ]
			&& isset( $_GET[ 'settings-updated' ] )
			&& true == $_GET[ 'settings-updated' ]
		) {
			echo '<div class="notice notice-success is-dismissible"><p>' . __('Your Configuration updated!', 'boxofhope-return-label-plugin') . '</p></div>';
		}
	}
}
 