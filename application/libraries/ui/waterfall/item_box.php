<?php

require_once LIB.'ui/photo.php';

class :ui:item_box extends :x:element {
  attribute
    string item_id @required;

  public function render() {
    return
      <div class="item-box">
        <ui:photo photo_id="3298513841439839" />
      </div>;
  }
}
