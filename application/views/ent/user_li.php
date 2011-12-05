(<?php echo $user->get_sid();?>) 
<?php echo $user->get_nickname();?>,
<?php echo $user->get_password();?>,
<?php echo $user->get_email_addr();?>,
<?php echo $user->get_last_device();?>,
<?php echo $user->get_create_time();?>,
<?php echo standard_date('DATE_RFC1123', $user->get_last_login_time());?>,
<?php echo $user->compress_user_info();?>,