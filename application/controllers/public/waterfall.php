<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/item.php';

class Waterfall extends CI_Controller {
  const MAX_WIDTH = 1000;
  const COLUMN = 5;
  
  function __construct() {
    parent::__construct();
  }

  protected function renderItemBlock($id) {
    return $this->load->view('ent/item_box',
                              array('debug_height' => rand(200,600)),
                              true);
  }
  
  protected function renderSingleFall($fall_width) {
    $items = array();
    for ($i=0; $i<50; $i++) {
      $items[] = $this->renderItemBlock($i);
    }
    return $this->load->view('waterfall/single_fall', 
                              array('fall_width' => $fall_width,
                                    'content' => $items),
                              true);
  }
  
  protected function renderMainPanel($max_width, $column) {
    $falls = array();
    $single_width = round($max_width / $column);
    for($width = 0; $width < $max_width; $width += $single_width) {
      $falls[] = $this->renderSingleFall($single_width, '', true);
    }
    $falls[] = $this->load->view('base/clear','',true);
    $this->load->view('base/div', array('content'=> $falls,
                                        'class' => 'waterfall'));
  }
  
  protected function renderUserProfile() {
    //$this->load->view('waterfall/user_profile');
  }
  
  protected function renderTopMenu() {
    $filter = $this->load->view('widget/product_filter', '', true);
    $filter = array($filter, $this->load->view('base/clear','',true));
    $this->load->view('base/div', array('content'=> $filter,
                                        'class' => 'top-menu-bar'));
  }
  
  public function index() {
    $data = array('css_files' => array('css/style.css', 
                                       'waterfall/waterfall.css'),
                  'page_title'=>'广场');
    $this->load->view('header', $data);
    $this->renderUserProfile();
    $this->renderTopMenu();
    $this->renderMainPanel(self::MAX_WIDTH, self::COLUMN);
    $this->load->view('footer');
  }
}