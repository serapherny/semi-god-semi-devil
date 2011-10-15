<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: update.php 13233 2009-08-24 08:17:25Z liguode $
*/

if(!@include('./common.php')) {
	exit('请将本文件移到程序根目录再运行!');
}

error_reporting(0);

//新SQL
$sqlfile = S_ROOT.'./data/install.sql';
if(!file_exists($sqlfile)) {
	show_msg('最新的SQL文件不存在,请先将最新的数据库结构文件 install.sql 已经上传到 ./data 目录下面后，再运行本升级程序');
}

$lockfile = './data/update.lock';
if(file_exists($lockfile)) {
	show_msg('请您先登录服务器ftp，手工删除 data/update.lock 文件，再次运行本文件进行UCenter Home升级。');
}

$PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];

//提交处理
if(submitcheck('delsubmit')) {
	//删除表
	if(!empty($_POST['deltables'])) {
		foreach ($_POST['deltables'] as $tname => $value) {
			$_SGLOBAL['db']->query("DROP TABLE `".tname($tname)."`");
		}
	}
	//删除字段
	if(!empty($_POST['delcols'])) {
		foreach ($_POST['delcols'] as $tname => $cols) {
			foreach ($cols as $col => $indexs) {
				if($col == 'PRIMARY') {
					$_SGLOBAL['db']->query("ALTER TABLE ".tname($tname)." DROP PRIMARY KEY", 'SILENT');//屏蔽错误
				} elseif($col == 'KEY') {
					foreach ($indexs as $index => $value) {
						$_SGLOBAL['db']->query("ALTER TABLE ".tname($tname)." DROP INDEX `$index`", 'SILENT');//屏蔽错误
					}
				} else {
					$_SGLOBAL['db']->query("ALTER TABLE ".tname($tname)." DROP `$col`");
				}
			}
		}
	}

	show_msg('删除表和字段操作完成了', 'update.php?step=delete');
}

