<?php

require_once LIB.'ui/waterfall/item_box.php';

class :ui:post_box extends :x:element {
  attribute
    string post_id @required;

  protected function prepare() {
    $post_id = $this->getAttribute('post_id');
    $post_model = $this->loader->model('ent/post_model');
    $post_ents = $post_model->get_ents($post_id);
    $this->post = $post_ents[$post_id];
  }

  public function render() {
    $item_id = $this->post->get('content');
    return
      <div class="post-box">
        <ui:item_box item_id={$item_id} />
        <div class="post-words">
          Some words in this post.
        </div>
      </div>;
  }
}
