<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/device.php';

class Simulate_android {
  function __construct() {
  }
  
  public function set_xmlrpc($xmlrpc) {
    $this->xmlrpc_ = $xmlrpc;
    return $this;
  }
  
  public function send_rpc($action, $content, $type) {
    
    $rpcs = array('user_rpc', 'photo_rpc');
    
    if (!in_array($type, $rpcs)) {
      return 'Failed: unsupported rpc type.';
    }
    
    $this->xmlrpc_->server(site_url('rpcs/'.$type), 10088);
    $this->xmlrpc_->method($action);
    
    //======================================================
    //    Uncomment this for debugging use.
    
    $this->xmlrpc_->set_debug(TRUE);
    
    //
    //======================================================
    
    /*
     * Don't change this. It is not easy to go right.
     */
    $request = array(
                  array(
                    // Param 0
                    array(
                          'UDID'=>'simu_android',
                          'KEY'=>'semi-god-semi-devil-v0.1-acdjiac5tq-simulate-android',
                          'CONTENT'=> array($content, 'struct')
                         ),
                   'struct'
    ));
      
    $this->xmlrpc_->request($request);
    if ( ! $this->xmlrpc_->send_request()) {
      return $this->xmlrpc_->display_error();
    }
    else {
      return $this->xmlrpc_->display_response();
    }
  }
  
  private $xmlrpc_;
}


/* End of file simulate_android.php */
/* Location: ./application/libraries/device/simulate_android.php */