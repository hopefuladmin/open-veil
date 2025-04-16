<?php
declare(strict_types=1);
namespace OpenVeil\PostType;

/**
* Trial Post Type
* 
* Registers and manages the Trial custom post type.
* 
* @package OpenVeil\PostType
*/
class Trial {
 /**
  * Sets up actions and filters for the Trial post type.
  */
 public function __construct() {
     add_action('init', [$this, 'register']);
     add_action('init', [$this, 'register_meta']);
     add_filter('manage_trial_posts_columns', [$this, 'add_columns']);
     add_action('manage_trial_posts_custom_column', [$this, 'render_columns'], 10, 2);
 }
 
 /**
  * Creates the Trial custom post type with all required labels and settings.
  *
  * @return void
  */
 public function register(): void {
     $labels = [
         'name'                  => _x('Trials', 'Post type general name', 'open-veil'),
         'singular_name'         => _x('Trial', 'Post type singular name', 'open-veil'),
         'menu_name'             => _x('Trials', 'Admin Menu text', 'open-veil'),
         'name_admin_bar'        => _x('Trial', 'Add New on Toolbar', 'open-veil'),
         'add_new'               => __('Add New', 'open-veil'),
         'add_new_item'          => __('Add New Trial', 'open-veil'),
         'new_item'              => __('New Trial', 'open-veil'),
         'edit_item'             => __('Edit Trial', 'open-veil'),
         'view_item'             => __('View Trial', 'open-veil'),
         'all_items'             => __('All Trials', 'open-veil'),
         'search_items'          => __('Search Trials', 'open-veil'),
         'parent_item_colon'     => __('Parent Trials:', 'open-veil'),
         'not_found'             => __('No trials found.', 'open-veil'),
         'not_found_in_trash'    => __('No trials found in Trash.', 'open-veil'),
         'featured_image'        => _x('Trial Cover Image', 'Overrides the "Featured Image" phrase', 'open-veil'),
         'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'open-veil'),
         'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'open-veil'),
         'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'open-veil'),
         'archives'              => _x('Trial archives', 'The post type archive label used in nav menus', 'open-veil'),
         'insert_into_item'      => _x('Insert into trial', 'Overrides the "Insert into post" phrase', 'open-veil'),
         'uploaded_to_this_item' => _x('Uploaded to this trial', 'Overrides the "Uploaded to this post" phrase', 'open-veil'),
         'filter_items_list'     => _x('Filter trials list', 'Screen reader text for the filter links heading on the post type listing screen', 'open-veil'),
         'items_list_navigation' => _x('Trials list navigation', 'Screen reader text for the pagination heading on the post type listing screen', 'open-veil'),
         'items_list'            => _x('Trials list', 'Screen reader text for the items list heading on the post type listing screen', 'open-veil'),
     ];
     
     $args = [
         'labels'             => $labels,
         'public'             => true,
         'publicly_queryable' => true,
         'show_ui'            => true,
         'show_in_menu'       => true,
         'query_var'          => true,
         'rewrite'            => ['slug' => 'trial'],
         'capability_type'    => 'post',
         'has_archive'        => true,
         'hierarchical'       => false,
         'menu_position'      => null,
         'menu_icon'          => 'dashicons-chart-area',
         'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
         'show_in_rest'       => true,
     ];
     
     register_post_type('trial', $args);
 }
 
