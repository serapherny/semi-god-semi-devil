<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['dbname'] = 'semi';

/*
|--------------------------------------------------------------------------
| config semi_user fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['user_table_fields']	=  array(

  'sid'           => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
                          ),

  'nickname'      => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255'
                          ),

  'password'      => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255'
                          ),

  'email_addr'    => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255'
                          ),
  'last_login_time' => array(
                           'type'        => 'BIGINT',
                           'null'        => TRUE
                          ),

  'friendlist'     => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
                           ),
  'create_time'   => array(
                           'type'        => 'BIGINT'
                           ),
  // All other information that cannot be used as a search key.
  // Stored using JSON in this field, including:
  //   profile_photo_id,
  //   device_list,
  //   last_active_time,
  //   create_time,
  //   birthday/gender/school/work/.....
  'user_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
                          )
);

$config['user_table_primary_keys'] = array();
$config['user_table_keys'] = array('email_addr');

/*
|--------------------------------------------------------------------------
| config semi_photo fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['photo_table_fields']	=  array(

  'sid'           => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
                          ),

  'file_path'     => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255'
                          ),

  'file_name'     => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255'
                          ),
  'file_ext'      => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255'
                          ),
  'create_time'   => array(
                           'type'        => 'BIGINT'
                          ),
  'author'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16',
                           'null'        => TRUE
                          ),
  'taglist'       => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
                          ),
  'itemlist'      => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
                          ),
// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:
// create_time,
// file_type,
// image_size,
// image_file_length, ...
  'photo_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
                          )
);

$config['photo_table_primary_keys'] = array();
$config['photo_table_keys'] = array();


/*
|--------------------------------------------------------------------------
| config semi_poll fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['poll_table_fields']	=  array(

  'sid'           => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
                          ),

  'poll_type'         => array(
                           'type'        => 'INT'
                          ),

  'visibility'   => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '8'
                          ),

  'author'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
                          ),

  'create_time'   => array(
                           'type'        => 'BIGINT'
                          ),

  'latest_comment_time'  => array(
                           'type'        => 'BIGINT',
                           'null'        => TRUE
                          ),

  'latest_comment'   => array(
                           'type'        => 'VARCHAR',
                           'constraint'   => '16',
                           'null'        => TRUE
                          ),

  'photo_1'      => array(
                          'type'         => 'VARCHAR',
                          'constraint'   => '16'
                         ),

  'photo_2'      => array(
                          'type'         => 'VARCHAR',
                          'constraint'   => '16'
                         ),

  'category'     => array(
                          'type'         => 'VARCHAR',
                          'constraint'   => '255',
                          'null'         => TRUE
                         ),

  'description'  => array(
                          'type'         => 'TEXT',
                          'null'         => TRUE
                         ),
// All other information that cannot be used as a search key.
// Stored using JSON in this field.
  'poll_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
                          )
);

$config['poll_table_primary_keys'] = array();
$config['poll_table_keys'] = array();

/*
|--------------------------------------------------------------------------
| config semi_item fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['item_table_fields']	=  array(

  'sid'           => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
                          ),

  'create_time'   => array(
                           'type'        => 'BIGINT'
                          ),

  'author'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16',
                           'null'        => TRUE
                          ),

  'taglist'          => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
                          ),

  'photolist'        => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
                          ),
// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:,
// ...
  'item_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
                           )
);

$config['item_table_primary_keys'] = array();
$config['item_table_keys'] = array();