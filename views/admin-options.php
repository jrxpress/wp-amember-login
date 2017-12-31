<div class="wrap" >
  <div id="icon-options-general" class="icon32"></div>
  <h2><?php _e( 'WP AMember Login Settings', 'wp-amember-login' ); ?></h2>

  <form method="post">
    <?php settings_fields( 'wp_amember_login_group' ); ?>
    <?php do_settings_sections( 'wp_amember_login_group' ); ?>
    <?php
    //load settings
    $api_url = ( isset( $this->options['api_url'] ) ) ? $this->options['api_url'] : '';
    $api_key = ( isset( $this->options['api_key'] ) ) ? $this->options['api_key'] : '';

    ?>
    <table class="form-table">
      <?php if ( current_user_can( 'manage_options' ) ) { ?>

        <tr valign="top"><th scope="row"><label for="submission_email_addresses"><?php _e( 'API URL', 'wp-amember-login' ); ?></label></th>
          <td>
            <input id="submission_email_addresses" name="wp_amember_login[api_url]" type="text" value="<?php echo esc_attr( $api_url ); ?>" class="regular-text" />
            <p class="description"><?php _e( 'API URL usually somthing like this "http://yourdomain.com/api/"', 'wp-amember-login' ); ?></p>
          </td>
        </tr>

        <tr valign="top"><th scope="row"><label for="submission_email_addresses"><?php _e( 'API KEY', 'wp-amember-login' ); ?></label></th>
          <td>
            <input id="submission_email_addresses" name="wp_amember_login[api_key]" type="text" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
            <p class="description"><?php _e( 'You can get your API KEY on admin panel AMember. Its usually somthing like this yAFMo8bZZyXxSG7FmcJX', 'wp-amember-login' ); ?></p>
          </td>
        </tr>
      <?php } /* endif current_user_can( 'manage_options' ); */ ?>

    </table>
    <p class="submit">
      <?php submit_button(); ?>
    </p>
  </form>
</div>
