<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('NOT_SET', 'NOT_SET');

class Ent {
  
  private $sid_ = NOT_SET;
  
  public function __construct() {
  }
  
  public function new_sid() {
    $this->sid_ = random_string('numeric', 16);
    return $this;
  }
  
  public function set_sid($sid) {
    $this->sid_ = $sid;
    return $this;
  }
  
  public function get_sid() {
    return $this->sid_;
  }
}