<h2>已有用户列表</h2>
<ul>
  <?php foreach($user_list as $user_rec):?>
    <li><?php $this->load->view('ent/user_li', $user_rec);?></li>
  <?php endforeach;?>
</ul>