<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class :ui:filter_bar extends :x:element {

  protected function dense_mode_buttons() {
    return <div class="button_group">
             <div class="dense_mode_button">b1</div>
             <div class="dense_mode_button">b2</div>
             <div class="filter_button">all category</div>
           </div>;
  }

  protected function recommend_buttons() {
    return <div class="button_group">
             <div class="filter_button">recommend</div>
             <div class="filter_button">featured</div>
           </div>;
  }

  protected function author_buttons() {
    return <div class="button_group">
             <div class="filter_button">My Posted</div>
             <div class="filter_button">Friend's Posted</div>
           </div>;
  }

  public function render() {
    return <div class="filter_bar">
             {$this->dense_mode_buttons()}
             {$this->recommend_buttons()}
             {$this->author_buttons()}
           </div>;
  }
}