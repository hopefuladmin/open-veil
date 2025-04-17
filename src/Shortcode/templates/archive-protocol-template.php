<?php
// Get posts_per_page from shortcode attributes
$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 10;

// Import the PostTypeUtility class
use OpenVeil\Utility\PostTypeUtility;

// Define taxonomies to filter by
$taxonomies = ['substance', 'laser_class', 'administration_method'];

// Define meta fields to filter by
$meta_fields = [];

// Add has_trials filter as a select instead of checkbox
$special_filters = [
    'has_trials' => [
        'label' => __('Trials', 'open-veil'),
        'options' => [
            '' => __('All Trials', 'open-veil'),
            'yes' => __('Yes', 'open-veil'),
            'no' => __('No', 'open-veil')
        ]
    ]
];

// Build query args with filters
$args = PostTypeUtility::post_filter($taxonomies, array_keys($meta_fields), 'protocol', $posts_per_page);

// Run the query
$protocols_query = new WP_Query($args);
?>

<div class="open-veil-archive protocol-archive">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php _e('Protocols', 'open-veil'); ?></h1>
            <div class="archive-description">
                <p><?php _e('Browse all available protocols.', 'open-veil'); ?></p>
            </div>
        </header>

        <?php 
        // Generate the filter form
        $taxonomy_labels = [
            'substance' => __('Substance', 'open-veil'),
            'laser_class' => __('Laser Class', 'open-veil'),
            'administration_method' => __('Administration', 'open-veil')
        ];
        
        echo PostTypeUtility::generate_filter_form('protocol', $taxonomy_labels, $special_filters);
        
        // Generate the view toggle
        echo PostTypeUtility::generate_view_toggle();
        ?>

        <?php if ($protocols_query->have_posts()) : ?>
            <div class="protocols-grid view-container grid-view-active">
                <?php while ($protocols_query->have_posts()) : $protocols_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('protocol-card'); ?>>
                        <div class="protocol-content">
                            <h3><?php the_title(); ?></h3>
                            <?php the_excerpt(); ?>
                        </div>

                        <div class="protocol-specs spec-container">
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Laser:', 'open-veil'); ?></span><span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'laser_wavelength', true); ?> nm</span>
                            </div>

                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Substance:', 'open-veil'); ?></span><span class="spec-value"><?php
                                    $substances = get_the_terms(get_the_ID(), 'substance');
                                    if (!empty($substances) && !is_wp_error($substances)) {
                                        $substance_names = [];
                                        foreach ($substances as $substance) {
                                            $substance_names[] = $substance->name;
                                        }
                                        echo implode(', ', $substance_names);
                                    } else {
                                        _e('Not specified', 'open-veil');
                                    }
                                    ?></span>
                            </div>

                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Trials:', 'open-veil'); ?></span><span class="spec-value"><?php
                                    $trials = get_posts([
                                        'post_type' => 'trial',
                                        'meta_query' => [
                                            [
                                                'key' => 'protocol_id',
                                                'value' => get_the_ID(),
                                                'compare' => '=',
                                            ]
                                        ],
                                        'posts_per_page' => -1,
                                        'fields' => 'ids',
                                    ]);

                                    echo count($trials);
                                    ?></span>
                            </div>
                        </div>

                        <footer class="protocol-footer">
                            <a href="<?php the_permalink(); ?>" class="button"><?php _e('View Protocol', 'open-veil'); ?></a>
                        </footer>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            $big = 999999999;
            echo '<div class="pagination">';
            echo paginate_links([
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $protocols_query->max_num_pages,
                'prev_text' => '&laquo; ' . __('Previous', 'open-veil'),
                'next_text' => __('Next', 'open-veil') . ' &raquo;',
            ]);
            echo '</div>';
            ?>
        <?php else : ?>
            <div class="no-protocols">
                <p><?php _e('No protocols found.', 'open-veil'); ?></p>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</div>