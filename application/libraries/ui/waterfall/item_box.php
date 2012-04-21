<?php

require_once LIB.'ui/photo.php';

class :ui:item_box extends :x:element {
  attribute
    string item_id @required;

  protected function prepare() {
    $item_id = $this->getAttribute('item_id');
    $item_model = $this->loader->model('ent/item_model');
    $item_ents = $item_model->get_ents($item_id);
    $this->item = $item_ents[$item_id];
  }

  public function render() {
    //print_r($this->item);
    $photo_ids = $this->item->get('photos');
    $container = <div class="item-box" />;
    foreach ($photo_ids as $photo_id) {
      $container->appendChild(<ui:photo photo_id={$photo_id} />);
    }
    return $container;
  }
}
