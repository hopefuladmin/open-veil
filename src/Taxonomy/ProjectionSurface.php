<?php
namespace OpenVeil\Taxonomy;

/**
 * Projection Surface Taxonomy
 * 
 * Registers the Projection Surface taxonomy for Protocol and Trial post types
 */
class ProjectionSurface {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register']);
    }
    
    /**
     * Register the Projection Surface taxonomy
     */
    public function register() {
        $labels = [
            'name'                       => _x('Projection Surfaces', 'Taxonomy general name', 'open-veil'),
            'singular_name'              => _x('Projection Surface', 'Taxonomy singular name', 'open-veil'),
            'search_items'               => __('Search Projection Surfaces', 'open-veil'),
            'popular_items'              => __('Popular Projection Surfaces', 'open-veil'),
            'all_items'                  => __('All Projection Surfaces', 'open-veil'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Projection Surface', 'open-veil'),
            'update_item'                => __('Update Projection Surface', 'open-veil'),
            'add_new_item'               => __('Add New Projection Surface', 'open-veil'),
            'new_item_name'              => __('New Projection Surface Name', 'open-veil'),
            'separate_items_with_commas' => __('Separate projection surfaces with commas', 'open-veil'),
            'add_or_remove_items'        => __('Add or remove projection surfaces', 'open-veil'),
            'choose_from_most_used'      => __('Choose from the most used projection surfaces', 'open-veil'),
            'not_found'                  => __('No projection surfaces found.', 'open-veil'),
            'menu_name'                  => __('Projection Surfaces', 'open-veil'),
        ];
        
        $args = [
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'projection-surface'],
            'show_in_rest'          => true,
        ];
        
        register_taxonomy('projection_surface', ['protocol', 'trial'], $args);
        
        // Add default terms
        $default_terms = [
            'Flat non-reflective',
            'Fabric',
            'Concrete',
            'Closet door',
        ];
        
        foreach ($default_terms as $term) {
            if (!term_exists($term, 'projection_surface')) {
                wp_insert_term($term, 'projection_surface');
            }
        }
    }
}
