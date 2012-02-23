<?php

require_once LIB.'ui/photo.php';

class :ui:photo_info extends :ui:photo {
  public function render() {
    $photo = $this->photo;
    return <div>
            {$photo->get_sid()},
            {$photo->get_file_path()},
            {$photo->get_file_name()},
            {$photo->get_file_ext()},
            {$photo->get_author()},
            <a href={$photo->addr()}>IMG</a>,
            {$photo->get_create_time()},
            {$photo->get_photo_info()}
           </div>;
  }
}