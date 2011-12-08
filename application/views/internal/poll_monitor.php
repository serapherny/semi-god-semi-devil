<?php if ($action_result): ?>
<div class="float_r color_red width_section padding_as borders_s borderc_gray"><?php print_r($action_result);?></div>
<?php endif;?>

<h2 class="clr_l">已有投票列表</h2>
<ul>
  <?php foreach($poll_list as $poll_entry):?>
    <li><?php $this->load->view('ent/poll_li', $poll_entry);?></li>
  <?php endforeach;?>
</ul>

<?php echo form_open_multipart(uri_string());?>
以用户ID:<input type="text" name="usid" size="15" />发出投票
Photo 1 <input type="text" name="photo_1_id" size="15" />
Photo 2 <input type="text" name="photo_2_id" size="15" />
<input type="submit" value="upload" />
<input type="hidden" name="mode" value="create" />
</form>

<?php echo form_open(uri_string()); ?>
	投票sid：<input type="text" name="sid" />
	<input type="hidden" name="mode" value="data" />
	<button type="submit">照片信息</button>
</form>

<div class="margin_tb height_bar">
<form action="<?php echo $rebuild_db_page;?>" method="post">
	<button class="float_l" type="submit">重建数据库</button>
	<div class="float_l margin_lb"><?php echo anchor(uri_string(), '刷新本页');?></div>
</form>
</div>


