<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('NOT_SET', '');

class EntType {
  const EntUser = 1;
  const EntPhoto = 2;
  const EntPoll = 3;
  const EntComment = 4;
  const EntTag = 5;
  const EntDevice = 6;
  const EntItem = 7;
  const EntPost = 8;

  const NextEnt = 9;

  public static function GetEntType($index) {
    $mapper = array(
      1 => 'EntUser',
      2 => 'EntPhoto',
      3 => 'EntPoll',
      4 => 'EntComment',
      5 => 'EntTag',
      6 => 'EntDevice',
      7 => 'EntItem',
      8 => 'EntPost'
    );
    if ($index >= len($mapper) && $index > 0) {
      return 'NOT_VALID';
    } else {
      return $mapper($index);
    }
  }

  public static function  IsValidEntType($index) {
    return ($index > 0 && $index < self::NextEnt);
  }
}

class Ent {

  // very basic member of Ent.

  private $sid_ = NOT_SET;
  protected $ent_type_ = NOT_SET; // class EntType

  // the constructer should populate the properties of itself.
  public function __construct() {
    $fields = $this->AllFieldsArray();
    foreach ($fields as $field) {
      $member = $field.'_';
      $this->$member = NOT_SET;
    }
  }

  // populating the properies:

  public function BaseFieldsArray() {
    return array('sid');
  }

  public function ZipFieldsArray($zipped) {
    return array();
  }

  public function AllFieldsArray() {
    return array_merge($this->BaseFieldsArray(),
           array_merge($this->ZipFieldsArray(true),
                       $this->ZipFieldsArray(false)));
  }

  //=====================================================================
  // Defining the reflection based general member functions.
  //=====================================================================

  // zip related:

  protected $zip_status_ = NOT_SET;

  public function zip() {
    if ($this->zip_status_ != 'zipped') {
      $match = array_combine($this->ZipFieldsArray(false),
                             $this->ZipFieldsArray(true));

      foreach ($match as $from => $to) {
        $the_array = $this->get($from);
        if (is_array($the_array)) {
          $this->set($to, implode(',', $the_array));
        }
      }
      $this->zip_status_ = 'zipped';
    }
  }

  public function unzip() {
    if ($this->zip_status_ != 'unzipped') {
      $match = array_combine($this->ZipFieldsArray(true),
                             $this->ZipFieldsArray(false));

      foreach ($match as $from => $to) {
        $the_string = $this->get($from);
        if ($the_string != NOT_SET) {
          $this->set($to, explode(',', $the_string));
        }
      }
      $this->zip_status_ = 'unzipped';
    }
  }

  // general getter and setter:
  //   using reflection system to get/set directly unless there are specially
  //   defined function for the behavior.

  public function get($field) {
    $getter = $field;
    $member = $field.'_';
    $reflector = new ReflectionClass(get_class($this));

    if ($reflector->hasMethod($getter)) {
      return $this->$getter();
    } else {
      return $this->$member;
    }
  }

  public function set($field, $value) {
    $setter = 'set_'.$field;
    $member = $field.'_';
    $reflector = new ReflectionClass(get_class($this));

    if ($reflector->hasMethod($setter)) {
      $this->$setter($value);
    } else {
      $this->$member = $value;
    }
    return $this;
  }

  // bulk input and output:

  public function load_array($the_array, $the_list = array(),
                             $mode = 'blacklist') {
    $whitelist = ($mode === 'whitelist');
    $fields = $this->AllFieldsArray();

    foreach ($fields as $field) {
      $lock = !in_array($field, $the_list) ^ $whitelist;
      if ($lock && isset($the_array[$field])) {
        $this->set($field, $the_array[$field]);
      }
    }
    return $this;
  }

  public function to_array($zipped, $the_list = array(), $mode = 'blacklist',
                           $check_empty = true) {
    $whitelist = ($mode === 'whitelist');
    $fields = $this->BaseFieldsArray();
    $fields = array_merge($fields, $this->ZipFieldsArray($zipped));

    foreach ($fields as $field) {
      $lock = !in_array($field, $the_list) ^ $whitelist;
      $value = $this->get($field);
      if ($lock && (!$check_empty || $value !== NOT_SET)) {
        $the_array[$field] = $value;
      }
    }
    return $the_array;
  }

  //=====================================================================
  // The followings are special member functions.
  //=====================================================================

  public function new_sid() {
    $this->sid_ = random_string('numeric', 16);
    return $this;
  }

  public function set_type($type) {
    if (EntType::IsValidEntType($type)) {
      $this->ent_type_ = $type;
    } else {
      log_message('warning', 'Setting invalid ent type.');
    }
    return $this;
  }

}