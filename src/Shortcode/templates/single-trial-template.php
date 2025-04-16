<?php
$protocol_id = get_post_meta(get_the_ID(), 'protocol_id', true);
$protocol = $protocol_id ? get_post($protocol_id) : null;
?>

<div class="open-veil-single trial-single">
    <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="trial-header">
                <h1 class="trial-title"><?php the_title(); ?></h1>
                <div class="trial-meta">
                    <span class="trial-author"><?php _e('By', 'open-veil'); ?> <?php the_author(); ?></span>
                    <span class="trial-date"><?php echo get_the_date(); ?></span>
                    <span class="trial-citation">
                        <a href="<?php the_permalink(); ?>?format=csl" target="_blank"><?php _e('Cite', 'open-veil'); ?></a>
                    </span>
                </div>

                <?php if ($protocol) : ?>
                    <div class="protocol-link">
                        <span><?php _e('Based on protocol:', 'open-veil'); ?></span>
                        <a href="<?php echo get_permalink($protocol_id); ?>"><?php echo get_the_title($protocol_id); ?></a>
                    </div>
                <?php endif; ?>
            </header>

            <div class="trial-content">
                <h2><?php _e('Trial Description', 'open-veil'); ?></h2>
                <?php the_content(); ?>
            </div>

            <div class="trial-comparison">
                <h2><?php _e('Protocol Comparison', 'open-veil'); ?></h2>

                <?php if ($protocol) : ?>
                    <div class="comparison-table">
                        <table>
                            <thead>
                                <tr>
                                    <th><?php _e('Parameter', 'open-veil'); ?></th>
                                    <th><?php _e('Protocol', 'open-veil'); ?></th>
                                    <th><?php _e('Trial', 'open-veil'); ?></th>
                                    <th><?php _e('Variance', 'open-veil'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php _e('Laser Wavelength', 'open-veil'); ?></td>
                                    <td><?php echo get_post_meta($protocol_id, 'laser_wavelength', true); ?> nm</td>
                                    <td><?php echo get_post_meta(get_the_ID(), 'laser_wavelength', true); ?> nm</td>
                                    <td>
                                        <?php
                                        $protocol_wavelength = get_post_meta($protocol_id, 'laser_wavelength', true);
                                        $trial_wavelength = get_post_meta(get_the_ID(), 'laser_wavelength', true);

                                        if ($protocol_wavelength && $trial_wavelength) {
                                            $variance = $trial_wavelength - $protocol_wavelength;
                                            $variance_percent = ($variance / $protocol_wavelength) * 100;

                                            echo sprintf('%+d nm (%+.1f%%)', $variance, $variance_percent);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Laser Power', 'open-veil'); ?></td>
                                    <td><?php echo get_post_meta($protocol_id, 'laser_power', true); ?> mW</td>
                                    <td><?php echo get_post_meta(get_the_ID(), 'laser_power', true); ?> mW</td>
                                    <td>
                                        <?php
                                        $protocol_power = get_post_meta($protocol_id, 'laser_power', true);
                                        $trial_power = get_post_meta(get_the_ID(), 'laser_power', true);

                                        if ($protocol_power && $trial_power) {
                                            $variance = $trial_power - $protocol_power;
                                            $variance_percent = ($variance / $protocol_power) * 100;

                                            echo sprintf('%+.2f mW (%+.1f%%)', $variance, $variance_percent);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Substance Dose', 'open-veil'); ?></td>
                                    <td><?php echo get_post_meta($protocol_id, 'substance_dose', true); ?> g</td>
                                    <td><?php echo get_post_meta(get_the_ID(), 'substance_dose', true); ?> g</td>
                                    <td>
                                        <?php
                                        $protocol_dose = get_post_meta($protocol_id, 'substance_dose', true);
                                        $trial_dose = get_post_meta(get_the_ID(), 'substance_dose', true);

                                        if ($protocol_dose && $trial_dose) {
                                            $variance = $trial_dose - $protocol_dose;
                                            $variance_percent = ($variance / $protocol_dose) * 100;

                                            echo sprintf('%+.2f g (%+.1f%%)', $variance, $variance_percent);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Projection Distance', 'open-veil'); ?></td>
                                    <td><?php echo get_post_meta($protocol_id, 'projection_distance', true); ?> feet</td>
                                    <td><?php echo get_post_meta(get_the_ID(), 'projection_distance', true); ?> feet</td>
                                    <td>
                                        <?php
                                        $protocol_distance = get_post_meta($protocol_id, 'projection_distance', true);
                                        $trial_distance = get_post_meta(get_the_ID(), 'projection_distance', true);

                                        if ($protocol_distance && $trial_distance) {
                                            $variance = $trial_distance - $protocol_distance;
                                            $variance_percent = ($variance / $protocol_distance) * 100;

                                            echo sprintf('%+.1f feet (%+.1f%%)', $variance, $variance_percent);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Laser Class', 'open-veil'); ?></td>
                                    <td>
                                        <?php
                                        $protocol_laser_classes = get_the_terms($protocol_id, 'laser_class');
                                        if (!empty($protocol_laser_classes) && !is_wp_error($protocol_laser_classes)) {
                                            $laser_class_names = [];
                                            foreach ($protocol_laser_classes as $laser_class) {
                                                $laser_class_names[] = $laser_class->name;
                                            }
                                            echo implode(', ', $laser_class_names);
                                        } else {
                                            _e('Not specified', 'open-veil');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $trial_laser_classes = get_the_terms(get_the_ID(), 'laser_class');
                                        if (!empty($trial_laser_classes) && !is_wp_error($trial_laser_classes)) {
                                            $laser_class_names = [];
                                            foreach ($trial_laser_classes as $laser_class) {
                                                $laser_class_names[] = $laser_class->name;
                                            }
                                            echo implode(', ', $laser_class_names);
                                        } else {
                                            _e('Not specified', 'open-veil');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (
                                            !empty($protocol_laser_classes) && !is_wp_error($protocol_laser_classes) &&
                                            !empty($trial_laser_classes) && !is_wp_error($trial_laser_classes)
                                        ) {

                                            $protocol_laser_class_ids = wp_list_pluck($protocol_laser_classes, 'term_id');
                                            $trial_laser_class_ids = wp_list_pluck($trial_laser_classes, 'term_id');

                                            $diff = array_diff($trial_laser_class_ids, $protocol_laser_class_ids);

                                            if (empty($diff)) {
                                                _e('Same', 'open-veil');
                                            } else {
                                                _e('Different', 'open-veil');
                                            }
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Substance', 'open-veil'); ?></td>
                                    <td>
                                        <?php
                                        $protocol_substances = get_the_terms($protocol_id, 'substance');
                                        if (!empty($protocol_substances) && !is_wp_error($protocol_substances)) {
                                            $substance_names = [];
                                            foreach ($protocol_substances as $substance) {
                                                $substance_names[] = $substance->name;
                                            }
                                            echo implode(', ', $substance_names);
                                        } else {
                                            _e('Not specified', 'open-veil');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $trial_substances = get_the_terms(get_the_ID(), 'substance');
                                        if (!empty($trial_substances) && !is_wp_error($trial_substances)) {
                                            $substance_names = [];
                                            foreach ($trial_substances as $substance) {
                                                $substance_names[] = $substance->name;
                                            }
                                            echo implode(', ', $substance_names);
                                        } else {
                                            _e('Not specified', 'open-veil');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (
                                            !empty($protocol_substances) && !is_wp_error($protocol_substances) &&
                                            !empty($trial_substances) && !is_wp_error($trial_substances)
                                        ) {

                                            $protocol_substance_ids = wp_list_pluck($protocol_substances, 'term_id');
                                            $trial_substance_ids = wp_list_pluck($trial_substances, 'term_id');

                                            $diff = array_diff($trial_substance_ids, $protocol_substance_ids);

                                            if (empty($diff)) {
                                                _e('Same', 'open-veil');
                                            } else {
                                                _e('Different', 'open-veil');
                                            }
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="no-protocol">
                        <p><?php _e('Protocol not found or has been deleted.', 'open-veil'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="trial-details">
                <h2><?php _e('Additional Details', 'open-veil'); ?></h2>

                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label"><?php _e('Administration Notes:', 'open-veil'); ?></span>
                        <span class="detail-value">
                            <?php
                            $administration_notes = get_post_meta(get_the_ID(), 'administration_notes', true);
                            echo $administration_notes ? esc_html($administration_notes) : __('None provided', 'open-veil');
                            ?>
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label"><?php _e('Additional Observers:', 'open-veil'); ?></span>
                        <span class="detail-value">
                            <?php
                            $additional_observers = get_post_meta(get_the_ID(), 'additional_observers', true);
                            echo $additional_observers ? __('Yes', 'open-veil') : __('No', 'open-veil');
                            ?>
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label"><?php _e('Equipment:', 'open-veil'); ?></span>
                        <span class="detail-value">
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

            <!-- Questionnaire Data -->
            <div class="trial-questionnaire">
                <h2><?php _e('Questionnaire Responses', 'open-veil'); ?></h2>

                <!-- About You Section -->
                <div class="questionnaire-section">
                    <h3><?php _e('About You', 'open-veil'); ?></h3>
                    <div class="questionnaire-grid">
                        <?php
                        $about_you_fields = [
                            'participant_name' => __('Name', 'open-veil'),
                            'participant_email' => __('Email Address', 'open-veil'),
                            'psychedelic_experience_level' => __('Psychedelic Experience Level (1-10)', 'open-veil'),
                            'dmt_experience_level' => __('DMT Experience Level (1-10)', 'open-veil'),
                            'simulation_theory_interest' => __('Interest in Simulation Theory', 'open-veil'),
                            'how_found_us' => __('How Did You Find Us?', 'open-veil'),
                        ];

                        foreach ($about_you_fields as $field => $label) {
                            $value = get_post_meta(get_the_ID(), $field, true);
                            if (!empty($value)) {
                                echo '<div class="questionnaire-item">';
                                echo '<span class="questionnaire-label">' . esc_html($label) . ':</span>';
                                echo '<span class="questionnaire-value">' . esc_html($value) . '</span>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Experiment Setup Section -->
                <div class="questionnaire-section">
                    <h3><?php _e('Experiment Setup', 'open-veil'); ?></h3>
                    <div class="questionnaire-grid">
                        <?php
                        $experiment_setup_fields = [
                            'received_laser_from_us' => __('Did You Receive the Laser From Us?', 'open-veil'),
                            'beam_shape' => __('Beam Shape', 'open-veil'),
                            'laser_power_source' => __('Laser Power Source', 'open-veil'),
                            'accessories_used' => __('Accessories Used', 'open-veil'),
                            'set_and_setting' => __('Set & Setting', 'open-veil'),
                            'experiment_time' => __('Time of Experiment', 'open-veil'),
                            'lighting_conditions' => __('Lighting Conditions', 'open-veil'),
                            'surfaces_used' => __('Surfaces Used', 'open-veil'),
                            'additional_setup_info' => __('Additional Setup Information', 'open-veil'),
                        ];

                        foreach ($experiment_setup_fields as $field => $label) {
                            $value = get_post_meta(get_the_ID(), $field, true);
                            if ($value !== '' && $value !== false) {
                                echo '<div class="questionnaire-item">';
                                echo '<span class="questionnaire-label">' . esc_html($label) . ':</span>';
                                if ($field === 'received_laser_from_us') {
                                    echo '<span class="questionnaire-value">' . ($value ? __('Yes', 'open-veil') : __('No', 'open-veil')) . '</span>';
                                } else {
                                    echo '<span class="questionnaire-value">' . esc_html($value) . '</span>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Substances Used Section -->
                <div class="questionnaire-section">
                    <h3><?php _e('Substances Used', 'open-veil'); ?></h3>
                    <div class="questionnaire-grid">
                        <?php
                        $substances_used_fields = [
                            'main_substance' => __('Main Substance', 'open-veil'),
                            'route_of_administration' => __('Route of Administration', 'open-veil'),
                            'other_substances' => __('Other Substances Taken Concurrently', 'open-veil'),
                            'intoxication_level' => __('Level of Intoxication (1-10)', 'open-veil'),
                            'visual_mental_effects' => __('Visual & Mental Effects', 'open-veil'),
                            'additional_substance_info' => __('Additional Substance Information', 'open-veil'),
                        ];

                        foreach ($substances_used_fields as $field => $label) {
                            $value = get_post_meta(get_the_ID(), $field, true);
                            if (!empty($value)) {
                                echo '<div class="questionnaire-item">';
                                echo '<span class="questionnaire-label">' . esc_html($label) . ':</span>';
                                echo '<span class="questionnaire-value">' . esc_html($value) . '</span>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Visual Effects Section -->
                <div class="questionnaire-section">
                    <h3><?php _e('Visual Effects and Laser Interaction', 'open-veil'); ?></h3>
                    <div class="questionnaire-grid">
                        <?php
                        // Check if beam changed
                        $beam_changed = get_post_meta(get_the_ID(), 'beam_changed', true);
                        if ($beam_changed !== '' && $beam_changed !== false) {
                            echo '<div class="questionnaire-item">';
                            echo '<span class="questionnaire-label">' . __('Did the Beam Change?', 'open-veil') . ':</span>';
                            echo '<span class="questionnaire-value">' . ($beam_changed ? __('Yes', 'open-veil') : __('No', 'open-veil')) . '</span>';
                            echo '</div>';

                            if ($beam_changed) {
                                $beam_changes_description = get_post_meta(get_the_ID(), 'beam_changes_description', true);
                                if (!empty($beam_changes_description)) {
                                    echo '<div class="questionnaire-item">';
                                    echo '<span class="questionnaire-label">' . __('Describe Beam Changes', 'open-veil') . ':</span>';
                                    echo '<span class="questionnaire-value">' . esc_html($beam_changes_description) . '</span>';
                                    echo '</div>';
                                }
                            }
                        }

                        // Check if code of reality was seen
                        $saw_code = get_post_meta(get_the_ID(), 'saw_code_of_reality', true);
                        if ($saw_code !== '' && $saw_code !== false) {
                            echo '<div class="questionnaire-item">';
                            echo '<span class="questionnaire-label">' . __('Did You See the Code of Reality?', 'open-veil') . ':</span>';
                            echo '<span class="questionnaire-value">' . ($saw_code ? __('Yes', 'open-veil') : __('No', 'open-veil')) . '</span>';
                            echo '</div>';

                            if ($saw_code) {
                                $code_fields = [
                                    'symbols_seen' => __('Symbols Seen?', 'open-veil'),
                                    'symbols_description' => __('Describe Symbols', 'open-veil'),
                                    'code_moving' => __('Was the Code Moving?', 'open-veil'),
                                    'movement_direction' => __('Direction of Movement', 'open-veil'),
                                    'characters_tiny' => __('Were Characters Tiny or Hard to See?', 'open-veil'),
                                    'size_changed' => __('Did Their Size Change?', 'open-veil'),
                                    'code_clarity' => __('Code Clarity (1-10)', 'open-veil'),
                                    'code_behaved_like_object' => __('Did the Code Behave Like a Real Object?', 'open-veil'),
                                    'could_influence_code' => __('Could You Influence the Code?', 'open-veil'),
                                    'influence_description' => __('Describe Influence', 'open-veil'),
                                    'code_persisted_without_laser' => __('Did the Code Persist Without the Laser?', 'open-veil'),
                                    'persisted_when_looked_away' => __('When You Looked Away', 'open-veil'),
                                    'persisted_after_turning_off' => __('After Turning Off', 'open-veil'),
                                    'where_else_seen' => __('Where Else Did You See It?', 'open-veil'),
                                ];

                                foreach ($code_fields as $field => $label) {
                                    $value = get_post_meta(get_the_ID(), $field, true);
                                    if ($value !== '' && $value !== false) {
                                        echo '<div class="questionnaire-item">';
                                        echo '<span class="questionnaire-label">' . esc_html($label) . ':</span>';
                                        if (in_array($field, ['symbols_seen', 'code_moving', 'characters_tiny', 'size_changed', 'code_behaved_like_object', 'could_influence_code', 'code_persisted_without_laser', 'persisted_when_looked_away', 'persisted_after_turning_off'])) {
                                            echo '<span class="questionnaire-value">' . ($value ? __('Yes', 'open-veil') : __('No', 'open-veil')) . '</span>';
                                        } else {
                                            echo '<span class="questionnaire-value">' . esc_html($value) . '</span>';
                                        }
                                        echo '</div>';
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Other Visual Phenomena Section -->
                <div class="questionnaire-section">
                    <h3><?php _e('Other Visual Phenomena', 'open-veil'); ?></h3>
                    <div class="questionnaire-grid">
                        <?php
                        $other_phenomena_fields = [
                            'noticed_anything_else' => __('Did You Notice Anything Else?', 'open-veil'),
                            'experiment_duration' => __('Experiment Duration (Minutes)', 'open-veil'),
                            'questions_comments_suggestions' => __('Questions, Comments, Suggestions', 'open-veil'),
                        ];

                        foreach ($other_phenomena_fields as $field => $label) {
                            $value = get_post_meta(get_the_ID(), $field, true);
                            if (!empty($value)) {
                                echo '<div class="questionnaire-item">';
                                echo '<span class="questionnaire-label">' . esc_html($label) . ':</span>';
                                echo '<span class="questionnaire-value">' . esc_html($value) . '</span>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php if (comments_open() || get_comments_number()) : ?>
                <div class="trial-comments">
                    <h2><?php _e('Discussion', 'open-veil'); ?></h2>
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>
        </article>
    </div>
</div>