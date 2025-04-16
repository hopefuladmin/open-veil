<?php

declare(strict_types=1);

namespace OpenVeil\ACF;

/**
 * ACF Options
 * 
 * Registers ACF options page for Open Veil plugin settings.
 * 
 * @package OpenVeil\ACF
 */
class Options
{
    /**
     * Sets up actions to register the options page and fields.
     */
    public function __construct()
    {
        add_action('acf/init', [$this, 'register_options_page']);
        add_action('acf/init', [$this, 'register_option_fields']);
    }

    /**
     * Creates the ACF options page for plugin settings.
     *
     * @return void
     */
    public function register_options_page(): void
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title'    => __('Open Veil Settings', 'open-veil'),
                'menu_title'    => __('Open Veil', 'open-veil'),
                'menu_slug'     => 'open-veil-settings',
                'capability'    => 'manage_options',
                'redirect'      => false,
                'position'      => 80,
                'icon_url'      => 'dashicons-visibility',
                'update_button' => __('Update Settings', 'open-veil'),
                'updated_message' => __('Settings Updated', 'open-veil'),
            ]);
        }
    }

    /**
     * Registers all ACF fields for the plugin settings page.
     *
     * @return void
     */
    public function register_option_fields(): void
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_open_veil_settings',
                'title' => __('Open Veil Settings', 'open-veil'),
                'fields' => [
                    [
                        'key' => 'field_guest_submissions',
                        'label' => __('Guest Submissions', 'open-veil'),
                        'name' => 'guest_submissions',
                        'type' => 'true_false',
                        'instructions' => __('If enabled, guests can submit trials without logging in.', 'open-veil'),
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_claim_token_expiry',
                        'label' => __('Claim Token Expiry', 'open-veil'),
                        'name' => 'claim_token_expiry',
                        'type' => 'number',
                        'instructions' => __('Number of days before claim tokens expire.', 'open-veil'),
                        'required' => 0,
                        'default_value' => 7,
                        'min' => 1,
                        'max' => 30,
                    ],
                    [
                        'key' => 'field_email_notifications',
                        'label' => __('Email Notifications', 'open-veil'),
                        'name' => 'email_notifications',
                        'type' => 'true_false',
                        'instructions' => __('If enabled, email notifications will be sent when trials are approved/published.', 'open-veil'),
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_api_access',
                        'label' => __('API Access', 'open-veil'),
                        'name' => 'api_access',
                        'type' => 'select',
                        'instructions' => __('Who can access the API.', 'open-veil'),
                        'required' => 0,
                        'choices' => [
                            'public' => __('Public', 'open-veil'),
                            'logged_in' => __('Logged-in Users Only', 'open-veil'),
                            'admin' => __('Administrators Only', 'open-veil'),
                        ],
                        'default_value' => 'public',
                        'return_format' => 'value',
                    ],
                    [
                        'key' => 'field_taxonomy_term_check',
                        'label' => __('Taxonomy Term Check', 'open-veil'),
                        'name' => 'taxonomy_term_check',
                        'type' => 'true_false',
                        'instructions' => __('If enabled, the plugin will check and create default taxonomy terms on each load. Disable for better performance on production sites.', 'open-veil'),
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'open-veil-settings',
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ]);
        }
    }

    /**
     * Retrieves an option value from ACF options or returns default.
     *
     * @param string $option Option name to retrieve
     * @param mixed $default Default value if option doesn't exist
     * @return mixed Option value or default
     */
    public static function get_option(string $option, $default = '')
    {
        if (function_exists('get_field')) {
            $value = get_field($option, 'option');
            return $value !== null ? $value : $default;
        }

        return $default;
    }
}
