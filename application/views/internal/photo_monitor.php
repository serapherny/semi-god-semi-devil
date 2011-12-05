<?php if ($action_result): ?>
<div class="float_r color_red width_section padding_as borders_s borderc_gray"><?php print_r($action_result);?></div>
<?php endif;?>

<h2 class="clr_l">已有照片列表</h2>
<ul>
  <?php foreach($photo_list as $photo_entry):?>
    <li><?php $this->load->view('ent/photo_li', $photo_entry);?></li>
  <?php endforeach;?>
</ul>

<?php echo form_open_multipart(uri_string());?>
<input type="file" name="userfile" size="20" />
<br /><br />
以用户ID:<input type="text" name="usid" size="15" />上传 - 
<input type="submit" value="upload" />
<input type="hidden" name="mode" value="upload" />
</form>

<?php echo form_open(uri_string()); ?>
	照片sid：<input type="text" name="sid" />
	<input type="hidden" name="mode" value="data" />
	<button type="submit">照片信息</button>
</form>

<div class="margin_tb height_bar">
<form action="<?php echo $rebuild_db_page;?>" method="post">
	<button class="float_l" type="submit">重建数据库</button>
	<div class="float_l margin_lb"><?php echo anchor(uri_string(), '刷新本页');?></div>
</form>
</div>


