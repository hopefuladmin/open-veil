<?php
namespace OpenVeil\Taxonomy;

/**
 * Administration Method Taxonomy
 * 
 * Registers the Administration Method taxonomy for Protocol and Trial post types
 */
class AdministrationMethod {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register']);
    }
    
    /**
     * Register the Administration Method taxonomy
     */
    public function register() {
        $labels = [
            'name'                       => _x('Administration Methods', 'Taxonomy general name', 'open-veil'),
            'singular_name'              => _x('Administration Method', 'Taxonomy singular name', 'open-veil'),
            'search_items'               => __('Search Administration Methods', 'open-veil'),
            'popular_items'              => __('Popular Administration Methods', 'open-veil'),
            'all_items'                  => __('All Administration Methods', 'open-veil'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Administration Method', 'open-veil'),
            'update_item'                => __('Update Administration Method', 'open-veil'),
            'add_new_item'               => __('Add New Administration Method', 'open-veil'),
            'new_item_name'              => __('New Administration Method Name', 'open-veil'),
            'separate_items_with_commas' => __('Separate administration methods with commas', 'open-veil'),
            'add_or_remove_items'        => __('Add or remove administration methods', 'open-veil'),
            'choose_from_most_used'      => __('Choose from the most used administration methods', 'open-veil'),
            'not_found'                  => __('No administration methods found.', 'open-veil'),
            'menu_name'                  => __('Administration Methods', 'open-veil'),
        ];
        
        $args = [
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'administration-method'],
            'show_in_rest'          => true,
        ];
        
        register_taxonomy('administration_method', ['protocol', 'trial'], $args);
        
        // Add default terms
        $default_terms = [
            'Inhalation',
            'Oral',
            'Sublingual',
            'Intranasal',
        ];
        
        foreach ($default_terms as $term) {
            if (!term_exists($term, 'administration_method')) {
                wp_insert_term($term, 'administration_method');
            }
        }
    }
}
