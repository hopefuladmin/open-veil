<?php
declare(strict_types=1);
namespace OpenVeil\ACF;

/**
 * ACF Fields
 * 
 * Registers Advanced Custom Fields for Protocol and Trial post types.
 * 
 * @package OpenVeil\ACF
 */
class Fields {
    /**
     * Sets up actions to register ACF fields.
     */
    public function __construct() {
        add_action('acf/init', [$this, 'register_fields']);
    }
    
    /**
     * Registers all custom fields for Protocol and Trial post types using ACF.
     *
     * @return void
     */
    public function register_fields(): void {
        // Protocol fields
        acf_add_local_field_group([
            'key' => 'group_protocol',
            'title' => __('Protocol Parameters', 'open-veil'),
            'fields' => [
                [
                    'key' => 'field_laser_wavelength',
                    'label' => __('Laser Wavelength (nm)', 'open-veil'),
                    'name' => 'laser_wavelength',
                    'type' => 'number',
                    'instructions' => __('Enter the laser wavelength in nanometers (400-700 nm)', 'open-veil'),
                    'required' => 1,
                    'min' => 400,
                    'max' => 700,
                ],
                [
                    'key' => 'field_laser_power',
                    'label' => __('Laser Power (mW)', 'open-veil'),
                    'name' => 'laser_power',
                    'type' => 'number',
                    'instructions' => __('Enter the laser power in milliwatts (0-5 mW)', 'open-veil'),
                    'required' => 1,
                    'min' => 0,
                    'max' => 5,
                ],
                [
                    'key' => 'field_substance_dose',
                    'label' => __('Substance Dose (g)', 'open-veil'),
                    'name' => 'substance_dose',
                    'type' => 'number',
                    'instructions' => __('Enter the substance dose in grams', 'open-veil'),
                    'required' => 1,
                    'min' => 0,
                ],
                [
                    'key' => 'field_projection_distance',
                    'label' => __('Projection Distance (feet)', 'open-veil'),
                    'name' => 'projection_distance',
                    'type' => 'number',
                    'instructions' => __('Enter the projection distance in feet', 'open-veil'),
                    'required' => 1,
                    'min' => 1,
                    'max' => 20,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'protocol',
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
            'show_in_rest' => 1,
        ]);
        
        // Trial fields
        acf_add_local_field_group([
            'key' => 'group_trial',
            'title' => __('Trial Parameters', 'open-veil'),
            'fields' => [
                [
                    'key' => 'field_protocol_id',
                    'label' => __('Protocol', 'open-veil'),
                    'name' => 'protocol_id',
                    'type' => 'post_object',
                    'instructions' => __('Select the protocol this trial is based on', 'open-veil'),
                    'required' => 1,
                    'post_type' => ['protocol'],
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_trial_laser_wavelength',
                    'label' => __('Laser Wavelength (nm)', 'open-veil'),
                    'name' => 'laser_wavelength',
                    'type' => 'number',
                    'instructions' => __('Enter the laser wavelength in nanometers (400-700 nm)', 'open-veil'),
                    'required' => 1,
                    'min' => 400,
                    'max' => 700,
                ],
                [
                    'key' => 'field_trial_laser_power',
                    'label' => __('Laser Power (mW)', 'open-veil'),
                    'name' => 'laser_power',
                    'type' => 'number',
                    'instructions' => __('Enter the laser power in milliwatts (0-5 mW)', 'open-veil'),
                    'required' => 1,
                    'min' => 0,
                    'max' => 5,
                ],
                [
                    'key' => 'field_trial_substance_dose',
                    'label' => __('Substance Dose (g)', 'open-veil'),
                    'name' => 'substance_dose',
                    'type' => 'number',
                    'instructions' => __('Enter the substance dose in grams', 'open-veil'),
                    'required' => 1,
                    'min' => 0,
                ],
                [
                    'key' => 'field_trial_projection_distance',
                    'label' => __('Projection Distance (feet)', 'open-veil'),
                    'name' => 'projection_distance',
                    'type' => 'number',
                    'instructions' => __('Enter the projection distance in feet', 'open-veil'),
                    'required' => 1,
                    'min' => 1,
                    'max' => 20,
                ],
                [
                    'key' => 'field_administration_notes',
                    'label' => __('Administration Notes', 'open-veil'),
                    'name' => 'administration_notes',
                    'type' => 'textarea',
                    'instructions' => __('Enter any notes about the administration method', 'open-veil'),
                    'required' => 0,
                ],
                [
                    'key' => 'field_additional_observers',
                    'label' => __('Additional Observers', 'open-veil'),
                    'name' => 'additional_observers',
                    'type' => 'true_false',
                    'instructions' => __('Were there additional observers who saw the same symbols/imagery?', 'open-veil'),
                    'required' => 0,
                    'ui' => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'trial',
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
            'show_in_rest' => 1,
        ]);
    }
}
