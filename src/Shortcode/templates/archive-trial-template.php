<?php
// Get posts_per_page from shortcode attributes
$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 10;

// Import the PostTypeUtility class
use OpenVeil\Utility\PostTypeUtility;

// Define taxonomies and meta fields to filter by
$taxonomies = ['substance'];
$meta_fields = ['protocol_id', 'additional_observers'];

// Build query args with filters
$args = PostTypeUtility::post_filter($taxonomies, $meta_fields, 'trial', $posts_per_page);

// Run the query
$trials_query = new WP_Query($args);
?>

<div class="open-veil-archive trial-archive">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php _e('Trials', 'open-veil'); ?></h1>
            <div class="archive-description">
                <p><?php _e('Browse all submitted trials.', 'open-veil'); ?></p>
            </div>
        </header>

        <?php 
        // Generate the filter form
        $taxonomy_labels = [
            'substance' => __('Substance', 'open-veil')
        ];
        
        $meta_fields_config = [
            'protocol_id' => [
                'label' => __('Protocol', 'open-veil'),
                'post_type' => 'protocol'
            ],
            'additional_observers' => [
                'label' => __('Additional Observers', 'open-veil'),
                'options' => [
                    '1' => __('Yes', 'open-veil'),
                    '0' => __('No', 'open-veil')
                ]
            ]
        ];
        
        echo PostTypeUtility::generate_filter_form('trial', $taxonomy_labels, $meta_fields_config);
        ?>

        <?php if ($trials_query->have_posts()) : ?>
            <div class="trials-grid">
                <?php while ($trials_query->have_posts()) : $trials_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('trial-card'); ?>>
                        <div class="trial-content">
                            <?php the_excerpt(); ?>
                        </div>

                        <div class="trial-specs">
                            <?php
                            $protocol_id = get_post_meta(get_the_ID(), 'protocol_id', true);
                            $protocol = $protocol_id ? get_post($protocol_id) : null;
                            ?>

                            <?php if ($protocol) : ?>
                                <div class="spec-item">
                                    <span class="spec-label"><?php _e('Protocol:', 'open-veil'); ?></span>
                                    <span class="spec-value"><a href="<?php echo get_permalink($protocol_id); ?>"><?php echo get_the_title($protocol_id); ?></a></span>
                                </div>
                            <?php endif; ?>

                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Substance:', 'open-veil'); ?></span>
                                <span class="spec-value">
                                    <?php
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
                                    ?>
                                </span>
                            </div>

                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Additional Observers:', 'open-veil'); ?></span>
                                <span class="spec-value">
                                    <?php
                                    $additional_observers = get_post_meta(get_the_ID(), 'additional_observers', true);
                                    echo $additional_observers ? __('Yes', 'open-veil') : __('No', 'open-veil');
                                    ?>
                                </span>
                            </div>
                        </div>

                        <footer class="trial-footer">
                            <a href="<?php the_permalink(); ?>" class="button"><?php _e('View Trial', 'open-veil'); ?></a>
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
                'total' => $trials_query->max_num_pages,
                'prev_text' => '&laquo; ' . __('Previous', 'open-veil'),
                'next_text' => __('Next', 'open-veil') . ' &raquo;',
            ]);
            echo '</div>';
            ?>
        <?php else : ?>
            <div class="no-trials">
                <p><?php _e('No trials found.', 'open-veil'); ?></p>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</div>