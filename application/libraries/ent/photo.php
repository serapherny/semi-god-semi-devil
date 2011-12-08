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

  public function BaseFieldsArray() {
    return array_merge(
      array('file_path', 
            'file_name', 
            'file_ext',
            'author', 
            'binary'),
      parent::BaseFieldsArray()
    );
  }
  
  public function CompressableFieldsArray($compressed) {
    if ($compressed){
      $fields = array('photo_info');
    } else {
      $fields = array('create_time');
    }
    return array_merge($fields, parent::BaseFieldsArray());
  }  
  
  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntPhoto);
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
}