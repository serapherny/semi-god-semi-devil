<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';

class User_model extends CI_Model {
  
  public function __construct() {
    parent::__construct();
  }
  
  public function get_user_list() {
    $zhen = new User();
    $chao = new User();
    return 
      array(
        array('user'=> $zhen->set_nickname('Zhen')),
        array('user'=> $chao->set_nickname('Chao'))
      );         
  }
  
  public function add_new_user($user) {
    if (!$user instanceof User) {
      log_message('error', 'Adding an invalid user.');
    } else {
      
    }
  }
  
  public function update_user($user) {
    if (!$user instanceof User) {
      log_message('error', 'Adding an invalid user.');
    } else {
  
    }
  }
  
  public function delete_user($id) {
    if (!$user instanceof User) {
      log_message('error', 'Adding an invalid user.');
    } else {
  
    }
  }
}

/* End of file user_model.php */
/* Location: ./application/models/ent/user_model.php */