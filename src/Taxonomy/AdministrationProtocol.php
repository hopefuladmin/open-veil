<?php
declare(strict_types=1);
namespace OpenVeil\Taxonomy;

/**
 * Administration Protocol Taxonomy
 * 
 * Registers the Administration Protocol taxonomy for Protocol and Trial post types.
 * 
 * @package OpenVeil\Taxonomy
 */
class AdministrationProtocol {
   /**
    * Sets up action to register the taxonomy.
    */
   public function __construct() {
       add_action('init', [$this, 'register']);
   }
   
   /**
    * Creates the Administration Protocol taxonomy and adds default terms.
    *
    * @return void
    */
   public function register(): void {
       $labels = [
           'name'                       => _x('Administration Protocols', 'Taxonomy general name', 'open-veil'),
           'singular_name'              => _x('Administration Protocol', 'Taxonomy singular name', 'open-veil'),
           'search_items'               => __('Search Administration Protocols', 'open-veil'),
           'popular_items'              => __('Popular Administration Protocols', 'open-veil'),
           'all_items'                  => __('All Administration Protocols', 'open-veil'),
           'parent_item'                => null,
           'parent_item_colon'          => null,
           'edit_item'                  => __('Edit Administration Protocol', 'open-veil'),
           'update_item'                => __('Update Administration Protocol', 'open-veil'),
           'add_new_item'               => __('Add New Administration Protocol', 'open-veil'),
           'new_item_name'              => __('New Administration Protocol Name', 'open-veil'),
           'separate_items_with_commas' => __('Separate administration protocols with commas', 'open-veil'),
           'add_or_remove_items'        => __('Add or remove administration protocols', 'open-veil'),
           'choose_from_most_used'      => __('Choose from the most used administration protocols', 'open-veil'),
           'not_found'                  => __('No administration protocols found.', 'open-veil'),
           'menu_name'                  => __('Administration Protocols', 'open-veil'),
       ];
       
       $args = [
           'hierarchical'          => true,
           'labels'                => $labels,
           'show_ui'               => true,
           'show_admin_column'     => true,
           'query_var'             => true,
           'rewrite'               => ['slug' => 'administration-protocol'],
           'show_in_rest'          => true,
       ];
       
       register_taxonomy('administration_protocol', ['protocol', 'trial'], $args);
       
       // Add default terms
       $taxonomy_term_check = \OpenVeil\ACF\Options::get_option('taxonomy_term_check', true);
       
       if ($taxonomy_term_check) {
           $default_terms = [
               'Single inhale',
               'Two inhales',
               'Multiple spaced',
               'Continuous',
           ];
           
           foreach ($default_terms as $term) {
               if (!term_exists($term, 'administration_protocol')) {
                   wp_insert_term($term, 'administration_protocol');
               }
           }
       }
   }
}
