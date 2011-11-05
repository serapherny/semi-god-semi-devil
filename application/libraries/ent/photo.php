<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Photo extends Ent {
  
  private
    $creater_ = NOT_SET,
    $source_file = NOT_SET,
    $image_info = NOT_SET,
    $items_ = array(),
    $tags_ = array();
  
  public function __construct() {
    parent::__construct();
  }
}