<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_user_rpc extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function index() {
    $this->load->helper('url');
    $server_url = site_url('rpcs/user_rpc');
    echo $server_url . '<br/>';
    
    $this->load->library('xmlrpc');
    
    $this->xmlrpc->server($server_url, 10088);
    $this->xmlrpc->method('create_user');
    //$this->xmlrpc->set_debug(TRUE);
    
    $content = array(
                     'nickname'=>'chao2',
                     'password'=>'chao2',
                     'email_addr'=>'chao2@gmail.com'
               );
    
    $request = array(
                  array(
                    // Param 0
                    array(
                          'UDID'=>'1234567',
                          'KEY'=>'semi-god-semi-devil-v0.1-acdjiac5tq-android',
                          'CONTENT'=> array($content, 'struct')
                         ),
                   'struct'
    ));
      
    $this->xmlrpc->request($request);
    if ( ! $this->xmlrpc->send_request()) {
      echo $this->xmlrpc->display_error();
    }
    else {
      echo '<pre>';
      print_r($this->xmlrpc->display_response());
      echo '</pre>';
    }
  }
}


/* End of file test_user_rpc.php */
/* Location: ./application/controllers/test/test_user_rpc.php */