<?php
/**
 * The template for displaying single protocol
 *
 * @package OpenVeil
 */

get_header();
?>

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
                            <span class="spec-label"><?php _e('Type:', 'open-veil'); ?></span>
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
                                    $administration_method_names = [];
                                    foreach ($administration_methods as $administration_method) {
                                        $administration_method_names[] = $administration_method->name;
                                    }
                                    echo implode(', ', $administration_method_names);
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
                                    $administration_protocol_names = [];
                                    foreach ($administration_protocols as $administration_protocol) {
                                        $administration_protocol_names[] = $administration_protocol->name;
                                    }
                                    echo implode(', ', $administration_protocol_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="spec-group">
                        <h3><?php _e('Projection', 'open-veil'); ?></h3>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Distance:', 'open-veil'); ?></span>
                            <span class="spec-value"><?php echo get_post_meta(get_the_ID(), 'projection_distance', true); ?> feet</span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Surface:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $projection_surfaces = get_the_terms(get_the_ID(), 'projection_surface');
                                if (!empty($projection_surfaces) && !is_wp_error($projection_surfaces)) {
                                    $projection_surface_names = [];
                                    foreach ($projection_surfaces as $projection_surface) {
                                        $projection_surface_names[] = $projection_surface->name;
                                    }
                                    echo implode(', ', $projection_surface_names);
                                } else {
                                    _e('Not specified', 'open-veil');
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="spec-item">
                            <span class="spec-label"><?php _e('Diffraction Grating:', 'open-veil'); ?></span>
                            <span class="spec-value">
                                <?php
                                $diffraction_grating_specs = get_the_terms(get_the_ID(), 'diffraction_grating_spec');
                                if (!empty($diffraction_grating_specs) && !is_wp_error($diffraction_grating_specs)) {
                                    $diffraction_grating_spec_names = [];
                                    foreach ($diffraction_grating_specs as $diffraction_grating_spec) {
                                        $diffraction_grating_spec_names[] = $diffraction_grating_spec->name;
                                    }
                                    echo implode(', ', $diffraction_grating_spec_names);
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
                    </div>
                </div>
            </div>

            <div class="protocol-content">
                <h2><?php _e('Protocol Description', 'open-veil'); ?></h2>
                <?php the_content(); ?>
            </div>

            <div class="protocol-trials">
                <h2><?php _e('Trials', 'open-veil'); ?></h2>
                
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
                    'posts_per_page' => 5,
                    'post_status' => 'publish',
                ]);
                
                if (!empty($trials)) {
                    echo '<div class="trials-list">';
                    foreach ($trials as $trial) {
                        ?>
                        <article class="trial-item">
                            <h3><a href="<?php echo get_permalink($trial->ID); ?>"><?php echo get_the_title($trial->ID); ?></a></h3>
                            <div class="trial-meta">
                                <span class="trial-author"><?php _e('By', 'open-veil'); ?> <?php echo get_the_author_meta('display_name', $trial->post_author); ?></span>
                                <span class="trial-date"><?php echo get_the_date('', $trial->ID); ?></span>
                            </div>
                            <div class="trial-excerpt">
                                <?php echo get_the_excerpt($trial->ID); ?>
                            </div>
                            <a href="<?php echo get_permalink($trial->ID); ?>" class="button button-small"><?php _e('View Trial', 'open-veil'); ?></a>
                        </article>
                        <?php
                    }
                    echo '</div>';
                    
                    $trials_count = count(get_posts([
                        'post_type' => 'trial',
                        'meta_query' => [
                            [
                                'key' => 'protocol_id',
                                'value' => get_the_ID(),
                                'compare' => '=',
                            ]
                        ],
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'fields' => 'ids',
                    ]));
                    
                    if ($trials_count > 5) {
                        echo '<div class="more-trials">';
                        echo '<a href="' . esc_url(add_query_arg(['protocol_id' => get_the_ID()], get_post_type_archive_link('trial'))) . '" class="button">' . sprintf(__('View All %d Trials', 'open-veil'), $trials_count) . '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="no-trials">';
                    echo '<p>' . __('No trials have been submitted for this protocol yet.', 'open-veil') . '</p>';
                    echo '</div>';
                }
                ?>
                
                <div class="submit-trial">
                    <h3><?php _e('Submit Your Trial', 'open-veil'); ?></h3>
                    <p><?php _e('Have you conducted this protocol? Share your experience by submitting a trial.', 'open-veil'); ?></p>
                    <a href="<?php echo esc_url(add_query_arg(['protocol_id' => get_the_ID()], home_url('/submit-trial/'))); ?>" class="button"><?php _e('Submit Trial', 'open-veil'); ?></a>
                </div>
            </div>
        </article>
    </div>
</div>

<?php
get_footer();
