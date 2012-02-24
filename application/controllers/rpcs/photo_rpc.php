<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/photo.php';
require_once LIB.'gatekeeper/gatekeeper.php';

class Photo_rpc extends CI_Controller {
  function __construct() {
    parent::__construct();
  }

  public function index() {
    $this->load->library('xmlrpc');
    $this->load->library('xmlrpcs');

    $config['functions']['upload_photo'] = array('function' => 'Photo_rpc.upload_photo');
    $config['functions']['photo_data'] = array('function' => 'Photo_rpc.photo_data');
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
      log_message('warning', 'rpc_photo parsed content: '.json_encode($content));
      $content['binary'] = $binary;
    } else {
      log_message('warning', 'rpc_photo parsed content: '.json_encode($content));
    }

    // validatePost returns FALSE if not valid.
    if ($content !== FALSE) {
      $action_result = 'failed : no such command.';
      $this->load->model('ent/photo_model', 'photo_model');
      $response_content = array();
      switch($cmd) {
        /*
         * ==========================================================
         * RPC Call of creating a new photo.
         * ==========================================================
         */
        case 'upload_photo':

          $photo = new Photo();
          $photo->load_array($content)->new_sid();

          $photo = $this->photo_model->upload_photo($photo);
          if ($photo instanceof Photo) {
            $action_result = 'suc';
            $response_content['photo'] = $photo->to_array($zipped = false,
                                                          array('binary'),
                                                          'blacklist');
          } else {
            $action_result = $photo;
          }
          break;

        /*
        * ==========================================================
        * RPC Call of deleting a photo.
        * ==========================================================
        */
        case 'delete_photo':
          break;

        /*
        * ==========================================================
        * RPC Call of retrieving photos information.
        * ==========================================================
        */
        case 'get_photo_data':
          $photo_id_list = $content['photo_id_list'];
          $ent_list = $this->photo_model->get_ents($photo_id_list);
          foreach ($ent_list as $sid => $ent) {
            $response_content[$sid] = $ent->to_array($zipped = false);
          }
          $action_result = 'suc';
          break;

        /*
        * ==========================================================
        * RPC Call of other photo operations will be added here.
        * ==========================================================
        */
        default:;
      }
    }
    log_message('warning', 'photo rpc action result: '.$action_result);
    log_message('warning', 'photo rpc response: '.json_encode($response_content));
    $response = array(
      array('action_result' => $action_result,
            'content' => array($response_content, 'array')),
      'struct');

    return $this->xmlrpc->send_response($response);
  }

  public function upload_photo($request) {
    return $this->process($request, 'upload_photo');
  }

  public function photo_data($request) {
    return $this->process($request, 'get_photo_data');
  }
}

/* End of file photo_rpc.php */
/* Location: ./application/controllers/rpcs/photo_rpc.php */