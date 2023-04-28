<?php

/**
 * Fired during plugin activation
 *
 * @link       https://boms-it.pl/boxofhope
 * @since      1.0.0
 *
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Boxofhope_Return_Label_Plugin
 * @subpackage Boxofhope_Return_Label_Plugin/includes
 * @author     Leonid Moshko <leomoshko@gmail.com>
 */
class Boxofhope_Return_Label_Plugin_Activator {
   public static function activate() {
      global $wpdb;
      $plugin_name_db_version = '1.0';

      $confifuration_table_name = $wpdb->prefix . 'boh_return_label';
      $order_table_name = $wpdb->prefix . 'wc_order_stats';

      $charset_collate = $wpdb->get_charset_collate();

      $query = "CREATE TABLE $confifuration_table_name (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_id BIGINT(20) UNSIGNED NOT NULL,
                    delivery_id VARCHAR(100) NULL DEFAULT '' ,
                    return_code VARCHAR(100) NULL DEFAULT '' ,
                    label_id VARCHAR(100) NULL DEFAULT '' ,
                    url_download TEXT NULL DEFAULT '' ,
                    created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (order_id) REFERENCES $order_table_name(order_id)
                ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      
      dbDelta($query);
      add_option( 'plugin_name_db_version', $plugin_name_db_version );
	}
}
