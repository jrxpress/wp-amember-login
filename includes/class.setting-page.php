<?php
class Setting_Page
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
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
      $page_title = __( 'WP AMember Login', 'textdomain' );
      $menu_title = 'WP AMember Login';
      $capability = 'manage_options';
      $menu_slug = 'wp-amember-login';
      $function = array( $this, 'create_admin_page' );
      $icon_url = '';
      $position = 999;
      add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
      if ( ! current_user_can( 'manage_options' ) ) {
        return;
      }
        $inputs = $_POST['wp_amember_login'];
        foreach ($inputs as $input => $value) {
            $result = update_option('wp_amember_login_'.$input, $value);
        }
        // Set class property
        foreach ($this->option_fields as $key => $value) {
            $this->options[$value] = get_option( $this->option_prefix.$value );
        }

        include( WP_AMEMBER_LOGIN__PLUGIN_DIR . 'views/admin-options.php' );
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
