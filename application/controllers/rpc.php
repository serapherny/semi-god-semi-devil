<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpc extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function index() {
    
    $this->load->library('xmlrpc');
    $this->load->library('xmlrpcs');
    
    $config['object'] = $this;
    
    $this->xmlrpcs->initialize($config);
    $this->xmlrpcs->serve();
    
  }
}

/* End of file rpc.php */
/* Location: ./application/controllers/rpc.php */



