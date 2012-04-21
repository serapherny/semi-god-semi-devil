<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/post.php';
require_once MODEL.'ent/ent_model.php';

class Post_model extends Ent_model {

  public function __construct() {
    parent::__construct();
  }

  protected function tableName() {
    return 'post';
  }

  protected function typeName() {
    return 'Post';
  }
}

/* End of file post_model.php */
/* Location: ./application/models/ent/post_model.php */