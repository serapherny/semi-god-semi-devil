<?php

class :ui:clear extends :div {
  attribute
    enum {"both", "left", "right"} side = "both";
  
  public function render() {
    return <div style="clear:{side};" />;
  }
}