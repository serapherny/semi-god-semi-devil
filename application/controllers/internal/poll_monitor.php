<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/poll.php';

class Poll_monitor extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function create_poll() {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('author', '作者sid', 'required');
    $this->form_validation->set_rules('type', '类型', 'required');
    $this->form_validation->set_rules('data', '数据');
    $this->form_validation->set_rules('cansee', '可见', 'required');
    // Check if the fields have been fulfilled.
    if ($this->form_validation->run() == TRUE ) {
      $content = array(
                     'author'=> $this->input->post('author'),
                     'template'=> $this->input->post('template'),
                     'data'=> $this->input->post('data'),
                     'cansee'=> $this->input->post('cansee')
      );
      $this->load->library('xmlrpc');
      $this->load->library('device/simulate_android');
      $rpc_result = $this->simulate_android->set_xmlrpc($this->xmlrpc)
                      ->send_rpc('create_poll', $content, 'poll_rpc');
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
    return $action_result;
  }
  
  public function poll_data() {
    $this->load->library('form_validation');
    // Check if the fields have been fulfilled.
    $this->form_validation->set_rules('sid', 'poll sid');
    if ($this->form_validation->run() == TRUE ) {
      $poll_list = explode(',', $this->input->post('sid'));
      $content = array('poll_list'=> array($poll_list, 'array'));
      $this->load->library('xmlrpc');
      $this->load->library('device/simulate_android');
      $rpc_result = $this->simulate_android->set_xmlrpc($this->xmlrpc)
                      ->send_rpc('poll_data', $content, 'poll_rpc');
      if ($rpc_result['action_result'] == 'suc') {
        $action_result = '成功取得用户数据';
      } else {
        $action_result = '取得用户数据失败， 错误类型：'.$rpc_result['action_result'];
      }
    } else {
      $error = validation_errors();
      if ($error) {
        $action_result = '输入有误：'.$error;
      } else {
        $action_result = '';
      }
    }
    return $action_result;
  }
  
  public function index() {
    $this->load->helper('url');
    $this->load->helper('form');
    $action_result = '';
    $mode = $this->input->post('mode');
    switch ($mode) {
      case 'create': $action_result = $this->create_poll(); break;
      case 'data':   $action_result = $this->poll_data(); break;
      default: break;
    }
    $this->load->model('ent/poll_model', 'poll_model');
    $data = array('poll_list' => $this->poll_model->get_poll_list(),
                  'rebuild_db_page' => site_url('internal/rebuild_db?table=poll'),
                  'action_result' => $action_result               
    );
    $this->load->view('header', array('page_title'=>'用户管理'));
  	$this->load->view('internal/poll_monitor', $data);
  	$this->load->view('footer');
  }
}

/* End of file poll_monitor.php */
/* Location: ./application/controllers/internal/poll_monitor.php */