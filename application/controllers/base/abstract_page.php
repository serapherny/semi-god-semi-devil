<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ui/base_page/footer.php';
require_once LIB.'ui/base_page/side_bar.php';
require_once LIB.'ui/base_page/head_bar.php';
require_once LIB.'ui/base_page/filter_bar.php';
require_once LIB.'ui/base_page/page_skeleton.php';

class AbstractPage extends CI_Controller {

  public function __construct() {
    parent::__construct();
  }

  protected function getHeader() {
    $header = <head />;

    $header->appendChild(
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    );

    $header->appendChild(
      <title>{get_page_title()}</title>
    );

    $css_file_list = get_css_file_list();
    foreach($css_file_list as $css_file) {
      if (substr($css_file, 0, 7) != 'http://') {
        $path = CSS.$css_file;
      } else {
        $path = $css_file;
      }
      $header->appendChild(
        <link href={$path} media="screen" rel="stylesheet" type="text/css" />
      );
    }

    $js_file_list = get_js_file_list();
    foreach($js_file_list as $js_file) {
    if (substr($js_file, 0, 7) != 'http://') {
        $path = JS.$js_file;
      } else {
        $path = $js_file;
      }
      $header->appendChild(<script src={$path} type="text/javascript" />);
    }
    return $header;
  }

  protected function getSideBar() {
    return <ui:side_bar />;
  }

  protected function getFooter() {
    return <ui:footer />;
  }

  protected function getHeadBar() {
    return <ui:head_bar />;
  }

  protected function getFilterBar() {
    return <ui:filter_bar />;
  }

  protected function getContent() {
    return <div>content</div>;
  }

  protected function pageSetting() { }

  public function index() {
    $this->pageSetting();
    echo <ui:page_skeleton>
            {$this->getHeader()}
            {$this->getSideBar()}
            {$this->getHeadBar()}
            {$this->getFilterBar()}
            {$this->getContent()}
            {$this->getFooter()}
          </ui:page_skeleton>;
  }
}
