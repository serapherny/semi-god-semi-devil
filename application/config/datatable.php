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
           
  'last_device'   => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16',
                           'null'        => TRUE
                          ),
                          
  'last_login_time' => array(
                           'type'        => 'BIGINT',
                           'null'        => TRUE
                          ),

  // All other information that cannot be used as a search key.
  // Stored using JSON in this field, including:
  //   profile_photo_id,
  //   friends_list,
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

  'author'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16',
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

  'type'      => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '255' 
                          ),

  'cansee'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '8'
                          ),
                          
  'author'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16',
                           'null'        => TRUE
                          ),
                            
  'create_time'   => array(
                           'type'        => 'BIGINT'
                          ),
                          
  'last_comment_time'  => array(
                           'type'        => 'BIGINT',
                           'null'        => TRUE
                          ),   
// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:
// poll_image(s)
// post_user_icon,
// post_user_name,
// description,
// latest_comment_id
  'poll_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
                          )
);

$config['poll_table_primary_keys'] = array();
$config['poll_table_keys'] = array();

