<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';
require_once LIB.'ent/device.php';

class User extends Ent {
 
  private 
    $nickname_ = NOT_SET,
    $email_addr_ = NOT_SET,
    $last_device_ = NOT_SET,
    $device_list_ = array();
  
  public function __construct() {
    parent::__construct();
  }
  
  public function set_nickname($nickname) {
    $this->nickname_ = $nickname;
    return $this;
  }
  
  public function get_nickname() {
    if ($this->nickname_ != NOT_SET) {
      return $this->nickname_;
    } else {
      log_message('error', 'Reading nickname from uninitialized User.');
      return NOT_SET;
    }
  }
  
  public function set_email_addr($email_addr) {
    $this->email_addr_ = $email_addr;
    return $this;
  }
  
  public function add_device($as_device_id) {
    // Device class will determine whether this id is valid.
    if (Device::is_device_id($as_device_id)) {
      if (!isset($device_list_[$as_device_id])) {
        // Record adding time of a device.
        $device_list_[$as_device_id] = now();
        return true; 
      } else {
        return false;
      }
    } else {
      log_message('error', 'Adding invalid device id.');
      return false;
    }
  }
}