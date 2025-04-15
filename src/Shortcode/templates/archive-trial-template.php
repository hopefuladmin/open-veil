<?php
// Get posts_per_page from shortcode attributes
$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 10;

// Set up the query
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'trial',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
];

// Add meta query for protocol_id if present
if (isset($_GET['protocol_id']) && !empty($_GET['protocol_id'])) {
    $args['meta_query'][] = [
        'key' => 'protocol_id',
        'value' => intval($_GET['protocol_id']),
        'compare' => '=',
    ];
}

// Add taxonomy filters if present
if (isset($_GET['substance']) && !empty($_GET['substance'])) {
    $args['tax_query'][] = [
        'taxonomy' => 'substance',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['substance']),
    ];
}

// Add meta query for additional_observers if present
if (isset($_GET['additional_observers']) && $_GET['additional_observers'] !== '') {
    $args['meta_query'][] = [
        'key' => 'additional_observers',
        'value' => intval($_GET['additional_observers']),
        'compare' => '=',
    ];
}

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

        <div class="trial-filters">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('trial')); ?>">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="protocol_id"><?php _e('Protocol', 'open-veil'); ?></label>
                        <?php
                        $protocols = get_posts([
                            'post_type' => 'protocol',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                        ]);
                        
                        if (!empty($protocols)) {
                            echo '<select name="protocol_id" id="protocol_id">';
                            echo '<option value="">' . __('All Protocols', 'open-veil') . '</option>';
                            
                            foreach ($protocols as $protocol) {
                                $selected = isset($_GET['protocol_id']) && $_GET['protocol_id'] == $protocol->ID ? 'selected' : '';
                                echo '<option value="' . esc_attr($protocol->ID) . '" ' . $selected . '>' . esc_html($protocol->post_title) . '</option>';
                            }
                            
                            echo '</select>';
                        }
                        ?>
                    </div>
                    
                    <div class="filter-group">
                        <label for="substance"><?php _e('Substance', 'open-veil'); ?></label>
                        <?php
                        $substances = get_terms([
                            'taxonomy' => 'substance',
                            'hide_empty' => true,
                        ]);
                        
                        if (!empty($substances) && !is_wp_error($substances)) {
                            echo '<select name="substance" id="substance">';
                            echo '<option value="">' . __('All Substances', 'open-veil') . '</option>';
                            
                            foreach ($substances as $substance) {
                                $selected = isset($_GET['substance']) && $_GET['substance'] === $substance->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($substance->slug) . '" ' . $selected . '>' . esc_html($substance->name) . '</option>';
                            }
                            
                            echo '</select>';
                        }
                        ?>
                    </div>
                    
                    <div class="filter-group">
                        <label for="additional_observers"><?php _e('Additional Observers', 'open-veil'); ?></label>
                        <select name="additional_observers" id="additional_observers">
                            <option value=""><?php _e('Any', 'open-veil'); ?></option>
                            <option value="1" <?php selected(isset($_GET['additional_observers']) && $_GET['additional_observers'] === '1'); ?>><?php _e('Yes', 'open-veil'); ?></option>
                            <option value="0" <?php selected(isset($_GET['additional_observers']) && $_GET['additional_observers'] === '0'); ?>><?php _e('No', 'open-veil'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="button"><?php _e('Filter', 'open-veil'); ?></button>
                    <a href="<?php echo esc_url(get_post_type_archive_link('trial')); ?>" class="button button-secondary"><?php _e('Reset', 'open-veil'); ?></a>
                </div>
            </form>
        </div>

        <?php if ($trials_query->have_posts()) : ?>
            <div class="trials-grid">
                <?php while ($trials_query->have_posts()) : $trials_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('trial-card'); ?>>
                        <header class="trial-header">
                            <h2 class="trial-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="trial-meta">
                                <span class="trial-author"><?php _e('By', 'open-veil'); ?> <?php the_author(); ?></span>
                                <span class="trial-date"><?php echo get_the_date(); ?></span>
                            </div>
                        </header>

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
                'current' => max(1, $paged),
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
