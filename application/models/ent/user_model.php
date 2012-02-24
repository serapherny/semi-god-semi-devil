<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';
require_once MODEL.'ent/ent_model.php';

class User_model extends Ent_model {

  public function __construct() {
    parent::__construct();
  }

  protected function tableName() {
    return 'user';
  }

  protected function typeName() {
    return 'User';
  }

  /*
   * Customized insertion function.
   */
  public function create_user($user_ent) {
    if (!$user_ent instanceof User) {
      return 'failed : not a valid instance of User.';
    } else {
      $this->db->where('email_addr', $user_ent->get('email_addr'));
      $this->db->from('user');
      if ($this->db->count_all_results() > 0) {
        return 'failed : email exists in database.';
      }
      $this->db->flush_cache();

      $user_ent->set('create_time',now())->set('last_login_time',now());
      $report = $this->insert($user_ent);
      if ($report[$user_ent->get('sid')] == 'inserted') {
        return $user_ent;
      } else {
        return $report[$user_ent->get('sid')];
      }
    }
  }
}

/* End of file user_model.php */
/* Location: ./application/models/ent/user_model.php */