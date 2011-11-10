<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';

class User_model extends CI_Model {
  
  public function __construct() {
    parent::__construct();
  }

  public function get_user_list($limit = 10, $offset = 0) {
    $result_set = array();
    $query = $this->db->get('user', $limit, $offset);
    foreach ($query->result_array() as $row) {
      $user = $this->rec_to_user($row);
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
      $user_rec = $this->user_to_rec($user);
      $this->db->insert('user', $user_rec);
      return 'suc';
    }
  }
  
  public function user_to_rec($user) {
    $user_rec = array(
            'sid'        => $user->get_sid(),
            'nickname'   => $user->get_nickname(),
            'email_addr' => $user->get_email_addr(),
            'last_device'=> $user->get_last_device(),
            'user_info'  => $user->compress_user_info()
    );
    return $user_rec;
  }
  
  public function rec_to_user($user_rec) {
    $user = new User();
    $user->set_sid($user_rec['sid'])
         ->set_email_addr($user_rec['email_addr'])
         ->set_last_device($user_rec['last_device'])
         ->set_nickname($user_rec['nickname'])
         ->decompress_user_info($user_rec['user_info']);
    return $user;
  }
}

/* End of file user_model.php */
/* Location: ./application/models/ent/user_model.php */