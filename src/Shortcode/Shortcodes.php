<?php

declare(strict_types=1);

namespace OpenVeil\Shortcode;

/**
 * Shortcodes
 * 
 * Registers shortcodes for displaying Protocol and Trial content.
 * 
 * @package OpenVeil\Shortcode
 */
class Shortcodes
{
    /**
     * Registers all shortcodes for the plugin.
     */
    public function __construct()
    {
        add_shortcode('open_veil_single_protocol', [$this, 'single_protocol_shortcode']);
        add_shortcode('open_veil_archive_protocol', [$this, 'archive_protocol_shortcode']);
        add_shortcode('open_veil_single_trial', [$this, 'single_trial_shortcode']);
        add_shortcode('open_veil_archive_trial', [$this, 'archive_trial_shortcode']);
    }

    /**
     * Renders a single protocol using the template.
     *
     * @param array $atts Shortcode attributes
     * @return string Shortcode output
     */
    public function single_protocol_shortcode(array $atts): string
    {
        $atts = shortcode_atts([
            'post_id' => 0,
        ], $atts, 'open_veil_single_protocol');

        // Get post ID
        $post_id = intval($atts['post_id']);
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        // Check if post exists and is a protocol
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'protocol') {
            return '<p>' . __('Protocol not found.', 'open-veil') . '</p>';
        }

        // Setup postdata
        $original_post = $GLOBALS['post'];
        $GLOBALS['post'] = $post;
        setup_postdata($post);

        // Start output buffering
        ob_start();
        include OPEN_VEIL_PLUGIN_DIR . 'src/Shortcode/templates/single-protocol-template.php';

        // Reset postdata
        $GLOBALS['post'] = $original_post;
        wp_reset_postdata();

        // Return the output
        return ob_get_clean();
    }

    /**
     * Renders a list of protocols using the template.
     *
     * @param array $atts Shortcode attributes
     * @return string Shortcode output
     */
    public function archive_protocol_shortcode(array $atts): string
    {
        $atts = shortcode_atts([
            'posts_per_page' => 10,
        ], $atts, 'open_veil_archive_protocol');

        // Start output buffering
        ob_start();
        include OPEN_VEIL_PLUGIN_DIR . 'src/Shortcode/templates/archive-protocol-template.php';

        // Return the output
        return ob_get_clean();
    }

    /**
     * Renders a single trial using the template.
     *
     * @param array $atts Shortcode attributes
     * @return string Shortcode output
     */
    public function single_trial_shortcode(array $atts): string
    {
        $atts = shortcode_atts([
            'post_id' => 0,
        ], $atts, 'open_veil_single_trial');

        // Get post ID
        $post_id = intval($atts['post_id']);
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        // Check if post exists and is a trial
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'trial') {
            return '<p>' . __('Trial not found.', 'open-veil') . '</p>';
        }

        // Setup postdata
        $original_post = $GLOBALS['post'];
        $GLOBALS['post'] = $post;
        setup_postdata($post);

        // Start output buffering
        ob_start();
        include OPEN_VEIL_PLUGIN_DIR . 'src/Shortcode/templates/single-trial-template.php';

        // Reset postdata
        $GLOBALS['post'] = $original_post;
        wp_reset_postdata();

        // Return the output
        return ob_get_clean();
    }

    /**
     * Renders a list of trials using the template.
     *
     * @param array $atts Shortcode attributes
     * @return string Shortcode output
     */
    public function archive_trial_shortcode(array $atts): string
    {
        $atts = shortcode_atts([
            'posts_per_page' => 10,
        ], $atts, 'open_veil_archive_trial');

        // Start output buffering
        ob_start();
        include OPEN_VEIL_PLUGIN_DIR . 'src/Shortcode/templates/archive-trial-template.php';

        // Return the output
        return ob_get_clean();
    }
}
