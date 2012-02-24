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

  public static function  IsValidPollType($index) {
    return ($index > 0 && $index < 3);
  }
}

class Poll extends Ent {

  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntPoll);
  }

  public function BaseFieldsArray() {
    return array_merge(
      array('photo_1',
            'photo_2',
            'author',
            'poll_type',
            'latest_comment',
            'description',
            'create_time',
            'visibility',
            'poll_info'),
      parent::BaseFieldsArray()
    );
  }

  public function ZipFieldsArray($zipped) {
    if ($zipped){
      $fields = array('commentlist');
    } else {
      $fields = array('comments');
    }
    return $fields;
  }


  //=====================================================================
  // The followings are special member functions.
  //=====================================================================

  public function validateNewpollData() {
    return true;
  }

  public function set_poll_type($poll_type) {
    if (PollType::IsValidPollType($poll_type)) {
      $this->poll_type_ = $poll_type;
    } else {
      log_message('warning', 'Setting invalid type.');
    }
    return $this;
  }
}