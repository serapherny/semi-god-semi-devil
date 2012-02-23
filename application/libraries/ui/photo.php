<?php

require_once LIB.'ent/photo.php';

class :ui:photo extends :x:element {
  attribute
    string photo_id @required;

  protected function prepare() {
    $photo_id = $this->getAttribute('photo_id');
    $photo_model = $this->loader->model('ent/photo_model');
    if ($photo_model->get_photo_data($photo_id, &$response) == 'suc') {
      $this->photo = new Photo();
      $this->photo->load_array($response[0], array());
    }
  }

  public function render() {
    $photo = $this->photo;
    return <a href={$photo->addr()}>
             <img src={$photo->addr()} width={rand(300,600)} />
           </a>;
  }
}