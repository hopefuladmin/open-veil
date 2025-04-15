<?php
declare(strict_types=1);
namespace OpenVeil\Taxonomy;

/**
 * Equipment Taxonomy
 * 
 * Registers the Equipment taxonomy for Protocol and Trial post types.
 * 
 * @package OpenVeil\Taxonomy
 */
class Equipment {
   /**
    * Sets up action to register the taxonomy.
    */
   public function __construct() {
       add_action('init', [$this, 'register']);
   }
   
   /**
    * Creates the Equipment taxonomy and adds default terms.
    *
    * @return void
    */
   public function register(): void {
       $labels = [
           'name'                       => _x('Equipment', 'Taxonomy general name', 'open-veil'),
           'singular_name'              => _x('Equipment', 'Taxonomy singular name', 'open-veil'),
           'search_items'               => __('Search Equipment', 'open-veil'),
           'popular_items'              => __('Popular Equipment', 'open-veil'),
           'all_items'                  => __('All Equipment', 'open-veil'),
           'parent_item'                => null,
           'parent_item_colon'          => null,
           'edit_item'                  => __('Edit Equipment', 'open-veil'),
           'update_item'                => __('Update Equipment', 'open-veil'),
           'add_new_item'               => __('Add New Equipment', 'open-veil'),
           'new_item_name'              => __('New Equipment Name', 'open-veil'),
           'separate_items_with_commas' => __('Separate equipment with commas', 'open-veil'),
           'add_or_remove_items'        => __('Add or remove equipment', 'open-veil'),
           'choose_from_most_used'      => __('Choose from the most used equipment', 'open-veil'),
           'not_found'                  => __('No equipment found.', 'open-veil'),
           'menu_name'                  => __('Equipment', 'open-veil'),
       ];
       
       $args = [
           'hierarchical'          => false,
           'labels'                => $labels,
           'show_ui'               => true,
           'show_admin_column'     => true,
           'update_count_callback' => '_update_post_term_count',
           'query_var'             => true,
           'rewrite'               => ['slug' => 'equipment'],
           'show_in_rest'          => true,
       ];
       
       register_taxonomy('equipment', ['protocol', 'trial'], $args);
       
       // Add default terms
       $taxonomy_term_check = \OpenVeil\ACF\Options::get_option('taxonomy_term_check', true);
       
       if ($taxonomy_term_check) {
           $default_terms = [
               'Laser',
               'Tripod',
               'Diffraction Grating',
               'Vape Pen',
               'Battery',
               'Non-Reflective Surface',
           ];
           
           foreach ($default_terms as $term) {
               if (!term_exists($term, 'equipment')) {
                   wp_insert_term($term, 'equipment');
               }
           }
       }
   }
}
