<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Story extends Ent {
  
  private
    $creater_ = NOT_SET;
  
  public function __construct() {
    parent::__construct();
  }
}