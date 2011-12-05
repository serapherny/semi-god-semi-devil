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
  
  public function compress_photo_info() {
    return 'compressed';
  }
  
  public function decompress_photo_info() {
    return $this;
  }  
  
  public function load_array($photo_entry, $blacklist, $whitelist = false) {
    
    $fields = array('sid', 'file_path', 'file_name', 'file_ext',
                    'author', 'binary', 'photo_info',
                    'create_time');
    
    foreach ($fields as $field) {
      $lock[$field] = !in_array($field, $blacklist) ^ $whitelist;
    }
    
    if ($lock['sid'] && isset($photo_entry['sid'])) {
      $this->set_sid($photo_entry['sid']);
    }

    if ($lock['file_path'] && isset($photo_entry['file_path'])) {
      $this->set_file_path($photo_entry['file_path']);
    }

    if ($lock['file_name'] && isset($photo_entry['file_name'])) {
      $this->set_file_name($photo_entry['file_name']);
    }
    
    if ($lock['file_ext'] && isset($photo_entry['file_ext'])) {
      $this->set_file_ext($photo_entry['file_ext']);
    }

    if ($lock['author'] && isset($photo_entry['author'])) {
      $this->set_author($photo_entry['author']);
    }
    
    if ($lock['binary'] && isset($photo_entry['binary'])) {
      $this->set_binary($photo_entry['binary']);
    }

    if ($lock['photo_info'] && isset($photo_entry['photo_info'])) {
      $this->decompress_photo_info($photo_entry['photo_info']);
    }

    if ($lock['create_time'] && isset($photo_entry['create_time'])) {
      $this->set_create_time($photo_entry['create_time']);
    }

    return $this;
  }

  public function to_array($compressed,
                           $filter_null,
                           $blacklist) {
    
    $fields = array('sid', 'file_path', 'file_name', 'file_ext',
                    'author', 'binary', 'photo_info',
                    'create_time');
    
    foreach ($fields as $field) {
      $lock[$field] = !in_array($field, $blacklist) ^ $whitelist;
    }
    
    $photo_entry = array();

    if ($lock['sid'] && (!$filter_null || $this->get_sid() !== NOT_SET)) {
      $photo_entry['sid'] = $this->get_sid();
    }

    if ($lock['file_path'] && (!$filter_null || $this->get_file_path() !== NOT_SET)) {
      $photo_entry['file_path'] = $this->get_file_path();
    }

    if ($lock['file_name'] && (!$filter_null || $this->get_file_name() !== NOT_SET)) {
      $photo_entry['file_name'] = $this->get_file_name();
    }
    
    if ($lock['file_ext'] && (!$filter_null || $this->get_file_ext() !== NOT_SET)) {
      $photo_entry['file_ext'] = $this->get_file_ext();
    }

    if ($lock['author'] && (!$filter_null || $this->get_author() !== NOT_SET)) {
      $photo_entry['author'] = $this->get_author();
    }

    if ($compressed) {
      if ($lock['photo_info']) {
        $photo_entry['photo_info'] = $this->compress_photo_info();

        if ($filter_null && $photo_entry['photo_info'] === NOT_SET) {
          unset($photo_entry['photo_info']);
        }
      }
    } else {
      if ($lock['create_time'] && (!$filter_null || $this->get_create_time() !== NOT_SET)) {
        $photo_entry['create_time'] = $this->get_create_time();
      }
    }

    return $photo_entry;
  }
}