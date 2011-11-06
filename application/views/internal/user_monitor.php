<h2>已有用户列表</h2>
<ul>
  <?php foreach($user_list as $user):?>
    <li><?php $this->load->view('ent/user_li', $user);?></li>
  <?php endforeach;?>
</ul>