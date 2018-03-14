<div class="wrap" >
  <div id="icon-options-general" class="icon32"></div>
  <h2><?php _e( 'WP AMember Login Settings', 'wp-amember-login' ); ?></h2>

  <table>
      <tr>
          <th>AMember Product ID</th>
          <th>Role</th>
          <th>Delete</th>
      </tr>
      <?php foreach($this->roles_mapping as $role):?>
      <tr>
          <td><?php echo $role->amember_product?></td>
          <td><?php echo $role->wordpress_role?></td>
          <td><a href="/wp-admin/admin.php?page=wp-amember-login-role&action=delete-role&id=<?php echo $role->id?>">Delete</a></td>
      </tr>
      <?php endforeach;?>
  </table>

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

        <tr valign="top"><th scope="row"><label for=""><?php _e( 'AMember Product', 'wp-amember-login' ); ?></label></th>
          <td>
            <select class="ajdebe_dropdown" name="wp_amember_login[amember_product]">
              <?php foreach($products as $product):?>
              <option name="wp_amember_login[amember_product]" value="<?php echo $product->id?>"> <?php echo $product->title?></option>

              <?php endforeach;?>
            </select>
            <p class="description"><?php _e( 'AMember Product Subscriptions to be mapped', 'wp-amember-login' ); ?></p>
          </td>
        </tr>

        <tr valign="top"><th scope="row"><label for=""><?php _e( 'User Role', 'wp-amember-login' ); ?></label></th>
          <td>
            <select class="ajdebe_dropdown" name="wp_amember_login[wordpress_role]">
              <?php foreach($roles as $role):?>
              <option name="wp_amember_login[wordpress_role]" value="<?php echo $role['name']?>"> <?php echo $role['name']?></option>

              <?php endforeach;?>
            </select>
            <p class="description"><?php _e( 'Associated User Role', 'wp-amember-login' ); ?></p>
          </td>
        </tr>
      <?php } /* endif current_user_can( 'manage_options' ); */ ?>

    </table>
    <p class="submit">
      <?php submit_button(); ?>
    </p>
  </form>
</div>
