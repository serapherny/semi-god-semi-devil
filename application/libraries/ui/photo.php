<?php

require_once LIB.'ent/photo.php';

class :ui:photo extends :x:element {
  attribute
    string photo_id @required;

  protected function prepare() {
    $photo_id = $this->getAttribute('photo_id');
    $photo_model = $this->loader->model('ent/photo_model');
    $photo_ents = $photo_model->get_ents($photo_id);
    $this->photo = $photo_ents[$photo_id];
  }

  public function render() {
    $photo = $this->photo;
    return <a href={$photo->addr()}>
             <img src={$photo->addr()} width={rand(300,600)} />
           </a>;
  }
}