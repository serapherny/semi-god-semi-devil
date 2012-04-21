<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'base/ent.php';

class Post extends Ent {
  public function __construct() {
    parent::__construct();
    $this->set_type(EntType::EntPost);
  }

  public function BaseFieldsArray() {
    return array_merge(
      array('author',
          	'create_time',
          	'content_type',
          	'content',
          	'post_info'),
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
}