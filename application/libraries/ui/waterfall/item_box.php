<?php
class :ui:item_box extends :x:element {
  attribute
    int width = 200,
    int height = 200;
  
  public function render() {
    $style = "height:".$this->getAttribute('height')."px;";
    return 
      <div class="item-box" style={$style}></div>;
  }
}
