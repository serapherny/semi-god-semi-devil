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
         * RPC Call of creating a new photo.
         * ==========================================================
         */
        case 'upload_photo': 
          
          $photo = new Photo();
          $blacklist = array();
          $photo->load_array($content, $blacklist);
          $valid = $photo->validateNewPhotoData();

          if (!$valid) {
            $action_result = 'failed : not valid or not enough new photo data.';
            break;
          }
          
          $this->load->model('ent/photo_model', 'photo_model');
          $action_result = $this->photo_model->upload_photo($photo, $response_content);
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
        * RPC Call of other photo operations will be added here.
        * ==========================================================
        */
        default:;
      }
    }
    
    $response = array(
      array('action_result' => $action_result,
            'content' => array($response_content, 'struct')),
      'struct');
    
    return $this->xmlrpc->send_response($response);
  }
  
  public function upload_photo($request) {
    return $this->process($request, 'upload_photo');
  }
}

/* End of file photo_rpc.php */
/* Location: ./application/controllers/rpcs/photo_rpc.php */