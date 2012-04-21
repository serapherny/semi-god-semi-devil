<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class :ui:head_bar extends :x:element {
  protected function menu_buttons() {
    return <ul class="top-menu-bar">
             <li>About US</li>
             <li>Add Review</li>
           </ul>;
  }
  public function render() {
    return <div class="head_bar">
             <div class="logo">logo</div>
             <div class="search_box">this is the search box.</div>
             {$this->menu_buttons()}
           </div>;
  }
}