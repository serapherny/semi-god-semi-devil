<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';

class User_monitor extends CI_Controller {
  function __construct() {
    parent::__construct();
  }

  public function index() {
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->library('form_validation');
    $this->form_validation->set_rules('nickname', '用户名', 'required');
    $this->form_validation->set_rules('password', '密码', 'required');
    $this->form_validation->set_rules('passconf', '验证密码', 'required');
    $this->form_validation->set_rules('email_addr', '邮箱', 'required');
    // Check if the fields have been fulfilled.
    if ($this->form_validation->run() == TRUE ) {
      $content = array(
                           'nickname'=> $this->input->post('nickname'),
                           'password'=> $this->input->post('password'),
                           'email_addr'=> $this->input->post('email_addr')
      );
      $this->load->library('xmlrpc');
      $this->load->library('device/simulate_android');
      $rpc_result = $this->simulate_android->set_xmlrpc($this->xmlrpc)
                         ->send_rpc('create_user', $content, 'user_rpc');
      if ($rpc_result['action_result'] == 'suc') {
        $action_result = '成功创建新用户';
      } else {
        $action_result = '创建用户失败， 错误类型：'.$rpc_result['action_result'];
      }
    } else {
      $error = validation_errors();
      if ($error) {
        $action_result = '输入有误：'.$error;
      } else {
        $action_result = '';
      }
    }
    
    $this->load->model('ent/user_model', 'user_model');
    $data = array('user_list' => $this->user_model->get_user_list(),
                  'rebuild_db_page' => site_url('internal/rebuild_db'),
                  'action_result' => $action_result               
    );
    $this->load->view('header', array('page_title'=>'用户管理'));
  	$this->load->view('internal/user_monitor', $data);
  	$this->load->view('footer');
  }
}

/* End of file user_monitor.php */
/* Location: ./application/controllers/internal/user_monitor.php */