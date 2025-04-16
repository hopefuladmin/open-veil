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
class Trial extends AbstractPostType
{

    /**
     * Get the post type name.
     * 
     * @return string Post type name
     */
    protected function getPostTypeName(): string
    {
        return 'trial';
    }

    /**
     * Set up post type labels.
     * 
     * @return array Labels array
     */
    protected function setupLabels(): array
    {
        return [
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
    }

    /**
     * Set up post type arguments.
     * 
     * @return array Arguments array
     */
    protected function setupArgs(): array
    {
        $args = parent::setupArgs();
        $args['menu_icon'] = 'dashicons-chart-area';

        return $args;
    }

    /**
     * Adds custom columns to the Trial post type admin list.
     * 
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function add_columns(array $columns): array
    {
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
    public function render_columns(string $column, int $post_id): void
    {
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
