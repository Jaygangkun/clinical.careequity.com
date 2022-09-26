<nav class="topnav">
  
    <div style="flex-grow: 8;"><img src="<?= base_url() ?>assets/img/new_logo.svg" class="header_logo"></div>
    <?php 
        $uri = $_SERVER['REQUEST_URI']; 
       
        if ($uri === "/profile" or $uri ==="/users" or $uri ==="/user-new"){
            ?>
    <div style="text-align: right;"><a href="<?php echo base_url()?>" class="header_home_wrap"><img src="<?= base_url() ?>assets/img/home.png" class="header_home"><img src="<?= base_url() ?>assets/img/home_hover.png" class="header_home_hover"></a></div>
            <?php
        }
    ?>
    
    <div style="text-align: right;"><a href="<?php echo base_url('/profile')?>" class="header_admin_wrap"><img src="<?= base_url() ?>assets/img/admin.png" class="header_admin"><img src="<?= base_url() ?>assets/img/admin_hover.png" class="header_admin_hover"></a></div>
    <div style="text-align: right;"><a href="<?php echo base_url('/login')?>" class="header_logout_wrap"><img src="<?= base_url() ?>assets/img/logout.png" class="header_logout"><img src="<?= base_url() ?>assets/img/logout_hover.png" class="header_logout_hover">Logout</a></div>
</nav>