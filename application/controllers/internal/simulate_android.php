<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simulate_android extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function create() {
    $this->load->helper('url');
    $server_url = site_url('rpcs/user_rpc');
    echo 'Submit to '.$server_url . '<br/>';
    
    $this->load->library('xmlrpc');
    
    $this->xmlrpc->server($server_url, 10088);
    $this->xmlrpc->method('create_user');
    //$this->xmlrpc->set_debug(TRUE);
    
    $content = array(
                     'nickname'=> $this->input->post('nickname'),
                     'password'=> $this->input->post('password'),
                     'email_addr'=> $this->input->post('email_addr')
               );
    
    $request = array(
                  array(
                    // Param 0
                    array(
                          'UDID'=>'simu_android',
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


/* End of file simulate_android.php */
/* Location: ./application/controllers/internal/simulate_android.php */