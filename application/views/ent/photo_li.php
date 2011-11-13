(<?php echo $photo->get_sid();?>)
<?php echo $photo->get_file_path();?>/<?php echo $photo->get_file_name();?>
<?php echo $photo->get_file_ext();?>,
<?php echo $photo->get_author();?>,
<?php echo anchor($photo->addr(), '点击查看');?>,
<?php echo $photo->get_create_time();?>,
<?php echo $photo->compress_photo_info();?>,