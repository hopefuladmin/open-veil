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
class Protocol extends AbstractPostType
{

    /**
     * Get the post type name.
     * 
     * @return string Post type name
     */
    protected function getPostTypeName(): string
    {
        return 'protocol';
    }

    /**
     * Set up post type labels.
     * 
     * @return array Labels array
     */
    protected function setupLabels(): array
    {
        return [
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
    }

    /**
     * Set up post type arguments.
     * 
     * @return array Arguments array
     */
    protected function setupArgs(): array
    {
        $args = parent::setupArgs();
        $args['menu_icon'] = 'dashicons-clipboard';

        return $args;
    }

    /**
     * Adds custom columns to the Protocol post type admin list.
     * 
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function add_columns(array $columns): array
    {
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
    public function render_columns(string $column, int $post_id): void
    {
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
