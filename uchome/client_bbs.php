<?php

/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: client_bbs.php 10978 2009-01-14 02:39:06Z liguode $
*/

if(!defined('UC_API')) {
	exit('Access denied');
}

//连接数据库
function bbs_dbconnect() {
	global $_SGLOBAL, $_SC;
	
	if(empty($_SGLOBAL['bbs_db'])) {
		if(empty($_SC['bbs_dbhost']) || empty($_SC['bbs_dbuser'])) {
			$_SGLOBAL['bbs_db'] = $_SGLOBAL['db'];
		} else {
			include_once(S_ROOT.'./source/class_mysql.php');
			$_SGLOBAL['bbs_db'] = new dbstuff;
			$_SGLOBAL['bbs_db']->charset = $_SC['bbs_dbcharset'];
			$_SGLOBAL['bbs_db']->connect($_SC['bbs_dbhost'], $_SC['bbs_dbuser'], $_SC['bbs_dbpw'], $_SC['bbs_dbname'], $_SC['bbs_pconnect']);
		}
	}
}

//读取论坛表名
function bbs_name($name) {
	global $_SGLOBAL, $_SC;
	
	$tablename = '';
	if($_SC['bbs_dbname']) $tablename .= "`{$_SC['bbs_dbname']}`.";
	$tablename .= "{$_SC['bbs_tablepre']}$name";
	return $tablename;
}

//设置论坛cookie
function bbs_setcookie($varname, $value, $life=0) {
	global $_SGLOBAL, $_SC;
	
	setcookie($_SC['bbs_cookiepre'].$varname, $value, $life?($_SGLOBAL['timestamp']+$life):0, $_SC['bbs_cookiepath'], $_SC['bbs_cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0);
	return '';
}

//添加好友
function bbs_friend_add($uid, $friendid, $comment='') {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	$friendid = intval($friendid);
	$query = $_SGLOBAL['bbs_db']->query("SELECT COUNT(*) FROM ".bbs_name('buddys')." WHERE uid='$uid' AND buddyid='$friendid'");
	$count = $_SGLOBAL['bbs_db']->result($query, 0);
	if(!$count) {
		$_SGLOBAL['bbs_db']->query("INSERT INTO ".bbs_name('buddys')." (uid,buddyid) VALUES ('$uid','$friendid')");
	}
	return 1;
}

//删除好友
function bbs_friend_delete($uid, $friendids) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	$_SGLOBAL['bbs_db']->query("DELETE FROM ".bbs_name('buddys')."
		WHERE (uid='$uid' AND buddyid IN (".simplode($friendids)."))
			OR (uid IN (".simplode($friendids).") AND buddyid='$uid')");
	return 1;
}

//好友列表
function bbs_friend_ls($uid, $page = 1, $pagesize = 10, $totalnum = 10, $direction = 0) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	$results = array();
	$query = $_SGLOBAL['bbs_db']->query("SELECT * FROM ".bbs_name('buddys')." WHERE buddyid='$uid' LIMIT 0,1000");
	while ($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
		$results[] = $value;
	}
	return $results;
}

//用户注册
function bbs_user_register($username, $password, $email, $questionid = '', $answer = '') {
	global $_SGLOBAL;
	
	$username = trim($username);
	if(strlen($username) > 15) {
		return -1;
	}
	$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $username)) {
		return -2;
	}
	if(!$password || $password != addslashes($password)) {
		return -1;
	}
	if(!isemail($email)) {
		return -4;
	}
	
	bbs_dbconnect();
	
	$query = $_SGLOBAL['bbs_db']->query("SELECT username,email FROM ".bbs_name('members')." WHERE username='$username' OR email='$email' LIMIT 1");
	if($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
		if($value['email'] == stripslashes($email)) {
			return -6;
		}
		return -3;
	}
	
	$regip = getonlineip();
	$password = md5($password);
	
	$query = $_SGLOBAL['bbs_db']->query("SELECT groupid FROM ".bbs_name('usergroups')." WHERE type='member' AND creditshigher>=0 ORDER BY creditshigher LIMIT 1");
	$groupid = $_SGLOBAL['bbs_db']->result($query, 0);
	
	$_SGLOBAL['bbs_db']->query("INSERT INTO ".bbs_name('members')." (username,password,groupid,email,regip,regdate)
		VALUES ('$username','$password','$groupid','$email','$regip','$_SGLOBAL[timestamp]')");
	return  $_SGLOBAL['bbs_db']->insert_id();
}

