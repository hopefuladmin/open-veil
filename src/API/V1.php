<?php

declare(strict_types=1);

namespace OpenVeil\API;

/**
 * REST API V1
 * 
 * Implements version 1 of the REST API.
 * 
 * @package OpenVeil\API
 */
class V1 extends AbstractAPI
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('v1');
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

        register_rest_route($this->namespace, '/protocol/name/(?P<slug>[a-zA-Z0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol_by_slug'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/protocol', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocols'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/protocol/trials/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocol_trials'],
            'permission_callback' => function () {
                return true; // Public access
            },
        ]);

        register_rest_route($this->namespace, '/protocol/author/(?P<author_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_protocols_by_author'],
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
     * Retrieves a protocol by its ID.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Protocol data or error
     */
    public function get_protocol(\WP_REST_Request $request)
    {
        $protocol_id = $request['id'];
        $protocol = get_post($protocol_id);

        if (!$protocol || $protocol->post_type !== 'protocol') {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }

        return $this->prepare_protocol_response($protocol);
    }

    /**
     * Retrieves a protocol by its slug.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Protocol data or error
     */
    public function get_protocol_by_slug(\WP_REST_Request $request)
    {
        $protocol_slug = $request['slug'];
        $protocol = get_page_by_path($protocol_slug, OBJECT, 'protocol');

        if (!$protocol) {
            return new \WP_Error('protocol_not_found', __('Protocol not found', 'open-veil'), ['status' => 404]);
        }

        return $this->prepare_protocol_response($protocol);
    }

    /**
     * Retrieves all published protocols.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array Array of protocols
     */
    public function get_protocols(\WP_REST_Request $request): array
    {
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
     * Retrieves all trials associated with a specific protocol.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Array of trials or error
     */
    public function get_protocol_trials($request)
    {
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
     * Retrieves all protocols created by a specific author.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array Array of protocols
     */
    public function get_protocols_by_author($request)
    {
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
     * Retrieves all published trials.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array Array of trials
     */
    public function get_trials($request)
    {
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
     * Retrieves a trial by its ID.
     *
     * @param \WP_REST_Request $request REST API request
     * @return array|\WP_Error Trial data or error
     */
    public function get_trial($request)
    {
        $trial_id = $request['id'];
        $trial = get_post($trial_id);

        if (!$trial || $trial->post_type !== 'trial') {
            return new \WP_Error('trial_not_found', __('Trial not found', 'open-veil'), ['status' => 404]);
        }

        return $this->prepare_trial_response($trial);
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
        if (isset($params['tax_input']) && is_array($params['tax_input'])) {
            foreach ($params['tax_input'] as $taxonomy => $terms) {
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
}