<?php
declare(strict_types=1);
namespace OpenVeil\Taxonomy;

/**
 * Laser Class Taxonomy
 * 
 * Registers the Laser Class taxonomy for Protocol and Trial post types.
 * 
 * @package OpenVeil\Taxonomy
 */
class LaserClass {
   /**
    * Sets up action to register the taxonomy.
    */
   public function __construct() {
       add_action('init', [$this, 'register']);
   }
   
   /**
    * Creates the Laser Class taxonomy and adds default terms.
    *
    * @return void
    */
   public function register(): void {
       $labels = [
           'name'                       => _x('Laser Classes', 'Taxonomy general name', 'open-veil'),
           'singular_name'              => _x('Laser Class', 'Taxonomy singular name', 'open-veil'),
           'search_items'               => __('Search Laser Classes', 'open-veil'),
           'popular_items'              => __('Popular Laser Classes', 'open-veil'),
           'all_items'                  => __('All Laser Classes', 'open-veil'),
           'parent_item'                => null,
           'parent_item_colon'          => null,
           'edit_item'                  => __('Edit Laser Class', 'open-veil'),
           'update_item'                => __('Update Laser Class', 'open-veil'),
           'add_new_item'               => __('Add New Laser Class', 'open-veil'),
           'new_item_name'              => __('New Laser Class Name', 'open-veil'),
           'separate_items_with_commas' => __('Separate laser classes with commas', 'open-veil'),
           'add_or_remove_items'        => __('Add or remove laser classes', 'open-veil'),
           'choose_from_most_used'      => __('Choose from the most used laser classes', 'open-veil'),
           'not_found'                  => __('No laser classes found.', 'open-veil'),
           'menu_name'                  => __('Laser Classes', 'open-veil'),
       ];
       
       $args = [
           'hierarchical'          => true,
           'labels'                => $labels,
           'show_ui'               => true,
           'show_admin_column'     => true,
           'query_var'             => true,
           'rewrite'               => ['slug' => 'laser-class'],
           'show_in_rest'          => true,
       ];
       
       register_taxonomy('laser_class', ['protocol', 'trial'], $args);
       
       // Add default terms
       $taxonomy_term_check = \OpenVeil\ACF\Options::get_option('taxonomy_term_check', true);
       
       if ($taxonomy_term_check) {
           $default_terms = [
               'Class 1',
               'Class 2',
               'Class 3R',
               'Class 3B',
               'Class 4',
           ];
           
           foreach ($default_terms as $term) {
               if (!term_exists($term, 'laser_class')) {
                   wp_insert_term($term, 'laser_class');
               }
           }
       }
   }
}
