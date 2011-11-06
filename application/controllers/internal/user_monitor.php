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
  
  public function create_user() {
    $keybox = array('ios_v0.1'=>'semi-god-semi-devil-v0.1-acdjiac5tq-ios',
                    'android_v0.1'=>'semi-god-semi-devil-v0.1-acdjiac5tq-android');
    
    $udid = $this->input->post('UDID');
    $key = $this->input->post('KEY');
    $content = $this->input->post('CONTENT');
    $client = array_search($key, $keybox);
    
    echo "UDID got: $udid <br/>";
    echo "KEY got: $key <br/>";
    echo "Client is: $client <br/>";
    echo "CONTENT got: $content <br/>";
  }
}

/* End of file user_monitor.php */
/* Location: ./application/controllers/user_monitor.php */