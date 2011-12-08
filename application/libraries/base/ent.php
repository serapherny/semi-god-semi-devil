<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('NOT_SET', '');

class EntType {
  const EntUser = 1;
  const EntPhoto = 2;
  const EntPoll = 3;
  const EntComment = 4;
  const EntTag = 5;
  const EntDevice = 6;
  
  public static function GetEntType($index) {
    $mapper = array(
      1 => 'EntUser',
      2 => 'EntPhoto',
      3 => 'EntPoll',
      4 => 'EntComment',
      5 => 'EntTag',
      6 => 'EntDevice'
    );
    if ($index >= len($mapper) && $index > 0) {
      return 'NOT_VALID';
    } else {
      return $mapper($index);
    }
  }
}

class Ent {
  
  private $sid_ = NOT_SET;
  
  protected $ent_type_ = NOT_SET; // class EntType
  
  public function __construct() {
  }
  
  public function new_sid() {
    $this->sid_ = random_string('numeric', 16);
    return $this;
  }
  
  public function set_sid($sid) {
    $this->sid_ = $sid;
    return $this;
  }
  
  public function get_sid() {
    return $this->sid_;
  }
  
  public function get_type() {
    return $this->type_;
  }
  
  public function set_type($type) {
    if ($type instanceof EntType) {
      $this->ent_type_ = type;
    } else {
      log_message('warning', 'Setting invalid ent type.');
    }
    return $this;
  }
  
  public function BaseFieldsArray() {
    return array('sid');
  }
  
  public function CompressableFieldsArray($compressed) {
    return array();
  }
  
  public function AllFieldsArray() {
    return array_merge($this->BaseFieldsArray(),
        array_merge($this->CompressableFieldsArray(true),
                    $this->CompressableFieldsArray(false)));
  }
  
  public function load_array($entry, $blacklist, $whitelist = false) {
  
    $fields = $this->AllFieldsArray();
  
    foreach ($fields as $field) {
      $lock = !in_array($field, $blacklist) ^ $whitelist;
      if ($lock && isset($entry[$field])) {
        $setter = 'set_'.$field;
        $this->$setter($entry[$field]);
      }
    }
    return $this;
  }
  
  public function to_array($compressed,
                           $filter_null,
                           $blacklist,
                           $whitelist = false) {
    $fields = $this->BaseFieldsArray();
    $fields = array_merge($fields, 
        $this->CompressableFieldsArray($compressed));
  
    foreach ($fields as $field) {
      $lock = !in_array($field, $blacklist) ^ $whitelist;
      $getter = 'get_'.$field;
      if ($lock && (!$filter_null || $this->$getter() !== NOT_SET)) {
        $entry[$field] = $this->$getter();
      }
    }
    return $entry;
  }
}