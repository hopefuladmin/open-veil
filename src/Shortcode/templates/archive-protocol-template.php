<?php
// Get posts_per_page from shortcode attributes
$posts_per_page = isset($atts['posts_per_page']) ? intval($atts['posts_per_page']) : 10;

// Set up the query
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'protocol',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
];

// Add taxonomy filters if present
if (isset($_GET['substance']) && !empty($_GET['substance'])) {
    $args['tax_query'][] = [
        'taxonomy' => 'substance',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['substance']),
    ];
}

if (isset($_GET['laser_class']) && !empty($_GET['laser_class'])) {
    $args['tax_query'][] = [
        'taxonomy' => 'laser_class',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['laser_class']),
    ];
}

if (isset($_GET['administration_method']) && !empty($_GET['administration_method'])) {
    $args['tax_query'][] = [
        'taxonomy' => 'administration_method',
        'field' => 'slug',
        'terms' => sanitize_text_field($_GET['administration_method']),
    ];
}

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

        <div class="protocol-filters">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('protocol')); ?>">
                <div class="filter-row">
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
                        <label for="laser_class"><?php _e('Laser Class', 'open-veil'); ?></label>
                        <?php
                        $laser_classes = get_terms([
                            'taxonomy' => 'laser_class',
                            'hide_empty' => true,
                        ]);
                        
                        if (!empty($laser_classes) && !is_wp_error($laser_classes)) {
                            echo '<select name="laser_class" id="laser_class">';
                            echo '<option value="">' . __('All Laser Classes', 'open-veil') . '</option>';
                            
                            foreach ($laser_classes as $laser_class) {
                                $selected = isset($_GET['laser_class']) && $_GET['laser_class'] === $laser_class->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($laser_class->slug) . '" ' . $selected . '>' . esc_html($laser_class->name) . '</option>';
                            }
                            
                            echo '</select>';
                        }
                        ?>
                    </div>
                    
                    <div class="filter-group">
                        <label for="administration_method"><?php _e('Administration Method', 'open-veil'); ?></label>
                        <?php
                        $administration_methods = get_terms([
                            'taxonomy' => 'administration_method',
                            'hide_empty' => true,
                        ]);
                        
                        if (!empty($administration_methods) && !is_wp_error($administration_methods)) {
                            echo '<select name="administration_method" id="administration_method">';
                            echo '<option value="">' . __('All Administration Methods', 'open-veil') . '</option>';
                            
                            foreach ($administration_methods as $administration_method) {
                                $selected = isset($_GET['administration_method']) && $_GET['administration_method'] === $administration_method->slug ? 'selected' : '';
                                echo '<option value="' . esc_attr($administration_method->slug) . '" ' . $selected . '>' . esc_html($administration_method->name) . '</option>';
                            }
                            
                            echo '</select>';
                        }
                        ?>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="button"><?php _e('Filter', 'open-veil'); ?></button>
                    <a href="<?php echo esc_url(get_post_type_archive_link('protocol')); ?>" class="button button-secondary"><?php _e('Reset', 'open-veil'); ?></a>
                </div>
            </form>
        </div>

        <?php if ($protocols_query->have_posts()) : ?>
            <div class="protocols-grid">
                <?php while ($protocols_query->have_posts()) : $protocols_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('protocol-card'); ?>>
                        <header class="protocol-header">
                            <h2 class="protocol-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="protocol-meta">
                                <span class="protocol-author"><?php _e('By', 'open-veil'); ?> <?php the_author(); ?></span>
                                <span class="protocol-date"><?php echo get_the_date(); ?></span>
                            </div>
                        </header>

                        <div class="protocol-content">
                            <?php the_excerpt(); ?>
                        </div>

                        <div class="protocol-specs">
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Laser:', 'open-veil'); ?></span>
                                <span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'laser_wavelength', true); ?> nm</span>
                            </div>
                            
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
                                <span class="spec-label"><?php _e('Trials:', 'open-veil'); ?></span>
                                <span class="spec-value">
                                    <?php
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
                                    ?>
                                </span>
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
                'current' => max(1, $paged),
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
