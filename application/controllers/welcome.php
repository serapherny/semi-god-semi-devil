<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once CONTROLLER.'base/abstract_page.php';

class Welcome extends AbstractPage {
  public function index() {
    set_page_title('Welcome');
    echo <ui:page_skeleton>
          {$this->getHeader()}
          {$this->getContent()}
        </ui:page_skeleton>;
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */