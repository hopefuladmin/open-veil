<?php

declare(strict_types=1);

namespace OpenVeil\API;

/**
 * Abstract REST API
 * 
 * Base class for all versioned API implementations.
 * 
 * @package OpenVeil\API
 */
abstract class AbstractAPI
{
    /**
     * API namespace
     */
    const API_NAMESPACE = 'open-veil';

    /**
     * API version
     */
    protected string $version;

    /**
     * Full API namespace with version
     */
    protected string $namespace;

    /**
     * Constructor
     * 
     * @param string $version API version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
        $this->namespace = self::API_NAMESPACE . '/' . $this->version;
        
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register API routes
     * 
     * @return void
     */
    abstract public function register_routes(): void;

    /**
     * Formats protocol data for API response.
     *
     * @param \WP_Post $protocol Protocol post object
     * @return array Formatted protocol data
     */
    protected function prepare_protocol_response(\WP_Post $protocol): array
    {
        $response = [
            'id' => $protocol->ID,
            'title' => $protocol->post_title,
            'content' => $protocol->post_content,
            'date' => $protocol->post_date,
            'modified' => $protocol->post_modified,
            'author' => [
                'id' => $protocol->post_author,
                'name' => get_the_author_meta('display_name', $protocol->post_author),
            ],
            'permalink' => get_permalink($protocol->ID),
            'meta' => [
                'laser_wavelength' => get_post_meta($protocol->ID, 'laser_wavelength', true),
                'laser_power' => get_post_meta($protocol->ID, 'laser_power', true),
                'substance_dose' => get_post_meta($protocol->ID, 'substance_dose', true),
                'projection_distance' => get_post_meta($protocol->ID, 'projection_distance', true),
            ],
            'taxonomies' => [],
        ];

        // Get taxonomy terms
        $taxonomies = [
            'laser_class',
            'diffraction_grating_spec',
            'equipment',
            'substance',
            'administration_method',
            'administration_protocol',
            'projection_surface',
        ];

        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($protocol->ID, $taxonomy);

            if (!empty($terms) && !is_wp_error($terms)) {
                $response['taxonomies'][$taxonomy] = [];

                foreach ($terms as $term) {
                    $response['taxonomies'][$taxonomy][] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                    ];
                }
            } else {
                $response['taxonomies'][$taxonomy] = [];
            }
        }

        return $response;
    }

    /**
     * Formats trial data for API response.
     *
     * @param \WP_Post $trial Trial post object
     * @return array Formatted trial data
     */
    protected function prepare_trial_response(\WP_Post $trial): array
    {
        $protocol_id = get_post_meta($trial->ID, 'protocol_id', true);
        $protocol = $protocol_id ? get_post($protocol_id) : null;

        $response = [
            'id' => $trial->ID,
            'title' => $trial->post_title,
            'content' => $trial->post_content,
            'date' => $trial->post_date,
            'modified' => $trial->post_modified,
            'author' => [
                'id' => $trial->post_author,
                'name' => get_the_author_meta('display_name', $trial->post_author),
            ],
            'permalink' => get_permalink($trial->ID),
            'protocol' => $protocol ? [
                'id' => $protocol->ID,
                'title' => $protocol->post_title,
                'permalink' => get_permalink($protocol->ID),
            ] : null,
            'meta' => [
                'protocol_id' => $protocol_id,
                'laser_wavelength' => get_post_meta($trial->ID, 'laser_wavelength', true),
                'laser_power' => get_post_meta($trial->ID, 'laser_power', true),
                'substance_dose' => get_post_meta($trial->ID, 'substance_dose', true),
                'projection_distance' => get_post_meta($trial->ID, 'projection_distance', true),
                'administration_notes' => get_post_meta($trial->ID, 'administration_notes', true),
                'additional_observers' => get_post_meta($trial->ID, 'additional_observers', true),
            ],
            'taxonomies' => [],
            'questionnaire' => $this->get_trial_questionnaire_data($trial->ID),
        ];

        // Get taxonomy terms
        $taxonomies = [
            'laser_class',
            'diffraction_grating_spec',
            'equipment',
            'substance',
            'administration_method',
            'administration_protocol',
            'projection_surface',
        ];

        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($trial->ID, $taxonomy);

            if (!empty($terms) && !is_wp_error($terms)) {
                $response['taxonomies'][$taxonomy] = [];

                foreach ($terms as $term) {
                    $response['taxonomies'][$taxonomy][] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                    ];
                }
            } else {
                $response['taxonomies'][$taxonomy] = [];
            }
        }

        return $response;
    }

    /**
     * Gets questionnaire data for a trial.
     *
     * @param int $trial_id Trial post ID
     * @return array Questionnaire data
     */
    protected function get_trial_questionnaire_data(int $trial_id): array
    {
        $questionnaire = [
            'about_you' => [
                'participant_name' => get_post_meta($trial_id, 'participant_name', true),
                'participant_email' => get_post_meta($trial_id, 'participant_email', true),
                'psychedelic_experience_level' => get_post_meta($trial_id, 'psychedelic_experience_level', true),
                'dmt_experience_level' => get_post_meta($trial_id, 'dmt_experience_level', true),
                'simulation_theory_interest' => get_post_meta($trial_id, 'simulation_theory_interest', true),
                'how_found_us' => get_post_meta($trial_id, 'how_found_us', true),
            ],
            'experiment_setup' => [
                'received_laser_from_us' => get_post_meta($trial_id, 'received_laser_from_us', true),
                'beam_shape' => get_post_meta($trial_id, 'beam_shape', true),
                'laser_power_source' => get_post_meta($trial_id, 'laser_power_source', true),
                'accessories_used' => get_post_meta($trial_id, 'accessories_used', true),
                'set_and_setting' => get_post_meta($trial_id, 'set_and_setting', true),
                'experiment_datetime' => get_post_meta($trial_id, 'experiment_datetime', true),
                'lighting_conditions' => get_post_meta($trial_id, 'lighting_conditions', true),
                'surfaces_used' => get_post_meta($trial_id, 'surfaces_used', true),
                'additional_setup_info' => get_post_meta($trial_id, 'additional_setup_info', true),
            ],
            'substances_used' => [
                'other_substances' => get_post_meta($trial_id, 'other_substances', true),
                'intoxication_level' => get_post_meta($trial_id, 'intoxication_level', true),
                'visual_mental_effects' => get_post_meta($trial_id, 'visual_mental_effects', true),
                'additional_substance_info' => get_post_meta($trial_id, 'additional_substance_info', true),
            ],
            'visual_effects' => [
                'beam_changed' => get_post_meta($trial_id, 'beam_changed', true),
                'beam_changes_description' => get_post_meta($trial_id, 'beam_changes_description', true),
                'saw_code_of_reality' => get_post_meta($trial_id, 'saw_code_of_reality', true),
                'symbols_seen' => get_post_meta($trial_id, 'symbols_seen', true),
                'symbols_description' => get_post_meta($trial_id, 'symbols_description', true),
                'code_moving' => get_post_meta($trial_id, 'code_moving', true),
                'movement_direction' => get_post_meta($trial_id, 'movement_direction', true),
                'characters_tiny' => get_post_meta($trial_id, 'characters_tiny', true),
                'size_changed' => get_post_meta($trial_id, 'size_changed', true),
                'code_clarity' => get_post_meta($trial_id, 'code_clarity', true),
                'code_behaved_like_object' => get_post_meta($trial_id, 'code_behaved_like_object', true),
                'could_influence_code' => get_post_meta($trial_id, 'could_influence_code', true),
                'influence_description' => get_post_meta($trial_id, 'influence_description', true),
                'code_persisted_without_laser' => get_post_meta($trial_id, 'code_persisted_without_laser', true),
                'persisted_when_looked_away' => get_post_meta($trial_id, 'persisted_when_looked_away', true),
                'persisted_after_turning_off' => get_post_meta($trial_id, 'persisted_after_turning_off', true),
                'where_else_seen' => get_post_meta($trial_id, 'where_else_seen', true),
            ],
            'other_phenomena' => [
                'noticed_anything_else' => get_post_meta($trial_id, 'noticed_anything_else', true),
                'experiment_duration' => get_post_meta($trial_id, 'experiment_duration', true),
                'questions_comments_suggestions' => get_post_meta($trial_id, 'questions_comments_suggestions', true),
            ],
        ];

        return $questionnaire;
    }

    /**
     * Retrieves schema information including available taxonomies and metadata fields.
     *
     * @return array Schema information
     */
    public function get_schema()
    {
        $taxonomies = [
            'equipment',
            'laser_class',
            'diffraction_grating_spec',
            'substance',
            'administration_method',
            'administration_protocol',
            'projection_surface',
        ];

        $schema = [
            'taxonomies' => [],
            'meta_keys' => [
                'protocol' => [
                    'laser_wavelength' => [
                        'type' => 'integer',
                        'description' => __('Laser wavelength in nanometers', 'open-veil'),
                        'range' => [400, 700],
                    ],
                    'laser_power' => [
                        'type' => 'number',
                        'description' => __('Laser power in milliwatts', 'open-veil'),
                        'range' => [0, 5],
                    ],
                    'substance_dose' => [
                        'type' => 'number',
                        'description' => __('Substance dose in grams', 'open-veil'),
                        'min' => 0,
                    ],
                    'projection_distance' => [
                        'type' => 'number',
                        'description' => __('Projection distance in feet', 'open-veil'),
                        'range' => [1, 20],
                    ],
                ],
                'trial' => [
                    'protocol_id' => [
                        'type' => 'integer',
                        'description' => __('ID of the protocol this trial is based on', 'open-veil'),
                        'required' => true,
                    ],
                    'laser_wavelength' => [
                        'type' => 'integer',
                        'description' => __('Laser wavelength in nanometers', 'open-veil'),
                        'range' => [400, 700],
                    ],
                    'laser_power' => [
                        'type' => 'number',
                        'description' => __('Laser power in milliwatts', 'open-veil'),
                        'range' => [0, 5],
                    ],
                    'substance_dose' => [
                        'type' => 'number',
                        'description' => __('Substance dose in grams', 'open-veil'),
                        'min' => 0,
                    ],
                    'projection_distance' => [
                        'type' => 'number',
                        'description' => __('Projection distance in feet', 'open-veil'),
                        'range' => [1, 20],
                    ],
                    'administration_notes' => [
                        'type' => 'string',
                        'description' => __('Notes about the administration method', 'open-veil'),
                    ],
                    'additional_observers' => [
                        'type' => 'boolean',
                        'description' => __('Whether there were additional observers', 'open-veil'),
                    ],
                ],
            ],
            'questionnaire' => [
                'about_you' => [
                    'participant_name' => [
                        'type' => 'string',
                        'description' => __('Participant name', 'open-veil'),
                    ],
                    'participant_email' => [
                        'type' => 'string',
                        'description' => __('Participant email', 'open-veil'),
                    ],
                    'psychedelic_experience_level' => [
                        'type' => 'string',
                        'description' => __('Psychedelic experience level', 'open-veil'),
                    ],
                    'dmt_experience_level' => [
                        'type' => 'string',
                        'description' => __('DMT experience level', 'open-veil'),
                    ],
                    'simulation_theory_interest' => [
                        'type' => 'string',
                        'description' => __('Interest in simulation theory', 'open-veil'),
                    ],
                    'how_found_us' => [
                        'type' => 'string',
                        'description' => __('How participant found the project', 'open-veil'),
                    ],
                ],
                'experiment_setup' => [
                    'received_laser_from_us' => [
                        'type' => 'boolean',
                        'description' => __('Whether the participant received laser from project', 'open-veil'),
                    ],
                    'beam_shape' => [
                        'type' => 'string',
                        'description' => __('Shape of the laser beam', 'open-veil'),
                    ],
                    'laser_power_source' => [
                        'type' => 'string',
                        'description' => __('Power source for the laser', 'open-veil'),
                    ],
                    'accessories_used' => [
                        'type' => 'string',
                        'description' => __('Accessories used with the laser', 'open-veil'),
                    ],
                    'set_and_setting' => [
                        'type' => 'string',
                        'description' => __('Set and setting for the experiment', 'open-veil'),
                    ],
                    'experiment_datetime' => [
                        'type' => 'string',
                        'description' => __('Date and time of the experiment', 'open-veil'),
                    ],
                    'lighting_conditions' => [
                        'type' => 'string',
                        'description' => __('Lighting conditions during experiment', 'open-veil'),
                    ],
                    'surfaces_used' => [
                        'type' => 'string',
                        'description' => __('Surfaces used for projection', 'open-veil'),
                    ],
                    'additional_setup_info' => [
                        'type' => 'string',
                        'description' => __('Additional setup information', 'open-veil'),
                    ],
                ],
                'substances_used' => [
                    'other_substances' => [
                        'type' => 'string',
                        'description' => __('Other substances used', 'open-veil'),
                    ],
                    'intoxication_level' => [
                        'type' => 'string',
                        'description' => __('Level of intoxication', 'open-veil'),
                    ],
                    'visual_mental_effects' => [
                        'type' => 'string',
                        'description' => __('Visual and mental effects', 'open-veil'),
                    ],
                    'additional_substance_info' => [
                        'type' => 'string',
                        'description' => __('Additional information about substances', 'open-veil'),
                    ],
                ],
                'visual_effects' => [
                    'beam_changed' => [
                        'type' => 'boolean',
                        'description' => __('Whether the beam changed visually', 'open-veil'),
                    ],
                    'beam_changes_description' => [
                        'type' => 'string',
                        'description' => __('Description of beam changes', 'open-veil'),
                    ],
                    'saw_code_of_reality' => [
                        'type' => 'boolean',
                        'description' => __('Whether participant saw code of reality', 'open-veil'),
                    ],
                    'symbols_seen' => [
                        'type' => 'string',
                        'description' => __('Types of symbols seen', 'open-veil'),
                    ],
                    'symbols_description' => [
                        'type' => 'string',
                        'description' => __('Description of symbols seen', 'open-veil'),
                    ],
                    'code_moving' => [
                        'type' => 'boolean',
                        'description' => __('Whether the code was moving', 'open-veil'),
                    ],
                    'movement_direction' => [
                        'type' => 'string',
                        'description' => __('Direction of code movement', 'open-veil'),
                    ],
                    'characters_tiny' => [
                        'type' => 'boolean',
                        'description' => __('Whether characters were tiny', 'open-veil'),
                    ],
                    'size_changed' => [
                        'type' => 'boolean',
                        'description' => __('Whether size of characters changed', 'open-veil'),
                    ],
                    'code_clarity' => [
                        'type' => 'string',
                        'description' => __('Clarity of the code', 'open-veil'),
                    ],
                    'code_behaved_like_object' => [
                        'type' => 'boolean',
                        'description' => __('Whether code behaved like physical object', 'open-veil'),
                    ],
                    'could_influence_code' => [
                        'type' => 'boolean',
                        'description' => __('Whether participant could influence code', 'open-veil'),
                    ],
                    'influence_description' => [
                        'type' => 'string',
                        'description' => __('Description of how code was influenced', 'open-veil'),
                    ],
                    'code_persisted_without_laser' => [
                        'type' => 'boolean',
                        'description' => __('Whether code persisted without laser', 'open-veil'),
                    ],
                    'persisted_when_looked_away' => [
                        'type' => 'boolean',
                        'description' => __('Whether code persisted when looked away', 'open-veil'),
                    ],
                    'persisted_after_turning_off' => [
                        'type' => 'boolean',
                        'description' => __('Whether code persisted after turning off laser', 'open-veil'),
                    ],
                    'where_else_seen' => [
                        'type' => 'string',
                        'description' => __('Where else code was seen', 'open-veil'),
                    ],
                ],
                'other_phenomena' => [
                    'noticed_anything_else' => [
                        'type' => 'string',
                        'description' => __('Other phenomena noticed', 'open-veil'),
                    ],
                    'experiment_duration' => [
                        'type' => 'string',
                        'description' => __('Duration of the experiment', 'open-veil'),
                    ],
                    'questions_comments_suggestions' => [
                        'type' => 'string',
                        'description' => __('Questions, comments, or suggestions', 'open-veil'),
                    ],
                ],
            ],
        ];

        // Get taxonomy terms
        foreach ($taxonomies as $taxonomy) {
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);

            if (!is_wp_error($terms)) {
                $schema['taxonomies'][$taxonomy] = [];

                foreach ($terms as $term) {
                    $schema['taxonomies'][$taxonomy][] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                    ];
                }
            }
        }

        return $schema;
    }
}