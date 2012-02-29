<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Tag extends Ent {
  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntTag);
  }

  public function BaseFieldsArray() {
    return array_merge(
      array('author',
          	'create_time',
          	'title',
          	'subtitle'),
    parent::BaseFieldsArray()
    );
  }

  public function ZipFieldsArray($zipped) {
    if ($zipped){
      $fields = array('subtaglist');
    } else {
      $fields = array('subtags');
    }
    return $fields;
  }
}