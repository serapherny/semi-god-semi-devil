<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Item extends Ent {
  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntItem);
  }

  public function BaseFieldsArray() {
    return array_merge(
    array('author',
          'create_time',
          'item_info'),
    parent::BaseFieldsArray()
    );
  }

  public function ZipFieldsArray($zipped) {
    if ($zipped){
      $fields = array('photolist', 'taglist');
    } else {
      $fields = array('photos', 'tags');
    }
    return $fields;
  }
}