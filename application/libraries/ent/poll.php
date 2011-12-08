<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

/*
 * Enumerating all possible poll types.
 */
class PollType {
  const SINGLE_POLL = 1;
  const DUAL_POLL = 2;
}

class Poll extends Ent {
  
  private
    $author_ = NOT_SET, // class User
    $type_ = NOT_SET, // class PoolType
    $cansee_ = NOT_SET,
    $create_time_ = NOT_SET,
    $last_comment_ = NOT_SET, // class Post
    $poll_photo_1_ = NOT_SET, // class Photo
    $poll_photo_2_ = NOT_SET, // class Photo
    $poll_info_ = NOT_SET;
  
  public function __construct() {
    parent::__construct();
  }
  
  public function get_poll_info() {
    return 'compressed';
  }
  
  public function set_poll_info() {
    return $this;
  }
  
  public function get_last_comment() {
    return $this->last_comment_;
  }
    
  public function set_last_comment($last_comment) {
    if ($last_comment instanceof Post) {
      $this->last_comment_ = $last_comment;
    } else {
      log_message('warning', 'Setting last comment with non-Post object.');
    }
    return $this;
  }

  public function get_author() {
    return $this->author_;
  }
  
  public function set_author($author) {
    if ($author instanceof User) {
      $this->author_ = $author;
    } else {
      log_message('warning', 'Setting last comment with non-User object.');
    }
    return $this;
  }
  
  public function get_poll_photo_1() {
    return $this->poll_photo_1_;
  }
  
  public function set_poll_photo_1($photo) {
    if ($photo instanceof Photo) {
      $this->poll_photo_1_ = $photo;
    } else {
      log_message('warning', 'Setting last comment with non-Photo object.');
    }
    return $this;
  }
  
  public function get_poll_photo_2() {
    return $this->poll_photo_2_;
  }
  
  public function set_poll_photo_2($photo) {
    if ($photo instanceof Photo) {
      $this->poll_photo_2_ = $photo;
    } else {
      log_message('warning', 'Setting last comment with non-Photo object.');
    }
    return $this;
  }
  
  public function set_create_time($create_time) {
    $this->create_time_ = $create_time;
    return $this;
  }
  
  public function get_create_time() {
    return $this->create_time_;
  }
  
  public function get_type() {
    return $this->type_;
  }
  
  public function set_type($type) {
    if ($type instanceof PollType) {
      $this->type_ = $type;
    } else {
      log_message('warning', 'Setting invalid type.');
    }
    return $this;
  }
  
  public function load_array($poll_entry, $blacklist, $whitelist = false) {
  
    $fields = array('sid' => 'basic', 
                    'poll_photo_1' => 'Photo', 
                    'poll_photo_2' => 'Photo',
                    'author' => 'User',
                    'type' => 'basic', 
                    'last_comment' => 'Post',
                    'poll_info' => 'basic',
                    'create_time' => 'basic');
    
    foreach ($fields as $field => $type) {
      $lock = !isset($field, $blacklist) ^ $whitelist;
      if ($lock && isset($poll_entry[$field])) {
        $setter = 'set_'.$field;
        if ($type == 'basic') {
          $this->$setter($photo_entry[$field]);
        } else {
          $instance = new $type;
          $instance->load_array($photo_entry[$field], array(), false);
          $this->$setter($instance);
        }
      }
    }
    return $this;
  }
  
  public function to_array($compressed,
                           $filter_null,
                           $blacklist,
                           $whitelist = false) {
    if ($compressed) {
      $fields = array('sid' => 'basic', 
                      'poll_photo_1' => 'Photo', 
                      'poll_photo_2' => 'Photo',
                      'author' => 'User',
                      'type' => 'basic', 
                      'last_comment' => 'Post',
                      'poll_info' => 'basic',
                      'create_time' => 'basic');
    } else {
      $fields = array('sid' => 'basic', 
                      'poll_photo_1' => 'Photo', 
                      'poll_photo_2' => 'Photo',
                      'author' => 'User',
                      'type' => 'basic', 
                      'last_comment' => 'Post',
                      'create_time' => 'basic');
    }
    
    foreach ($fields as $field => $type) {
      $lock = !isset($field, $blacklist) ^ $whitelist;
      $getter = 'get_'.$field;
      if ($lock && (!$filter_null || $this->$getter() !== NOT_SET)) {
        if ($type == 'basic') {
          $poll_entry[$field] = $this->$getter();
        } else {
          $instance = new $type;
          $poll_entry[$field] = 
            $instance->to_array($compressed, $filter_null, array(), false);
        }
      }
    }

    return $poll_entry;
  }
}