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
        case 'create_user': 
          $user = new User();
          
          if (isset($content['nickname'])) {
            $user->set_nickname($content['nickname']);
          }
          
          if (isset($content['email_addr'])) {
            $user->set_email_addr($content['email_addr']);
          }

          if (isset($content['password'])) {
            $user->set_password($content['password']);
          }
          
          $user->add_device($content['UDID'])
               ->set_last_device($content['UDID']);

          $valid = $user->validateNewUserData();

          if (!$valid) {
            $action_result = 'failed : not valid or not enough new user data.';
            break;
          }
          
          $this->load->model('ent/user_model', 'user_model');
          $action_result = $this->user_model->create_user($user);
          break;
          
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