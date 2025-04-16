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
       
       // Register the questionnaire field groups
       $this->register_questionnaire_fields();
   }
   
   /**
    * Registers all questionnaire fields for the Trial post type.
    *
    * @return void
    */
   private function register_questionnaire_fields(): void {
       // About You section
       acf_add_local_field_group([
           'key' => 'group_questionnaire_about_you',
           'title' => __('Questionnaire: About You', 'open-veil'),
           'fields' => [
               [
                   'key' => 'field_participant_name',
                   'label' => __('Name', 'open-veil'),
                   'name' => 'participant_name',
                   'type' => 'text',
                   'instructions' => __('Your full name', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_participant_email',
                   'label' => __('Email Address', 'open-veil'),
                   'name' => 'participant_email',
                   'type' => 'email',
                   'instructions' => __('Your email address', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_psychedelic_experience_level',
                   'label' => __('Psychedelic Experience Level (1-10)', 'open-veil'),
                   'name' => 'psychedelic_experience_level',
                   'type' => 'range',
                   'instructions' => __('Rate your overall experience with psychedelics', 'open-veil'),
                   'required' => 0,
                   'min' => 1,
                   'max' => 10,
                   'step' => 1,
                   'default_value' => 5,
               ],
               [
                   'key' => 'field_dmt_experience_level',
                   'label' => __('DMT Experience Level (1-10)', 'open-veil'),
                   'name' => 'dmt_experience_level',
                   'type' => 'range',
                   'instructions' => __('Rate your experience specifically with DMT', 'open-veil'),
                   'required' => 0,
                   'min' => 1,
                   'max' => 10,
                   'step' => 1,
                   'default_value' => 5,
               ],
               [
                   'key' => 'field_simulation_theory_interest',
                   'label' => __('Interest in Simulation Theory', 'open-veil'),
                   'name' => 'simulation_theory_interest',
                   'type' => 'text',
                   'instructions' => __('Describe your interest in simulation theory', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_how_found_us',
                   'label' => __('How Did You Find Us?', 'open-veil'),
                   'name' => 'how_found_us',
                   'type' => 'text',
                   'instructions' => __('How did you learn about this research?', 'open-veil'),
                   'required' => 0,
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
           'menu_order' => 10,
           'position' => 'normal',
           'style' => 'default',
           'label_placement' => 'top',
           'instruction_placement' => 'label',
           'hide_on_screen' => '',
           'active' => true,
           'description' => '',
           'show_in_rest' => 1,
       ]);
       
       // Experiment Setup section
       acf_add_local_field_group([
           'key' => 'group_questionnaire_experiment_setup',
           'title' => __('Questionnaire: Experiment Setup', 'open-veil'),
           'fields' => [
               [
                   'key' => 'field_received_laser_from_us',
                   'label' => __('Did You Receive the Laser From Us?', 'open-veil'),
                   'name' => 'received_laser_from_us',
                   'type' => 'true_false',
                   'instructions' => __('Select Yes if you received the laser from our research team', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
               ],
               [
                   'key' => 'field_beam_shape',
                   'label' => __('Beam Shape', 'open-veil'),
                   'name' => 'beam_shape',
                   'type' => 'text',
                   'instructions' => __('Describe the shape of the laser beam', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_laser_power_source',
                   'label' => __('Laser Power Source', 'open-veil'),
                   'name' => 'laser_power_source',
                   'type' => 'text',
                   'instructions' => __('What power source did you use for the laser?', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_accessories_used',
                   'label' => __('Accessories Used', 'open-veil'),
                   'name' => 'accessories_used',
                   'type' => 'textarea',
                   'instructions' => __('List any accessories used with the laser', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_set_and_setting',
                   'label' => __('Set & Setting', 'open-veil'),
                   'name' => 'set_and_setting',
                   'type' => 'textarea',
                   'instructions' => __('Describe the environment and your mindset during the experiment', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_experiment_datetime',
                   'label' => __('Date + Time of Experiment', 'open-veil'),
                   'name' => 'experiment_datetime',
                   'type' => 'date_time_picker',
                   'instructions' => __('When did you conduct the experiment?', 'open-veil'),
                   'required' => 0,
                   'display_format' => 'F j, Y g:i a',
                   'return_format' => 'Y-m-d H:i:s',
                   'first_day' => 1,
               ],
               [
                   'key' => 'field_lighting_conditions',
                   'label' => __('Lighting Conditions', 'open-veil'),
                   'name' => 'lighting_conditions',
                   'type' => 'text',
                   'instructions' => __('Describe the lighting in the room', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_surfaces_used',
                   'label' => __('Surfaces Used', 'open-veil'),
                   'name' => 'surfaces_used',
                   'type' => 'textarea',
                   'instructions' => __('What surfaces did you project the laser onto?', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_additional_setup_info',
                   'label' => __('Additional Setup Information', 'open-veil'),
                   'name' => 'additional_setup_info',
                   'type' => 'textarea',
                   'instructions' => __('Any other details about your experimental setup', 'open-veil'),
                   'required' => 0,
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
           'menu_order' => 20,
           'position' => 'normal',
           'style' => 'default',
           'label_placement' => 'top',
           'instruction_placement' => 'label',
           'hide_on_screen' => '',
           'active' => true,
           'description' => '',
           'show_in_rest' => 1,
       ]);
       
       // Substances Used section
       acf_add_local_field_group([
           'key' => 'group_questionnaire_substances_used',
           'title' => __('Questionnaire: Substances Used', 'open-veil'),
           'fields' => [
               [
                   'key' => 'field_other_substances',
                   'label' => __('Other Substances Taken Concurrently', 'open-veil'),
                   'name' => 'other_substances',
                   'type' => 'textarea',
                   'instructions' => __('List any other substances taken during the experiment', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_intoxication_level',
                   'label' => __('Level of Intoxication (1-10)', 'open-veil'),
                   'name' => 'intoxication_level',
                   'type' => 'range',
                   'instructions' => __('Rate your level of intoxication during the experiment', 'open-veil'),
                   'required' => 0,
                   'min' => 1,
                   'max' => 10,
                   'step' => 1,
                   'default_value' => 5,
               ],
               [
                   'key' => 'field_visual_mental_effects',
                   'label' => __('Visual & Mental Effects', 'open-veil'),
                   'name' => 'visual_mental_effects',
                   'type' => 'textarea',
                   'instructions' => __('Describe the visual and mental effects experienced', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_additional_substance_info',
                   'label' => __('Additional Substance Information', 'open-veil'),
                   'name' => 'additional_substance_info',
                   'type' => 'textarea',
                   'instructions' => __('Any other details about substances used', 'open-veil'),
                   'required' => 0,
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
           'menu_order' => 30,
           'position' => 'normal',
           'style' => 'default',
           'label_placement' => 'top',
           'instruction_placement' => 'label',
           'hide_on_screen' => '',
           'active' => true,
           'description' => '',
           'show_in_rest' => 1,
       ]);
       
       // Visual Effects and Laser Interaction section
       acf_add_local_field_group([
           'key' => 'group_questionnaire_visual_effects',
           'title' => __('Questionnaire: Visual Effects and Laser Interaction', 'open-veil'),
           'fields' => [
               [
                   'key' => 'field_beam_changed',
                   'label' => __('Did the Beam Change?', 'open-veil'),
                   'name' => 'beam_changed',
                   'type' => 'true_false',
                   'instructions' => __('Did you observe any changes to the laser beam?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
               ],
               [
                   'key' => 'field_beam_changes_description',
                   'label' => __('Describe Beam Changes', 'open-veil'),
                   'name' => 'beam_changes_description',
                   'type' => 'textarea',
                   'instructions' => __('If the beam changed, please describe how', 'open-veil'),
                   'required' => 0,
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_beam_changed',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_saw_code_of_reality',
                   'label' => __('Did You See the Code of Reality?', 'open-veil'),
                   'name' => 'saw_code_of_reality',
                   'type' => 'true_false',
                   'instructions' => __('Did you observe what appears to be the "Code of Reality"?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
               ],
               [
                   'key' => 'field_symbols_seen',
                   'label' => __('Symbols Seen?', 'open-veil'),
                   'name' => 'symbols_seen',
                   'type' => 'true_false',
                   'instructions' => __('Did you observe any symbols or characters?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_saw_code_of_reality',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_symbols_description',
                   'label' => __('Describe Symbols', 'open-veil'),
                   'name' => 'symbols_description',
                   'type' => 'textarea',
                   'instructions' => __('Describe the symbols or characters you observed', 'open-veil'),
                   'required' => 0,
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_symbols_seen',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_code_moving',
                   'label' => __('Was the Code Moving?', 'open-veil'),
                   'name' => 'code_moving',
                   'type' => 'true_false',
                   'instructions' => __('Did the code or symbols appear to be in motion?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_saw_code_of_reality',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_movement_direction',
                   'label' => __('Direction of Movement', 'open-veil'),
                   'name' => 'movement_direction',
                   'type' => 'text',
                   'instructions' => __('If the code was moving, in what direction?', 'open-veil'),
                   'required' => 0,
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_code_moving',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_characters_tiny',
                   'label' => __('Were Characters Tiny or Hard to See?', 'open-veil'),
                   'name' => 'characters_tiny',
                   'type' => 'true_false',
                   'instructions' => __('Were the characters or symbols small or difficult to perceive?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_symbols_seen',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_size_changed',
                   'label' => __('Did Their Size Change?', 'open-veil'),
                   'name' => 'size_changed',
                   'type' => 'true_false',
                   'instructions' => __('Did the size of the characters or symbols change during observation?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_symbols_seen',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_code_clarity',
                   'label' => __('Code Clarity (1-10)', 'open-veil'),
                   'name' => 'code_clarity',
                   'type' => 'range',
                   'instructions' => __('Rate the clarity of the code or symbols observed', 'open-veil'),
                   'required' => 0,
                   'min' => 1,
                   'max' => 10,
                   'step' => 1,
                   'default_value' => 5,
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_saw_code_of_reality',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_code_behaved_like_object',
                   'label' => __('Did the Code Behave Like a Real Object?', 'open-veil'),
                   'name' => 'code_behaved_like_object',
                   'type' => 'true_false',
                   'instructions' => __('Did the code appear to have physical properties?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_saw_code_of_reality',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_could_influence_code',
                   'label' => __('Could You Influence the Code?', 'open-veil'),
                   'name' => 'could_influence_code',
                   'type' => 'true_false',
                   'instructions' => __('Were you able to interact with or influence the code?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_saw_code_of_reality',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_influence_description',
                   'label' => __('Describe Influence', 'open-veil'),
                   'name' => 'influence_description',
                   'type' => 'textarea',
                   'instructions' => __('If you could influence the code, describe how', 'open-veil'),
                   'required' => 0,
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_could_influence_code',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_code_persisted_without_laser',
                   'label' => __('Did the Code Persist Without the Laser?', 'open-veil'),
                   'name' => 'code_persisted_without_laser',
                   'type' => 'true_false',
                   'instructions' => __('Did you continue to see the code when the laser was not present?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_saw_code_of_reality',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_persisted_when_looked_away',
                   'label' => __('When You Looked Away', 'open-veil'),
                   'name' => 'persisted_when_looked_away',
                   'type' => 'true_false',
                   'instructions' => __('Did the code remain visible when you looked away and back again?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_code_persisted_without_laser',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_persisted_after_turning_off',
                   'label' => __('After Turning Off', 'open-veil'),
                   'name' => 'persisted_after_turning_off',
                   'type' => 'true_false',
                   'instructions' => __('Did the code remain visible after turning off the laser?', 'open-veil'),
                   'required' => 0,
                   'ui' => 1,
                   'ui_on_text' => __('Yes', 'open-veil'),
                   'ui_off_text' => __('No', 'open-veil'),
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_code_persisted_without_laser',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
               ],
               [
                   'key' => 'field_where_else_seen',
                   'label' => __('Where Else Did You See It?', 'open-veil'),
                   'name' => 'where_else_seen',
                   'type' => 'textarea',
                   'instructions' => __('If the code appeared elsewhere, describe where', 'open-veil'),
                   'required' => 0,
                   'conditional_logic' => [
                       [
                           [
                               'field' => 'field_code_persisted_without_laser',
                               'operator' => '==',
                               'value' => '1',
                           ],
                       ],
                   ],
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
           'menu_order' => 40,
           'position' => 'normal',
           'style' => 'default',
           'label_placement' => 'top',
           'instruction_placement' => 'label',
           'hide_on_screen' => '',
           'active' => true,
           'description' => '',
           'show_in_rest' => 1,
       ]);
       
       // Other Visual Phenomena section
       acf_add_local_field_group([
           'key' => 'group_questionnaire_other_phenomena',
           'title' => __('Questionnaire: Other Visual Phenomena', 'open-veil'),
           'fields' => [
               [
                   'key' => 'field_noticed_anything_else',
                   'label' => __('Did You Notice Anything Else?', 'open-veil'),
                   'name' => 'noticed_anything_else',
                   'type' => 'textarea',
                   'instructions' => __('Describe any other visual phenomena or effects observed', 'open-veil'),
                   'required' => 0,
               ],
               [
                   'key' => 'field_experiment_duration',
                   'label' => __('Experiment Duration (Minutes)', 'open-veil'),
                   'name' => 'experiment_duration',
                   'type' => 'number',
                   'instructions' => __('How long did the experiment last?', 'open-veil'),
                   'required' => 0,
                   'min' => 1,
               ],
               [
                   'key' => 'field_questions_comments_suggestions',
                   'label' => __('Questions, Comments, Suggestions', 'open-veil'),
                   'name' => 'questions_comments_suggestions',
                   'type' => 'textarea',
                   'instructions' => __('Any additional feedback, questions, or suggestions', 'open-veil'),
                   'required' => 0,
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
           'menu_order' => 50,
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
   
   /**
    * Sanitizes an integer value.
    * 
    * @param mixed $value The value to sanitize
    * @return int Sanitized value
    */
   public function sanitize_integer($value): int {
       return absint($value);
   }
   
   /**
    * Sanitizes a float value.
    * 
    * @param mixed $value The value to sanitize
    * @return float Sanitized value
    */
   public function sanitize_float($value): float {
       return floatval($value);
   }
   
   /**
    * Sanitizes a boolean value.
    * 
    * @param mixed $value The value to sanitize
    * @return bool Sanitized value
    */
   public function sanitize_boolean($value): bool {
       return (bool) $value;
   }
}
