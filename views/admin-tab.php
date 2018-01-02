<?php
    $page = $_GET['page'];
?>
<h2 class="nav-tab-wrapper" id="meta_tabs">
				<a href="?page=wp-amember-login" class="nav-tab <?php if($page == 'wp-amember-login'){echo 'nav-tab-active';}?>">Credentials</a>
        <a href="?page=wp-amember-login-role" class="nav-tab <?php if($page == 'wp-amember-login-role'){echo 'nav-tab-active';}?>" >Role Mapping</a>
</h2>