if(empty($_GET['step'])) $_GET['step'] = 'start';
//处理开始
if($_GET['step'] == 'start') {
	//开始
	show_msg('本升级程序会参照最新的SQL文件，对你的UCHome数据库进行升级。<br><br>
		升级前请做好以下前期工作：<br><br>
		<b>第一步：</b><br>
		备份当前的数据库，避免升级失败，造成数据丢失而无法恢复；<br><br>
		<b>第二步：</b><br>
		将程序包 ./upload/ 目录中，除 config.new.php 文件、./install/ 目录以外的其他所有文件，全部上传并覆盖当前程序。<b>特别注意的是，最新数据库结构 ./data/install.sql 文件不要忘记上传，否则会导致升级失败</b>；<br><br>
		<b>第三步：</b><br>
		确认已经将程序包 ./update 目录中最新的 update.php 升级程序上传到服务器程序根目录中<br>
		<br><br><a href="update.php?step=check">已经做好以上工作，升级开始</a><br><br>
		特别提醒：为了数据安全，升级完毕后，不要忘记删除本升级文件。');

} elseif ($_GET['step'] == 'check') {
	
	//UCenter_Client
	include_once S_ROOT.'./uc_client/client.php';
	if(!function_exists('uc_check_version')) {
		show_msg('请将UCHome程序包中最新版本的 ./upload/uc_client 上传至程序根目录覆盖原有目录和文件后，再尝试升级。');
	}
	
	$uc_root = get_uc_root();
	$return = uc_check_version();
	if (empty($return)) {
		$upgrade_url = 'http://'.$_SERVER['HTTP_HOST'].$PHP_SELF.'?step=sql';
	} else {
		if($return['db'] == '1.5.0') {
			header("Location: update.php?step=sql");//UC升级完成
			exit();
		}
		$upgrade_url = 'http://'.$_SERVER['HTTP_HOST'].$PHP_SELF.'?step=check';
	}
	
	$ucupdate = UC_API."/upgrade/upgrade2.php?action=db&forward=".urlencode($upgrade_url);
	
	show_msg('<b>您的 UCenter 程序还没有升级完成，请如下操作：</b><br>UCenter Home支持了最新版本的UCenter，请先升级您的UCenter。<br><br>
		1. <a href="http://download.comsenz.com/UCenter/1.5.0/" target="_blank">点击这里下载对应编码的 UCenter 1.5.0 程序</a><br>
		2. 将解压缩得到的 ./upload 目录下的程序覆盖到已安装的UCenter目录 <b>'.($uc_root ? $uc_root : UC_API).'</b><br>
		&nbsp;&nbsp;&nbsp; (确保其升级程序 <b>./upgrade/upgrade2.php</b> 也已经上传到UCenter的 ./upgrade 目录)<br><br>
		确认完成以上UCenter程序升级操作完成后，您才可以：<br>
		<a href="'.$ucupdate.'" target="_blank">新窗口中访问 upgrade2.php 进行UCenter数据库升级</a><br>
		在打开的新窗口中，如果UCenter升级成功，程序会自动进行下一步的升级。<br>这时，您关闭本窗口即可。
		<br><br>
		如果您无法通过上述UCenter升级步骤，请调查问题后，务必将UCenter正常升级后，再继续本升级程序。<br>或者您可以：<br><a href="update.php?step=sql" style="color:#CCC;">跳过UCenter升级</a>，但这可能会带来一些未知兼容问题。');

} elseif ($_GET['step'] == 'sql') {
	
	//关闭站点
	if(empty($_SCONFIG['close'])) {
		
		$datas[] = "('close', '1')";
		$datas[] = "('closereason', '更多惊喜，更多期待，站点升级中，请稍等...')";
		
		$_SGLOBAL['db']->query("REPLACE INTO ".tname('config')." (var, datavalue) VALUES ".implode(',', $datas));
		
		include_once(S_ROOT.'./source/function_cache.php');
		config_cache();//缓存
	}
	

	//新的SQL
	$sql = sreadfile($sqlfile);
	preg_match_all("/CREATE\s+TABLE\s+uchome\_(.+?)\s+\((.+?)\)\s+(TYPE|ENGINE)\=/is", $sql, $matches);
	$newtables = empty($matches[1])?array():$matches[1];
	$newsqls = empty($matches[0])?array():$matches[0];
	if(empty($newtables) || empty($newsqls)) {
		show_msg('最新的SQL不存在,请先将最新的数据库结构文件 install.sql 已经上传到 ./data 目录下面后，再运行本升级程序');
	}

	//升级表
	$i = empty($_GET['i'])?0:intval($_GET['i']);
	$count_i = count($newtables);
	if($i>=$count_i) {
		//处理完毕
		show_msg('数据库结构升级完毕，进入下一步数据升级操作', 'update.php?step=data', 1);
	}
	//当前处理表
	$newtable = $newtables[$i];
	$newcols = getcolumn($newsqls[$i]);

	//获取当前SQL
	if(!$query = $_SGLOBAL['db']->query("SHOW CREATE TABLE ".tname($newtable), 'SILENT')) {
		//添加表
		preg_match("/(CREATE TABLE .+?)\s+[TYPE|ENGINE]+\=/is", $newsqls[$i], $maths);
		if(strpos($newtable, 'session')) {
			$type = mysql_get_server_info() > '4.1' ? " ENGINE=MEMORY".(empty($_SC['dbcharset'])?'':" DEFAULT CHARSET=$_SC[dbcharset]" ): " TYPE=HEAP";
		} else {
			$type = mysql_get_server_info() > '4.1' ? " ENGINE=MYISAM".(empty($_SC['dbcharset'])?'':" DEFAULT CHARSET=$_SC[dbcharset]" ): " TYPE=MYISAM";
		}
		$usql = $maths[1].$type;
		$usql = str_replace("CREATE TABLE uchome_", 'CREATE TABLE '.$_SC['tablepre'], $usql);
		if(!$_SGLOBAL['db']->query($usql, 'SILENT')) {
			show_msg('添加表 '.tname($newtable).' 出错,请手工执行以下SQL语句后,再重新运行本升级程序:<br><br>'.shtmlspecialchars($usql));
		} else {
			$msg = '添加表 '.tname($newtable).' 完成';
		}
	} else {
		$value = $_SGLOBAL['db']->fetch_array($query);
		$oldcols = getcolumn($value['Create Table']);

		//获取升级SQL文
		$updates = array();
		foreach ($newcols as $key => $value) {
			if($key == 'PRIMARY') {
				if($value != $oldcols[$key]) {
					if(!empty($oldcols[$key])) $updates[] = "DROP PRIMARY KEY";
					$updates[] = "ADD PRIMARY KEY $value";
				}
			} elseif ($key == 'KEY') {
				foreach ($value as $subkey => $subvalue) {
					if(!empty($oldcols['KEY'][$subkey])) {
						if($subvalue != $oldcols['KEY'][$subkey]) {
							$updates[] = "DROP INDEX `$subkey`";
							$updates[] = "ADD INDEX `$subkey` $subvalue";
						}
					} else {
						$updates[] = "ADD INDEX `$subkey` $subvalue";
					}
				}
			} else {
				if(!empty($oldcols[$key])) {
					if(str_replace('mediumtext', 'text', $value) != str_replace('mediumtext', 'text', $oldcols[$key])) {
						$updates[] = "CHANGE `$key` `$key` $value";
					}
				} else {
					$updates[] = "ADD `$key` $value";
				}
			}
		}

		//升级处理
		if(!empty($updates)) {
			$usql = "ALTER TABLE ".tname($newtable)." ".implode(', ', $updates);
			if(!$_SGLOBAL['db']->query($usql, 'SILENT')) {
				show_msg('升级表 '.tname($newtable).' 出错,请手工执行以下升级语句后,再重新运行本升级程序:<br><br><b>升级SQL语句</b>:<div style=\"position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;\">'.shtmlspecialchars($usql)."</div><br><b>Error</b>: ".$_SGLOBAL['db']->error()."<br><b>Errno.</b>: ".$_SGLOBAL['db']->errno());
			} else {
				$msg = '升级表 '.tname($newtable).' 完成';
			}
		} else {
			$msg = '检查表 '.tname($newtable).' 完成，不需升级';
		}
	}

	//处理下一个
	$next = 'update.php?step=sql&i='.($_GET['i']+1);
	show_msg("[ $i / $count_i ] ".$msg, $next);

} elseif ($_GET['step'] == 'data') {
	
	if(empty($_GET['op'])) $_GET['op'] = 'threadnum';
	
	if($_GET['op'] == 'threadnum') {
		
		//1.5->2.0
		$nextop = 'postnum';
		
		//群组话题数
		$perpage = 100;
		$start = empty($_GET['start'])?0:intval($_GET['start']);
		
		include_once(S_ROOT.'./source/function_stat.php');
		if($_SCONFIG['update_release'] < '20090501' && mtag_threadnum_stat($start, $perpage)) {
			show_msg("[数据升级] 群组话题数 ( $start ) 完毕，继续进入下一批", 'update.php?step=data&op='.$_GET['op'].'&start='.($start+$perpage));
		} else {
			show_msg("[数据升级] 群组话题数 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		}
		
	} elseif($_GET['op'] == 'postnum') {
		
		//1.5->2.0
		$nextop = 'trace';
		
		//群组回帖数
		$perpage = 100;
		$start = empty($_GET['start'])?0:intval($_GET['start']);
		
		include_once(S_ROOT.'./source/function_stat.php');
		if($_SCONFIG['update_release'] < '20090501' && mtag_postnum_stat($start, $perpage)) {
			show_msg("[数据升级] 群组回帖数 ( $start ) 完毕，继续进入下一批", 'update.php?step=data&op='.$_GET['op'].'&start='.($start+$perpage));
		} else {
			show_msg("[数据升级] 群组回帖数 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		}
		
	} elseif($_GET['op'] == 'trace') {
		
		$nextop = 'userappfeild';
		
		//踩一脚升级数据
		//1.5->2.0
		$inserts = $uas = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('trace')." LIMIT 0,1000", 'SILENT');
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value = saddslashes($value);
			$uas[] = "{$value[uid]}-{$value[blogid]}";
			$inserts[] = "('$value[uid]','$value[username]','$value[blogid]','blogid','1','$value[dateline]')";
			$_SGLOBAL['db']->query("DELETE FROM ".tname('trace')." WHERE uid='$value[uid]' AND blogid='$value[blogid]'");
		}
		if($inserts) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('clickuser')." (uid,username,id,idtype,clickid,dateline) VALUES ".implode(',', $inserts));
			show_msg("[数据升级] 踩一脚 ( ".implode(',', $uas)." ) 完毕，继续进入下一批", 'update.php?step=data&op='.$_GET['op']);
		} else {
			show_msg("[数据升级] 踩一脚 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		}
	
	} elseif($_GET['op'] == 'userappfeild') {
		
		$nextop = 'moderator';
		
		//用户userappfeild处理
		//1.5->2.0
		$inserts = $uas = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('userapp')." WHERE (profilelink !='' OR myml !='') LIMIT 0,100", 'SILENT');
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value = saddslashes($value);
			$uas[] = "{$value[uid]}-{$value[appid]}";
			$inserts[] = "('$value[uid]','$value[appid]','$value[profilelink]','$value[myml]')";
			updatetable('userapp', array('profilelink'=>'', 'myml'=>''), array('uid'=>$value['uid'], 'appid'=>$value['appid']));
		}
		if($inserts) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('userappfield')." (uid,appid,profilelink,myml) VALUES ".implode(',', $inserts));
			//跳入下一个处理
			show_msg("[数据升级] 用户应用 ( ".implode(',', $uas)." ) 完毕，继续进入下一批", 'update.php?step=data&op='.$_GET['op']);
		} else {
			show_msg("[数据升级] 用户应用 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		}
		
	} elseif($_GET['op'] == 'moderator') {
		
		$nextop = 'email';
		
		//数据处理
		//1.2->1.5 处理群主问题
		$query = $_SGLOBAL['db']->query("SELECT tagid,tagname,moderator FROM ".tname('mtag')." WHERE moderator!='' LIMIT 0,1", 'SILENT');
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			$moderatorarr = explode("\t", $value['moderator']);
			$moderatorarr = saddslashes($moderatorarr);
			$m_query = $_SGLOBAL['db']->query("SELECT uid,username FROM ".tname('space')." WHERE username IN (".simplode($moderatorarr).")");
			while ($m_value = $_SGLOBAL['db']->fetch_array($m_query)) {
				$grade = $moderatorarr[0] == $m_value['username'] ? 9 : 8;
				updatetable('tagspace', array('grade'=>$grade), array('tagid'=>$value['tagid'], 'uid'=>$m_value['uid']));
			}
			updatetable('mtag', array('moderator'=>''), array('tagid'=>$value['tagid']));
			//跳入下一个处理
			show_msg("[数据升级] 群组群主 ( $value[tagname] ) 完毕，继续进入下一批", 'update.php?step=data&op='.$_GET['op']);
		} else {
			show_msg("[数据升级] 群组群主 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		}
		
	} elseif($_GET['op'] == 'email') {
		
		$nextop = 'tracenum';
		
		//邮件处理
		//1.5RC2->1.5正式版
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mailqueue')." WHERE cid='0' LIMIT 0,1", 'SILENT');
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($value['email']) {
				//检查
				$value['email'] = addslashes($value['email']);
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mailcron')." WHERE email='$value[email]' LIMIT 1");
				if($v = $_SGLOBAL['db']->fetch_array($query)) {
					updatetable('mailqueue', array('cid'=>$v['cid']), array('email'=>$value['email']));
				}
			} elseif ($value['touid']) {
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mailcron')." WHERE touid='$value[touid]' LIMIT 1");
				if($v = $_SGLOBAL['db']->fetch_array($query)) {
					$spacemail = getcount('spacefield', array('uid'=>$value['touid']), 'email');
					if($spacemail) {
						$spacemail = addslashes($spacemail);
						updatetable('mailcron', array('email'=>$spacemail), array('cid'=>$v['cid']));
						updatetable('mailqueue', array('cid'=>$v['cid']), array('touid'=>$value['touid']));
					}
				}
			}
			//跳入下一个处理
			show_msg("[数据升级] 邮件队列 ( $value[email] $value[touid] ) 完毕，继续进入下一批", 'update.php?step=data&op='.$_GET['op']);
		} else {
			show_msg("[数据升级] 邮件队列 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		}
		
	} elseif($_GET['op'] == 'tracenum') {
		
		$nextop = 'cron';
		
		//更新踩一脚数据统计
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('blog')." WHERE click_1>0 LIMIT 1", 'SILENT');
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('blog')." SET click_1=tracenum", 'SILENT');
			$_SGLOBAL['db']->query("UPDATE ".tname('usergroup')." SET allowclick=allowtrace", 'SILENT');
		}
		
		show_msg("[数据升级] 踩一脚数据统计 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'cron') {
		
		$nextop = 'task';
		
		//1.0->1.2 计划任务
		$datas = array();
		$query = $_SGLOBAL['db']->query("SELECT type FROM ".tname('cron')." WHERE filename='cleantrace.php' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$datas[] = "1, 'system', '清理脚印和最新访客', 'cleantrace.php', $_SGLOBAL[timestamp], $_SGLOBAL[timestamp], -1, -1, 2, '3'";
		}
		if($datas) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('cron')." (available, type, name, filename, lastrun, nextrun, weekday, day, hour, minute) VALUES (".implode('),(', $datas).")");
		}
	
		//1.5RC1->1.5RC2 更改sendmail
		$_SGLOBAL['db']->query("DELETE FROM ".tname('cron')." WHERE filename='sendmail.php'");
		
		show_msg("[数据升级] 计划任务 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'task') {
		
		$nextop = 'usergroup';
		
		//1.2->1.5 有奖活动
		$datas = $filenames = array();
		$query = $_SGLOBAL['db']->query("SELECT filename FROM ".tname('task'));
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$filenames[$value['filename']] = 1;
		}
		if(empty($filenames['avatar.php'])) $datas[] = "1, '更新一下自己的头像', '头像就是你在这里的个人形象。<br>设置自己的头像后，会让更多的朋友记住您。', 'avatar.php', 1, '', 0, 20, 'image/task/avatar.gif'";
		if(empty($filenames['profile.php'])) $datas[] = "1, '将个人资料补充完整', '把自己的个人资料填写完整吧。<br>这样您会被更多的朋友找到的，系统也会帮您找到朋友。', 'profile.php', 2, '', 0, 20, 'image/task/profile.gif'";
		if(empty($filenames['blog.php'])) $datas[] = "1, '发表自己的第一篇日志', '现在，就写下自己的第一篇日志吧。<br>与大家一起分享自己的生活感悟。', 'blog.php', 3, '', 0, 5, 'image/task/blog.gif'";
		if(empty($filenames['friend.php'])) $datas[] = "1, '寻找并添加五位好友', '有了好友，您发的日志、图片等会被好友及时看到并传播出去；<br>您也会在首页方便及时的看到好友的最新动态。', 'friend.php', 4, '', 0, 50, 'image/task/friend.gif'";
		if(empty($filenames['email.php'])) $datas[] = "1, '验证激活自己的邮箱', '填写自己真实的邮箱地址并验证通过。<br>您可以在忘记密码的时候使用该邮箱取回自己的密码；<br>还可以及时接受站内的好友通知等等。', 'email.php', 5, '', 0, 10, 'image/task/email.gif'";
		if(empty($filenames['invite.php'])) $datas[] = "1, '邀请10个新朋友加入', '邀请一下自己的QQ好友或者邮箱联系人，让亲朋好友一起来加入我们吧。', 'invite.php', 6, '', 0, 100, 'image/task/friend.gif'";
		if(empty($filenames['gift.php'])) $datas[] = "1, '领取每日访问大礼包', '每天登录访问自己的主页，就可领取大礼包。', 'gift.php', 99, 'day', 0, 5, 'image/task/gift.gif'";
	
		if($datas) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('task')." (`available`, `name`, `note`, `filename`, `displayorder`, `nexttype`, `nexttime`, `credit`, `image`) VALUES (".implode('),(', $datas).")");
		}
		
		//更新每日礼包
		if($filenames['gift.php']['nexttype'] != 'day') {
			updatetable('task', array('nexttype'=>'day', 'nexttime'=>0), array('filename'=>'gift.php'));
		}
	
		show_msg("[数据升级] 有奖活动 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'usergroup') {
		
		$nextop = 'config';
		
		//1.2->1.5用户组
		$query = $_SGLOBAL['db']->query("SELECT gid FROM ".tname('usergroup')." WHERE allowpoke='1' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('usergroup')." SET allowcss=allowhtml,allowpoke=allowpost,allowfriend=allowpost,allowtrace=allowpost");
		}
		//1.5rc2->1.5
		$query = $_SGLOBAL['db']->query("SELECT gid FROM ".tname('usergroup')." WHERE allowmtag='1' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('usergroup')." SET allowmtag=allowthread,managereport=manageconfig");
		}
		
		//1.5->2.0 用户组数据升级
		$query = $_SGLOBAL['db']->query("SELECT gid FROM ".tname('usergroup')." WHERE managetopic='1' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('usergroup')." SET
				explower=creditlower,
				allowmagic=allowblog,
				allowpoll=allowblog,
				allowevent=allowblog,
				allowtopic=manageconfig,
				allowstat=manageconfig,
				spamignore=manageblog,
				managepoll=manageblog,
				manageevent=manageblog,
				manageeventclass=manageconfig,
				managemagic=manageconfig,
				managemagiclog=manageconfig,
				managetopic=manageconfig,
				managespacegroup=manageconfig,
				managespaceinfo=manageconfig,
				managespacecredit=manageconfig,
				managespacenote=manageconfig,
				manageip=manageconfig,
				managehotuser=manageconfig,
				videophotoignore=manageconfig,
				searchignore=manageconfig,
				allowpm=allowblog,
				allowclick=allowblog,
				allowviewvideopic=manageblog,
				allowmyop=allowblog,
				managebatch=manageblog,
				managedefaultuser=manageconfig,
				manageclick=manageconfig,
				managedelspace=manageconfig,
				managevideophoto=manageconfig,
				managelog=manageconfig");
			
			$datas = array();
			//增加禁止访问用户组
			$datas = array(
				'grouptitle' => '禁止访问',
				'system' => -1,
				'banvisit' => 1
			);
			inserttable('usergroup', $datas);
		}
		
		//添加禁止发言用户组
		$query = $_SGLOBAL['db']->query("SELECT gid FROM ".tname('usergroup')." WHERE system='-1' AND grouptitle='禁止发言' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$datas = array();
			//增加禁止访问用户组
			$datas = array(
				'grouptitle' => '禁止发言',
				'system' => -1,
				'maxattachsize' => 1,
				'maxfriendnum' => 1,
				'postinterval' => 9999,
				'searchinterval' => 9999,
				'domainlength' => 99,
				'seccode' => 1,
				'verifyevent' => 1
			);
			inserttable('usergroup', $datas);
		}

		//1.5->2.0 切换经验值
		$query = $_SGLOBAL['db']->query("SELECT credit FROM ".tname('space')." WHERE experience > 0 LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET experience=credit");
		}
		
		//1.5正式版，用户组图标
		$query = $_SGLOBAL['db']->query("SELECT gid FROM ".tname('usergroup')." WHERE icon!='' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('usergroup')." SET color='red',icon='image/group/admin.gif' WHERE manageconfig='1'");
		}

		show_msg("[数据升级] 用户组 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'config') {
		
		$nextop = 'credit';
		
		//1.2->1.5rc2
		$datas = array();
		if(empty($_SCONFIG['feedfilternum'])) {
			$datas[] = "('feedfilternum', '10')";
		}
		
		//1.5->2.0 统计
		if(!isset($_SCONFIG['updatestat'])) {
			$datas[] = "('updatestat', '1')";
		}
		
		//1.5->2.0 热点
		if(empty($_SCONFIG['feedhotday'])) {
			$datas[] = "('feedhiddenicon', 'friend,profile,task,wall')";
			$datas[] = "('maxreward', '10')";
			$datas[] = "('feedhotday', '2')";
			$datas[] = "('feedhotnum', '3')";
			$datas[] = "('feedhotmin', '3')";
		}
		
		//隐私
		//1.5->2.0
		if(!isset($_SCONFIG['privacy']['feed']['poll'])) {
			$newfeed = array('poll', 'joinpoll', 'event', 'join', 'show', 'credit', 'spaceopen', 'invite', 'task', 'profile', 'click');
			foreach($newfeed as $key => $val) {
				$_SCONFIG['privacy']['feed'][$val] = 1;
			}
			$newview = array('poll', 'event');
			foreach($newview as $key => $val) {
				$_SCONFIG['privacy']['view'][$val] = 0;
			}
			$datas[] = "('privacy', '".serialize($_SCONFIG['privacy'])."')";
		}
		
		//1.5->2.0 礼物
		if(!isset($_SCONFIG['my_showgift'])) {
			$datas[] = "('my_showgift', '1')";
		}
		
		//2.0alpha->2.0 最新成员
		if(!isset($_SCONFIG['newspacenum'])) {
			$datas[] = "('newspacenum', '3')";
		}
		//排行榜缓存
		if(!isset($_SCONFIG['topcachetime'])) {
			$datas[] = "('topcachetime', '60')";
		}
		
		if($datas) {
			$_SGLOBAL['db']->query("REPLACE INTO ".tname('config')." (var, datavalue) VALUES ".implode(',', $datas));
		}
		
		show_msg("[数据升级] 站点配置 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'credit') {
		
		$nextop = 'eventclass';
		
		//1.5->2.0 升级积分规则
		$ruls = array();
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('creditrule')." WHERE action='register' LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			//加积分
			$ruls[] = "('开通空间', 'register', '0', '0', '1', '1', '10', '0', '0')";
			$ruls[] = "('实名认证', 'realname', '0', '0', '1', '1', '20', '0', '20')";
			$ruls[] = "('邮箱认证', 'realemail', '0', '0', '1', '1', '40', '0', '40')";
			$ruls[] = "('成功邀请好友', 'invitefriend', '4', '0', '20', '1', '10', '0', '10')";
			$ruls[] = "('设置头像', 'setavatar', '0', '0', '1', '1', '15', '0', '15')";
			$ruls[] = "('视频认证', 'videophoto', '0', '0', '1', '1', '40', '0', '40')";
			$ruls[] = "('成功举报', 'report', '4', '0', '0', '1', '2', '0', '2')";
			$ruls[] = "('更新心情', 'updatemood', '1', '0', '3', '1', '3', '0', '3')";
			$ruls[] = "('热点信息', 'hotinfo', '4', '0', '0', '1', '10', '0', '10')";
			$ruls[] = "('每天登陆', 'daylogin', '1', '0', '1', '1', '15', '0', '15')";
			$ruls[] = "('访问别人空间', 'visit', '1', '0', '10', '1', '1', '2', '1')";
			$ruls[] = "('打招呼', 'poke', '1', '0', '10', '1', '1', '2', '1')";
			$ruls[] = "('留言', 'guestbook', '1', '0', '20', '1', '2', '2', '2')";
			$ruls[] = "('被留言', 'getguestbook', '1', '0', '5', '1', '1', '2', '0')";
			$ruls[] = "('发表记录', 'doing', '1', '0', '5', '1', '1', '0', '1')";
			$ruls[] = "('发表日志', 'publishblog', '1', '0', '3', '1', '5', '0', '5')";
			$ruls[] = "('上传图片', 'uploadimage', '1', '0', '10', '1', '2', '0', '2')";
			$ruls[] = "('拍大头贴', 'camera', '1', '0', '5', '1', '3', '0', '3')";
			$ruls[] = "('发表话题', 'publishthread', '1', '0', '5', '1', '5', '0', '5')";
			$ruls[] = "('回复话题', 'replythread', '1', '0', '10', '1', '1', '1', '1')";
			$ruls[] = "('创建投票', 'createpoll', '1', '0', '5', '1', '2', '0', '2')";
			$ruls[] = "('参与投票', 'joinpoll', '1', '0', '10', '1', '1', '1', '1')";
			$ruls[] = "('发起活动', 'createevent', '1', '0', '1', '1', '3', '0', '3')";
			$ruls[] = "('参与活动', 'joinevent', '1', '0', '1', '1', '1', '1', '1')";
			$ruls[] = "('推荐活动', 'recommendevent', '4', '0', '0', '1', '10', '0', '10')";
			$ruls[] = "('发起分享', 'createshare', '1', '0', '3', '1', '2', '0', '2')";
			$ruls[] = "('评论', 'comment', '1', '0', '40', '1', '1', '1', '1')";
			$ruls[] = "('被评论', 'getcomment', '1', '0', '20', '1', '1', '1', '0')";
			$ruls[] = "('安装应用', 'installapp', '4', '0', '0', '1', '5', '3', '5')";
			$ruls[] = "('使用应用', 'useapp', '1', '0', '10', '1', '1', '3', '1')";
			$ruls[] = "('信息表态', 'click', '1', '0', '10', '1', '1', '1', '1')";
	
			//扣积分
			$ruls[] = "('修改实名', 'editrealname', '0', '0', '1', '0', '5', '0', '0')";
			$ruls[] = "('更改邮箱认证', 'editrealemail', '0', '0', '1', '0', '5', '0', '0')";
			$ruls[] = "('头像被删除', 'delavatar', '0', '0', '1', '0', '10', '0', '10')";
			$ruls[] = "('获取邀请码', 'invitecode', '0', '0', '1', '0', '0', '0', '0')";
			$ruls[] = "('搜索一次', 'search', '0', '0', '1', '0', '1', '0', '0')";
			$ruls[] = "('日志导入', 'blogimport', '0', '0', '1', '0', '10', '0', '0')";
			$ruls[] = "('修改域名', 'modifydomain', '0', '0', '1', '0', '5', '0', '0')";
			$ruls[] = "('日志被删除', 'delblog', '0', '0', '1', '0', '10', '0', '10')";
			$ruls[] = "('记录被删除', 'deldoing', '0', '0', '1', '0', '2', '0', '2')";
			$ruls[] = "('图片被删除', 'delimage', '0', '0', '1', '0', '4', '0', '4')";
			$ruls[] = "('投票被删除', 'delpoll', '0', '0', '1', '0', '4', '0', '4')";
			$ruls[] = "('话题被删除', 'delthread', '0', '0', '1', '0', '4', '0', '4')";
			$ruls[] = "('活动被删除', 'delevent', '0', '0', '1', '0', '6', '0', '6')";
			$ruls[] = "('分享被删除', 'delshare', '0', '0', '1', '0', '4', '0', '4')";
			$ruls[] = "('留言被删除', 'delguestbook', '0', '0', '1', '0', '4', '0', '4')";
			$ruls[] = "('评论被删除', 'delcomment', '0', '0', '1', '0', '2', '0', '2')";
	
			$_SGLOBAL['db']->query("INSERT INTO ".tname('creditrule')." (`rulename`, `action`, `cycletype`, `cycletime`, `rewardnum`, `rewardtype`, `credit`, `norepeat`, `experience`) VALUES ".implode(',', $ruls));
		}
		
		show_msg("[数据升级] 积分规则 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'eventclass') {
		
		$nextop = 'report';
		
		//1.5->2.0 活动
		$query = $_SGLOBAL['db']->query("SELECT classid FROM ".tname('eventclass')." LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$datas = array(
				"1, '生活/聚会', 0, '费用说明：\r\n集合地点：\r\n着装要求：\r\n联系方式：\r\n注意事项：', 1",
				"2, '出行/旅游', 0, '路线说明:\r\n费用说明:\r\n装备要求:\r\n交通工具:\r\n集合地点:\r\n联系方式:\r\n注意事项:', 2",
				"3, '比赛/运动', 0, '费用说明：\r\n集合地点：\r\n着装要求：\r\n场地介绍：\r\n联系方式：\r\n注意事项：', 4",
				"4, '电影/演出', 0, '剧情介绍：\r\n费用说明：\r\n集合地点：\r\n联系方式：\r\n注意事项：', 3",
				"5, '教育/讲座', 0, '主办单位：\r\n活动主题：\r\n费用说明：\r\n集合地点：\r\n联系方式：\r\n注意事项：', 5",
				"6, '其它', 0, '', 6"
			);	
			$_SGLOBAL['db']->query("INSERT INTO ".tname('eventclass')." (classid, classname, poster, template, displayorder) VALUES (".implode('),(', $datas).")");
		}
		
		show_msg("[数据升级] 活动分类 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'report') {
		
		$nextop = 'click';
		
		//1.5->2.0 升级举报
		$idtype = array(
			'albumid' => 'album',
			'blogid' => 'blog',
			'tagid' => 'mtag',
			'tid' => 'thread',
			'uid' => 'space',
			'pid' => 'poll',
			'eventid' => 'event',
			'sid' => 'share'
		);
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('report')." WHERE idtype IN(".simplode($idtype).")"), 0);
		if($count) {
			foreach($idtype as $key => $val) {
				$_SGLOBAL['db']->query("UPDATE ".tname('report')." SET idtype='$key' WHERE idtype='$val'");
			}
		}
		
		show_msg("[数据升级] 举报 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'click') {
		
		$nextop = 'magic';
		
		//1.5->2.0 表态
		$query = $_SGLOBAL['db']->query("SELECT clickid FROM ".tname('click')." LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$datas = array(
				"'1', '路过', 'luguo.gif', 'blogid'",
				"'2', '雷人', 'leiren.gif', 'blogid'",
				"'3', '握手', 'woshou.gif', 'blogid'",
				"'4', '鲜花', 'xianhua.gif', 'blogid'",
				"'5', '鸡蛋', 'jidan.gif', 'blogid'",
				
				"'6', '漂亮', 'piaoliang.gif', 'picid'",
				"'7', '酷毙', 'kubi.gif', 'picid'",
				"'8', '雷人', 'leiren.gif', 'picid'",
				"'9', '鲜花', 'xianhua.gif', 'picid'",
				"'10', '鸡蛋', 'jidan.gif', 'picid'",
				
				"'11', '搞笑', 'gaoxiao.gif', 'tid'",
				"'12', '迷惑', 'mihuo.gif', 'tid'",
				"'13', '雷人', 'leiren.gif', 'tid'",
				"'14', '鲜花', 'xianhua.gif', 'tid'",
				"'15', '鸡蛋', 'jidan.gif', 'tid'"
			);	
			$_SGLOBAL['db']->query("INSERT INTO ".tname('click')." (clickid, `name`, icon, idtype) VALUES (".implode('),(', $datas).")");
		}
		
		show_msg("[数据升级] 表态 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'magic') {
		
		$nextop = 'network';
		
		$datas = $magics = array();
		$query = $_SGLOBAL['db']->query("SELECT mid FROM ".tname('magic'));
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$magics[$value['mid']] = 1;
		}
		
		//1.5->2.0 道具
		if(empty($magics['invisible'])) $datas[] = "'invisible', '隐身草', '让自己隐身登录，不显示在线，24小时内有效', '5', '50', '86400', '10', '86400', '1'";
		if(empty($magics['friendnum'])) $datas[] = "'friendnum', '好友增容卡', '在允许添加的最多好友数限制外，增加10个好友名额', '3', '30', '86400', '999', '0', '1'";
		if(empty($magics['attachsize'])) $datas[] = "'attachsize', '附件增容卡', '使用一次，可以给自己增加 10M 的附件空间', '3', '30', '86400', '999', '0', '1'";
		if(empty($magics['thunder'])) $datas[] = "'thunder', '雷鸣之声', '发布一条全站信息，让大家知道自己上线了', '5', '500', '86400', '5', '86400', '1'";
		if(empty($magics['updateline'])) $datas[] = "'updateline', '救生圈', '把指定对象的发布时间更新为当前时间', '5', '200', '86400', '999', '0', '1'";
		
		if(empty($magics['downdateline'])) $datas[] = "'downdateline', '时空机', '把指定对象的发布时间修改为过去的时间', '5', '250', '86400', '999', '0', '1'";		
		if(empty($magics['color'])) $datas[] = "'color', '彩色灯', '把指定对象的标题变成彩色的', '5', '50', '86400', '999', '0', '1'";
		if(empty($magics['thunder'])) $datas[] = "'hot', '热点灯', '把指定对象的热度增加站点推荐的热点值', '5', '50', '86400', '999', '0', '1'";
		if(empty($magics['visit'])) $datas[] = "'visit', '互访卡', '随机选择10个好友，向其打招呼、留言或访问空间', '2', '20', '86400', '999', '0', '1'";
		if(empty($magics['icon'])) $datas[] = "'icon', '彩虹蛋', '给指定对象的标题前面增加图标（最多8个图标）', '2', '20', '86400', '999', '0', '1'";
		
		if(empty($magics['flicker'])) $datas[] = "'flicker', '彩虹炫', '让评论、留言的文字闪烁起来', '3', '30', '86400', '999', '0', '1'";
		if(empty($magics['gift'])) $datas[] = "'gift', '红包卡', '在自己的空间埋下积分红包送给来访者', '2', '20', '86400', '999', '0', '1'";
		if(empty($magics['superstar'])) $datas[] = "'superstar', '超级明星', '在个人主页，给自己的头像增加超级明星标识', '3', '30', '86400', '999', '0', '1'";
		if(empty($magics['viewmagiclog'])) $datas[] = "'viewmagiclog', '八卦镜', '查看指定用户最近使用的道具记录', '5', '100', '86400', '999', '0', '1'";
		if(empty($magics['viewmagic'])) $datas[] = "'viewmagic', '透视镜', '查看指定用户当前持有的道具', '5', '100', '86400', '999', '0', '1'";
		
		if(empty($magics['viewvisitor'])) $datas[] = "'viewvisitor', '偷窥镜', '查看指定用户最近访问过的10个空间', '5', '100', '86400', '999', '0', '1'";
		if(empty($magics['call'])) $datas[] = "'call', '点名卡', '发通知给自己的好友，让他们来查看指定的对象', '5', '50', '86400', '999', '0', '1'";
		if(empty($magics['coupon'])) $datas[] = "'coupon', '代金券','购买道具时折换一定量的积分', '0', '0', '0', '0', '0', '1'";
		if(empty($magics['frame'])) $datas[] = "'frame', '相框', '给自己的照片添上相框', '3', '30', '86400', '999', '0', '1'";
		if(empty($magics['bgimage'])) $datas[] = "'bgimage', '信纸', '给指定的对象添加信纸背景', '3', '30', '86400', '999', '0', '1'";
		
		if(empty($magics['doodle'])) $datas[] = "'doodle', '涂鸦板', '允许在留言、评论等操作时使用涂鸦板', '3', '30', '86400', '999', '0', '1'";
		if(empty($magics['anonymous'])) $datas[] = "'anonymous', '匿名卡', '在指定的地方，让自己的名字显示为匿名', '5', '50', '86400', '999', '0', '1'";
		if(empty($magics['reveal'])) $datas[] = "'reveal', '照妖镜', '可以查看一次匿名用户的真实身份', '5', '100', '86400', '999', '0', '1'";
		if(empty($magics['license'])) $datas[] = "'license', '道具转让许可证', '使用许可证，将道具赠送给指定好友', '1', '10', '3600', '999', '0', '1'";
		if(empty($magics['detector'])) $datas[] = "'detector', '探测器', '探测埋了红包的空间', '1', '10', '86400', '999', '0', '1'";
		
		if($datas) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('magic')."(`mid`, `name`, `description`, `experience`, `charge`, `provideperoid`, `providecount`, `useperoid`, `usecount`) VALUES (".implode('),(', $datas).")");
		}

		show_msg("[数据升级] 道具 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'network') {
		
		$nextop = 'menuorder';
		
		//随便看看
		@include_once(S_ROOT.'./data/data_network.php');
		if(empty($_SGLOBAL['network']['blog']['order'])) {
			$network = array(
				'blog' => array('hot1'=>3, 'cache'=>600),
				'pic' => array('hot1'=>3, 'cache'=>700),
				'thread' => array('hot1'=>3, 'cache'=>800),
				'event' => array('cache'=>900),
				'poll' => array('cache'=>500),
			);
			data_set('network', $network);
		}
		
		show_msg("[数据升级] 随便看看配置 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} elseif($_GET['op'] == 'menuorder') {
		
		$nextop = 'end';
		
		//1.5->2.0 表态
		$query = $_SGLOBAL['db']->query("SELECT menuorder FROM ".tname('userapp')." WHERE menuorder>0 LIMIT 1");
		if(!$value = $_SGLOBAL['db']->fetch_array($query)) {
			$_SGLOBAL['db']->query("UPDATE ".tname('userapp')." SET menuorder=displayorder");
		}
		
		show_msg("[数据升级] 用户应用菜单顺序 全部结束，进入下一步", 'update.php?step=data&op='.$nextop);
		
	} else {
		//结束
		$next = 'update.php?step=delete';
		show_msg("数据库数据升级完毕，进入下一步数据库结构清理操作", $next);
	}
	
}elseif ($_GET['step'] == 'delete') {

	//检查需要删除的字段
	//老表集合
	$oldtables = array();
	$query = $_SGLOBAL['db']->query("SHOW TABLES LIKE '$_SC[tablepre]%'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$values = array_values($value);
		if(!strexists($values[0], 'cache')) {
			$oldtables[] = $values[0];//分表、缓存
		}
	}

	//新表集合
	$sql = sreadfile($sqlfile);
	preg_match_all("/CREATE\s+TABLE\s+uchome\_(.+?)\s+\((.+?)\)\s+(TYPE|ENGINE)\=/is", $sql, $matches);
	$newtables = empty($matches[1])?array():$matches[1];
	$newsqls = empty($matches[0])?array():$matches[0];

	//需要删除的表
	$deltables = array();
	$delcolumns = array();

	//老的有，新的没有
	foreach ($oldtables as $tname) {
		$tname = substr($tname, strlen($_SC['tablepre']));
		if(in_array($tname, $newtables)) {
			//比较字段是否多余
			$query = $_SGLOBAL['db']->query("SHOW CREATE TABLE ".tname($tname));
			$cvalue = $_SGLOBAL['db']->fetch_array($query);
			$oldcolumns = getcolumn($cvalue['Create Table']);

			//新的
			$i = array_search($tname, $newtables);
			$newcolumns = getcolumn($newsqls[$i]);

			//老的有，新的没有的字段
			foreach ($oldcolumns as $colname => $colstruct) {
				if(!strexists($colname, 'field_') && !strexists($colname, 'click_')) {
					if($colname == 'PRIMARY') {
						//关键字
						if(empty($newcolumns[$colname])) {
							$delcolumns[$tname][] = 'PRIMARY';
						}
					} elseif($colname == 'KEY') {
						//索引
						foreach ($colstruct as $key_index => $key_value) {
							if(empty($newcolumns[$colname][$key_index])) {
								$delcolumns[$tname]['KEY'][$key_index] = $key_value;
							}
						}
					} else {
						//普通字段
						if(empty($newcolumns[$colname])) {
							$delcolumns[$tname][] = $colname;
						}
					}
				}
			}
		} else {
			$deltables[] = $tname;
		}
	}

	//显示
	show_header();
	echo '<form method="post" action="update.php?step=delete">';

	//删除表
	$deltablehtml = '';
	if($deltables) {
		$deltablehtml .= '<table>';
		foreach ($deltables as $tablename) {
			$deltablehtml .= "<tr><td><input type=\"checkbox\" name=\"deltables[$tablename]\" value=\"1\"></td><td>{$_SC['tablepre']}$tablename</td></tr>";
		}
		$deltablehtml .= '</table>';
		echo "<p>以下 数据表 与标准数据库相比是多余的:<br>您可以根据需要自行决定是否删除</p>$deltablehtml";
	}

	//删除字段
	$delcolumnhtml = '';
	if($delcolumns) {
		$delcolumnhtml .= '<table>';
		foreach ($delcolumns as $tablename => $cols) {
			foreach ($cols as $col) {
				if (is_array($col)) {
					foreach ($col as $index => $indexvalue) {
						$delcolumnhtml .= "<tr><td><input type=\"checkbox\" name=\"delcols[$tablename][KEY][$index]\" value=\"1\"></td><td>{$_SC['tablepre']}$tablename</td><td>索引 $index $indexvalue</td></tr>";
					}
				} elseif($col == 'PRIMARY') {
					$delcolumnhtml .= "<tr><td><input type=\"checkbox\" name=\"delcols[$tablename][PRIMARY]\" value=\"1\"></td><td>{$_SC['tablepre']}$tablename</td><td>主键 PRIMARY</td></tr>";
				} else {
					$delcolumnhtml .= "<tr><td><input type=\"checkbox\" name=\"delcols[$tablename][$col]\" value=\"1\"></td><td>{$_SC['tablepre']}$tablename</td><td>字段 $col</td></tr>";
				}
			}
		}
		$delcolumnhtml .= '</table>';

		echo "<p>以下 字段 与标准数据库相比是多余的:<br>您可以根据需要自行决定是否删除</p>$delcolumnhtml";
	}

	if(empty($deltables) && empty($delcolumns)) {
		echo "<p>与标准数据库相比，没有需要删除的数据表和字段</p><a href=\"update.php?step=cache\">请点击进入下一步站点缓存更新操作</a></p>";
	} else {
		echo "<p><input type=\"submit\" name=\"delsubmit\" value=\"提交删除\"></p><p>您也可以忽略多余的表和字段<br><a href=\"update.php?step=cache\">直接进入下一步站点缓存更新操作</a></p>";
	}
	echo '<input type="hidden" name="formhash" value="'.formhash().'"></form>';

	show_footer();
	exit();

}elseif ($_GET['step'] == 'cache') {
	
	//打开站点
	if($_SCONFIG['close']) {
		$_SGLOBAL['db']->query("REPLACE INTO ".tname('config')." (var, datavalue) VALUES ('close', '0')");
	}
	
	//更新此次升级数据库ver
	$_SGLOBAL['db']->query("REPLACE INTO ".tname('config')." (var, datavalue) VALUES ('update_release', '".X_RELEASE."')");
	
	//更新缓存
	include_once(S_ROOT.'./source/function_cache.php');

	//配置缓存
	config_cache();
	usergroup_cache();
	profilefield_cache();
	profield_cache();
	censor_cache();
	block_cache();
	eventclass_cache();
	magic_cache();
	click_cache();
	task_cache();
	ad_cache();
	creditrule_cache();
	userapp_cache();
	//app_cache();
	network_cache();
	
	//模板
	tpl_cache();
	
	//模块
	block_data_cache();
	
	//缓存文件
	$fiels = sreaddir(S_ROOT.'./data', array('txt'));
	foreach ($fiels as $value) {
		@unlink(S_ROOT.'./data/'.$value);
	}

	//写log
	if(@$fp = fopen($lockfile, 'w')) {
		fwrite($fp, 'UCenter Home');
		fclose($fp);
	}

	show_msg('恭喜，升级完成！注意：为了您的数据安全，请立即登录服务器FTP删除本升级文件!');
}


