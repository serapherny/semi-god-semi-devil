
---------------------------------------
  目录文件说明
---------------------------------------

  1. update.php：
     将UCenter Home升级到最新版本的升级程序。
  2. convert.php：
     X-Space转换到UCenter Home的转换程序。
  3. client_bbs.php
     将UCenter Home整合到Discuz! 6.0/5.x/4.x/3.x/2.x的文件。
  
---------------------------------------
  update.php 文件使用方法
---------------------------------------

  适用于 UCenter Home 升级安装。
  如果你之前安装过UCenter Home，请如下进行升级操作：
  
  1. 请先自行备份当前的数据库，避免升级失败，造成数据丢失而无法恢复。
  2. 将程序包 ./upload 目录中，除config.new.php文件、./install目录以外的其他所有文件，
     全部上传并覆盖当前程序。
  3. 将本目录中的 update.php 文件上传到服务器程序根目录，并在浏览器运行。
     根据升级程序的提示，进行数据库升级操作。
     
---------------------------------------     
  convert.php 文件使用方法
---------------------------------------

  适用于 从 X-Space 转换到 UCenter Home。
  如果您的站点之前使用了X-Space，将X-Space转换到UCenter Home操作：
  
  1. 下载并安装UCenter。
     http://download.comsenz.com/UCenter/
  2. 升级Discuz!论坛到最新的6.1.0版本
     http://download.comsenz.com/Discuz/
  3. 上传程序包 ./upload 目录中的所有文件到服务器，并进行全新安装(参考全新安装说明)。
  4. 将本目录中的 convert.php 文件上传到服务器程序根目录，并在浏览器运行。
     根据转换程序的提示，进行X-Space到UCenter Home的转换操作。

---------------------------------------     
  client_bbs.php 文件使用方法
---------------------------------------

  适用于当前安装Discuz! 6.0/5.x/4.x/3.x/2.x的用户，整合使用UCenter Home。

  1. 下载并安装最新版本的UCenter。
     http://download.comsenz.com/UCenter/1.5.0/
  2. 上传程序包 ./upload 目录中的所有文件到服务器，并进行UCenter Home的全新安装
     (参考UCenter Home全新安装说明)。
  3. 修改UCenter Home的根目录下的 ./config.php 文件：
  
	修改方法，在文件最后添加当前你使用的Discuz!的数据库连接参数：

	//Discuz6.0/5.x/4.x/3.x 配置参数
	$_SC['bbs_dbhost']  		= ''; //Mysql服务器地址
	$_SC['bbs_dbuser']  		= ''; //用户
	$_SC['bbs_dbpw'] 	 	= ''; //密码
	$_SC['bbs_dbcharset'] 		= ''; //字符集
	$_SC['bbs_pconnect'] 		= 0; //是否持续连接
	$_SC['bbs_dbname']  		= 'discuz'; //数据库
	$_SC['bbs_tablepre'] 		= 'cdb_'; //表名前缀
	$_SC['bbs_cookiepre'] 		= 'cdb_'; //COOKIE前缀
	$_SC['bbs_cookiedomain'] 	= ''; //COOKIE作用域
	$_SC['bbs_cookiepath'] 		= '/'; //COOKIE作用路径

  4. 将本目录中的  client_bbs.php 文件上传到 ./uc_client 目录下。
  5. 修改 ./uc_client 目录下的 client.php 文件：
	
	修改方法，找到：

	global $uc_controls;

	在这一行下面添加：

	global $_SC;
	if($_SC['bbs_dbname'] && in_array($model, array('friend','user','pm'))) {
		include_once UC_ROOT.'./client_bbs.php';
		$func_name = "bbs_{$model}_{$action}";
		if(function_exists($func_name)) {
			return call_user_func_array("bbs_{$model}_{$action}", $args);
		}
	}
