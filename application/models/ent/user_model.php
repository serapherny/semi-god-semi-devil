<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';

class User_model extends CI_Model {
  
  public function __construct() {
    parent::__construct();
  }

  public function get_user_list($limit = 100, $offset = 0) {
    $result_set = array();
    $query = $this->db->get('user', $limit, $offset);
    foreach ($query->result_array() as $row) {
      $user = new User();
      $user->load_array($row, $blacklist = array());
      $result_set[] = array('user'=>$user);
    }
    return $result_set;
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
  
  public function get_user_data($user_list, $user_ents) {
    $user_ents = array();
    $this->db->where_in('sid', $user_list);
    $query = $this->db->get('user');
    foreach ($query->result_array() as $row) {
      $user_ents[] = array('user'=>$row);
    }
    return 'suc';
  }  
  
  public function create_user($user) {
    if (!$user instanceof User) {
      return 'failed : not a valid instance of User.';
    } else {
      $this->db->where('email_addr', $user->get_email_addr());
      $query = $this->db->get('user', 1, 0);
      if ($query->num_rows() > 0) {
        return 'failed : email exists in database.';
      }
      // Here we are sure that insert can proceed.
      $blacklist = array();
      $user->set_create_time(now());
      $user_rec = $user->to_array($compressed = true, $filter_null = true, $blacklist);
      $this->db->insert('user', $user_rec);
      
      return 'suc';
    }
  }
  
}

/* End of file user_model.php */
/* Location: ./application/models/ent/user_model.php */