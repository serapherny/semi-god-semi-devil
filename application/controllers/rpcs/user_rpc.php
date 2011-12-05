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
    $config['functions']['bind_user'] = array('function' => 'User_rpc.bind_user');
    $config['functions']['user_data'] = array('function' => 'User_rpc.user_data');
    $config['object'] = $this;
  
    $this->xmlrpcs->initialize($config);
    $this->xmlrpcs->serve();
  }
  
  public function process($request, $cmd) {
    
    $this->load->library('xmlrpc');
    $parameters = $request->output_parameters();
    log_message('warning', 'rpc request: '.json_encode($parameters));
    $action_result = 'failed : not passing gatekeeper.';
    
    $gatekeeper = new Gatekeeper();
    $content = $gatekeeper->validatePost($parameters['0']);
    log_message('warning', 'rpc parsed content: '.json_encode($content));
    // validatePost returns FALSE if not valid.
    if ($content !== FALSE) {
      $action_result = 'failed : no such command.';
      $response_content = array();
      switch($cmd) {
        /* 
         * ==========================================================
         * RPC Call of creating a new user.
         * ==========================================================
         */
        case 'create_user': 
          
          $user = new User();
          $blacklist = array('sid', 'create_time');
          $user->new_sid()->load_array($content, $blacklist);
          $valid = $user->validateIdentifiable() && 
              $user->validateBasicDataForNewUser();

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
          $blacklist = array('create_time', 'sid', 'email_addr');
          $user->load_array($content, $blacklist);
          $valid = $user->validateIdentifiable();
          
          if (!$valid) {
            $action_result = 'failed : not valid or not enough user data for updating.';
            break;
          }
          
          $this->load->model('ent/user_model', 'user_model');
          $action_result = $this->user_model->update_user($user);
          break;
          
        /*
         * ==========================================================
         * RPC Call of bind a device to an existing user.
         * ==========================================================
         */
        case 'bind_user':
          $user = new User();
          $whitelist = array('email_addr', 'sid', 'UDID', 'last_device', 'password');
          $user->load_array($content, $whitelist, true);
          $valid = $user->validateBindable();
          
          if (!$valid) {
            $action_result = 'failed : not valid or not enough user data for binding.';
            break;
          }
          
          $this->load->model('ent/user_model', 'user_model');
          $action_result = $this->user_model->bind_user($user);
          break;
          
         /*
          * ==========================================================
          * RPC Call of getting data from existing user(s).
          * ==========================================================
          */
        case 'user_data':
          
          $user_list = $content['user_list'];
          $this->load->model('ent/user_model', 'user_model');
          // If user list is empty, we by default get data of the user himself.
          if (!$user_list) {
            $action_result = $this->user_model->get_myself(&$response_content);
          } else {
            // Else we get user data according to the user list.
            if (!is_array($user_list)) {
              $user_list = array($user_list);
            }
            $action_result = $this->user_model->get_user_data($user_list, &$response_content);
          }
          break;
          
        default:;
      }
    }
    log_message('warning', 'user rpc action result: '.$action_result);
    log_message('warning', 'user rpc response: '.json_encode($response_content));
    $response = array(
      array('action_result' => $action_result,
            'content' => array($response_content, 'struct')),
      'struct');
    
    return $this->xmlrpc->send_response($response);
  }
  
  public function create_user($request) {
    return $this->process($request, 'create_user');
  }
  
  public function update_user($request) {
    return $this->process($request, 'update_user');
  }
  
  public function bind_user($request) {
    return $this->process($request, 'bind_user');
  }
  
  public function user_data($request) {
    return $this->process($request, 'user_data');
  }
}

/* End of file user_rpc.php */
/* Location: ./application/controllers/rpcs/user_rpc.php */