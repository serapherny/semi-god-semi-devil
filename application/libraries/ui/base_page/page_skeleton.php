<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ui/base_page/footer.php';
require_once LIB.'ui/base_page/side_bar.php';
require_once LIB.'ui/base_page/head_bar.php';
require_once LIB.'ui/base_page/filter_bar.php';

class :ui:page_skeleton extends :x:element {
  children (:head, :ui:side_bar?,
            :ui:head_bar?, :ui:filter_bar?,
            :div, :ui:footer?);

  public function render() {
    return <html>
             {$this->getChildren('head')}
             <body>
               <div id="mainarea">
                 <div id="main_sidebar">
                   {$this->getChildren('ui:side_bar')}
                 </div>
                 <div id="main_page">
                   {$this->getChildren('ui:head_bar')}
                   {$this->getChildren('ui:filter_bar')}
                   {$this->getChildren('div')}
                   {$this->getChildren('ui:footer')}
                 </div>
               </div>
             </body>
           </html>;
  }
}