<?php if ($action_result): ?>
<div class="float_r color_red width_section padding_as borders_s borderc_gray"><?php print_r($action_result);?></div>
<?php endif;?>

<h2 class="clr_l">已有用户列表</h2>
<ul>
  <?php foreach($user_list as $user):?>
    <li>(<?php echo $user->get('sid');?>)
        <?php echo $user->get('nickname');?>,
        <?php echo $user->get('password');?>,
        <?php echo $user->get('email_addr');?>,
        <?php echo $user->get('create_time');?>,
        <?php echo standard_date('DATE_RFC1123', $user->get('last_login_time'));?>
    </li>
  <?php endforeach;?>
</ul>

<?php echo form_open(uri_string()); ?>
	用户名：<input type="text" name="nickname" />
	密码：<input type="text" name="password" />
	验证密码：<input type="text" name="passconf" />
	邮箱：<input type="text" name="email_addr"/>
	<input type="hidden" name="mode" value="create" />
	<button type="submit">创建用户</button>
</form>

<?php echo form_open(uri_string()); ?>
	用户sid：<input type="text" name="sid_data" />
	<input type="hidden" name="mode" value="data" />
	<button type="submit">用户信息</button>
</form>

<div class="margin_tb height_bar">
<form action="<?php echo $rebuild_db_page;?>" method="post">
	<button class="float_l" type="submit">重建数据库</button>
	<div class="float_l margin_lb"><?php echo anchor(uri_string(), '刷新本页');?></div>
</form>
</div>



