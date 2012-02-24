(<?php echo $photo->get('sid');?>)
<?php echo $photo->get('file_path');?>/<?php echo $photo->get('file_name');?>
<?php echo $photo->get('file_ext');?>,
<?php echo $photo->get('author');?>,
<?php echo anchor($photo->addr(), '点击查看');?>,
<?php echo $photo->get('create_time');?>