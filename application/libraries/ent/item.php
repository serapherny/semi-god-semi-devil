<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Item extends Ent {
  
  private
    $photos_ = array(),
    $creater_ = NOT_SET,
    $tags_ = array();
  
  public function __construct() {
    parent::__construct();
  }
}