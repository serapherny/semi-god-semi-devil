<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Photo extends Ent {

  private
    $author_ = NOT_SET,
    $photo_info = NOT_SET,
    $items_ = array(),
    $tags_ = array(),
    $binary_ = NOT_SET,
    $file_name_ = NOT_SET,
    $file_ext_ = NOT_SET,
    $file_path_ = NOT_SET,
    $create_time_ = NOT_SET;

  public function __construct() {
    parent::__construct();
  }

  public function save_binary_to_file($filename) {
    return write_file($filename, base64_decode($this->binary_));
  }

  public function set_binary($binary) {
    $this->binary_ = $binary;
    return $this;
  }

  public function addr() {
    return '../'.$this->file_path_.'/'.
           $this->file_name_.
           $this->file_ext_;
  }
  
  public function get_author() {
    return $this->author_;
  }

  public function set_author($author) {
    $this->author_ = $author;
    return $this;
  }

  public function get_image_info() {
    return $this->image_info_;
  }

  public function set_image_info($image_info) {
    $this->image_info_ = $image_info;
    return $this;
  }

  public function get_file_name() {
    return $this->file_name_;
  }

  public function set_file_name($file_name) {
    $this->file_name_ = $file_name;
    return $this;
  }
  
  public function get_file_ext() {
    return $this->file_ext_;
  }
  
  public function set_file_ext($file_ext) {
    $this->file_ext_ = $file_ext;
    return $this;
  }  

  public function get_file_path() {
    return $this->file_path_;
  }

  public function set_file_path($file_path) {
    $this->file_path_ = $file_path;
    return $this;
  }

  public function validateNewPhotoData() {
    return $this->file_ext_ !== NOT_SET &&
           $this->binary_ !== NOT_SET;
  }

  public function set_create_time($create_time) {
    $this->create_time_ = $create_time;
    return $this;
  }
  
  public function get_create_time() {
    return $this->create_time_;
  }
  
  public function get_photo_info() {
    return 'compressed';
  }
  
  public function set_photo_info() {
    return $this;
  }  
  
  public function load_array($photo_entry, $blacklist, $whitelist = false) {
    
    $fields = array('sid', 'file_path', 'file_name', 'file_ext',
                    'author', 'binary', 'photo_info',
                    'create_time');
    
    foreach ($fields as $field) {
      $lock = !in_array($field, $blacklist) ^ $whitelist;
      if ($lock && isset($photo_entry[$field])) {
        $setter = 'set_'.$field;
        $this->$setter($photo_entry[$field]);
      }
    }
    return $this;
  }

  public function to_array($compressed,
                           $filter_null,
                           $blacklist, 
                           $whitelist = false) {
    if ($compressed) {
      $fields = array('sid', 'file_path', 'file_name', 'file_ext',
                      'author', 'binary', 'photo_info');
    } else {
      $fields = array('sid', 'file_path', 'file_name', 'file_ext',
                      'author', 'binary', 'create_time');
    }
    
    foreach ($fields as $field) {
      $lock = !in_array($field, $blacklist) ^ $whitelist;
      $getter = 'get_'.$field;
      if ($lock && (!$filter_null || $this->$getter() !== NOT_SET)) {
        $photo_entry[$field] = $this->$getter();
      }
    }
    
    return $photo_entry;
  }
}