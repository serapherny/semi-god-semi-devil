<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';
require_once LIB.'ent/device.php';

class User extends Ent {
 
  private
    $email_addr_ = NOT_SET,
    $nickname_ = NOT_SET,
    $password_ = NOT_SET,
    $profile_photo = NOT_SET,
    $last_device_ = NOT_SET,
    $device_list_ = array();
  
  public function __construct() {
    parent::__construct();
  }
  
  public function validateNewUserData() {
    return ($this->nickname_ !== NOT_SET &&
            $this->password_ !== NOT_SET &&
            ($this->email_addr_ !== NOT_SET || 
             $this->last_device_ !== NOT_SET));
  }
  
  public function set_nickname($nickname) {
    $this->nickname_ = $nickname;
    return $this;
  }
  
  public function get_nickname() {
      return $this->nickname_;
  }
  
  public function set_password($password) {
    $this->password_ = $password;
    return $this;
  }
  
  public function get_password() {
    return $this->password_;
  }
  
  public function set_email_addr($email_addr) {
    $this->email_addr_ = $email_addr;
    return $this;
  }
  
  public function get_email_addr() {
    return $this->email_addr_;
  }
  
  public function add_device($device_id) {
    // Device class will determine whether this id is valid.
    if (Device::is_device_id($device_id)) {
      if (!isset($this->device_list_[$device_id])) {
        // Record adding time of a device.
        $this->device_list_[$device_id] = now();
      }
    } else {
      log_message('error', 'Adding invalid device id.');
    }
    return $this;
  }
  
  public function set_last_device($device_id) {
    if (Device::is_device_id($device_id)) {
      $this->last_device_ = $device_id;
    } else {
      log_message('error', 'Adding invalid device id.');
    }
    return $this;
  }
  
  public function get_last_device() {
    return $this->last_device_;
  }
  
  public function compress_user_info() {
    return 'compressed';
  }
  
  public function decompress_user_info() {
    return $this;
  }
}