<?php

class :ui:notice extends :x:element {
  attribute
    enum {'error', 'warn', 'info'} type @required;

  children any;

  public function render() {
    $type = $this->getAttribute('type');
    $root = <div />;
    $root->appendChild($this->getChildren());
    if ($type == 'error') {
      $root->addClass('color_red');
    } else {
      $root->addClass('color_olive');
    }
    return $root;
  }
}