//正则匹配,获取字段/索引/关键字信息
function getcolumn($creatsql) {

	preg_match("/\((.+)\)/is", $creatsql, $matchs);

	$cols = explode("\n", $matchs[1]);
	$newcols = array();
	foreach ($cols as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		$value = remakesql($value);//特使字符替换
		if(substr($value, -1) == ',') $value = substr($value, 0, -1);//去掉末尾逗号

		$vs = explode(' ', $value);
		$cname = $vs[0];

		if(strtoupper($cname) == 'KEY') {
			$subvalue = trim(substr($value, 3));
			$subvs = explode(' ', $subvalue);
			$subcname = $subvs[0];
			$newcols['KEY'][$subcname] = trim(substr($value, (5+strlen($subcname))));
		} elseif(strtoupper($cname) == 'INDEX') {
			$subvalue = trim(substr($value, 5));
			$subvs = explode(' ', $subvalue);
			$subcname = $subvs[0];
			$newcols['KEY'][$subcname] = trim(substr($value, (7+strlen($subcname))));
		} elseif(strtoupper($cname) == 'PRIMARY') {
			$newcols['PRIMARY'] = trim(substr($value, 11));
		} else {
			$newcols[$cname] = trim(substr($value, strlen($cname)));
		}
	}
	return $newcols;
}

