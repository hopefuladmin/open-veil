<?php
declare(strict_types=1);
namespace OpenVeil\Utility;

/**
 * Post Type Utility
 * 
 * Provides utility functions for working with Open Veil post types.
 * 
 * @package OpenVeil\Utility
 */
class PostTypeUtility {
    /**
     * Open Veil post types
     *
     * @var array
     */
    private static $post_types = ['protocol', 'trial'];
    
    /**
     * Checks if the current page is a front-end Open Veil page.
     * 
     * This includes single protocol/trial posts and their archive pages.
     *
     * @return bool True if on a front-end Open Veil page, false otherwise
     */
    public static function is_open_veil_page(): bool {
        // Not on front-end
        if (is_admin()) {
            return false;
        }
        
        // Check for singular posts or archive pages
        return (is_singular(self::$post_types) || is_post_type_archive(self::$post_types));
    }
    
    /**
     * Gets the array of Open Veil post types.
     *
     * @return array Array of post type names
     */
    public static function get_post_types(): array {
        return self::$post_types;
    }
}
