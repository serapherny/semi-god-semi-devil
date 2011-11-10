<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('NOT_SET', FALSE);

class Ent {
  
  private $sid_ = NOT_SET;
  
  public function __construct() {
    $this->sid_ = random_string('numeric', 16);
  }
  
  public function set_sid($sid) {
    $this->sid_ = $sid;
    return $this;
  }
  
  public function get_sid() {
    if ($this->sid_ != NOT_SET) {
      return $this->sid_;
    } else {
      log_message('error', 'Read sid from uninitialized Ent.');
      return NOT_SET;
    }
  }
}