//整理sql文
function remakesql($value) {
	$value = trim(preg_replace("/\s+/", ' ', $value));//空格标准化
	$value = str_replace(array('`',', ', ' ,', '( ' ,' )'), array('', ',', ',','(',')'), $value);//去掉无用符号
	$value = preg_replace('/(text NOT NULL) default \'\'/i',"\\1", $value);//去掉无用符号
	return $value;
}

//显示
function show_msg($message, $url_forward='') {
	global $_SGLOBAL;

	obclean();

	if($url_forward) {
		$_SGLOBAL['extrahead'] = '<meta http-equiv="refresh" content="1; url='.$url_forward.'">';
		$message = "<a href=\"$url_forward\">$message(跳转中...)</a>";
	} else {
		$_SGLOBAL['extrahead'] = '';
	}

	show_header();
	print<<<END
	<table>
	<tr><td>$message</td></tr>
	</table>
END;
	show_footer();
	exit();
}


//页面头部
function show_header() {
	global $_SGLOBAL, $_SC;

	$nowarr = array($_GET['step'] => ' class="current"');

	if(empty($_SGLOBAL['extrahead'])) $_SGLOBAL['extrahead'] = '';

	print<<<END
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=$_SC[charset]" />
	$_SGLOBAL[extrahead]
	<title> UCenter Home 数据库升级程序 </title>
	<style type="text/css">
	* {font-size:12px; font-family: Verdana, Arial, Helvetica, sans-serif; line-height: 1.5em; word-break: break-all; }
	body { text-align:center; margin: 0; padding: 0; background: #F5FBFF; }
	.bodydiv { margin: 40px auto 0; width:720px; text-align:left; border: solid #86B9D6; border-width: 5px 1px 1px; background: #FFF; }
	h1 { font-size: 18px; margin: 1px 0 0; line-height: 50px; height: 50px; background: #E8F7FC; color: #5086A5; padding-left: 10px; }
	#menu {width: 100%; margin: 10px auto; text-align: center; }
	#menu td { height: 30px; line-height: 30px; color: #999; border-bottom: 3px solid #EEE; }
	.current { font-weight: bold; color: #090 !important; border-bottom-color: #F90 !important; }
	.showtable { width:100%; border: solid; border-color:#86B9D6 #B2C9D3 #B2C9D3; border-width: 3px 1px 1px; margin: 10px auto; background: #F5FCFF; }
	.showtable td { padding: 3px; }
	.showtable strong { color: #5086A5; }
	.datatable { width: 100%; margin: 10px auto 25px; }
	.datatable td { padding: 5px 0; border-bottom: 1px solid #EEE; }
	input { border: 1px solid #B2C9D3; padding: 5px; background: #F5FCFF; }
	.button { margin: 10px auto 20px; width: 100%; }
	.button td { text-align: center; }
	.button input, .button button { border: solid; border-color:#F90; border-width: 1px 1px 3px; padding: 5px 10px; color: #090; background: #FFFAF0; cursor: pointer; }
	#footer { font-size: 10px; line-height: 40px; background: #E8F7FC; text-align: center; height: 38px; overflow: hidden; color: #5086A5; margin-top: 20px; }
	</style>
	<script src="source/script_ajax.js" type="text/javascript" language="javascript"></script>
	<script src="source/script_common.js" type="text/javascript" language="javascript"></script>
	</head>
	<body>
	<div class="bodydiv">
	<h1>UCenter Home 数据库升级工具</h1>
	<div style="width:90%;margin:0 auto;">
	<table id="menu">
	<tr>
	<td{$nowarr[start]}>升级开始</td>
	<td{$nowarr[sql]}>数据库结构升级</td>
	<td{$nowarr[data]}>数据升级</td>
	<td{$nowarr[delete]}>数据库结构删除</td>
	<td{$nowarr[cache]}>升级完成</td>
	</tr>
	</table>
	<br>
END;
}

//页面顶部
function show_footer() {
	print<<<END
	</div>
	<div id="footer">&copy; Comsenz Inc. 2001-2009 http://u.discuz.net</div>
	</div>
	<br>
	</body>
	</html>
END;
}

function get_uc_root() {
	$uc_root = '';
	$uc = parse_url(UC_API);
	if($uc['host'] == $_SERVER['HTTP_HOST']) {
		$php_self_len = strlen($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
		$uc_root = substr(__FILE__, 0, -$php_self_len).$uc['path'];
	}
	return $uc_root;
}


?>
