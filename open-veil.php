<?php

declare(strict_types=1);

/**
 * Plugin Name: Open Veil
 * Plugin URI: https://carmelosantana.org/openveil
 * Description: A WordPress plugin designed to structure, collect, and share experimental protocol data and community-submitted trials.
 * Version: 0.1.9
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
define('OPEN_VEIL_VERSION', '0.1.9');
define('OPEN_VEIL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OPEN_VEIL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
require_once OPEN_VEIL_PLUGIN_DIR . 'vendor/autoload.php';

/**
 * Initializes all the main plugin components including
 * post types, taxonomies, REST API, ACF fields, and template support.
 *
 * @return void
 */
function open_veil_init(): void
{
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
        new \OpenVeil\ACF\Options();
    }

    // Register JSON-LD and CSL-JSON support
    new \OpenVeil\Template\Loader();

    // Register block editor support
    if (function_exists('register_block_type')) {
        new \OpenVeil\BlockEditor\TemplateSupport();
    }

    // Register shortcodes
    new \OpenVeil\Shortcode\Shortcodes();

    // Add theme support for block templates
    add_theme_support('block-templates');
}
add_action('plugins_loaded', 'open_veil_init');

// Activation hook
register_activation_hook(__FILE__, function (): void {
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
register_deactivation_hook(__FILE__, function (): void {
    // Flush rewrite rules
    flush_rewrite_rules();
});
