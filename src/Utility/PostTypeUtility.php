<?php

declare(strict_types=1);

namespace OpenVeil\Utility;

/**
 * Post Type Utility
 * 
 * Provides utility functions for working with Open Veil post types.
 * 
 * @package OpenVeil\Utility
 */
class PostTypeUtility
{
    /**
     * Open Veil post types
     *
     * @var array
     */
    private static $post_types = ['protocol', 'trial'];

    /**
     * Sorting options for Open Veil post types
     *
     * @var array
     */
    private static $sort_options = [
        'date_desc' => ['label' => 'Newest First', 'orderby' => 'date', 'order' => 'DESC'],
        'date_asc' => ['label' => 'Oldest First', 'orderby' => 'date', 'order' => 'ASC'],
        'title_asc' => ['label' => 'Title (A-Z)', 'orderby' => 'title', 'order' => 'ASC'],
        'title_desc' => ['label' => 'Title (Z-A)', 'orderby' => 'title', 'order' => 'DESC'],
    ];

    /**
     * Checks if the current page is a front-end Open Veil page.
     * 
     * This includes single protocol/trial posts and their archive pages.
     *
     * @return bool True if on a front-end Open Veil page, false otherwise
     */
    public static function is_open_veil_page(): bool
    {
        // Not on front-end
        if (is_admin()) {
            return false;
        }

        // Check for singular posts or archive pages
        return (is_singular(self::$post_types) || is_post_type_archive(self::$post_types));
    }

    /**
     * Gets the array of Open Veil post types.
     *
     * @return array Array of post type names
     */
    public static function get_post_types(): array
    {
        return self::$post_types;
    }
    
    /**
     * Generate query arguments with filters from GET parameters.
     *
     * @param array $taxonomies Array of taxonomies to filter by
     * @param array $meta_fields Array of meta fields to filter by
     * @param string $post_type Post type to query (defaults to current query post type)
     * @param int $posts_per_page Number of posts per page
     * @return array Query arguments for WP_Query
     */
    public static function post_filter(array $taxonomies = [], array $meta_fields = [], string $post_type = '', int $posts_per_page = 10): array
    {
        // If no post type specified, try to get from current query
        if (empty($post_type)) {
            $post_type = get_query_var('post_type');
        }
        
        // Ensure post type is valid
        if (empty($post_type) || !in_array($post_type, self::$post_types)) {
            $post_type = 'post'; // Default to regular posts if invalid
        }
        
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        // Base query args
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'post_status' => 'publish',
        ];
        
