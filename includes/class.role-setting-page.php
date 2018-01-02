<?php
class Role_Setting_Page
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $option_fields = ['api_key', 'api_url'];
    private $option_prefix = 'wp_amember_login_';
    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_role_setting_page' ) );
        add_action( 'admin_init',  array( $this, 'init'));
    }

    public function init(){

        register_setting('wp_amember_login', 'wp_amember_login');

        add_settings_section( 'section-credential', __( 'WP AMember Login Role Settings', 'section-role-setting' ), 'section_credential_callback', 'wp-amember-login' );
        add_settings_field( 'field-api-url', __( 'API URL', 'api-url' ), 'field_api_url_callback', 'wp-amember-login', 'section-role-setting' );
        add_settings_field( 'field-api-key', __( 'API KEY', 'api-key' ), 'field_api_key_callback', 'wp-amember-login', 'section-role-setting' );
    }

    function section_credential_callback() {
	     _e( 'Some help text regarding Section One goes here.', 'textdomain' );
    }

    function field_api_url_callback(){

      $settings = (array) get_option( 'wp_amember_login' );
      $field = "api_url";
      $value = esc_attr( $settings[$field] );

      echo "<input type='text' name='wp_amember_login[$field]' value='$value' />";
    }

    function field_api_key_callback(){

      $settings = (array) get_option( 'wp_amember_login' );
      $field = "api_key";
      $value = esc_attr( $settings[$field] );

      echo "<input type='text' name='wp_amember_login[$field]' value='$value' />";
    }

    /**
     * Add options page
     */
    public function add_role_setting_page()
    {
      $parent_slug = "wp-amember-login";
      $page_title = __( 'WP AMember Login', 'textdomain' );
      $menu_title = 'Role Setting';
      $capability = 'manage_options';
      $menu_slug = 'wp-amember-login-role';
      $function = array( $this, 'page_setting_role' );
      $icon_url = '';
      $position = 999;
      add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    }

    /**
     * Options page callback
     */

    public function page_setting_role(){
      if ( ! current_user_can( 'manage_options' ) ) {
        return;
      }
        $aMemberAPI = new AMember_API_Handler();
        $products = $aMemberAPI->get_all_products();
        $roles = get_editable_roles();

        $inputs = $_POST['wp_amember_login'];
        foreach ($inputs as $input => $value) {
            $result = update_option('wp_amember_login_'.$input, $value);
        }
        // Set class property
        foreach ($this->option_fields as $key => $value) {
            $this->options[$value] = get_option( $this->option_prefix.$value );
        }

        include( WP_AMEMBER_LOGIN__PLUGIN_DIR . 'views/admin-tab.php' );
        include( WP_AMEMBER_LOGIN__PLUGIN_DIR . 'views/admin-setting-role.php' );
    }


    public function wp_amember_login_setting($args){
        var_dump($args);
        die();
    }
    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'id_number', // ID
            'ID Number', // Title
            array( $this, 'id_number_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'title',
            'Title',
            array( $this, 'title_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
}
