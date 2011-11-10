<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| config semi_user fields configuration.
|--------------------------------------------------------------------------
|
*/

$config['dbname'] = 'semi';

$config['user_table_fields']	=  array(

  'sid'           => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
                          ),   
                               
  'nickname'      => array(
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

  // All other information that cannot be used as a search key.
  // Stored using JSON in this field, including:
  //   profile_photo_id,
  //   friends_list,
  //   device_list,
  //   last_log_in_time,
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
