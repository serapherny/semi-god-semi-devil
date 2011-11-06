<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';

class User_controller extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function create_user() {
    $user_json = $this->input->post('user_json');
    
  }
}

/* End of file user_controller.php */
/* Location: ./application/controllers/user_controller.php */