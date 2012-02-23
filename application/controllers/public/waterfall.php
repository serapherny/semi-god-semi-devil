<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/item.php';
require_once LIB.'ui/clear.php';
require_once LIB.'ui/user_profile.php';
require_once LIB.'ui/waterfall/item_box.php';

class Waterfall extends CI_Controller {
  const MAX_WIDTH = 1000;
  const COLUMN = 4;
  const USER_PROFILE_WIDTH = 180;

  function __construct() {
    parent::__construct();
  }

  protected function renderItemBlock($id) {
    return <ui:item_box />;
  }

  protected function renderSingleFall($fall_width) {
    $style = "width:".$fall_width."px;";
    $container = <div class="single-fall" style={$style} />;
    for ($i=0; $i<5; $i++) {
      $container->appendChild($this->renderItemBlock($i));
    }
    return $container;
  }

  protected function renderWaterfall($max_width, $column) {
    if ($column < 1) return <div />;
    $container = <div class="waterfall"/>;
    $single_width = round($max_width / $column);
    for($width = 0; $width < $max_width; $width += $single_width + 1) {
      $container->appendChild($this->renderSingleFall($single_width));
    }
    $container->appendChild(<ui:clear/>);
    return $container;
  }

  protected function renderLeftColumn($loggin) {
    if ($loggin) {
      return <ui:user_profile />;
    }
  }

  protected function renderRightColumn($loggin) {
    $container = <div />;
    $top_filter = <div class="top-menu-bar">
                    <div class="mainmenu-filter">
                      <a href="#">Filter by category</a>
                    </div>
                  </div>;

    $container->appendChild($top_filter);

    if ($loggin) {
      $container->addClass('with_user_profile');
      $container->appendChild($this->renderWaterfall(
          self::MAX_WIDTH - self::USER_PROFILE_WIDTH, self::COLUMN - 1));
    } else {
      $container->appendChild($this->renderWaterfall(
          self::MAX_WIDTH, self::COLUMN));
    }
    return $container;
  }

  public function index() {
    $data = array('css_files' => array('css/style.css',
                                       'waterfall/waterfall.css'),
                  'page_title'=>'广场');
    echo $this->load->view('header', $data, true);
    $loggin = true;
    $left_col = $this->renderLeftColumn($loggin);
    $right_col = $this->renderRightColumn($loggin);
    echo <div>{$left_col}{$right_col}</div>;
    echo $this->load->view('footer', array(), true);
  }
}