<?php
namespace OpenVeil\API;

/**
 * REST API
 * 
 * Registers REST API endpoints for Protocol and Trial post types
 */
class Rest {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Protocol endpoints
        register_rest_route('open-veil', '/protocol/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/protocol/name/(?P<slug>[a-zA-Z0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol_by_slug'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/protocol', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocols'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/protocol/trials/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol_trials'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/protocol/author/(?P<author_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocols_by_author'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/protocol', [
            'methods' => 'POST',
            'callback' => [$this, 'create_protocol'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
        
        register_rest_route('open-veil', '/protocol/(?P<id>\d+)/csl', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol_csl'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        // Trial endpoints
        register_rest_route('open-veil', '/trial', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trials'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/trial/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trial'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        register_rest_route('open-veil', '/trial', [
            'methods' => 'POST',
            'callback' => [$this, 'create_trial'],
            'permission_callback' => function() {
                return true; // Allow guest submissions
            },
        ]);
        
        register_rest_route('open-veil', '/trial/(?P<id>\d+)/csl', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trial_csl'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
        
        // Schema endpoint
        register_rest_route('open-veil', '/schema', [
            'methods' => 'GET',
            'callback' => [$this, 'get_schema'],
            'permission_callback' => function() {
                return true; // Public access
            },
        ]);
    }
    
    /**
     * Get a protocol by ID
     */
    public function get_protocol($request) {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);
        
        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }
        
        return $this->prepare_protocol_response($protocol);
    }
    
    /**
     * Get a protocol by slug
     */
    public function get_protocol_by_slug($request) {
        $protocol_slug = $request['slug'];
        $protocol = get_page_by_path($protocol_slug, OBJECT, 'protocol');
        
        if (!$protocol) {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }
        
        return $this->prepare_protocol_response($protocol);
    }
    
    /**
     * Get all protocols
     */
    public function get_protocols($request) {
        $args = [
            'post_type' => 'protocol',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];
        
        $protocols = get_posts($args);
        $response = [];
        
        foreach ($protocols as $protocol) {
            $response[] = $this->prepare_protocol_response($protocol);
        }
        
        return $response;
    }
    
    /**
     * Get all trials for a protocol
     */
    public function get_protocol_trials($request) {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);
        
        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }
        
        $args = [
            'post_type' => 'trial',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'protocol_id',
                    'value' => $protocol_id,
                    'compare' => '=',
                ]
            ],
        ];
        
        $trials = get_posts($args);
        $response = [];
        
        foreach ($trials as $trial) {
            $response[] = $this->prepare_trial_response($trial);
        }
        
        return $response;
    }
    
    /**
     * Get protocols by author
     */
    public function get_protocols_by_author($request) {
        $author_id = $request['author_id'];
        
        $args = [
            'post_type' => 'protocol',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'author' => $author_id,
        ];
        
        $protocols = get_posts($args);
        $response = [];
        
        foreach ($protocols as $protocol) {
            $response[] = $this->prepare_protocol_response($protocol);
        }
        
        return $response;
    }
    
    /**
     * Create a new protocol
     */
    public function create_protocol($request) {
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
        if (isset($params['tax_input']) && is_array($params['tax_input'])) {
            foreach ($params['tax_input'] as $taxonomy => $terms) {
                wp_set_object_terms($protocol_id, $terms, $taxonomy);
            }
        }
        
        // Return the created protocol
        $protocol = get_post($protocol_id);
        
        return $this->prepare_protocol_response($protocol);
    }
    
    /**
     * Get CSL-JSON for a protocol
     */
    public function get_protocol_csl($request) {
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
     * Get all trials
     */
    public function get_trials($request) {
        $args = [
            'post_type' => 'trial',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];
        
        $trials = get_posts($args);
        $response = [];
        
        foreach ($trials as $trial) {
            $response[] = $this->prepare_trial_response($trial);
        }
        
        return $response;
    }
    
    /**
     * Get a trial by ID
     */
    public function get_trial($request) {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);
        
        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }
        
        return $this->prepare_trial_response($trial);
    }
    
    /**
     * Create a new trial
     */
    public function create_trial($request) {
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
        if (isset($params['tax_input']) && is_array($params['tax_input'])) {
            foreach ($params['tax_input'] as $taxonomy => $terms) {
                wp_set_object_terms($trial_id, $terms, $taxonomy);
            }
        }
        
        // Generate claim URL for guest submissions
        $claim_url = '';
        $results_url = get_permalink($trial_id);
        
        if (!is_user_logged_in()) {
            $claim_token = wp_generate_password(32, false);
            update_post_meta($trial_id, '_claim_token', $claim_token);
            update_post_meta($trial_id, '_claim_token_expiry', time() + (7 * DAY_IN_SECONDS)); // 7 days expiry
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
     * Get CSL-JSON for a trial
     */
    public function get_trial_csl($request) {
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
     * Get schema information
     */
    public function get_schema() {
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
     * Prepare protocol response
     */
    private function prepare_protocol_response($protocol) {
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
     * Prepare trial response
     */
    private function prepare_trial_response($trial) {
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
}
