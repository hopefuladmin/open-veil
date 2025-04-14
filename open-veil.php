<?php
/**
 * Plugin Name: Open Veil
 * Plugin URI: https://carmelosantana.org/openveil
 * Description: A WordPress plugin designed to structure, collect, and share experimental protocol data and community-submitted trials.
 * Version: 0.1.0
 * Author: Carmelo Santana
 * Author URI: https://carmelosantana.org
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: open-veil
 * Domain Path: /languages
 * 
 * @package OpenVeil
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('OPEN_VEIL_VERSION', '0.1.0');
define('OPEN_VEIL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OPEN_VEIL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
require_once OPEN_VEIL_PLUGIN_DIR . 'vendor/autoload.php';

// Initialize the plugin
function open_veil_init() {
    // Register post types
    new \OpenVeil\PostType\Protocol();
    new \OpenVeil\PostType\Trial();
    
    // Register taxonomies
    new \OpenVeil\Taxonomy\Equipment();
    new \OpenVeil\Taxonomy\LaserClass();
    new \OpenVeil\Taxonomy\DiffractionGratingSpec();
    new \OpenVeil\Taxonomy\Substance();
    new \OpenVeil\Taxonomy\AdministrationMethod();
    new \OpenVeil\Taxonomy\AdministrationProtocol();
    new \OpenVeil\Taxonomy\ProjectionSurface();
    
    // Register REST API
    new \OpenVeil\API\Rest();
    
    // Register ACF fields
    if (class_exists('ACF')) {
        new \OpenVeil\ACF\Fields();
    }
    
    // Register admin pages
    new \OpenVeil\Admin\Settings();
    
    // Register templates
    new \OpenVeil\Template\Loader();
}
add_action('plugins_loaded', 'open_veil_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Create custom post types
    new \OpenVeil\PostType\Protocol();
    new \OpenVeil\PostType\Trial();
    
    // Create taxonomies
    new \OpenVeil\Taxonomy\Equipment();
    new \OpenVeil\Taxonomy\LaserClass();
    new \OpenVeil\Taxonomy\DiffractionGratingSpec();
    new \OpenVeil\Taxonomy\Substance();
    new \OpenVeil\Taxonomy\AdministrationMethod();
    new \OpenVeil\Taxonomy\AdministrationProtocol();
    new \OpenVeil\Taxonomy\ProjectionSurface();
    
    // Flush rewrite rules
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Flush rewrite rules
    flush_rewrite_rules();
});
