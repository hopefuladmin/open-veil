<?php

declare(strict_types=1);

namespace OpenVeil\API\V1;

/**
 * REST API V1
 * 
 * Implements version 1 of the REST API.
 * 
 * @package OpenVeil\API
 */
class Api
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
     * Default items per page limit
     */
    const DEFAULT_PER_PAGE = 10;

    /**
     * Maximum items per page limit
     */
    const MAX_PER_PAGE = 100;

    /**
     * Valid sort fields for protocols
     */
    protected array $protocol_sort_fields = [
        'id' => 'ID',
        'title' => 'post_title',
        'date' => 'post_date',
        'modified' => 'post_modified',
        'author' => 'post_author',
    ];

    /**
     * Valid sort fields for trials
     */
    protected array $trial_sort_fields = [
        'id' => 'ID',
        'title' => 'post_title',
        'date' => 'post_date',
        'modified' => 'post_modified',
        'author' => 'post_author',
    ];

    /**
     * Valid embeddable resources
     */
    protected array $embeddable_resources = [
        'protocol' => ['trials'],  // Protocols can embed trials
        'trial' => ['protocol'],   // Trials can embed protocol
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->version = 'v1';
        $this->namespace = self::API_NAMESPACE . '/' . $this->version;
        
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Sets up all REST API endpoints for protocols and trials.
     *
     * @return void
     */
    public function register_routes(): void
    {
        // Protocol endpoints
        register_rest_route($this->namespace, '/protocol/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/protocol/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_protocol'],
            'permission_callback' => [$this, 'can_edit_protocol'],
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, '/protocol/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_protocol'],
            'permission_callback' => [$this, 'can_delete_protocol'],
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, '/protocol', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocols'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/protocol', [
            'methods' => 'POST',
            'callback' => [$this, 'create_protocol'],
            'permission_callback' => function () {
                $api_access = \OpenVeil\ACF\Options::get_option('api_access', 'public');

                if ($api_access === 'admin') {
                    return current_user_can('manage_options');
                } elseif ($api_access === 'logged_in') {
                    return is_user_logged_in();
                }

                return true;
            },
        ]);

        register_rest_route($this->namespace, '/protocol/(?P<id>\d+)/csl', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol_csl'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        // Trial endpoints
        register_rest_route($this->namespace, '/trial', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trials'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/trial/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trial'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/trial/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_trial'],
            'permission_callback' => [$this, 'can_edit_trial'],
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, '/trial/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_trial'],
            'permission_callback' => [$this, 'can_delete_trial'],
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, '/trial', [
            'methods' => 'POST',
            'callback' => [$this, 'create_trial'],
            'permission_callback' => function () {
                $api_access = \OpenVeil\ACF\Options::get_option('api_access', 'public');
                $guest_submissions = \OpenVeil\ACF\Options::get_option('guest_submissions', true);

                if ($api_access === 'admin') {
                    return current_user_can('manage_options');
                } elseif ($api_access === 'logged_in') {
                    return is_user_logged_in();
                }

                return $guest_submissions || is_user_logged_in();
            },
        ]);

        register_rest_route($this->namespace, '/trial/(?P<id>\d+)/csl', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trial_csl'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        // Schema endpoint
        register_rest_route($this->namespace, '/schema', [
            'methods' => 'GET',
            'callback' => [$this, 'get_schema'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);
    }

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
            'protocol' => $protocol 
                ? [
                    'id' => $protocol->ID,
                    'title' => $protocol->post_title,
                    'permalink' => get_permalink($protocol->ID),
                  ]
                : null,
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
     * Gets questionnaire data for a trial using the schema definition.
     *
     * @param int $trial_id Trial post ID
     * @return array Questionnaire data
     */
    protected function get_trial_questionnaire_data(int $trial_id): array
    {
        $schema = $this->get_schema();
        $questionnaire = [];
        
        // If schema has questionnaire definition, use it to build the data structure
        if (isset($schema['questionnaire']) && is_array($schema['questionnaire'])) {
            foreach ($schema['questionnaire'] as $section => $fields) {
                if (!is_array($fields)) {
                    continue;
                }
                
                $questionnaire[$section] = [];
                
                foreach ($fields as $field => $field_data) {
                    $value = get_post_meta($trial_id, $field, true);
                    
                    // Convert values to the appropriate type based on schema
                    if (isset($field_data['type'])) {
                        switch ($field_data['type']) {
                            case 'boolean':
                                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                                break;
                            case 'integer':
                                $value = $value !== '' ? (int)$value : '';
                                break;
                            case 'number':
                                $value = $value !== '' ? (float)$value : '';
                                break;
                        }
                    }
                    
                    $questionnaire[$section][$field] = $value;
                }
            }
        }
        
        return $questionnaire;
    }

    /**
     * Checks if the current user can edit a protocol.
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function can_edit_protocol(\WP_REST_Request $request)
    {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);

        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }

        // Admin can edit any protocol
        if (current_user_can('manage_options')) {
            return true;
        }

        // Authors can edit their own protocols
        if (is_user_logged_in() && get_current_user_id() === (int)$protocol->post_author) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the current user can delete a protocol.
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function can_delete_protocol(\WP_REST_Request $request)
    {
        // Use the same permissions as editing for now
        return $this->can_edit_protocol($request);
    }

    /**
     * Checks if the current user can edit a trial.
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function can_edit_trial(\WP_REST_Request $request)
    {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);

        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }

        // Admin can edit any trial
        if (current_user_can('manage_options')) {
            return true;
        }

        // Authors can edit their own trials
        if (is_user_logged_in() && get_current_user_id() === (int)$trial->post_author) {
            return true;
        }

        // Check for claim token for guest submissions
        $claim_token = isset($_GET['claim_token']) ? sanitize_text_field($_GET['claim_token']) : '';
        if (!empty($claim_token)) {
            $stored_token = get_post_meta($trial_id, '_claim_token', true);
            $expiry = (int)get_post_meta($trial_id, '_claim_token_expiry', true);

            if ($claim_token === $stored_token && time() < $expiry) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the current user can delete a trial.
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function can_delete_trial(\WP_REST_Request $request)
    {
        // Use the same permissions as editing for now, but without claim token support
        $trial_id = $request['id'];
        $trial = get_post($trial_id);

        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }

        // Admin can delete any trial
        if (current_user_can('manage_options')) {
            return true;
        }

        // Authors can delete their own trials
        if (is_user_logged_in() && get_current_user_id() === (int)$trial->post_author) {
            return true;
        }

        return false;
    }

    /**
     * Updates an existing protocol.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Updated protocol or error
     */
    public function update_protocol(\WP_REST_Request $request)
    {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);
        
        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }
        
        $params = $request->get_params();
        $updated = false;
        
        // Update post fields if provided
        $post_data = [];
        
        if (isset($params['title'])) {
            $post_data['post_title'] = sanitize_text_field($params['title']);
            $updated = true;
        }
        
        if (isset($params['content'])) {
            $post_data['post_content'] = wp_kses_post($params['content']);
            $updated = true;
        }
        
        // Update post if changes are requested
        if (!empty($post_data)) {
            $post_data['ID'] = $protocol_id;
            wp_update_post($post_data);
        }
        
        // Update meta fields if provided
        if (isset($params['meta']) && is_array($params['meta'])) {
            foreach ($params['meta'] as $key => $value) {
                update_post_meta($protocol_id, $key, $value);
                $updated = true;
            }
        }
        
        // Update taxonomies if provided
        if (isset($params['taxonomies']) && is_array($params['taxonomies'])) {
            foreach ($params['taxonomies'] as $taxonomy => $terms) {
                wp_set_object_terms($protocol_id, $terms, $taxonomy);
                $updated = true;
            }
        }
        
        if (!$updated) {
            return new \WP_Error('no_changes', __('No changes were provided', 'open-veil'), ['status' => 400]);
        }
        
        // Return the updated protocol
        $protocol = get_post($protocol_id);
        return $this->prepare_protocol_response($protocol);
    }
    
    /**
     * Deletes a protocol.
     *
     * @param \WP_REST_Request $request REST API request
     * @return \WP_REST_Response|\WP_Error Success response or error
     */
    public function delete_protocol(\WP_REST_Request $request)
    {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);
        
        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }
        
        // Check if there are trials associated with this protocol
        $trials = get_posts([
            'post_type' => 'trial',
            'post_status' => 'any',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'protocol_id',
                    'value' => $protocol_id,
                    'compare' => '=',
                ]
            ],
        ]);
        
        if (!empty($trials)) {
            return new \WP_Error(
                'protocol_has_trials',
                __('Cannot delete protocol with associated trials', 'open-veil'),
                ['status' => 400]
            );
        }
        
        $result = wp_delete_post($protocol_id, true);
        
        if (!$result) {
            return new \WP_Error('delete_failed', __('Failed to delete protocol', 'open-veil'), ['status' => 500]);
        }
        
        return new \WP_REST_Response(
            ['message' => __('Protocol deleted successfully', 'open-veil')],
            200
        );
    }
    
    /**
     * Updates an existing trial.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Updated trial or error
     */
    public function update_trial(\WP_REST_Request $request)
    {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);
        
        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }
        
        $params = $request->get_params();
        $updated = false;
        
        // Update post fields if provided
        $post_data = [];
        
        if (isset($params['title'])) {
            $post_data['post_title'] = sanitize_text_field($params['title']);
            $updated = true;
        }
        
        if (isset($params['content'])) {
            $post_data['post_content'] = wp_kses_post($params['content']);
            $updated = true;
        }
        
        // Update post if changes are requested
        if (!empty($post_data)) {
            $post_data['ID'] = $trial_id;
            wp_update_post($post_data);
        }
        
        // Update meta fields if provided
        if (isset($params['meta']) && is_array($params['meta'])) {
            foreach ($params['meta'] as $key => $value) {
                update_post_meta($trial_id, $key, $value);
                $updated = true;
            }
        }
        
        // Update taxonomies if provided
        if (isset($params['taxonomies']) && is_array($params['taxonomies'])) {
            foreach ($params['taxonomies'] as $taxonomy => $terms) {
                wp_set_object_terms($trial_id, $terms, $taxonomy);
                $updated = true;
            }
        }
        
        // Update questionnaire data if provided
        if (isset($params['questionnaire']) && is_array($params['questionnaire'])) {
            $this->save_questionnaire_data($trial_id, $params['questionnaire']);
            $updated = true;
        }
        
        if (!$updated) {
            return new \WP_Error('no_changes', __('No changes were provided', 'open-veil'), ['status' => 400]);
        }
        
        // Return the updated trial
        $trial = get_post($trial_id);
        return $this->prepare_trial_response($trial);
    }
    
    /**
     * Deletes a trial.
     *
     * @param \WP_REST_Request $request REST API request
     * @return \WP_REST_Response|\WP_Error Success response or error
     */
    public function delete_trial(\WP_REST_Request $request)
    {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);
        
        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }
        
        $result = wp_delete_post($trial_id, true);
        
        if (!$result) {
            return new \WP_Error('delete_failed', __('Failed to delete trial', 'open-veil'), ['status' => 500]);
        }
        
        return new \WP_REST_Response(
            ['message' => __('Trial deleted successfully', 'open-veil')],
            200
        );
    }

    /**
     * Retrieves a protocol by its ID.
     *
     * @param \WP_REST_Request $request REST API request
     * @return \WP_REST_Response|\WP_Error Standardized response or error
     */
    public function get_protocol(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);

        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }

        $embed_options = $this->parse_embed_params($request, 'protocol');
        $selected_fields = $this->parse_field_selection($request);

        $protocol_data = $this->prepare_protocol_for_response($protocol, $embed_options);

        // Apply field selection if needed
        if ($selected_fields !== null) {
            $protocol_data = $this->apply_field_selection($protocol_data, $selected_fields);
        }

        return $this->create_response($protocol_data);
    }

    /**
     * Retrieves all published protocols with support for pagination, sorting, filtering, embedding and field selection.
     *
     * @param \WP_REST_Request $request REST API request
     * @return \WP_REST_Response Standardized response
     */
    public function get_protocols(\WP_REST_Request $request): \WP_REST_Response
    {
        // Get query parameters
        $pagination = $this->get_pagination_params($request);
        $sorting = $this->get_sorting_params($request, 'protocol');
        $taxonomy_filters = $this->get_taxonomy_filters($request);
        $search_params = $this->get_search_params($request);
        $selected_fields = $this->parse_field_selection($request);
        $embed_options = $this->parse_embed_params($request, 'protocol');
        
        // Build WP_Query arguments
        $args = array_merge(
            [
                'post_type' => 'protocol',
                'post_status' => 'publish',
                'posts_per_page' => $pagination['per_page'],
                'offset' => $pagination['offset'],
            ],
            $sorting,
            $taxonomy_filters,
            $search_params
        );
        
        // Execute query
        $query = new \WP_Query($args);
        $protocols = [];
        
        // Format each protocol for the response
        foreach ($query->posts as $protocol) {
            $protocol_data = $this->prepare_protocol_for_response($protocol, $embed_options);
            
            // Apply field selection if needed
            if ($selected_fields !== null) {
                $protocol_data = $this->apply_field_selection($protocol_data, $selected_fields);
            }
            
            $protocols[] = $protocol_data;
        }
        
        // Calculate pagination information
        $total = $query->found_posts;
        $total_pages = ceil($total / $pagination['per_page']);
        
        // Create and return standardized response
        return $this->create_response(
            $protocols,
            $pagination,
            $total,
            $total_pages,
            $this->namespace . '/protocol',
            200
        );
    }

    /**
     * Prepares a protocol for API response with proper _links and optional embedded resources.
     *
     * @param \WP_Post $protocol Protocol post object
     * @param array $embed_options Embedding options
     * @return array Formatted protocol data with _links and optional _embedded
     */
    protected function prepare_protocol_for_response(\WP_Post $protocol, array $embed_options = []): array
    {
        // Base protocol data
        $data = $this->prepare_protocol_response($protocol);
        
        // Add hypermedia links
        $data['_links'] = [
            'self' => [
                'href' => rest_url($this->namespace . '/protocol/' . $protocol->ID),
            ],
            'collection' => [
                'href' => rest_url($this->namespace . '/protocol'),
            ],
            'csl' => [
                'href' => rest_url($this->namespace . '/protocol/' . $protocol->ID . '/csl'),
            ],
        ];
        
        // Add trial links if they exist
        $trial_args = [
            'post_type' => 'trial',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'protocol_id',
                    'value' => $protocol->ID,
                    'compare' => '=',
                ]
            ],
        ];
        
        $trials = get_posts($trial_args);
        
        if (!empty($trials)) {
            $data['_links']['trials'] = [];
            
            foreach ($trials as $trial) {
                $data['_links']['trials'][] = [
                    'href' => rest_url($this->namespace . '/trial/' . $trial->ID),
                    'title' => $trial->post_title,
                ];
            }
            
            // Embed trials if requested
            if (isset($embed_options['trials']) && $embed_options['trials']) {
                $data['_embedded'] = [];
                $data['_embedded']['trials'] = [];
                
                foreach ($trials as $trial) {
                    $data['_embedded']['trials'][] = $this->prepare_trial_for_response($trial);
                }
            }
        }
        
        return $data;
    }

    /**
     * Retrieves all published trials with support for pagination, sorting, filtering, embedding and field selection.
     *
     * @param \WP_REST_Request $request REST API request
     * @return \WP_REST_Response Standardized response
     */
    public function get_trials(\WP_REST_Request $request): \WP_REST_Response
    {
        // Get query parameters
        $pagination = $this->get_pagination_params($request);
        $sorting = $this->get_sorting_params($request, 'trial');
        $taxonomy_filters = $this->get_taxonomy_filters($request);
        $search_params = $this->get_search_params($request);
        $selected_fields = $this->parse_field_selection($request);
        $embed_options = $this->parse_embed_params($request, 'trial');
        
        // Filter by protocol_id if specified
        $meta_query = [];
        $protocol_id = $request->get_param('protocol_id');
        
        if (!empty($protocol_id)) {
            $meta_query[] = [
                'key' => 'protocol_id',
                'value' => $protocol_id,
                'compare' => '=',
            ];
        }
        
        // Build WP_Query arguments
        $args = array_merge(
            [
                'post_type' => 'trial',
                'post_status' => 'publish',
                'posts_per_page' => $pagination['per_page'],
                'offset' => $pagination['offset'],
            ],
            $sorting,
            $taxonomy_filters,
            $search_params
        );
        
        // Add meta query if it exists
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
        
        // Execute query
        $query = new \WP_Query($args);
        $trials = [];
        
        // Format each trial for the response
        foreach ($query->posts as $trial) {
            $trial_data = $this->prepare_trial_for_response($trial, $embed_options);
            
            // Apply field selection if needed
            if ($selected_fields !== null) {
                $trial_data = $this->apply_field_selection($trial_data, $selected_fields);
            }
            
            $trials[] = $trial_data;
        }
        
        // Calculate pagination information
        $total = $query->found_posts;
        $total_pages = ceil($total / $pagination['per_page']);
        
        // Create and return standardized response
        return $this->create_response(
            $trials,
            $pagination,
            $total,
            $total_pages,
            $this->namespace . '/trial',
            200
        );
    }

    /**
     * Prepares a trial for API response with proper _links and optional embedded resources.
     *
     * @param \WP_Post $trial Trial post object
     * @param array $embed_options Embedding options
     * @return array Formatted trial data with _links and optional _embedded
     */
    protected function prepare_trial_for_response(\WP_Post $trial, array $embed_options = []): array
    {
        // Base trial data
        $data = $this->prepare_trial_response($trial);
        
        // Get protocol data for links
        $protocol_id = get_post_meta($trial->ID, 'protocol_id', true);
        $protocol = $protocol_id ? get_post($protocol_id) : null;
        
        // Add hypermedia links
        $data['_links'] = [
            'self' => [
                'href' => rest_url($this->namespace . '/trial/' . $trial->ID),
            ],
            'collection' => [
                'href' => rest_url($this->namespace . '/trial'),
            ],
            'csl' => [
                'href' => rest_url($this->namespace . '/trial/' . $trial->ID . '/csl'),
            ],
        ];
        
        // Add protocol link if it exists
        if ($protocol) {
            $data['_links']['protocol'] = [
                'href' => rest_url($this->namespace . '/protocol/' . $protocol_id),
                'title' => $protocol->post_title,
            ];
            
            // Embed protocol if requested
            if (isset($embed_options['protocol']) && $embed_options['protocol']) {
                $data['_embedded'] = [];
                $data['_embedded']['protocol'] = $this->prepare_protocol_for_response($protocol);
            }
        }
        
        // Remove the legacy protocol structure and use _links/_embedded instead
        if (isset($data['protocol'])) {
            unset($data['protocol']);
        }
        
        return $data;
    }

    /**
     * Retrieves a trial by its ID with support for embedding and field selection.
     *
     * @param \WP_REST_Request $request REST API request
     * @return \WP_REST_Response|\WP_Error Standardized response or error
     */
    public function get_trial(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);

        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }

        $embed_options = $this->parse_embed_params($request, 'trial');
        $selected_fields = $this->parse_field_selection($request);

        $trial_data = $this->prepare_trial_for_response($trial, $embed_options);

        // Apply field selection if needed
        if ($selected_fields !== null) {
            $trial_data = $this->apply_field_selection($trial_data, $selected_fields);
        }

        return $this->create_response($trial_data);
    }

    /**
     * Creates a new protocol.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Created protocol or error
     */
    public function create_protocol(\WP_REST_Request $request)
    {
        $params = $request->get_params();

        // Validate required fields
        if (empty($params['title'])) {
            return new \WP_Error('missing_title', __('Title is required', 'open-veil'), ['status' => 400]);
        }

        // Create post
        $post_data = [
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => isset($params['content']) ? wp_kses_post($params['content']) : '',
            'post_status' => 'publish',
            'post_type' => 'protocol',
        ];

        $protocol_id = wp_insert_post($post_data);

        if (is_wp_error($protocol_id)) {
            return $protocol_id;
        }

        // Save meta fields
        if (isset($params['meta']) && is_array($params['meta'])) {
            foreach ($params['meta'] as $key => $value) {
                update_post_meta($protocol_id, $key, $value);
            }
        }

        // Save taxonomies
        if (isset($params['taxonomies']) && is_array($params['taxonomies'])) {
            foreach ($params['taxonomies'] as $taxonomy => $terms) {
                wp_set_object_terms($protocol_id, $terms, $taxonomy);
            }
        }

        // Return the created protocol
        $protocol = get_post($protocol_id);

        return $this->prepare_protocol_response($protocol);
    }

    /**
     * Retrieves citation data for a protocol in CSL-JSON format.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error CSL-JSON data or error
     */
    public function get_protocol_csl($request)
    {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);

        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }

        $author = get_user_by('id', $protocol->post_author);
        $author_name = $author ? $author->display_name : '';

        $csl = [
            'id' => $protocol_id,
            'type' => 'article',
            'title' => $protocol->post_title,
            'author' => [
                [
                    'family' => $author_name,
                    'given' => '',
                ]
            ],
            'issued' => [
                'date-parts' => [
                    [date('Y', strtotime($protocol->post_date)), date('m', strtotime($protocol->post_date)), date('d', strtotime($protocol->post_date))]
                ]
            ],
            'URL' => get_permalink($protocol_id),
            'publisher' => get_bloginfo('name'),
        ];

        return $csl;
    }

    /**
     * Creates a new trial.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Created trial or error
     */
    public function create_trial($request)
    {
        $params = $request->get_params();

        // Validate required fields
        if (empty($params['title'])) {
            return new \WP_Error('missing_title', __('Title is required', 'open-veil'), ['status' => 400]);
        }

        if (empty($params['meta']['protocol_id'])) {
            return new \WP_Error('missing_protocol_id', __('Protocol ID is required', 'open-veil'), ['status' => 400]);
        }

        // Check if protocol exists
        $protocol = get_post($params['meta']['protocol_id']);
        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('invalid_protocol_id', __('Invalid Protocol ID', 'open-veil'), ['status' => 400]);
        }

        // Determine post status based on user
        $post_status = current_user_can('edit_posts') ? 'publish' : 'pending';

        // Create post
        $post_data = [
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => isset($params['content']) ? wp_kses_post($params['content']) : '',
            'post_status' => $post_status,
            'post_type' => 'trial',
        ];

        // Set author if user is logged in
        if (is_user_logged_in()) {
            $post_data['post_author'] = get_current_user_id();
        }

        $trial_id = wp_insert_post($post_data);

        if (is_wp_error($trial_id)) {
            return $trial_id;
        }

        // Save meta fields
        if (isset($params['meta']) && is_array($params['meta'])) {
            foreach ($params['meta'] as $key => $value) {
                update_post_meta($trial_id, $key, $value);
            }
        }

        // Save taxonomies
        if (isset($params['taxonomies']) && is_array($params['taxonomies'])) {
            foreach ($params['taxonomies'] as $taxonomy => $terms) {
                wp_set_object_terms($trial_id, $terms, $taxonomy);
            }
        }

        // Save questionnaire data if provided
        if (isset($params['questionnaire']) && is_array($params['questionnaire'])) {
            $this->save_questionnaire_data($trial_id, $params['questionnaire']);
        }

        // Generate claim URL for guest submissions
        $claim_url = '';
        $results_url = get_permalink($trial_id);

        if (!is_user_logged_in()) {
            $claim_token = wp_generate_password(32, false);
            update_post_meta($trial_id, '_claim_token', $claim_token);
            $claim_token_expiry = \OpenVeil\ACF\Options::get_option('claim_token_expiry', 7);
            update_post_meta($trial_id, '_claim_token_expiry', time() + ($claim_token_expiry * DAY_IN_SECONDS));
            $claim_url = add_query_arg(['claim_token' => $claim_token], $results_url);
        }

        // Return the created trial with claim URL if guest submission
        $trial = get_post($trial_id);
        $response = $this->prepare_trial_response($trial);

        if (!empty($claim_url)) {
            $response['claim_url'] = $claim_url;
        }

        $response['results_url'] = $results_url;

        return $response;
    }

    /**
     * Saves questionnaire data for a trial.
     *
     * @param int $trial_id Trial post ID
     * @param array $questionnaire Questionnaire data
     * @return void
     */
    private function save_questionnaire_data(int $trial_id, array $questionnaire): void
    {
        foreach ($questionnaire as $section => $fields) {
            if (!is_array($fields)) {
                continue;
            }

            foreach ($fields as $field => $value) {
                update_post_meta($trial_id, $field, $value);
            }
        }
    }

    /**
     * Retrieves citation data for a trial in CSL-JSON format.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error CSL-JSON data or error
     */
    public function get_trial_csl($request)
    {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);

        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }

        $author = get_user_by('id', $trial->post_author);
        $author_name = $author ? $author->display_name : __('Anonymous', 'open-veil');

        $protocol_id = get_post_meta($trial_id, 'protocol_id', true);
        $protocol = $protocol_id ? get_post($protocol_id) : null;
        $protocol_title = $protocol ? $protocol->post_title : '';

        $csl = [
            'id' => $trial_id,
            'type' => 'article',
            'title' => $trial->post_title,
            'author' => [
                [
                    'family' => $author_name,
                    'given' => '',
                ]
            ],
            'issued' => [
                'date-parts' => [
                    [date('Y', strtotime($trial->post_date)), date('m', strtotime($trial->post_date)), date('d', strtotime($trial->post_date))]
                ]
            ],
            'URL' => get_permalink($trial_id),
            'publisher' => get_bloginfo('name'),
            'container-title' => $protocol_title,
        ];

        return $csl;
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

    /**
     * Get standardized pagination parameters from the request.
     *
     * @param \WP_REST_Request $request The REST request.
     * @return array Pagination parameters.
     */
    protected function get_pagination_params(\WP_REST_Request $request): array {
        $per_page = (int) $request->get_param('per_page');
        $page = (int) $request->get_param('page');
        $offset = (int) $request->get_param('offset');

        // Set defaults and validate limits
        if ($per_page <= 0) {
            $per_page = self::DEFAULT_PER_PAGE;
        }
        if ($per_page > self::MAX_PER_PAGE) {
            $per_page = self::MAX_PER_PAGE;
        }
        if ($page <= 0) {
            $page = 1;
        }
        
        $params = [
            'per_page' => $per_page,
            'page' => $page,
            'offset' => $offset > 0 ? $offset : ($page - 1) * $per_page,
        ];

        return $params;
    }

    /**
     * Get sorting parameters from the request.
     *
     * @param \WP_REST_Request $request The REST request.
     * @param string $post_type The post type to get sort fields for.
     * @return array Sorting parameters.
     */
    protected function get_sorting_params(\WP_REST_Request $request, string $post_type): array {
        $orderby = $request->get_param('orderby');
        $order = $request->get_param('order');

        // Validate order - handle null and convert to uppercase
        if (empty($order) || !in_array(strtoupper($order), ['ASC', 'DESC'])) {
            $order = 'DESC';
        } else {
            $order = strtoupper($order);
        }

        // Select sort fields based on post type
        $valid_fields = $post_type === 'protocol' ? $this->protocol_sort_fields : $this->trial_sort_fields;

        // Process orderby parameter
        $orderby_args = [];
        if ($orderby) {
            // Handle meta field sorting
            if (strpos($orderby, 'meta.') === 0) {
                $meta_key = substr($orderby, 5);
                $orderby_args = [
                    'meta_key' => $meta_key,
                    'orderby' => 'meta_value'
                ];

                // Determine if it's a numeric meta field for proper sorting
                if (in_array($meta_key, ['laser_power', 'laser_wavelength', 'substance_dose', 'projection_distance'])) {
                    $orderby_args['orderby'] = 'meta_value_num';
                }
            } 
            // Handle standard fields
            elseif (isset($valid_fields[$orderby])) {
                $orderby_args['orderby'] = $valid_fields[$orderby];
            }
        }

        // Set default order if no valid orderby was provided
        if (empty($orderby_args)) {
            $orderby_args['orderby'] = 'date';
        }

        $orderby_args['order'] = $order;

        return $orderby_args;
    }
    
    /**
     * Get taxonomy filters from the request.
     *
     * @param \WP_REST_Request $request The REST request.
     * @return array Taxonomy query arguments.
     */
    protected function get_taxonomy_filters(\WP_REST_Request $request): array {
        $taxonomies = [
            'laser_class',
            'diffraction_grating_spec',
            'equipment',
            'substance',
            'administration_method',
            'administration_protocol',
            'projection_surface',
        ];
        
        $tax_query = [];
        
        foreach ($taxonomies as $taxonomy) {
            // Check for direct taxonomy term names
            $terms = $request->get_param($taxonomy);
            $slug_terms = $request->get_param($taxonomy . '_slug');
            
            if (!empty($terms) || !empty($slug_terms)) {
                $tax_item = [
                    'taxonomy' => $taxonomy,
                    'field' => !empty($terms) ? 'name' : 'slug',
                    'terms' => !empty($terms) ? explode(',', $terms) : explode(',', $slug_terms),
                    'operator' => 'IN',
                ];
                
                $tax_query[] = $tax_item;
            }
        }
        
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        
        return !empty($tax_query) ? ['tax_query' => $tax_query] : [];
    }
    
    /**
     * Get search parameters from the request.
     *
     * @param \WP_REST_Request $request The REST request.
     * @return array Search query arguments.
     */
    protected function get_search_params(\WP_REST_Request $request): array {
        $search = $request->get_param('search');
        
        if (empty($search)) {
            return [];
        }
        
        return ['s' => sanitize_text_field($search)];
    }
    
    /**
     * Parse field selection parameters to determine which fields to include in the response.
     *
     * @param \WP_REST_Request $request The REST request.
     * @return array|null Selected fields or null for all fields.
     */
    protected function parse_field_selection(\WP_REST_Request $request): ?array {
        $fields_param = $request->get_param('_fields');
        
        if (empty($fields_param)) {
            return null;
        }
        
        $fields = explode(',', $fields_param);
        $selected_fields = [];
        
        foreach ($fields as $field) {
            $field = trim($field);
            
            // Support dot notation for nested fields
            $parts = explode('.', $field);
            
            if (count($parts) === 1) {
                $selected_fields[$field] = true;
            } else {
                $parent = $parts[0];
                $child = $parts[1];
                
                if (!isset($selected_fields[$parent])) {
                    $selected_fields[$parent] = [];
                }
                
                if (is_array($selected_fields[$parent])) {
                    $selected_fields[$parent][$child] = true;
                }
            }
        }
        
        return $selected_fields;
    }
    
    /**
     * Parse embedding parameters to determine which related resources to include.
     *
     * @param \WP_REST_Request $request The REST request.
     * @param string $post_type Current post type.
     * @return array Embed options.
     */
    protected function parse_embed_params(\WP_REST_Request $request, string $post_type): array {
        $embed_param = $request->get_param('_embed');
        
        if (empty($embed_param)) {
            // Check for legacy include_protocol param
            if ($post_type === 'trial' && $request->get_param('include_protocol') === '1') {
                return ['protocol' => true];
            }
            
            return [];
        }
        
        $embeds = explode(',', $embed_param);
        $embed_options = [];
        $allowed_embeds = $this->embeddable_resources[$post_type] ?? [];
        
        foreach ($embeds as $embed) {
            $embed = trim($embed);
            
            if (in_array($embed, $allowed_embeds)) {
                $embed_options[$embed] = true;
            }
        }
        
        return $embed_options;
    }
    
    /**
     * Add pagination headers to the REST response.
     *
     * @param \WP_REST_Response $response The REST response.
     * @param int $total Total number of items.
     * @param int $total_pages Total number of pages.
     * @param array $pagination Pagination parameters.
     * @param string $route The current route.
     * @return \WP_REST_Response Modified response with pagination headers.
     */
    protected function add_pagination_headers(\WP_REST_Response $response, int $total, int $total_pages, array $pagination, string $route): \WP_REST_Response {
        $response->header('X-WP-Total', $total);
        $response->header('X-WP-TotalPages', $total_pages);
        
        // Add pagination links
        $links = [];
        $page = $pagination['page'];
        $per_page = $pagination['per_page'];
        $base_url = rest_url($route);
        
        // Remove existing pagination parameters
        $request_params = $_GET;
        unset($request_params['page'], $request_params['per_page'], $request_params['offset']);
        
        // First page
        $query_args = array_merge($request_params, ['page' => 1, 'per_page' => $per_page]);
        $links['first'] = add_query_arg($query_args, $base_url);
        
        // Last page
        if ($total_pages > 0) {
            $query_args = array_merge($request_params, ['page' => $total_pages, 'per_page' => $per_page]);
            $links['last'] = add_query_arg($query_args, $base_url);
        }
        
        // Previous page
        if ($page > 1) {
            $query_args = array_merge($request_params, ['page' => $page - 1, 'per_page' => $per_page]);
            $links['prev'] = add_query_arg($query_args, $base_url);
        }
        
        // Next page
        if ($page < $total_pages) {
            $query_args = array_merge($request_params, ['page' => $page + 1, 'per_page' => $per_page]);
            $links['next'] = add_query_arg($query_args, $base_url);
        }
        
        if (!empty($links)) {
            $response->add_links($links);
        }
        
        return $response;
    }
    
    /**
     * Create a standardized API response.
     *
     * @param mixed $data The response data.
     * @param array $pagination Pagination information.
     * @param int $total Total number of items.
     * @param int $total_pages Total number of pages.
     * @param string $route The current route.
     * @param int $status HTTP status code.
     * @return \WP_REST_Response Standardized response.
     */
    protected function create_response($data, array $pagination = [], int $total = 0, $total_pages = 0, string $route = '', int $status = 200): \WP_REST_Response {
        // Ensure total_pages is an integer
        $total_pages = (int)$total_pages;
        
        $response_data = [
            'data' => $data,
            'meta' => [
                'status' => $status,
                'timestamp' => current_time('mysql'),
            ]
        ];
        
        if (!empty($pagination)) {
            $response_data['meta']['pagination'] = [
                'page' => $pagination['page'],
                'per_page' => $pagination['per_page'],
                'total' => $total,
                'total_pages' => $total_pages,
            ];
        }
        
        $response = new \WP_REST_Response($response_data, $status);
        
        if (!empty($pagination) && !empty($route)) {
            $response = $this->add_pagination_headers($response, $total, $total_pages, $pagination, $route);
        }
        
        return $response;
    }
    
    /**
     * Apply field selection to an item.
     *
     * @param array $item The item data.
     * @param array|null $fields Selected fields.
     * @return array Filtered item.
     */
    protected function apply_field_selection(array $item, ?array $fields): array {
        if ($fields === null) {
            return $item;
        }
        
        $filtered = [];
        
        foreach ($fields as $field => $include) {
            if (!isset($item[$field])) {
                continue;
            }
            
            if ($include === true) {
                $filtered[$field] = $item[$field];
            } elseif (is_array($include) && is_array($item[$field])) {
                $filtered[$field] = [];
                
                foreach ($include as $subfield => $subinclude) {
                    if ($subinclude && isset($item[$field][$subfield])) {
                        $filtered[$field][$subfield] = $item[$field][$subfield];
                    }
                }
            }
        }
        
        // Always include _links if present and requested
        if (isset($fields['_links']) && isset($item['_links'])) {
            $filtered['_links'] = $item['_links'];
        }
        
        // Always include _embedded if present and requested
        if (isset($fields['_embedded']) && isset($item['_embedded'])) {
            $filtered['_embedded'] = $item['_embedded'];
        }
        
        return $filtered;
    }
}