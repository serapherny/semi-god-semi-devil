<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Photo extends Ent {

  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntPhoto);
  }

  public function BaseFieldsArray() {
    return array_merge(
      array('file_path',
            'file_name',
            'file_ext',
            'author',
            'binary',
            'create_time',
            'photo_info'),
      parent::BaseFieldsArray()
    );
  }

  public function ZipFieldsArray($zipped) {
    if ($zipped){
      $fields = array('taglist', 'itemlist');
    } else {
      $fields = array('tags', 'items');
    }
    return $fields;
  }

  //=====================================================================
  // The followings are special member functions.
  //=====================================================================

  public function save_binary_to_file($filename) {
    return write_file($filename, base64_decode($this->binary_));
  }

  public function addr() {
    return '../'.$this->file_path_.'/'.
           $this->file_name_.
           $this->file_ext_;
  }

  // for validations:
  public function validateNewPhotoData() {
    return $this->file_ext_ !== NOT_SET &&
           $this->binary_ !== NOT_SET;
  }
}