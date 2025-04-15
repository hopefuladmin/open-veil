<div class="open-veil-single protocol-single">
    <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="protocol-header">
                <h1 class="protocol-title"><?php the_title(); ?></h1>
                <div class="protocol-meta">
                    <span class="protocol-author"><?php _e('By', 'open-veil'); ?> <?php the_author(); ?></span>
                    <span class="protocol-date"><?php echo get_the_date(); ?></span>
                    <span class="protocol-citation">
                        <a href="<?php the_permalink(); ?>?format=csl" target="_blank"><?php _e('Cite', 'open-veil'); ?></a>
                    </span>
                </div>
            </header>

            <div class="protocol-specs">
                <h2><?php _e('Protocol Specifications', 'open-veil'); ?></h2>
                
                <div class="specs-grid">
                    <div class="spec-group">
                        <h3><?php _e('Laser', 'open-veil'); ?></h3>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Wavelength:', 'open-veil'); ?></span>
                            <span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'laser_wavelength', true); ?> nm</span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Power:', 'open-veil'); ?></span>
                            <span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'laser_power', true); ?> mW</span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Class:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $laser_classes = get_the_terms(get_the_ID(), 'laser_class');
                                if (!empty($laser_classes) && !is_wp_error($laser_classes)) {
                                    $laser_class_names = [];
                                    foreach ($laser_classes as $laser_class) {
                                        $laser_class_names[] = $laser_class->name;
                                    }
                                    echo implode(', ', $laser_class_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="spec-group">
                        <h3><?php _e('Substance', 'open-veil'); ?></h3>
                        
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
                            <span class="spec-label"><?php _e('Dose:', 'open-veil'); ?></span>
                            <span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'substance_dose', true); ?> g</span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Administration Method:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $administration_methods = get_the_terms(get_the_ID(), 'administration_method');
                                if (!empty($administration_methods) && !is_wp_error($administration_methods)) {
                                    $method_names = [];
                                    foreach ($administration_methods as $method) {
                                        $method_names[] = $method->name;
                                    }
                                    echo implode(', ', $method_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Administration Protocol:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $administration_protocols = get_the_terms(get_the_ID(), 'administration_protocol');
                                if (!empty($administration_protocols) && !is_wp_error($administration_protocols)) {
                                    $protocol_names = [];
                                    foreach ($administration_protocols as $protocol) {
                                        $protocol_names[] = $protocol->name;
                                    }
                                    echo implode(', ', $protocol_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="spec-group">
                        <h3><?php _e('Equipment', 'open-veil'); ?></h3>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Equipment:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $equipment = get_the_terms(get_the_ID(), 'equipment');
                                if (!empty($equipment) && !is_wp_error($equipment)) {
                                    echo '<ul class="equipment-list">';
                                    foreach ($equipment as $item) {
                                        echo '<li>' . esc_html($item->name) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    _e('No equipment specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Diffraction Grating:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $diffraction_gratings = get_the_terms(get_the_ID(), 'diffraction_grating_spec');
                                if (!empty($diffraction_gratings) && !is_wp_error($diffraction_gratings)) {
                                    $grating_names = [];
                                    foreach ($diffraction_gratings as $grating) {
                                        $grating_names[] = $grating->name;
                                    }
                                    echo implode(', ', $grating_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Projection Distance:', 'open-veil'); ?></span>
                            <span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'projection_distance', true); ?> feet</span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Projection Surface:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $projection_surfaces = get_the_terms(get_the_ID(), 'projection_surface');
                                if (!empty($projection_surfaces) && !is_wp_error($projection_surfaces)) {
                                    $surface_names = [];
                                    foreach ($projection_surfaces as $surface) {
                                        $surface_names[] = $surface->name;
                                    }
                                    echo implode(', ', $surface_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="protocol-content">
                <h2><?php _e('Protocol Description', 'open-veil'); ?></h2>
                <?php the_content(); ?>
            </div>

            <div class="protocol-trials">
                <h2><?php _e('Related Trials', 'open-veil'); ?></h2>
                
                <?php
                $trials = get_posts([
                    'post_type' => 'trial',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'meta_query' => [
                        [
                            'key' => 'protocol_id',
                            'value' => get_the_ID(),
                            'compare' => '=',
                        ]
                    ],
                ]);
                
                if (!empty($trials)) :
                ?>
                    <div class="trials-list">
                        <?php foreach ($trials as $trial) : ?>
                            <div class="trial-item">
                                <h3><a href="<?php echo get_permalink($trial->ID); ?>"><?php echo get_the_title($trial->ID); ?></a></h3>
                                <div class="trial-meta">
                                    <span class="trial-author"><?php _e('By', 'open-veil'); ?> <?php echo get_the_author_meta('display_name', $trial->post_author); ?></span>
                                    <span class="trial-date"><?php echo get_the_date('', $trial->ID); ?></span>
                                </div>
                                <div class="trial-excerpt">
                                    <?php echo get_the_excerpt($trial->ID); ?>
                                </div>
                                <a href="<?php echo get_permalink($trial->ID); ?>" class="button button-small"><?php _e('View Trial', 'open-veil'); ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php
                    $total_trials = count(get_posts([
                        'post_type' => 'trial',
                        'posts_per_page' => -1,
                        'fields' => 'ids',
                        'post_status' => 'publish',
                        'meta_query' => [
                            [
                                'key' => 'protocol_id',
                                'value' => get_the_ID(),
                                'compare' => '=',
                            ]
                        ],
                    ]));
                    
                    if ($total_trials > 3) :
                    ?>
                        <div class="more-trials">
                            <a href="<?php echo add_query_arg(['protocol_id' => get_the_ID()], get_post_type_archive_link('trial')); ?>" class="button"><?php _e('View All Trials', 'open-veil'); ?></a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="no-trials">
                        <p><?php _e('No trials found for this protocol.', 'open-veil'); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="submit-trial">
                    <h3><?php _e('Submit Your Trial', 'open-veil'); ?></h3>
                    <p><?php _e('Have you conducted a trial based on this protocol? Share your results with the community.', 'open-veil'); ?></p>
                    <a href="<?php echo add_query_arg(['protocol_id' => get_the_ID()], get_permalink(get_page_by_path('submit-trial'))); ?>" class="button"><?php _e('Submit Trial', 'open-veil'); ?></a>
                </div>
            </div>

            <?php if (comments_open() || get_comments_number()) : ?>
                <div class="protocol-comments">
                    <h2><?php _e('Discussion', 'open-veil'); ?></h2>
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>
        </article>
    </div>
</div>
