<?php
/**
 * Plugin Name: MAM REAXML Properties
 * Plugin URI: https://github.com/AliSal92/mam-properties
 * Description: a Wordpress plugin to easily add properties to your website using REAXML feed. (requires ACF Pro to be installed and active)
 * Version: 1.0
 * Author: AliSal
 * Text Domain: mam-reaxml-properties
 * Author URI: https://github.com/AliSal92/
 * MAM Properties is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * MAM Properties is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MAM Properties. If not, see <http://www.gnu.org/licenses/>.
 */

namespace MAM;

use MAM\Plugin\Init;

/**
 * Prevent direct access
 */
defined('ABSPATH') or die('</3');

/**
 * Require once the Composer Autoload
 */
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
register_activation_hook( __FILE__ . '/ActivateDeactivate.php', 'activate_mam_property_plugin' );


/**
 * The code that runs during plugin deactivation
 */
register_activation_hook( __FILE__ . '/ActivateDeactivate.php', 'deactivate_mam_property_plugin' );

/**
 * Initialize and run all the core classes of the plugin
 */
if ( class_exists( 'MAM\Plugin\Init' ) ) {
    Init::register_services();
}