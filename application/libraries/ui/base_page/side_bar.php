<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ui/user_profile.php';

class :ui:side_bar extends :x:element {
  protected function account_menu() {
    return <div class="account_menu">
             <div class="account_menu_item">item1</div>
             <div class="account_menu_item">item2</div>
           </div>;
  }

  protected function main_menu() {
    return <ul class="main_menu">
             <li>menu1</li>
             <li>menu2</li>
             <li>menu3</li>
             <li>menu4</li>
             <li>menu5</li>
           </ul>;
  }

  public function render() {
    return <div>
             {$this->account_menu()}
             <ui:user_profile />
             {$this->main_menu()}
           </div>;
  }
}