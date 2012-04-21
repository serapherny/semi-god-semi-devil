<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/item.php';
require_once MODEL.'ent/ent_model.php';

class Item_model extends Ent_model {

  public function __construct() {
    parent::__construct();
  }

  protected function tableName() {
    return 'item';
  }

  protected function typeName() {
    return 'Item';
  }
}

/* End of file item_model.php */
/* Location: ./application/models/ent/item_model.php */