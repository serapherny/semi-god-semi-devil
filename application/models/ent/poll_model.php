<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/poll.php';

class Poll_model extends CI_Model {
  
  public function __construct() {
    parent::__construct();
  }

  public function get_poll_list($limit = 100, $offset = 0) {
    $result_set = array();
    $query = $this->db->get('poll', $limit, $offset);
    foreach ($query->result_array() as $row) {
      $poll = new poll();
      $poll->load_array($row, $blacklist = array());
      $result_set[] = array('poll'=>$poll);
    }
    return $result_set;
  }
  
  public function get_poll_data($poll_id_list, &$response_content) {
    $response_content = array();
    $this->db->where_in('sid', $poll_id_list);
    $query = $this->db->get('poll');
    foreach ($query->result_array() as $row) {
      $response_content[] = array('poll'=>$row);
    }
    return 'suc';
  }
  
  public function create_poll($poll, &$response_content) {

    $response_content = array();
    
    if (!$poll instanceof Poll) {
      return 'failed : not a valid instance of poll.';
    } else {
      $poll->set_create_time(now());
      $blacklist = array();
      $poll_entry = $poll->to_array($compressed = true, $filter_null = true, $blacklist);
      $this->db->insert('poll', $poll_entry);
      $response_content['sid'] = $poll->get_sid();
      return 'suc';
    }
  }
  

}

/* End of file poll_model.php */
/* Location: ./application/models/ent/poll_model.php */