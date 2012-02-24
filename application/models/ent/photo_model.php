<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/photo.php';
require_once MODEL.'ent/ent_model.php';

class Photo_model extends Ent_model {

  public function __construct() {
    parent::__construct();
  }

  protected function tableName() {
    return 'photo';
  }

  protected function typeName() {
    return 'Photo';
  }

  /*
   * Upload a photo to the database. The $photo_ent must be an object of Photo;
   *
   * if succeeds, this function returns a full Photo entity; else it returns
   * a string showing the detailed error.
   *
   */

  // We don't save binary to database; only file infos.
  protected function insertBlacklist() {
    return array('binary');
  }

  public function upload_photo($photo_ent) {

    if (!$photo_ent instanceof Photo) {
      return 'failed : not a valid instance of Photo.';
    } else {
      // Check if the photo has all info needed.
      if (!$photo_ent->validateNewPhotoData()) {
        return 'failed : ont enough new photo data.';
      }

      // Autofill the rest of the ent.
      $photo_ent->set('create_time', now());
      $date_dir = mdate('%Y%m%d', $photo_ent->get('create_time'));

      $file_path = './photos/'.$date_dir;
      $photo_ent->set('file_path', $file_path);

      $file_name = random_string('alpha', 10);
      $photo_ent->set('file_name', $file_name);

      $file_ext = $photo_ent->get('file_ext');

      $this->load->helper('file');
      $this->load->helper('directory');

      $map = directory_map('./photos/', 1);
      if (!in_array($date_dir, $map)) {
        mkdir('./photos/'.$date_dir);
      }

      $written = $photo_ent->save_binary_to_file(
         $file_path.'/'.$file_name.$file_ext);

      // For the cases that file are not successfully created.
      if (!$written) {
        return 'failed: not able to file:'.
               $file_path.'/'.$file_name.$file_ext;
      }

      $response_content['file_path'] = $file_path.'/'.$file_name.$file_ext;

      $this->insert($photo_ent);

      // At this point, everything is good, return the filled ent.
      return $photo_ent;
    }
  }


}

/* End of file photo_model.php */
/* Location: ./application/models/ent/photo_model.php */