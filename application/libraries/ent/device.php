<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Device extends Ent {
  
  private 
    $udid_ = NOT_SET,
    $user_ = NOT_SET;
  
  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntDevice);
  }
  
  public function get_udid() {
    return $this->udid_;
  }
  
  public function set_udid($udid) {
    $this->udid_ = $udid;
    return $this;
  }
  
  public static function is_device_id($as_udid) {
    // Not really checking yet.
    return true;
  }
}