 /**
  * Registers metadata fields to expose them in the REST API.
  *
  * @return void
  */
 public function register_meta(): void {
     // Basic trial fields
     register_post_meta('trial', 'protocol_id', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'integer',
         'sanitize_callback' => [$this, 'sanitize_integer'],
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     register_post_meta('trial', 'laser_wavelength', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'integer',
         'sanitize_callback' => [$this, 'sanitize_integer'],
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     register_post_meta('trial', 'laser_power', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'number',
         'sanitize_callback' => [$this, 'sanitize_float'],
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     register_post_meta('trial', 'substance_dose', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'number',
         'sanitize_callback' => [$this, 'sanitize_float'],
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     register_post_meta('trial', 'projection_distance', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'number',
         'sanitize_callback' => [$this, 'sanitize_float'],
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     register_post_meta('trial', 'administration_notes', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'string',
         'sanitize_callback' => 'sanitize_text_field',
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     register_post_meta('trial', 'additional_observers', [
         'show_in_rest' => true,
         'single' => true,
         'type' => 'boolean',
         'sanitize_callback' => [$this, 'sanitize_boolean'],
         'auth_callback' => function() {
             return current_user_can('edit_posts');
         }
     ]);
     
     // Register questionnaire fields for REST API
     $this->register_questionnaire_meta();
 }
 
 /**
  * Registers questionnaire metadata fields to expose them in the REST API.
  *
  * @return void
  */
 private function register_questionnaire_meta(): void {
     // About You section
     $about_you_fields = [
         'participant_name' => 'string',
         'participant_email' => 'string',
         'psychedelic_experience_level' => 'integer',
         'dmt_experience_level' => 'integer',
         'simulation_theory_interest' => 'string',
         'how_found_us' => 'string',
     ];
     
     foreach ($about_you_fields as $field => $type) {
         register_post_meta('trial', $field, [
             'show_in_rest' => true,
             'single' => true,
             'type' => $type,
             'sanitize_callback' => $type === 'string' ? 'sanitize_text_field' : [$this, 'sanitize_' . $type],
             'auth_callback' => function() {
                 return current_user_can('edit_posts');
             }
         ]);
     }
     
     // Experiment Setup section
     $experiment_setup_fields = [
         'received_laser_from_us' => 'boolean',
         'beam_shape' => 'string',
         'laser_power_source' => 'string',
         'accessories_used' => 'string',
         'set_and_setting' => 'string',
         'experiment_datetime' => 'string',
         'lighting_conditions' => 'string',
         'surfaces_used' => 'string',
         'additional_setup_info' => 'string',
     ];
     
     foreach ($experiment_setup_fields as $field => $type) {
         register_post_meta('trial', $field, [
             'show_in_rest' => true,
             'single' => true,
             'type' => $type,
             'sanitize_callback' => $type === 'string' ? 'sanitize_text_field' : [$this, 'sanitize_' . $type],
             'auth_callback' => function() {
                 return current_user_can('edit_posts');
             }
         ]);
     }
     
     // Substances Used section
     $substances_used_fields = [
         'other_substances' => 'string',
         'intoxication_level' => 'integer',
         'visual_mental_effects' => 'string',
         'additional_substance_info' => 'string',
     ];
     
     foreach ($substances_used_fields as $field => $type) {
         register_post_meta('trial', $field, [
             'show_in_rest' => true,
             'single' => true,
             'type' => $type,
             'sanitize_callback' => $type === 'string' ? 'sanitize_text_field' : [$this, 'sanitize_' . $type],
             'auth_callback' => function() {
                 return current_user_can('edit_posts');
             }
         ]);
     }
     
     // Visual Effects and Laser Interaction section
     $visual_effects_fields = [
         'beam_changed' => 'boolean',
         'beam_changes_description' => 'string',
         'saw_code_of_reality' => 'boolean',
         'symbols_seen' => 'boolean',
         'symbols_description' => 'string',
         'code_moving' => 'boolean',
         'movement_direction' => 'string',
         'characters_tiny' => 'boolean',
         'size_changed' => 'boolean',
         'code_clarity' => 'integer',
         'code_behaved_like_object' => 'boolean',
         'could_influence_code' => 'boolean',
         'influence_description' => 'string',
         'code_persisted_without_laser' => 'boolean',
         'persisted_when_looked_away' => 'boolean',
         'persisted_after_turning_off' => 'boolean',
         'where_else_seen' => 'string',
     ];
     
     foreach ($visual_effects_fields as $field => $type) {
         register_post_meta('trial', $field, [
             'show_in_rest' => true,
             'single' => true,
             'type' => $type,
             'sanitize_callback' => $type === 'string' ? 'sanitize_text_field' : [$this, 'sanitize_' . $type],
             'auth_callback' => function() {
                 return current_user_can('edit_posts');
             }
         ]);
     }
     
     // Other Visual Phenomena section
     $other_phenomena_fields = [
         'noticed_anything_else' => 'string',
         'experiment_duration' => 'integer',
         'questions_comments_suggestions' => 'string',
     ];
     
     foreach ($other_phenomena_fields as $field => $type) {
         register_post_meta('trial', $field, [
             'show_in_rest' => true,
             'single' => true,
             'type' => $type,
             'sanitize_callback' => $type === 'string' ? 'sanitize_text_field' : [$this, 'sanitize_' . $type],
             'auth_callback' => function() {
                 return current_user_can('edit_posts');
             }
         ]);
     }
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
  * Sanitizes a boolean value.
  * 
  * @param mixed $value The value to sanitize
  * @return bool Sanitized value
  */
 public function sanitize_boolean($value): bool {
     return (bool) $value;
 }
 
 /**
  * Adds custom columns to the Trial post type admin list.
  * 
  * @param array $columns Existing columns
  * @return array Modified columns
  */
 public function add_columns(array $columns): array {
     $columns['protocol'] = __('Protocol', 'open-veil');
     $columns['laser_wavelength'] = __('Wavelength (nm)', 'open-veil');
     $columns['substance'] = __('Substance', 'open-veil');
     $columns['additional_observers'] = __('Additional Observers', 'open-veil');
     $columns['saw_code_of_reality'] = __('Code of Reality', 'open-veil');
     
     return $columns;
 }
 
 /**
  * Renders content for custom columns in the Trial post type admin list.
  * 
  * @param string $column Column name
  * @param int $post_id Post ID
  * @return void
  */
 public function render_columns(string $column, int $post_id): void {
     switch ($column) {
         case 'protocol':
             $protocol_id = get_post_meta($post_id, 'protocol_id', true);
             if ($protocol_id) {
                 $protocol = get_post($protocol_id);
                 if ($protocol) {
                     echo '<a href="' . get_edit_post_link($protocol_id) . '">' . $protocol->post_title . '</a>';
                 } else {
                     echo __('Protocol not found', 'open-veil');
                 }
             } else {
                 echo '-';
             }
             break;
             
         case 'laser_wavelength':
             echo get_post_meta($post_id, 'laser_wavelength', true) ?: '-';
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
             
         case 'additional_observers':
             $additional_observers = get_post_meta($post_id, 'additional_observers', true);
             echo $additional_observers ? __('Yes', 'open-veil') : __('No', 'open-veil');
             break;
             
         case 'saw_code_of_reality':
             $saw_code = get_post_meta($post_id, 'saw_code_of_reality', true);
             echo $saw_code ? __('Yes', 'open-veil') : __('No', 'open-veil');
             break;
     }
 }
}
