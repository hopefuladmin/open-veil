<?php

declare(strict_types=1);

namespace OpenVeil\Template;

use OpenVeil\ACF\Options;
use OpenVeil\Utility\PostTypeUtility;

/**
 * Template Loader
 * 
 * Handles JSON-LD metadata, CSL-JSON output, and script/style enqueuing for Protocol and Trial post types.
 * 
 * @package OpenVeil\Template
 */
class Loader
{
    /**
     * Sets up actions to add JSON-LD metadata, handle CSL-JSON output, and enqueue scripts/styles.
     */
    public function __construct()
    {
        add_action('wp_head', [$this, 'add_json_ld']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Handle CSL-JSON output
        add_action('template_redirect', function () {
            if (isset($_GET['format']) && $_GET['format'] === 'csl') {
                $this->output_csl_json();
                exit;
            }
        });
    }

    /**
     * Enqueues JavaScript and CSS files for the front end.
     *
     * @return void
     */
    public function enqueue_assets(): void
    {
        // Only enqueue on the front end for protocol and trial post types or archives
        // Enqueue the CSS file
        wp_enqueue_style(
            'open-veil-styles',
            OPEN_VEIL_PLUGIN_URL . 'assets/css/open-veil.css',
            [],
            OPEN_VEIL_VERSION
        );

        // Enqueue the JavaScript file
        wp_enqueue_script(
            'open-veil-scripts',
            OPEN_VEIL_PLUGIN_URL . 'assets/js/open-veil.js',
            ['jquery'],
            OPEN_VEIL_VERSION,
            true
        );

        // Localize the script with necessary data
        wp_localize_script(
            'open-veil-scripts',
            'openVeil',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => esc_url_raw(rest_url('open-veil/')),
                'nonce' => wp_create_nonce('wp_rest'),
            ]
        );
    }

    /**
     * Adds structured data markup for Protocol and Trial post types.
     *
     * @return void
     */
    public function add_json_ld(): void
    {
        if (!is_singular(PostTypeUtility::get_post_types())) {
            return;
        }

        $post_type = get_post_type();
        $post_id = get_the_ID();

        if ($post_type === 'protocol') {
            $this->add_protocol_json_ld($post_id);
        } elseif ($post_type === 'trial') {
            $this->add_trial_json_ld($post_id);
        }
    }

    /**
     * Adds JSON-LD metadata for a Protocol post.
     * 
     * @param int $post_id Protocol post ID
     * @return void
     */
    private function add_protocol_json_ld(int $post_id): void
    {
        $protocol = get_post($post_id);
        $author = get_user_by('id', $protocol->post_author);

        $json_ld = [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => $protocol->post_title,
            'description' => get_the_excerpt($post_id),
            'author' => [
                '@type' => 'Person',
                'name' => $author ? $author->display_name : '',
            ],
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url(),
                ],
            ],
            'mainEntityOfPage' => get_permalink($post_id),
        ];

        echo '<script type="application/ld+json">' . wp_json_encode($json_ld) . '</script>';
    }

    /**
     * Adds JSON-LD metadata for a Trial post.
     * 
     * @param int $post_id Trial post ID
     * @return void
     */
    private function add_trial_json_ld(int $post_id): void
    {
        $trial = get_post($post_id);
        $author = get_user_by('id', $trial->post_author);
        $protocol_id = get_post_meta($post_id, 'protocol_id', true);
        $protocol = $protocol_id ? get_post($protocol_id) : null;

        $json_ld = [
            '@context' => 'https://schema.org',
            '@type' => 'ScholarlyArticle',
            'headline' => $trial->post_title,
            'description' => get_the_excerpt($post_id),
            'author' => [
                '@type' => 'Person',
                'name' => $author ? $author->display_name : __('Anonymous', 'open-veil'),
            ],
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url(),
                ],
            ],
            'mainEntityOfPage' => get_permalink($post_id),
        ];

        if ($protocol) {
            $json_ld['isBasedOn'] = get_permalink($protocol_id);
        }

        echo '<script type="application/ld+json">' . wp_json_encode($json_ld) . '</script>';
    }

    /**
     * Generates and outputs citation data in CSL-JSON format.
     *
     * @return void
     */
    private function output_csl_json(): void
    {
        if (!is_singular(PostTypeUtility::get_post_types())) {
            return;
        }

        $post_type = get_post_type();
        $post_id = get_the_ID();
        $post = get_post($post_id);
        $author = get_user_by('id', $post->post_author);
        $author_name = $author ? $author->display_name : __('Anonymous', 'open-veil');

        $csl = [
            'id' => $post_id,
            'type' => 'article',
            'title' => $post->post_title,
            'author' => [
                [
                    'family' => $author_name,
                    'given' => '',
                ]
            ],
            'issued' => [
                'date-parts' => [
                    [date('Y', strtotime($post->post_date)), date('m', strtotime($post->post_date)), date('d', strtotime($post->post_date))]
                ]
            ],
            'URL' => get_permalink($post_id),
            'publisher' => get_bloginfo('name'),
        ];

        if ($post_type === 'trial') {
            $protocol_id = get_post_meta($post_id, 'protocol_id', true);
            $protocol = $protocol_id ? get_post($protocol_id) : null;

            if ($protocol) {
                $csl['container-title'] = $protocol->post_title;
            }
        }

        header('Content-Type: application/vnd.citationstyles.csl+json');
        echo wp_json_encode($csl);
        exit;
    }
}
