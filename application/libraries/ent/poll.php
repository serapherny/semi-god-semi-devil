<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

/*
 * Enumerating all possible poll types.
 */
class PollType {
  const SINGLE_POLL = 1;
  const DUAL_POLL = 2;
  
  public static function GetPollType($index) {
    $mapper = array(
      1 => 'SINGLE_POLL',
      2 => 'DUAL_POLL'
    );
    if ($index >= len($mapper) && $index > 0) {
      return 'NOT_VALID';
    } else {
      return $mapper($index);
    }
  }
}

class Poll extends Ent {
  
  private
    $author_ = NOT_SET,
    $poll_type_ = NOT_SET,
    $vilibility_ = NOT_SET,
    $create_time_ = NOT_SET,
    $latest_comment_ = NOT_SET,
    $photo_1_ = NOT_SET,
    $photo_2_ = NOT_SET,
    $description_ = NOT_SET,
    $poll_info_ = NOT_SET;
  
  public function BaseFieldsArray() {
    return array_merge(
      array('photo_1', 
            'photo_2',
            'author',
            'poll_type', 
            'last_comment',
            'description',
            'create_time'),
      parent::BaseFieldsArray()
    );
  }
  
  public function CompressableFieldsArray($compressed) {
    if ($compressed){
      $fields = array('poll_info');
    } else {
      $fields = array();
    }
    return array_merge($fields, parent::BaseFieldsArray());
  }
  
  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntPoll);
  }
  
  public function validateNewpollData() {
    return true;
  }
  
  public function get_poll_info() {
    return 'compressed';
  }
  
  public function set_poll_info() {
    return $this;
  }
  
  public function get_last_comment() {
    return $this->latest_comment_;
  }
    
  public function set_last_comment($last_comment) {
    $this->latest_comment_ = $last_comment;
    return $this;
  }

  public function get_author() {
    return $this->author_;
  }
  
  public function set_author($author) {
    $this->author_ = $author;
    return $this;
  }
  
  public function get_photo_1() {
    return $this->photo_1_;
  }
  
  public function set_photo_1($photo) {
    $this->photo_1_ = $photo;
    return $this;
  }
  
  public function get_photo_2() {
    return $this->photo_2_;
  }
  
  public function set_photo_2($photo) {
    $this->photo_2_ = $photo;
    return $this;
  }
  
  public function get_description() {
    return $this->description_;
  }
  
  public function set_description($description) {
    $this->description_ = $description;
    return $this;
  }
  
  public function set_create_time($create_time) {
    $this->create_time_ = $create_time;
    return $this;
  }
  
  public function get_create_time() {
    return $this->create_time_;
  }
  
  public function get_poll_type() {
    return $this->poll_type_;
  }
  
  public function set_poll_type($poll_type) {
    if ($poll_type instanceof PollType) {
      $this->poll_type_ = $poll_type;
    } else {
      log_message('warning', 'Setting invalid type.');
    }
    return $this;
  }
}