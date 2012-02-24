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
      $this->load->model('ent/user_model', 'user_model');
      switch($cmd) {
        /*
         * ==========================================================
         * RPC Call of creating a new user.
         * ==========================================================
         */
        case 'create_user':

          $user = new User();
          $blacklist = array('sid', 'create_time');
          $user->new_sid()->load_array($content, $blacklist, 'blacklist');

          $user = $this->user_model->create_user($user);
          if ($user instanceof User) {
            $response_content['user'] = $user->to_array($zipped = false);
            $action_result = 'suc';
          } else {
            echo $user;
            $action_result = $user;
          }
          break;

        /*
         * ==========================================================
         * RPC Call of updating an existing user.
         * ==========================================================
         */
        case 'update_user':
          // TODO: add codes.
          break;

         /*
          * ==========================================================
          * RPC Call of getting data from existing user(s).
          * ==========================================================
          */
        case 'user_data':

          $user_list = $content['user_list'];
          $ent_list = $this->user_model->get_ents($user_list);
          foreach ($ent_list as $sid => $ent) {
            if ($ent instanceof User) {
              $response_content[$sid] = $ent->to_array($zipped = false);
            }
          }
          $action_result = 'suc';
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

  public function user_data($request) {
    return $this->process($request, 'user_data');
  }
}

/* End of file user_rpc.php */
/* Location: ./application/controllers/rpcs/user_rpc.php */