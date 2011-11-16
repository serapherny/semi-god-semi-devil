<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/photo.php';

class Photo_model extends CI_Model {
  
  public function __construct() {
    parent::__construct();
  }

  public function get_photo_list($limit = 10, $offset = 0) {
    $result_set = array();
    $query = $this->db->get('photo', $limit, $offset);
    foreach ($query->result_array() as $row) {
      $photo = new Photo();
      $photo->load_array($row, $blacklist = array());
      $result_set[] = array('photo'=>$photo);
    }
    return $result_set;
  }
  
  public function upload_photo($photo, &$response_content) {

    $response_content = array();
    
    if (!$photo instanceof Photo) {
      return 'failed : not a valid instance of Photo.';
    } else {
      $photo->set_create_time(now());
      $date_dir = mdate('%Y%m%d', $photo->get_create_time());
      $file_path = './photos/'.$date_dir;
      $photo->set_file_path($file_path);
      
      $file_name = random_string('alpha', 10);
      $photo->set_file_name($file_name);
      
      $file_ext = $photo->get_file_ext();
      
      $this->load->helper('file');
      $this->load->helper('directory');
      
      $map = directory_map('./photos/', 1);
      if (!in_array($date_dir, $map)) {
        mkdir('./photos/'.$date_dir);
      }
      
      $written = $photo->save_binary_to_file(
         $file_path.'/'.$file_name.$file_ext);
      
      if (!$written) {
        return 'failed: not able to file:'.
               $file_path.'/'.$file_name.$file_ext;
      }
      
      $response_content['file_path'] = $file_path.'/'.$file_name.$file_ext;
      
      $blacklist = array('binary');
      
      $photo_entry = $photo->to_array($compressed = true, $filter_null = true, $blacklist);
      $this->db->insert('photo', $photo_entry);
      return 'suc';
    }
  }
  

}

/* End of file photo_model.php */
/* Location: ./application/models/ent/photo_model.php */