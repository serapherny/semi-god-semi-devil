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
  
  public function compress_user_info() {
    return 'compressed';
  }
  
  public function decompress_user_info() {
    return $this;
  }
  
  public function to_array($compressed, 
                           $filter_null = true, 
                           $blacklist = array(),
                           $whitelist = false) {
    
    $fields = array('sid', 'nickname', 'email_addr', 'password',
                     'last_device', 'create_time', 'last_login_time',
                     'device_list', 'user_info');
    
    foreach ($fields as $field) {
      $lock[$field] = !in_array($field, $blacklist) ^ $whitelist;
    }
    
    $user_rec = array();

    if ($lock['sid'] && (!$filter_null || $this->get_sid() !== NOT_SET)) {
      $user_rec['sid'] = $this->get_sid();
    }
    
    if ($lock['nickname'] && (!$filter_null || $this->get_nickname() !== NOT_SET)) {
      $user_rec['nickname'] = $this->get_nickname();
    }

    if ($lock['email_addr'] && (!$filter_null || $this->get_email_addr() !== NOT_SET)) {
      $user_rec['email_addr'] = $this->get_email_addr();
    }

    if ($lock['password'] && (!$filter_null || $this->get_password() !== NOT_SET)) {
      $user_rec['password'] = $this->get_password();
    } 
    
    if ($lock['last_device'] && (!$filter_null || $this->get_last_device() !== NOT_SET)) {
      $user_rec['last_device'] = $this->get_last_device();
    }
    
    if ($lock['last_login_time'] && (!$filter_null || $this->get_last_login_time() !== NOT_SET)) {
      $user_rec['last_login_time'] = $this->get_last_login_time();
    }

    if ($compressed) {
      if ($lock['user_info']) {
        $user_rec['user_info'] = $this->compress_user_info();
        
        if ($filter_null && $user_rec['user_info'] === NOT_SET) {
          unset($user_rec['user_info']);
        }
      }
    } else {
      if ($lock['create_time'] && (!$filter_null || $this->get_create_time() !== NOT_SET)) {
         $user_rec['create_time'] = $this->get_create_time();
      }
      
      if ($lock['device_list'] && (!$filter_null || $this->get_device_list() !== array())) {
        $user_rec['device_list'] = $this->get_device_list();
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
    }
    
    if ($lock['sid'] && isset($user_rec['sid'])) {
      $this->set_sid($user_rec['sid']);
    }
    
    if ($lock['nickname'] && isset($user_rec['nickname'])) {
      $this->set_nickname($user_rec['nickname']);
    }
    
    if ($lock['email_addr'] && isset($user_rec['email_addr'])) {
      $this->set_email_addr($user_rec['email_addr']);
    }
    
    if ($lock['password'] && isset($user_rec['password'])) {
      $this->set_password($user_rec['password']);
    }
    
    if ($lock['last_device'] && isset($user_rec['last_device'])) {
      $this->set_last_device($user_rec['last_device']);
    }
    
    if ($lock['last_login_time'] && isset($user_rec['last_login_time'])) {
      $this->set_last_login_time($user_rec['last_login_time']);
    }
    
    if ($lock['UDID'] && isset($user_rec['UDID'])) {
      $this->add_device($user_rec['UDID'])
           ->set_last_device($user_rec['UDID']);
    }
    
    if ($lock['user_info'] && isset($user_rec['user_info'])) {
      $this->decompress_user_info($user_rec['user_info']);
    }
    
    if ($lock['create_time'] && isset($user_rec['create_time'])) {
      $this->set_create_time($user_rec['create_time']);
    }
    
    return $this;
  }
}