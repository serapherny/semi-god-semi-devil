<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ent/photo.php';

class Photo_monitor extends CI_Controller {
  function __construct() {
    parent::__construct();
  }

  public function create_photo() {
    $this->load->helper(array('form', 'url', 'file'));

    // This path is for website temp use only.
    $config['upload_path'] = './uploads/';
    $config['allowed_types'] = 'gif|jpg|png'; // pic last if contains non-pic.
    $config['max_size'] = '5000'; // I guess it use KB as unit.
    $config['max_width']  = '3000';
    $config['max_height']  = '2000';

    $this->load->library('upload', $config);
    $this->load->library('form_validation');
    $this->form_validation->set_rules('usid', '用户ID', 'required');
    // The outer lib functino checks inputs.
    if ($this->form_validation->run() == TRUE ) {

      // The inner function checks upload result.
      if ( $this->upload->do_upload()) {
        // It gets an array of all info of uploaded file.
        $upload_data = $this->upload->data();
        // $binary saves base64-formed whole file data.
        $binary = base64_encode(read_file($upload_data['full_path']));
        // Send binary here for the Simulated_android to simulate rpc.
        $content = array(
          'author' => $this->input->post('usid'),
          //'file_name' => $upload_data['file_name'],
          'file_ext' => $upload_data['file_ext'],
          'binary' => $binary
        );
        $this->load->library('xmlrpc');
        $this->load->library('device/simulate_android');
        $rpc_result = $this->simulate_android->set_xmlrpc($this->xmlrpc)
                           ->send_rpc('upload_photo', $content, 'photo_rpc');
        if ($rpc_result['action_result'] == 'suc') {
          $action_result = '成功上传新图片';
        } else { // Here we failed the rpc calling.
          $action_result = '上传图片失败， 错误信息：'.$rpc_result['action_result'];
        }
      } else { // Here we failed the upload check.
        $error = $this->upload->display_errors();
        if ($error) {
          $action_result = '上传失败：'.$error;
        } else {
          $action_result = '';
        }
      }
    } else { // Here we failed the field check.
      $error = validation_errors();
      if ($error) {
        $action_result = '输入有误：'.$error;
      } else {
        $action_result = '';
      }
    }
    return $action_result;
  }

  public function photo_data() {
    $this->load->library('form_validation');
    $this->form_validation->set_rules('sid', '照片sid', 'required');
    // Check if the fields have been fulfilled.
    if ($this->form_validation->run() == TRUE ) {
      $photo_id_list = explode(',', $this->input->post('sid'));
      $content = array('photo_id_list'=> array($photo_id_list, 'array'));
      $this->load->library('xmlrpc');
      $this->load->library('device/simulate_android');
      $rpc_result = $this->simulate_android->set_xmlrpc($this->xmlrpc)
      ->send_rpc('photo_data', $content, 'photo_rpc');
      if ($rpc_result['action_result'] == 'suc') {
        $action_result = '成功取得照片数据';
      } else {
        $action_result = '取得照片数据失败， 错误类型：'.$rpc_result['action_result'];
      }
    } else {
      $error = validation_errors();
      if ($error) {
        $action_result = '输入有误：'.$error;
      } else {
        $action_result = '';
      }
    }
    return $action_result;
  }

  public function index() {
    $this->load->helper('url');
    $this->load->helper('form');
    $action_result = '';
    $mode = $this->input->post('mode');
    switch ($mode) {
      case 'upload':     $action_result = $this->create_photo(); break;
      case 'data':       $action_result = $this->photo_data(); break;
      default: break;
    }
    $this->load->model('ent/photo_model', 'photo_model');

    $data = array('css_files' => array('css/style.css'),
                  'page_title'=>'图片管理');
    $this->load->view('header', $data);

    $data = array('photo_list' => $this->photo_model->get_photo_list(),
                      'rebuild_db_page' => site_url('internal/rebuild_db?table=photo'),
                      'action_result' => $action_result);

  	$this->load->view('internal/photo_monitor', $data);

  	$this->load->view('footer');
  }
}

/* End of file photo_monitor.php */
/* Location: ./application/controllers/internal/photo_monitor.php */