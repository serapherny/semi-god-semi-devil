<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';
require_once LIB.'ent/device.php';

class User extends Ent {

  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntUser);
  }

  public function BaseFieldsArray() {
    return array_merge(
      array('nickname',
            'email_addr',
            'password',
            'last_login_time',
            'create_time',
            'user_info'),
      parent::BaseFieldsArray()
    );
  }

  public function ZipFieldsArray($zipped) {
    if ($zipped){
      $fields = array('friendlist');
    } else {
      $fields = array('friends');
    }
    return $fields;
  }

  //=====================================================================
  // The followings are special member functions.
  //=====================================================================

  public function validateBasicDataForNewUser() {
    return $this->nickname_ !== NOT_SET &&
           $this->password_ !== NOT_SET;
  }

  private function validateUserIdentifiable() {
    return $this->email_addr_ !== NOT_SET ||
           $this->get_sid() !== NOT_SET;
  }

  private function validateLoginable() {
    return $this->validateUserIdentifiable() &&
           $this->password_ !== NOT_SET;
  }

}