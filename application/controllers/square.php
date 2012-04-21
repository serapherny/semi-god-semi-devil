<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once CONTROLLER.'base/abstract_page.php';
require_once LIB.'ui/clear.php';
require_once LIB.'ui/waterfall/post_box.php';

class Square extends AbstractPage {

  protected function renderPostBrick($post_id) {
    return <ui:post_box post_id={$post_id}/>;
  }

  protected function getContent() {
    $container = <div id="waterfall"/>;
    $this->load->model('ent/post_model', 'post_model');
    $post_ents = $this->post_model->get_id_list();
    foreach ($post_ents as $post_ent) {
      $container->appendChild($this->renderPostBrick($post_ent->get('sid')));
    }
    return $container;
  }

  protected function pageSetting() {
    set_page_title('广场');
    require_static('waterfall/waterfall.css');
    require_js(JQUERY);
    require_js('waterfall/jquery.masonry.min.js');
    require_js('waterfall/waterfall.js');
  }
}