//用户登陆检查
function bbs_user_login($username, $password, $isuid = 0, $checkques = 0, $questionid = '', $answer = '') {
	global $_SGLOBAL;
	
	$isuid = intval($isuid);
	if($isuid) {
		$sql = "uid='$username'";
	} else {
		$sql = "username='$username'";
	}
	
	$password = md5($password);

	bbs_dbconnect();
	
	$query = $_SGLOBAL['bbs_db']->query("SELECT * FROM ".bbs_name('members')." WHERE $sql");
	if($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
		if($value['password'] == $password) {

			$q = $_SGLOBAL['bbs_db']->query("SELECT value FROM ".bbs_name('settings')." WHERE variable='authkey'");
			$authkey = $_SGLOBAL['bbs_db']->result($q, 0);
			$discuz_auth_key = md5($authkey.$_SERVER['HTTP_USER_AGENT']);
			bbs_setcookie('auth', authcode("$value[password]\t$value[secques]\t$value[uid]", 'ENCODE', $discuz_auth_key), 3600*24*30);
			bbs_setcookie('sid', '');
		
			return array($value['uid'], $value['username'], $value['password'], $value['email']);
		} else {
			return array(-2);
		}
	} else {
		return array(-1);
	}
}

//编辑用户
function bbs_user_edit($username, $oldpw, $newpw, $email, $ignoreoldpw = 0, $questionid = '', $answer = '') {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	if(!$newpw || $newpw != addslashes($newpw)) {
		return -7;
	}
	if(!isemail($email)) {
		return -4;
	}
	
	bbs_dbconnect();
	
	if(!$ignoreoldpw) {
		$query = $_SGLOBAL['bbs_db']->query("SELECT * FROM ".bbs_name('members')." WHERE username='$username'");
		if($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
			if($value['password'] != $oldpw) {
				return -1;
			}
			if($value['email'] != stripslashes($email)) {
				$newuid = $_SGLOBAL['bbs_db']->result($_SGLOBAL['bbs_db']->query("SELECT uid FROM ".bbs_name('members')." WHERE email='$email' LIMIT 1"), 0);
				if($newuid) {
					return -6;
				}
			}
		} else {
			return -8;
		}
	}
	$_SGLOBAL['bbs_db']->query("UPDATE ".bbs_name('members')." SET password='$newpw', email='$email' WHERE username='$username'");
	return 1;
}

//检查用户名是否为合法
function bbs_user_check_username($username) {
	global $_SGLOBAL;
	
	$username = trim($username);
	if(strlen($username) > 15) {
		return -1;
	}
	$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $username)) {
		return -2;
	}
	
	bbs_dbconnect();
	
	$query = $_SGLOBAL['bbs_db']->query("SELECT uid FROM ".bbs_name('members')." WHERE username='$username'");
	if($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
		return -3;
	}
	return 1;
}

//取得用户数据
function bbs_user_get_user($username, $isuid=0) {
	global $_SGLOBAL;
	
	if($isuid) {
		$sql = "uid='$username'";
	} else {
		$sql = "username='$username'";
	}
	
	bbs_dbconnect();
	
	$query = $_SGLOBAL['bbs_db']->query("SELECT * FROM ".bbs_name('members')." WHERE $sql");
	if($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
		return array($value['uid'], $value['username'], $value['email']);
	} else {
		return array();
	}
}

//检查新短消息
function bbs_pm_check_newpm($uid, $more = 0) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	$newpm = $_SGLOBAL['bbs_db']->result($_SGLOBAL['bbs_db']->query("SELECT newpm FROM ".bbs_name('members')." WHERE uid='$uid'"), 0);
	return $newpm;
}

