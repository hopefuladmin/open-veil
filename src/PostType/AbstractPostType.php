<?php
declare(strict_types=1);
namespace OpenVeil\PostType;

/**
 * Abstract Post Type Base Class
 * 
 * Base class for all custom post types with shared functionality.
 * 
 * @package OpenVeil\PostType
 */
abstract class AbstractPostType {
    /**
     * Post type name/key.
     *
     * @var string
     */
    protected string $postType;
    
    /**
     * Post type settings.
     *
     * @var array
     */
    protected array $args = [];
    
    /**
     * Post type labels.
     *
     * @var array
     */
    protected array $labels = [];
    
    /**
     * Sets up actions and filters for the post type.
     */
    public function __construct() {
        $this->postType = $this->getPostTypeName();
        
        add_action('init', [$this, 'register']);
        add_filter('manage_' . $this->postType . '_posts_columns', [$this, 'add_columns']);
        add_action('manage_' . $this->postType . '_posts_custom_column', [$this, 'render_columns'], 10, 2);
        add_filter('use_block_editor_for_post_type', [$this, 'disable_block_editor'], 10, 2);
    }
    
    /**
     * Get the post type name.
     * 
     * @return string Post type name
     */
    abstract protected function getPostTypeName(): string;
    
    /**
     * Set up post type labels.
     * 
     * @return array Labels array
     */
    abstract protected function setupLabels(): array;
    
    /**
     * Set up post type arguments.
     * 
     * @return array Arguments array
     */
    protected function setupArgs(): array {
        return [
            'labels'             => $this->setupLabels(),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => $this->postType],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
            'show_in_rest'       => true,  // Enable REST API
        ];
    }
    
    /**
     * Register the post type.
     * 
     * @return void
     */
    public function register(): void {
        $this->args = $this->setupArgs();
        register_post_type($this->postType, $this->args);
    }
    
    /**
     * Disable block editor for this post type.
     *
     * @param bool $use_block_editor Whether the post type uses the block editor
     * @param string $post_type The post type being checked
     * @return bool Whether to use block editor
     */
    public function disable_block_editor(bool $use_block_editor, string $post_type): bool {
        if ($post_type === $this->postType) {
            return false;
        }
        return $use_block_editor;
    }
    
    /**
     * Adds custom columns to the post type admin list.
     * Should be implemented by child classes.
     * 
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    abstract public function add_columns(array $columns): array;
    
    /**
     * Renders content for custom columns in the post type admin list.
     * Should be implemented by child classes.
     * 
     * @param string $column Column name
     * @param int $post_id Post ID
     * @return void
     */
    abstract public function render_columns(string $column, int $post_id): void;
    
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
}