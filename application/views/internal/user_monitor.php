<form action="<?php echo $rebuild_db_page;?>" method="post">
	<button type="submit">重建数据库</button>
</form>

<h2>已有用户列表</h2>
<ul>
  <?php foreach($user_list as $user_rec):?>
    <li><?php $this->load->view('ent/user_li', $user_rec);?></li>
  <?php endforeach;?>
</ul>

<form action="<?php echo $create_user_page;?>" method="post">
	用户名：<input type="text" name="nickname" />
	密码：<input type="text" name="password" />
	邮箱：<input type="text" name="email_addr"/>
	<button type="submit">创建用户</button>
</form>


