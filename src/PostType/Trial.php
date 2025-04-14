<?php
namespace OpenVeil\PostType;

/**
 * Trial Post Type
 * 
 * Registers the Trial custom post type
 */
class Trial {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_meta']);
        add_filter('manage_trial_posts_columns', [$this, 'add_columns']);
        add_action('manage_trial_posts_custom_column', [$this, 'render_columns'], 10, 2);
    }
    
    /**
     * Register the Trial post type
     */
    public function register() {
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
     * Register meta fields for the Trial post type
     */
    public function register_meta() {
        register_post_meta('trial', 'protocol_id', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        register_post_meta('trial', 'laser_wavelength', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        register_post_meta('trial', 'laser_power', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'number',
            'sanitize_callback' => 'floatval',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        register_post_meta('trial', 'substance_dose', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'number',
            'sanitize_callback' => 'floatval',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
        
        register_post_meta('trial', 'projection_distance', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'number',
            'sanitize_callback' => 'floatval',
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
            'sanitize_callback' => 'rest_sanitize_boolean',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
    }
    
    /**
     * Add custom columns to the Trial post type admin list
     */
    public function add_columns($columns) {
        $columns['protocol'] = __('Protocol', 'open-veil');
        $columns['laser_wavelength'] = __('Wavelength (nm)', 'open-veil');
        $columns['substance'] = __('Substance', 'open-veil');
        $columns['additional_observers'] = __('Additional Observers', 'open-veil');
        
        return $columns;
    }
    
    /**
     * Render custom column content for the Trial post type admin list
     */
    public function render_columns($column, $post_id) {
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
        }
    }
}
