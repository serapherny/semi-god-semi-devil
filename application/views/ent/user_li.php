(<?php echo $user->get('sid');?>)
<?php echo $user->get('nickname');?>,
<?php echo $user->get('password');?>,
<?php echo $user->get('email_addr');?>,
<?php echo $user->get('last_device');?>,
<?php echo $user->get('create_time');?>,
<?php echo standard_date('DATE_RFC1123', $user->get('last_login_time'));?>,
