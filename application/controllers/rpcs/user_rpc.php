<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/user.php';
require_once LIB.'gatekeeper/gatekeeper.php';

class User_rpc extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function index() {
    $this->load->library('xmlrpc');
    $this->load->library('xmlrpcs');
  
    $config['functions']['create_user'] = array('function' => 'User_rpc.create_user');
    $config['functions']['update_user'] = array('function' => 'User_rpc.update_user');
    $config['object'] = $this;
  
    $this->xmlrpcs->initialize($config);
    $this->xmlrpcs->serve();
  }
  
  public function process($request, $cmd) {
    
    $this->load->library('xmlrpc');
    $parameters = $request->output_parameters();
    
    $action_result = 'failed : not passing gatekeeper.';
    
    $gatekeeper = new Gatekeeper();
    $content = $gatekeeper->validatePost($parameters['0']);
    // validatePost returns FALSE if not valid.
    if ($content !== FALSE) {
      $action_result = 'failed : no such command.';
      
      switch($cmd) {
        /* 
         * ==========================================================
         * RPC Call of creating a new user.
         * ==========================================================
         */
        case 'create_user': 
          
          $user = new User();
          $blacklist = array();
          $user->load_array($content, $blacklist);
          $valid = $user->validateNewUserData();

          if (!$valid) {
            $action_result = 'failed : not valid or not enough new user data.';
            break;
          }
          
          $this->load->model('ent/user_model', 'user_model');
          $action_result = $this->user_model->create_user($user);
          break;
          
        /*
         * ==========================================================
         * RPC Call of updating an existing user.
         * ==========================================================
         */
        case 'update_user':      

          $user = new User();
          // We definitely don't want to update these info.
          $blacklist = array('sid', 'create_time', 'user_info');
          $user->load_array($content, $blacklist);
          
        default:;
      }
    }
    
    $response = array(
      array('action_result' => $action_result,
            'content' => $content),
      'struct');
    
    return $this->xmlrpc->send_response($response);
  }
  
  public function create_user($request) {
    return $this->process($request, 'create_user');
  }
}

/* End of file user_rpc.php */
/* Location: ./application/controllers/rpcs/user_rpc.php */