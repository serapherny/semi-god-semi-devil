<?php

require_once LIB.'ui/photobox.php';

class :ui:item_box extends :x:element {
  attribute
    int width = 200,
    int height = 200;
  
  public function render() {
    $style = "height:".$this->getAttribute('height')."px;";
    return 
      <div class="item-box" style={$style}>
        <ui:photobox photo_id={"8647241256616250"} />
      </div>;
  }
}
