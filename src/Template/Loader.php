<?php
namespace OpenVeil\Template;

/**
 * Template Loader
 * 
 * Loads templates for Protocol and Trial post types
 */
class Loader {
    /**
     * Constructor
     */
    public function __construct() {
        add_filter('template_include', [$this, 'template_loader']);
        add_filter('body_class', [$this, 'body_classes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_head', [$this, 'add_json_ld']);
    }
    
    /**
     * Template loader
     */
    public function template_loader($template) {
        $post_type = get_post_type();
        
        if ($post_type === 'protocol') {
            if (is_single()) {
                $template = $this->locate_template('single-protocol.php', $template);
            } elseif (is_archive()) {
                $template = $this->locate_template('archive-protocol.php', $template);
            }
        } elseif ($post_type === 'trial') {
            if (is_single()) {
                $template = $this->locate_template('single-trial.php', $template);
            } elseif (is_archive()) {
                $template = $this->locate_template('archive-trial.php', $template);
            }
        }
        
        // Handle CSL-JSON output
        if (isset($_GET['format']) && $_GET['format'] === 'csl') {
            $this->output_csl_json();
            exit;
        }
        
        return $template;
    }
    
    /**
     * Locate template
     */
    private function locate_template($template_name, $default_template) {
        $template = locate_template($template_name);
        
        if (!$template) {
            $template = OPEN_VEIL_PLUGIN_DIR . 'template/' . $template_name;
            
            if (!file_exists($template)) {
                $template = $default_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Add body classes
     */
    public function body_classes($classes) {
        $post_type = get_post_type();
        
        if ($post_type === 'protocol' || $post_type === 'trial') {
            $classes[] = 'open-veil';
            $classes[] = 'open-veil-' . $post_type;
        }
        
        return $classes;
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        $post_type = get_post_type();
        
        if ($post_type === 'protocol' || $post_type === 'trial') {
            wp_enqueue_style('open-veil', OPEN_VEIL_PLUGIN_URL . 'assets/css/open-veil.css', [], OPEN_VEIL_VERSION);
            wp_enqueue_script('open-veil', OPEN_VEIL_PLUGIN_URL . 'assets/js/open-veil.js', ['jquery'], OPEN_VEIL_VERSION, true);
            
            wp_localize_script('open-veil', 'openVeil', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('open-veil-nonce'),
                'restUrl' => rest_url('open-veil/'),
            ]);
        }
    }
    
    /**
     * Add JSON-LD metadata
     */
    public function add_json_ld() {
        if (!is_singular(['protocol', 'trial'])) {
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
     * Add Protocol JSON-LD metadata
     */
    private function add_protocol_json_ld($post_id) {
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
     * Add Trial JSON-LD metadata
     */
    private function add_trial_json_ld($post_id) {
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
     * Output CSL-JSON
     */
    private function output_csl_json() {
        if (!is_singular(['protocol', 'trial'])) {
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
