<?php
declare(strict_types=1);
namespace OpenVeil\PostType;

/**
 * Protocol Post Type
 * 
 * Registers and manages the Protocol custom post type.
 * 
 * @package OpenVeil\PostType
 */
class Protocol {
  /**
   * Sets up actions and filters for the Protocol post type.
   */
  public function __construct() {
      add_action('init', [$this, 'register']);
      add_action('init', [$this, 'register_meta']);
      add_filter('manage_protocol_posts_columns', [$this, 'add_columns']);
      add_action('manage_protocol_posts_custom_column', [$this, 'render_columns'], 10, 2);
  }
  
  /**
   * Creates the Protocol custom post type with all required labels and settings.
   *
   * @return void
   */
  public function register(): void {
      $labels = [
          'name'                  => _x('Protocols', 'Post type general name', 'open-veil'),
          'singular_name'         => _x('Protocol', 'Post type singular name', 'open-veil'),
          'menu_name'             => _x('Protocols', 'Admin Menu text', 'open-veil'),
          'name_admin_bar'        => _x('Protocol', 'Add New on Toolbar', 'open-veil'),
          'add_new'               => __('Add New', 'open-veil'),
          'add_new_item'          => __('Add New Protocol', 'open-veil'),
          'new_item'              => __('New Protocol', 'open-veil'),
          'edit_item'             => __('Edit Protocol', 'open-veil'),
          'view_item'             => __('View Protocol', 'open-veil'),
          'all_items'             => __('All Protocols', 'open-veil'),
          'search_items'          => __('Search Protocols', 'open-veil'),
          'parent_item_colon'     => __('Parent Protocols:', 'open-veil'),
          'not_found'             => __('No protocols found.', 'open-veil'),
          'not_found_in_trash'    => __('No protocols found in Trash.', 'open-veil'),
          'featured_image'        => _x('Protocol Cover Image', 'Overrides the "Featured Image" phrase', 'open-veil'),
          'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'open-veil'),
          'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'open-veil'),
          'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'open-veil'),
          'archives'              => _x('Protocol archives', 'The post type archive label used in nav menus', 'open-veil'),
          'insert_into_item'      => _x('Insert into protocol', 'Overrides the "Insert into post" phrase', 'open-veil'),
          'uploaded_to_this_item' => _x('Uploaded to this protocol', 'Overrides the "Uploaded to this post" phrase', 'open-veil'),
          'filter_items_list'     => _x('Filter protocols list', 'Screen reader text for the filter links heading on the post type listing screen', 'open-veil'),
          'items_list_navigation' => _x('Protocols list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'open-veil'),
          'items_list'            => _x('Protocols list', 'Screen reader text for the items list heading on the post type listing screen', 'open-veil'),
      ];
      
      $args = [
          'labels'             => $labels,
          'public'             => true,
          'publicly_queryable' => true,
          'show_ui'            => true,
          'show_in_menu'       => true,
          'query_var'          => true,
          'rewrite'            => ['slug' => 'protocol'],
          'capability_type'    => 'post',
          'has_archive'        => true,
          'hierarchical'       => false,
          'menu_position'      => null,
          'menu_icon'          => 'dashicons-clipboard',
          'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
          'show_in_rest'       => true,
      ];
      
      register_post_type('protocol', $args);
  }
  
  /**
   * Registers metadata fields to expose them in the REST API.
   *
   * @return void
   */
  public function register_meta(): void {
      register_post_meta('protocol', 'laser_wavelength', [
          'show_in_rest' => true,
          'single' => true,
          'type' => 'integer',
          'sanitize_callback' => [$this, 'sanitize_integer'],
          'auth_callback' => function() {
              return current_user_can('edit_posts');
          }
      ]);
      
      register_post_meta('protocol', 'laser_power', [
          'show_in_rest' => true,
          'single' => true,
          'type' => 'number',
          'sanitize_callback' => [$this, 'sanitize_float'],
          'auth_callback' => function() {
              return current_user_can('edit_posts');
          }
      ]);
      
      register_post_meta('protocol', 'substance_dose', [
          'show_in_rest' => true,
          'single' => true,
          'type' => 'number',
          'sanitize_callback' => [$this, 'sanitize_float'],
          'auth_callback' => function() {
              return current_user_can('edit_posts');
          }
      ]);
      
      register_post_meta('protocol', 'projection_distance', [
          'show_in_rest' => true,
          'single' => true,
          'type' => 'number',
          'sanitize_callback' => [$this, 'sanitize_float'],
          'auth_callback' => function() {
              return current_user_can('edit_posts');
          }
      ]);
  }
  
  /**
   * Sanitizes an integer value.
   * 
   * @param mixed $value The value to sanitize
   * @return int Sanitized value
   */
  public function sanitize_integer($value): int {
      return absint($value);
  }
  
  /**
   * Sanitizes a float value.
   * 
   * @param mixed $value The value to sanitize
   * @return float Sanitized value
   */
  public function sanitize_float($value): float {
      return floatval($value);
  }
  
  /**
   * Adds custom columns to the Protocol post type admin list.
   * 
   * @param array $columns Existing columns
   * @return array Modified columns
   */
  public function add_columns(array $columns): array {
      $columns['laser_wavelength'] = __('Wavelength (nm)', 'open-veil');
      $columns['laser_power'] = __('Power (mW)', 'open-veil');
      $columns['substance'] = __('Substance', 'open-veil');
      $columns['trials'] = __('Trials', 'open-veil');
      
      return $columns;
  }
  
  /**
   * Renders content for custom columns in the Protocol post type admin list.
   * 
   * @param string $column Column name
   * @param int $post_id Post ID
   * @return void
   */
  public function render_columns(string $column, int $post_id): void {
      switch ($column) {
          case 'laser_wavelength':
              echo get_post_meta($post_id, 'laser_wavelength', true) ?: '-';
              break;
              
          case 'laser_power':
              echo get_post_meta($post_id, 'laser_power', true) ?: '-';
              break;
              
          case 'substance':
              $terms = get_the_terms($post_id, 'substance');
              if (!empty($terms) && !is_wp_error($terms)) {
                  $substances = [];
                  foreach ($terms as $term) {
                      $substances[] = $term->name;
                  }
                  echo implode(', ', $substances);
              } else {
                  echo '-';
              }
              break;
              
          case 'trials':
              $trials = get_posts([
                  'post_type' => 'trial',
                  'meta_query' => [
                      [
                          'key' => 'protocol_id',
                          'value' => $post_id,
                          'compare' => '=',
                      ]
                  ],
                  'posts_per_page' => -1,
                  'fields' => 'ids',
              ]);
              
              echo count($trials);
              break;
      }
  }
}