//发送短消息
function bbs_pm_sendpm($fromuid, $msgto, $subject, $message, $instantly = 1, $replypmid = 0, $isusername = 0) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$fromuid = $_SGLOBAL['supe_uid'];
	
	$newmsgto = array();
	$msgto = explode(',', $msgto);
	foreach ($msgto as $value) {
		$value = $isusername?trim($value):intval($value);
		if($value) {
			$newmsgto[$value] = $value;
		}
	}
	if($isusername) {
		$query = $_SGLOBAL['bbs_db']->query("SELECT uid, username FROM ".bbs_name('members')." WHERE username IN (".simplode($newmsgto).")");
		while ($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
			$newmsgto[] = $value['uid'];
		}
	}
	
	$subject = getstr($subject, 50, 1, 1);
	if(empty($subject)) $subject = getstr($message, 30, 1, 1);
	$message = getstr($message, 5000, 1, 1);
	
	$inserts = array();
	foreach ($newmsgto as $touid) {
		$touid = intval($touid);
		if($touid) {
			$inserts[] = "('$_SGLOBAL[supe_username]', '$_SGLOBAL[supe_uid]', '$touid', 'inbox', '1', '$subject', '$_SGLOBAL[timestamp]', '$message')";
		}
	}
	if($inserts) {
		$_SGLOBAL['bbs_db']->query("INSERT INTO ".bbs_name('pms')." (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message) VALUES ".implode(',', $inserts));
		$_SGLOBAL['bbs_db']->query("UPDATE ".bbs_name('members')." SET newpm='1' WHERE uid IN (".simplode($newmsgto).")");
	}
	return count($inserts);
}

//删除短消息
function bbs_pm_delete($uid, $folder, $pmids) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	$_SGLOBAL['bbs_db']->query("DELETE FROM ".bbs_name('pms')." WHERE msgtoid='$uid' AND pmid IN (".simplode($pmids).")");
	return 1;
}

//获取短消息列表
function bbs_pm_ls($uid, $page = 1, $pagesize = 10, $folder = 'inbox', $filter = 'newpm', $msglen = 0) {
	global $_SGLOBAL, $_SN;

	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	$uid = $_SGLOBAL['supe_uid'];
	$page = intval($page);
	$pagesize = intval($pagesize);
	
	bbs_dbconnect();
	
	$list = array();
	$count = $_SGLOBAL['bbs_db']->result($_SGLOBAL['bbs_db']->query("SELECT COUNT(*) FROM ".bbs_name('pms')." WHERE msgtoid='$uid'"),0);
	if($count) {
		$start = ($page-1)*$pagesize;
		if($start<0) $start = 0;
		$query = $_SGLOBAL['bbs_db']->query("SELECT * FROM ".bbs_name('pms')." WHERE msgtoid='$uid' ORDER BY dateline DESC LIMIT $start,$pagesize");
		while ($value = $_SGLOBAL['bbs_db']->fetch_array($query)) {
			$value['touid'] = $value['msgfromid'];
			$value['message'] = preg_replace("/(\[.*?\]|\<.*?\>)/", '', getstr($value['message'], 200)).' ...';
			realname_set($value['msgfromid'], $value['msgfrom']);
			$list[] = $value;
		}
	}
	realname_get();
	return array('count'=>$count, 'data'=>$list);
}

//忽略未读消息提示
function bbs_pm_ignore($uid) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	$_SGLOBAL['bbs_db']->query("UPDATE ".bbs_name('members')." SET newpm='0' WHERE uid='$uid'");
	return '';
}

//获取短消息内容
function bbs_pm_view($uid, $_pmid, $touid = 0, $daterange = 1) {
	global $_SGLOBAL, $_SN, $pmid;
	
	if(empty($_SGLOBAL['supe_uid'])) exit('Access denied');
	
	bbs_dbconnect();
	
	$uid = $_SGLOBAL['supe_uid'];
	
	$query = $_SGLOBAL['bbs_db']->query("SELECT * FROM ".bbs_name('pms')." WHERE pmid='$pmid' AND (msgtoid='$uid' OR msgfromid='$uid')");
	$pm = $_SGLOBAL['bbs_db']->fetch_array($query);
	realname_set($pm['msgfromid'], $pm['msgfrom']);
	realname_get();
	
	$pm['message'] = getstr($pm['message'], 0, 0, 0, 0, 1, 1);
	
	return array($pm);
}

?>