        // Add taxonomy filters
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                if (isset($_GET[$taxonomy]) && !empty($_GET[$taxonomy])) {
                    $args['tax_query'][] = [
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => sanitize_text_field($_GET[$taxonomy]),
                    ];
                }
            }
        }
        
        // Add meta filters
        if (!empty($meta_fields)) {
            foreach ($meta_fields as $meta_field) {
                if (isset($_GET[$meta_field]) && $_GET[$meta_field] !== '') {
                    $meta_value = $_GET[$meta_field];
                    
                    // Determine if value should be cast to integer
                    if (is_numeric($meta_value)) {
                        $meta_value = intval($meta_value);
                    }
                    
                    $args['meta_query'][] = [
                        'key' => $meta_field,
                        'value' => $meta_value,
                        'compare' => '=',
                    ];
                }
            }
        }
        
        // Add special filter for protocols with trials
        if ($post_type === 'protocol' && isset($_GET['has_trials']) && $_GET['has_trials'] !== '') {
            // Handle the "has_trials" filter with multiple options
            if ($_GET['has_trials'] === 'yes') {
                // Get all protocol IDs that have associated trials
                $protocols_with_trials = [];
                $trials = get_posts([
                    'post_type' => 'trial',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'post_status' => 'publish',
                    'meta_query' => [
                        [
                            'key' => 'protocol_id',
                            'compare' => 'EXISTS',
                        ],
                    ],
                ]);
                
                if (!empty($trials)) {
                    foreach ($trials as $trial_id) {
                        $protocol_id = get_post_meta($trial_id, 'protocol_id', true);
                        if (!empty($protocol_id)) {
                            $protocols_with_trials[$protocol_id] = true;
                        }
                    }
                    
                    if (!empty($protocols_with_trials)) {
                        $args['post__in'] = array_keys($protocols_with_trials);
                    } else {
                        // No protocols with trials found, force no results
                        $args['post__in'] = [0];
                    }
                } else {
                    // No trials found, so no protocols will match
                    $args['post__in'] = [0]; // Force no results
                }
            } elseif ($_GET['has_trials'] === 'no') {
                // Get protocols that do NOT have associated trials
                $protocols_with_trials = [];
                $trials = get_posts([
                    'post_type' => 'trial',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'post_status' => 'publish',
                    'meta_query' => [
                        [
                            'key' => 'protocol_id',
                            'compare' => 'EXISTS',
                        ],
                    ],
                ]);
                
                if (!empty($trials)) {
                    foreach ($trials as $trial_id) {
                        $protocol_id = get_post_meta($trial_id, 'protocol_id', true);
                        if (!empty($protocol_id)) {
                            $protocols_with_trials[$protocol_id] = true;
                        }
                    }
                    
                    if (!empty($protocols_with_trials)) {
                        // Exclude protocols that have trials
                        $args['post__not_in'] = array_keys($protocols_with_trials);
                    }
                }
                // If no trials exist at all, all protocols qualify as "not yet having trials"
                // so we don't need to add any additional filtering
            }
            // If has_trials is empty or "any", we don't need to add any filtering
        }
        
        // Handle sorting
        if (isset($_GET['sort']) && !empty($_GET['sort'])) {
            $sort_key = sanitize_text_field($_GET['sort']);
            if (isset(self::$sort_options[$sort_key])) {
                $sort_config = self::$sort_options[$sort_key];
                $args['orderby'] = $sort_config['orderby'];
                $args['order'] = $sort_config['order'];
            }
        }
        
        return $args;
    }
    
    /**
     * Generate filter form inputs for taxonomies.
     *
     * @param array $taxonomies Array of taxonomies to create filters for
     * @param string $post_type Post type for the form action
     * @return string HTML for the filter form inputs
     */
    public static function generate_taxonomy_filters(array $taxonomies, string $post_type): string
    {
        $output = '';
        
        foreach ($taxonomies as $taxonomy => $label) {
            $output .= '<div class="filter-group">';
            $output .= '<label for="' . esc_attr($taxonomy) . '">' . esc_html($label) . '</label>';
            
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ]);
            
            if (!empty($terms) && !is_wp_error($terms)) {
                $output .= '<select name="' . esc_attr($taxonomy) . '" id="' . esc_attr($taxonomy) . '">';
                $output .= '<option value="">' . sprintf(__('All %s', 'open-veil'), $label) . '</option>';
                
                foreach ($terms as $term) {
                    $selected = isset($_GET[$taxonomy]) && $_GET[$taxonomy] === $term->slug ? 'selected' : '';
                    $output .= '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
                }
                
                $output .= '</select>';
            }
            
            $output .= '</div>';
        }
        
        return $output;
    }
    
    /**
     * Generate filter form for a post type with taxonomies and meta fields.
     *
     * @param string $post_type Post type for filtering
     * @param array $taxonomies Array of taxonomies to filter by with labels
     * @param array $meta_fields Array of meta fields config with labels and options
     * @return string HTML for complete filter form
     */
    public static function generate_filter_form(string $post_type, array $taxonomies = [], array $meta_fields = []): string
    {
        $output = '<div class="' . esc_attr($post_type) . '-filters">';
        $output .= '<form method="get" action="' . esc_url(get_post_type_archive_link($post_type)) . '">';
        $output .= '<div class="filter-row">';
        
        // Add taxonomy filters
        if (!empty($taxonomies)) {
            $output .= self::generate_taxonomy_filters($taxonomies, $post_type);
        }
        
        // Add meta field filters
        if (!empty($meta_fields)) {
            foreach ($meta_fields as $meta_key => $meta_config) {
                $output .= '<div class="filter-group">';
                $output .= '<label for="' . esc_attr($meta_key) . '">' . esc_html($meta_config['label']) . '</label>';
                
                // If this is a select with options
                if (isset($meta_config['options']) && is_array($meta_config['options'])) {
                    $output .= '<select name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '">';
                    
                    foreach ($meta_config['options'] as $value => $option_label) {
                        $selected = isset($_GET[$meta_key]) && $_GET[$meta_key] == $value ? 'selected' : '';
                        $output .= '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($option_label) . '</option>';
                    }
                    
                    $output .= '</select>';
                } 
                // For post reference fields (like protocol_id)
                elseif (isset($meta_config['post_type'])) {
                    $posts = get_posts([
                        'post_type' => $meta_config['post_type'],
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                    ]);
                    
                    if (!empty($posts)) {
                        $output .= '<select name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '">';
                        $output .= '<option value="">' . sprintf(__('All %s', 'open-veil'), $meta_config['label']) . '</option>';
                        
                        foreach ($posts as $post_item) {
                            $selected = isset($_GET[$meta_key]) && $_GET[$meta_key] == $post_item->ID ? 'selected' : '';
                            $output .= '<option value="' . esc_attr($post_item->ID) . '" ' . $selected . '>' . esc_html($post_item->post_title) . '</option>';
                        }
                        
                        $output .= '</select>';
                    }
                }
                // Checkbox type
                elseif (isset($meta_config['type']) && $meta_config['type'] === 'checkbox') {
                    $checked = isset($_GET[$meta_key]) && $_GET[$meta_key] === '1' ? 'checked' : '';
                    $output .= '<div class="checkbox-wrapper">';
                    $output .= '<input type="checkbox" name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '" value="1" ' . $checked . '>';
                    $output .= '</div>';
                }
                // Default to text input
                else {
                    $current_value = isset($_GET[$meta_key]) ? esc_attr($_GET[$meta_key]) : '';
                    $output .= '<input type="text" name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '" value="' . $current_value . '">';
                }
                
                $output .= '</div>';
            }
        }
        
        // Add sort options
        $output .= '<div class="filter-group">';
        $output .= '<label for="sort">' . __('Sort By', 'open-veil') . '</label>';
        $output .= '<select name="sort" id="sort">';
        
        foreach (self::$sort_options as $value => $sort_config) {
            $selected = isset($_GET['sort']) && $_GET['sort'] === $value ? 'selected' : '';
            $output .= '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($sort_config['label']) . '</option>';
        }
        
        $output .= '</select>';
        $output .= '</div>';
        
        $output .= '</div>';
        
        $output .= '<div class="filter-actions">';
        $output .= '<button type="submit" class="button">' . __('Apply Filters', 'open-veil') . '</button>';
        $output .= '<a href="' . esc_url(get_post_type_archive_link($post_type)) . '" class="button button-secondary">' . __('Reset', 'open-veil') . '</a>';
        $output .= '</div>';
        
        $output .= '</form>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Generate view toggle buttons
     *
     * @return string HTML for view toggle buttons
     */
    public static function generate_view_toggle(): string
    {
        $output = '<div class="view-toggle">';
        $output .= '<button type="button" class="button view-toggle-button grid-view active" data-view="grid">';
        $output .= '<span class="dashicons dashicons-grid-view"></span> ' . __('Grid', 'open-veil');
        $output .= '</button>';
        $output .= '<button type="button" class="button view-toggle-button list-view" data-view="list">';
        $output .= '<span class="dashicons dashicons-list-view"></span> ' . __('List', 'open-veil');
        $output .= '</button>';
        $output .= '</div>';
        
        return $output;
    }
}
