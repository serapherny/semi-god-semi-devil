<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/poll.php';
require_once LIB.'gatekeeper/gatekeeper.php';

class Poll_rpc extends CI_Controller {
  function __construct() {
    parent::__construct();
  }
  
  public function index() {
    $this->load->library('xmlrpc');
    $this->load->library('xmlrpcs');
  
    $config['functions']['create_poll'] = array('function' => 'Poll_rpc.create_poll');
    $config['functions']['poll_data'] = array('function' => 'Poll_rpc.poll_data');
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
    
    // We do this to avoid recording the whole image file.
    if (isset($content['binary'])) {
      $binary = $content['binary'];
      unset($content['binary']);
      log_message('warning', 'rpc_poll parsed content: '.json_encode($content));
      $content['binary'] = $binary;
    } else {
      log_message('warning', 'rpc_poll parsed content: '.json_encode($content));
    }
    
    // validatePost returns FALSE if not valid.
    if ($content !== FALSE) {
      $action_result = 'failed : no such command.';
      
      switch($cmd) {
        /* 
         * ==========================================================
         * RPC Call of creating a new poll.
         * ==========================================================
         */
        case 'create_poll': 
          
          $poll = new poll();
          $blacklist = array();
          $poll->load_array($content, $blacklist)->new_sid();
          $valid = $poll->validateNewpollData();

          if (!$valid) {
            $action_result = 'failed : not valid or not enough new poll data.';
            break;
          }
          
          $this->load->model('ent/poll_model', 'poll_model');
          $action_result = $this->poll_model->create_poll($poll, &$response_content);
          break;
          
        /*
        * ==========================================================
        * RPC Call of deleting a poll.
        * ==========================================================
        */         
        case 'delete_poll':
          break;
          
        /*
        * ==========================================================
        * RPC Call of retrieving polls information.
        * ==========================================================
        */
        case 'get_poll_data':
          $poll_id_list = $content['poll_id_list'];
          if (!is_array($poll_id_list)) {
            $poll_id_list = array($poll_id_list);
          }
          $this->load->model('ent/poll_model', 'poll_model');
          $action_result = $this->poll_model->get_poll_data($poll_id_list, &$response_content);
          break;  
          
        /*
        * ==========================================================
        * RPC Call of other poll operations will be added here.
        * ==========================================================
        */
        default:;
      }
    }
    log_message('warning', 'poll rpc action result: '.$action_result);
    log_message('warning', 'poll rpc response: '.json_encode($response_content));    
    $response = array(
      array('action_result' => $action_result,
            'content' => array($response_content, 'struct')),
      'struct');
    
    return $this->xmlrpc->send_response($response);
  }
  
  public function create_poll($request) {
    return $this->process($request, 'create_poll');
  }
  
  public function poll_data($request) {
    return $this->process($request, 'get_poll_data');
  }
}

/* End of file poll_rpc.php */
/* Location: ./application/controllers/rpcs/poll_rpc.php */