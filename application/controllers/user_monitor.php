<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';

class User_monitor extends CI_Controller {
  function __construct() {
    parent::__construct();
  }

  public function index() {
    $this->load->model('ent/user_model', 'user_model');
    $data = array('user_list' => $this->user_model->get_user_list());
    $this->load->view('header', array('page_title'=>'用户管理'));
  	$this->load->view('internal/user_monitor', $data);
  	$this->load->view('footer');
  }
}

/* End of file user_monitor.php */
/* Location: ./application/controllers/user_monitor.php */