<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://boms-it.pl/boxofhope
 * @since             1.0.0
 * @package           Boxofhope_Return_Label_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       BoxOfHope Return Label Plugin
 * Plugin URI:        https://boxofhope.pl/wp-plugin
 * Description:       By joining our initiative, you are helping to maintain the natural environment by promoting the idea of a closed-loop economy. With your commitment, we can reduce waste and make better use of resources. Together, we can do a lot of good for our planet.
That's why we encourage you to take advantage of this functionality and enable your customers to easily donate unwanted items to charity. Together, we can work for the benefit of the local community and the natural environment, making the world a better place.
 * Version:           1.0.0
 * Author:            Leonid Moshko
 * Author URI:        https://boms-it.pl/boxofhope
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       boxofhope-return-label-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOXOFHOPE_RETURN_LABEL_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-boxofhope-return-label-plugin-activator.php
 */
function activate_boxofhope_return_label_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-boxofhope-return-label-plugin-activator.php';
	Boxofhope_Return_Label_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-boxofhope-return-label-plugin-deactivator.php
 */
function deactivate_boxofhope_return_label_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-boxofhope-return-label-plugin-deactivator.php';
	Boxofhope_Return_Label_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_boxofhope_return_label_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_boxofhope_return_label_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-boxofhope-return-label-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_boxofhope_return_label_plugin() {

	$plugin = new Boxofhope_Return_Label_Plugin();
	$plugin->run();

}
run_boxofhope_return_label_plugin();
