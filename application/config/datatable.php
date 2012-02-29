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

/*
|--------------------------------------------------------------------------
| config semi_item_list fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['item_list_table_fields']	=  array(

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

  'itemlist'        => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
),
// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:,
// ...
  'item_list_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
)
);

$config['item_list_table_primary_keys'] = array();
$config['item_list_table_keys'] = array();



/*
|--------------------------------------------------------------------------
| config semi_tag fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['tag_table_fields']	=  array(

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

  'subtaglist'          => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
),

// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:,
// ...
  'tag_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
)
);

$config['tag_table_primary_keys'] = array();
$config['tag_table_keys'] = array();


/*
 |--------------------------------------------------------------------------
| config semi_comment fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['comment_table_fields']	=  array(

  'sid'           => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
),

  'target'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
),

  'create_time'   => array(
                           'type'        => 'BIGINT'
),

  'author'        => array(
                           'type'        => 'VARCHAR',
                           'constraint'  => '16'
),

  'commentlist'    => array(
                           'type'        => 'TEXT',
                           'null'        => TRUE
),

// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:,
// ...
  'comment_info'     => array(
                           'type'        => 'TEXT',
                           'null'	     => TRUE
)
);

$config['comment_table_primary_keys'] = array();
$config['comment_table_keys'] = array();

/*
|--------------------------------------------------------------------------
| config semi_post fields configuration.
|--------------------------------------------------------------------------
|
*/
$config['post_table_fields']	=  array(

	'sid'           => array(
  					         'type'        => 'VARCHAR',
                             'constraint'  => '16'
),

	'content_type'  => array(
                            'type'        => 'TEXT',
),

	'content'  => array(
                            'type'        => 'TEXT',
),

	'create_time'   => array(
							'type'        => 'BIGINT'
),

	'author'        => array(
                              'type'        => 'VARCHAR',
                              'constraint'  => '16'
),

    'commentlist'    => array(
                              'type'        => 'TEXT',
                              'null'        => TRUE
),

// All other information that cannot be used as a search key.
// Stored using JSON in this field, including:,
// ...
    'post_info'     => array(
                              'type'        => 'TEXT',
                              'null'	     => TRUE
)
);

$config['post_table_primary_keys'] = array();
$config['post_table_keys'] = array();