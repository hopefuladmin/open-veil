<?php
declare(strict_types=1);
namespace OpenVeil\BlockEditor;

/**
 * Template Parts Registration
 * 
 * Registers and handles template parts for the block editor.
 * 
 * @package OpenVeil\BlockEditor
 */
class TemplateParts {
  /**
   * Sets up actions to register template parts.
   */
  public function __construct() {
      add_action('init', [$this, 'register_template_parts']);
  }
  
  /**
   * Registers header and footer template parts for the block editor.
   *
   * @return void
   */
  public function register_template_parts(): void {
      if (!function_exists('register_block_type')) {
          return;
      }
      
      // Register header template part
      register_block_type('open-veil/header-template-part', [
          'render_callback' => [$this, 'render_header_template_part'],
      ]);
      
      // Register footer template part
      register_block_type('open-veil/footer-template-part', [
          'render_callback' => [$this, 'render_footer_template_part'],
      ]);
  }
  
  /**
   * Renders the header template part.
   *
   * @param array $attributes Block attributes
   * @param string $content Block content
   * @return string Rendered header
   */
  public function render_header_template_part(array $attributes, string $content): string {
      ob_start();
      
      // First try to get the theme's header template part
      $template_part = get_block_template(get_stylesheet() . '//header', 'wp_template_part');
      
      if ($template_part && !empty($template_part->content)) {
          echo do_blocks($template_part->content);
      } else {
          // Fall back to the traditional header
          get_header();
      }
      
      return ob_get_clean();
  }
  
  /**
   * Renders the footer template part.
   *
   * @param array $attributes Block attributes
   * @param string $content Block content
   * @return string Rendered footer
   */
  public function render_footer_template_part(array $attributes, string $content): string {
      ob_start();
      
      // First try to get the theme's footer template part
      $template_part = get_block_template(get_stylesheet() . '//footer', 'wp_template_part');
      
      if ($template_part && !empty($template_part->content)) {
          echo do_blocks($template_part->content);
      } else {
          // Fall back to the traditional footer
          get_footer();
      }
      
      return ob_get_clean();
  }
}
