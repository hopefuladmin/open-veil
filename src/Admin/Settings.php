<?php
namespace OpenVeil\Admin;

/**
 * Settings
 * 
 * Registers admin settings page for Open Veil
 */
class Settings {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Add settings page
     */
    public function add_settings_page() {
        add_options_page(
            __('Open Veil Settings', 'open-veil'),
            __('Open Veil', 'open-veil'),
            'manage_options',
            'open-veil-settings',
            [$this, 'render_settings_page']
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('open-veil', 'open_veil_settings');
        
        add_settings_section(
            'open_veil_general_section',
            __('General Settings', 'open-veil'),
            [$this, 'render_general_section'],
            'open-veil-settings'
        );
        
        add_settings_field(
            'guest_submissions',
            __('Guest Submissions', 'open-veil'),
            [$this, 'render_guest_submissions_field'],
            'open-veil-settings',
            'open_veil_general_section'
        );
        
        add_settings_field(
            'claim_token_expiry',
            __('Claim Token Expiry', 'open-veil'),
            [$this, 'render_claim_token_expiry_field'],
            'open-veil-settings',
            'open_veil_general_section'
        );
        
        add_settings_field(
            'email_notifications',
            __('Email Notifications', 'open-veil'),
            [$this, 'render_email_notifications_field'],
            'open-veil-settings',
            'open_veil_general_section'
        );
        
        add_settings_section(
            'open_veil_api_section',
            __('API Settings', 'open-veil'),
            [$this, 'render_api_section'],
            'open-veil-settings'
        );
        
        add_settings_field(
            'api_access',
            __('API Access', 'open-veil'),
            [$this, 'render_api_access_field'],
            'open-veil-settings',
            'open_veil_api_section'
        );
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('open-veil');
                do_settings_sections('open-veil-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render general section
     */
    public function render_general_section() {
        echo '<p>' . __('Configure general settings for Open Veil.', 'open-veil') . '</p>';
    }
    
    /**
     * Render guest submissions field
     */
    public function render_guest_submissions_field() {
        $options = get_option('open_veil_settings');
        $guest_submissions = isset($options['guest_submissions']) ? $options['guest_submissions'] : 1;
        ?>
        <label>
            <input type="checkbox" name="open_veil_settings[guest_submissions]" value="1" <?php checked(1, $guest_submissions); ?>>
            <?php _e('Allow guest submissions', 'open-veil'); ?>
        </label>
        <p class="description"><?php _e('If checked, guests can submit trials without logging in.', 'open-veil'); ?></p>
        <?php
    }
    
    /**
     * Render claim token expiry field
     */
    public function render_claim_token_expiry_field() {
        $options = get_option('open_veil_settings');
        $claim_token_expiry = isset($options['claim_token_expiry']) ? $options['claim_token_expiry'] : 7;
        ?>
        <input type="number" name="open_veil_settings[claim_token_expiry]" value="<?php echo esc_attr($claim_token_expiry); ?>" min="1" max="30">
        <p class="description"><?php _e('Number of days before claim tokens expire.', 'open-veil'); ?></p>
        <?php
    }
    
    /**
     * Render email notifications field
     */
    public function render_email_notifications_field() {
        $options = get_option('open_veil_settings');
        $email_notifications = isset($options['email_notifications']) ? $options['email_notifications'] : 1;
        ?>
        <label>
            <input type="checkbox" name="open_veil_settings[email_notifications]" value="1" <?php checked(1, $email_notifications); ?>>
            <?php _e('Send email notifications', 'open-veil'); ?>
        </label>
        <p class="description"><?php _e('If checked, email notifications will be sent when trials are approved/published.', 'open-veil'); ?></p>
        <?php
    }
    
    /**
     * Render API section
     */
    public function render_api_section() {
        echo '<p>' . __('Configure API settings for Open Veil.', 'open-veil') . '</p>';
    }
    
    /**
     * Render API access field
     */
    public function render_api_access_field() {
        $options = get_option('open_veil_settings');
        $api_access = isset($options['api_access']) ? $options['api_access'] : 'public';
        ?>
        <select name="open_veil_settings[api_access]">
            <option value="public" <?php selected('public', $api_access); ?>><?php _e('Public', 'open-veil'); ?></option>
            <option value="logged_in" <?php selected('logged_in', $api_access); ?>><?php _e('Logged-in Users Only', 'open-veil'); ?></option>
            <option value="admin" <?php selected('admin', $api_access); ?>><?php _e('Administrators Only', 'open-veil'); ?></option>
        </select>
        <p class="description"><?php _e('Who can access the API.', 'open-veil'); ?></p>
        <?php
    }
}
