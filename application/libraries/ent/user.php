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
    $create_time_ = NOT_SET,
    $last_login_time_ = NOT_SET,
    $device_list_ = array();
  
  public function __construct() {
    parent::__construct();
  }
  
  public function validateBasicDataForNewUser() {
    return $this->nickname_ !== NOT_SET &&
           $this->password_ !== NOT_SET;
  }
  
  private function validateDeviceIdentifiable() {
    return $this->last_device_ !== NOT_SET;
  }
  
  private function validateUserIdentifiable() {
    return $this->email_addr_ !== NOT_SET ||
           $this->get_sid() !== NOT_SET;
  }
  
  private function validateLoginable() {
    return $this->validateUserIdentifiable() &&
           $this->password_ !== NOT_SET;
  }
  
  public function validateIdentifiable() {
    return $this->validateUserIdentifiable() ||
           $this->validateDeviceIdentifiable();
  }
  
  public function validateBindable() {
    return $this->validateLoginable() &&
           $this->validateDeviceIdentifiable(); 
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
  
  public function get_device_list() {
    return $this->device_list_;
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
  
  public function set_create_time($create_time) {
    $this->create_time_ = $create_time;
    return $this;
  }
  
  public function get_create_time() {
    return $this->create_time_;
  }
  
  public function set_last_login_time($login_time) {
    $this->last_login_time_ = $login_time;
    return $this;
  }
  
  public function get_last_login_time() {
    return $this->last_login_time_;
  }
  
  public function get_user_info() {
    return 'compressed';
  }
  
  public function set_user_info() {
    return $this;
  }
  
  public function to_array($compressed, 
                           $filter_null = true, 
                           $blacklist = array(),
                           $whitelist = false) {
    
    $user_rec = array();

    if ($compressed) {
      $fields = array('sid', 'nickname', 'email_addr', 'password',
                           'last_device', 'last_login_time', 'user_info');
    } else {
      $fields = array('sid', 'nickname', 'email_addr', 'password',
                           'last_device', 'create_time', 'last_login_time',
                           'device_list');
    }
    
    foreach ($fields as $field) {
      $lock = !in_array($field, $blacklist) ^ $whitelist;
      $getter = 'get_'.$field;
      if ($lock && (!$filter_null || $this->$getter() !== NOT_SET)) {
        $user_rec[$field] = $this->$getter();
      }
    }
    
    return $user_rec;
  }
  
  public function load_array($user_rec, $blacklist = array(), $whitelist = false) {
    
    $fields = array('sid', 'nickname', 'email_addr', 'password',
                    'last_device', 'create_time', 'last_login_time',
                    'device_list', 'UDID', 'user_info');
    
    foreach ($fields as $field) {
      $lock[$field] = !in_array($field, $blacklist) ^ $whitelist;
      if ($lock[$field] && isset($user_rec[$field])) {
        $setter = 'set_'.$field;
        if ($field == 'UDID') {
          $this->set_last_device($user_rec['UDID'])
               ->add_device($user_rec['UDID']);
        } else {
          $this->$setter($user_rec[$field]);
        }
      }
    }
    return $this;
  }
}