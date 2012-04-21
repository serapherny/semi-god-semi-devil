<?php

class :ui:clear extends :x:element {
  attribute
    enum {"both", "left", "right"} side = "both";

  public function render() {
    $side = $this->getAttribute('side');
    if ($side == 'left') {
      return <div class="clr_l" />;
    } else if ($side == 'right') {
      return <div class="clr_r" />;
    } else {
      return <div class="clr" />;
    }
  }
}