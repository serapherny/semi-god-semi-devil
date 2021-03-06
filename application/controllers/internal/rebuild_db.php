<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once LIB.'ui/notice.php';

class Rebuild_db extends CI_Controller {
  function __construct() {
    parent::__construct();
  }

  private function notify($type, $message) {
    // Push a colorful notification.
    echo <ui:notice type={$type}>{$message}</ui:notice>;
  }

  public function rebuild_table($table_name) {

    $config_pre = $table_name.'_table';
    $fields = $this->config->item($config_pre.'_fields');
    $this->dbforge->add_field($fields);
    $this->dbforge->add_field('id'); // This serves as a short userid.

    $primary_keys = $this->config->item($config_pre.'_primary_keys');
    $this->dbforge->add_key($primary_keys, TRUE);

    $keys = $this->config->item($config_pre.'_keys');
    $this->dbforge->add_key($keys, TRUE);

    $this->dbforge->drop_table($table_name);
    $this->dbforge->create_table($table_name);

    $this->notify('info', "成功建立 $table_name 表!");

    // Read out the field meta info from the table for printing.
    $fields_info = $this->db->field_data($table_name);

    $field_info_list = array();
    $field_info_list[] = array('域名', '数值类型', '长度', '是否主键');
    foreach ($fields_info as $field_info)
    {
      $field_info_list[] = array( $field_info->name,
                                  $field_info->type,
                                  $field_info->max_length,
                                  $field_info->primary_key);
    }

    // Generate a table automaticly.
    $this->load->library('table');
    $content = $this->table->generate($field_info_list);
    $this->load->helper('url');
    // Print it plain.
    $url = site_url('internal/'.$table_name.'_monitor');
    echo <div class="float_r">
           <a href={$url}>{$table_name}管理</a>
         </div>;
    echo $content;
  }

  public function rebuild_db() {
    $this->config->load('datatable');
    $dbname = $this->config->item('dbname');

    // Delete the original database first.
    if (!$this->dbforge->drop_database($dbname)) {
      $this->notify('error', '数据库删除失败!');
      return;
    } else {
      $this->notify('info', '成功删除原有数据库!');
    }

    // Create a new database.
    if (!$this->dbforge->create_database($dbname)) {
      $this->notify('error', '新数据库建立失败！');
      return;
    } else {
      $this->notify('info', '成功建立新数据库!');
    }

    $this->db->query('use ' . $dbname);
    $this->notify('info', '成功连接到新数据库!');

    $this->load->helper('url');
    $url = site_url('internal/test_init');
    echo <div class="float_r">
           <a href={$url}>建立测试数据</a>
         </div>;

    // Create user table.
    $this->rebuild_table('user');

    // Create photo table.
    $this->rebuild_table('photo');

    // Create item table.
    $this->rebuild_table('item');

    // Create item list table.
    $this->rebuild_table('item_list');

    // Create post table.
    $this->rebuild_table('post');

    // Create comment table.
    $this->rebuild_table('comment');

    // Create tag table.
    $this->rebuild_table('tag');

  }

  public function index() {
    // Load database forge module.
    $this->load->dbforge();
    $this->config->load('datatable');

    $data = array('css_files' => array('css/style.css'),
      	          'page_title'=>'重建数据库');

    echo $this->load->view('header', $data, true);
    $table = $this->input->get('table');
    if ($table) {
      $this->rebuild_table($table);
    } else {
      $this->rebuild_db();
    }
  	echo $this->load->view('footer', array(), true);
  }
}

/* End of file rebuild_db.php */
/* Location: ./application/controllers/rebuild_db.php */