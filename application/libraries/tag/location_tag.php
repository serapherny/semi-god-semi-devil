<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/tag.php';

class Location_tag extends Tag {
  
  private
    $creater_ = NOT_SET,
    $location_ = NOT_SET;
  
  public function __construct() {
    parent::__construct();
  }
}