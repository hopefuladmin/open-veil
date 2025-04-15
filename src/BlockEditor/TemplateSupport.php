<?php
declare(strict_types=1);
namespace OpenVeil\BlockEditor;

/**
 * Block Editor Template Support
 * 
 * Provides support for using HTML templates with PHP in the block editor.
 * 
 * @package OpenVeil\BlockEditor
 */
class TemplateSupport {
  /**
   * Sets up actions and filters for block editor support.
   */
  public function __construct() {
      add_action('init', [$this, 'register_block_patterns']);
      add_filter('block_editor_settings_all', [$this, 'add_template_settings'], 10, 2);
  }
  
  /**
   * Registers Open Veil block patterns for the editor.
   *
   * @return void
   */
  public function register_block_patterns(): void {
      if (!function_exists('register_block_pattern_category')) {
          return;
      }
      
      // Register pattern category
      register_block_pattern_category(
          'open-veil',
          ['label' => __('Open Veil', 'open-veil')]
      );
      
      // Register patterns for protocol and trial templates
      register_block_pattern(
          'open-veil/protocol-template',
          [
              'title' => __('Protocol Template', 'open-veil'),
              'description' => __('Template for displaying protocol content', 'open-veil'),
              'categories' => ['open-veil'],
              'content' => '<!-- wp:shortcode -->[open_veil_single_protocol]<!-- /wp:shortcode -->',
          ]
      );
      
      register_block_pattern(
          'open-veil/trial-template',
          [
              'title' => __('Trial Template', 'open-veil'),
              'description' => __('Template for displaying trial content', 'open-veil'),
              'categories' => ['open-veil'],
              'content' => '<!-- wp:shortcode -->[open_veil_single_trial]<!-- /wp:shortcode -->',
          ]
      );
      
      register_block_pattern(
          'open-veil/protocol-archive-template',
          [
              'title' => __('Protocol Archive Template', 'open-veil'),
              'description' => __('Template for displaying protocol archives', 'open-veil'),
              'categories' => ['open-veil'],
              'content' => '<!-- wp:shortcode -->[open_veil_archive_protocol]<!-- /wp:shortcode -->',
          ]
      );
      
      register_block_pattern(
          'open-veil/trial-archive-template',
          [
              'title' => __('Trial Archive Template', 'open-veil'),
              'description' => __('Template for displaying trial archives', 'open-veil'),
              'categories' => ['open-veil'],
              'content' => '<!-- wp:shortcode -->[open_veil_archive_trial]<!-- /wp:shortcode -->',
          ]
      );
  }
  
  /**
   * Modifies editor settings for Protocol and Trial post types.
   *
   * @param array $editor_settings Editor settings
   * @param \WP_Block_Editor_Context $editor_context Editor context
   * @return array Modified editor settings
   */
  public function add_template_settings(array $editor_settings, $editor_context): array {
      if (!empty($editor_context->post)) {
          $post_type = $editor_context->post->post_type;
          
          if ($post_type === 'protocol' || $post_type === 'trial') {
              // Add template settings
              $editor_settings['templateLock'] = 'all';
              
              // Set default template based on post type
              if ($post_type === 'protocol') {
                  $editor_settings['template'] = [
                      ['core/shortcode', ['text' => '[open_veil_single_protocol]']]
                  ];
              } else {
                  $editor_settings['template'] = [
                      ['core/shortcode', ['text' => '[open_veil_single_trial]']]
                  ];
              }
          }
      }
      
      return $editor_settings;
  }
}
