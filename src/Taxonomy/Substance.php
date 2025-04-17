<?php

declare(strict_types=1);

namespace OpenVeil\Taxonomy;

/**
 * Substance Taxonomy
 * 
 * Registers the Substance taxonomy for Protocol and Trial post types.
 * 
 * @package OpenVeil\Taxonomy
 */
class Substance
{
    /**
     * Sets up action to register the taxonomy.
     */
    public function __construct()
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Creates the Substance taxonomy and adds default terms.
     *
     * @return void
     */
    public function register(): void
    {
        $labels = [
            'name'                       => _x('Substances', 'Taxonomy general name', 'open-veil'),
            'singular_name'              => _x('Substance', 'Taxonomy singular name', 'open-veil'),
            'search_items'               => __('Search Substances', 'open-veil'),
            'popular_items'              => __('Popular Substances', 'open-veil'),
            'all_items'                  => __('All Substances', 'open-veil'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Substance', 'open-veil'),
            'update_item'                => __('Update Substance', 'open-veil'),
            'add_new_item'               => __('Add New Substance', 'open-veil'),
            'new_item_name'              => __('New Substance Name', 'open-veil'),
            'separate_items_with_commas' => __('Separate substances with commas', 'open-veil'),
            'add_or_remove_items'        => __('Add or remove substances', 'open-veil'),
            'choose_from_most_used'      => __('Choose from the most used substances', 'open-veil'),
            'not_found'                  => __('No substances found.', 'open-veil'),
            'menu_name'                  => __('Substances', 'open-veil'),
        ];

        $args = [
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => 'substance'],
            'show_in_rest'          => true,
        ];

        register_taxonomy('substance', ['protocol', 'trial'], $args);

        // Add default terms
        $taxonomy_term_check = \OpenVeil\ACF\Options::get_option('taxonomy_term_check', true);

        if ($taxonomy_term_check) {
            $default_terms = [
                'N,N-DMT',
                'LSD',
                'Psilocybin',
                'Mescaline',
            ];

            foreach ($default_terms as $term) {
                if (!term_exists($term, 'substance')) {
                    wp_insert_term($term, 'substance');
                }
            }
        }
    }
}
