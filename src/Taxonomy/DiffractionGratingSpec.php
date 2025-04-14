<?php
namespace OpenVeil\Taxonomy;

/**
 * Diffraction Grating Spec Taxonomy
 * 
 * Registers the Diffraction Grating Spec taxonomy for Protocol and Trial post types
 */
class DiffractionGratingSpec {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register']);
    }
    
    /**
     * Register the Diffraction Grating Spec taxonomy
     */
    public function register() {
        $labels = [
            'name'                       => _x('Diffraction Grating Specs', 'Taxonomy general name', 'open-veil'),
            'singular_name'              => _x('Diffraction Grating Spec', 'Taxonomy singular name', 'open-veil'),
            'search_items'               => __('Search Diffraction Grating Specs', 'open-veil'),
            'popular_items'              => __('Popular Diffraction Grating Specs', 'open-veil'),
            'all_items'                  => __('All Diffraction Grating Specs', 'open-veil'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Diffraction Grating Spec', 'open-veil'),
            'update_item'                => __('Update Diffraction Grating Spec', 'open-veil'),
            'add_new_item'               => __('Add New Diffraction Grating Spec', 'open-veil'),
            'new_item_name'              => __('New Diffraction Grating Spec Name', 'open-veil'),
            'separate_items_with_commas' => __('Separate diffraction grating specs with commas', 'open-veil'),
            'add_or_remove_items'        => __('Add or remove diffraction grating specs', 'open-veil'),
            'choose_from_most_used'      => __('Choose from the most used diffraction grating specs', 'open-veil'),
            'not_found'                  => __('No diffraction grating specs found.', 'open-veil'),
            'menu_name'                  => __('Diffraction Grating Specs', 'open-veil'),
        ];
        
        $args = [
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'diffraction-grating-spec'],
            'show_in_rest'          => true,
        ];
        
        register_taxonomy('diffraction_grating_spec', ['protocol', 'trial'], $args);
        
        // Add default terms
        $default_terms = [
            'Standard',
            'Custom',
            'High Precision',
            'Low Precision',
        ];
        
        foreach ($default_terms as $term) {
            if (!term_exists($term, 'diffraction_grating_spec')) {
                wp_insert_term($term, 'diffraction_grating_spec');
            }
        }
    }
}
