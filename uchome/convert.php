<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: convert.php 10835 2008-12-26 10:01:40Z zhengqingpeng $
*/

error_reporting(7);

if(!@include('./common.php')) {
	exit('请将本文件移到程序根目录再运行!');
}

$formhash = formhash();

//每次处理个数
$perpage = 10;

$start = empty($_GET['start'])?0:intval($_GET['start']);
$turl = 'convert.php';
$set = array();
$tpre = '';

@include(S_ROOT.'./data/data_convert.php');
if($set) {
	$tpre = '`'.$set['dbname'].'`.'.$set['tablepre'];
}

$lockfile = './data/convert.lock';
if(file_exists($lockfile)) {
	show_msg('警告!您已经转换过数据<br>
		为了保证数据安全，请立即手动删除 convert.php 文件<br>
		如果您想重新转换数据，请删除 data/convert.lock 文件，再次运行本文件');
}

if(submitcheck('setsubmit')) {
	include_once(S_ROOT.'./source/function_cache.php');
	
	cache_write('convert', "set", $_POST['set']);
	
	show_msg('数据库保存完成', $turl.'?step=300');
	
} elseif(submitcheck('opensubmit')) {
	$uid = getcount('member', array('username'=>$_POST['username']), 'uid');
	if(!$uid) {
		show_msg('指定的用户名不存在，请谨慎填写管理员用户名', 'convert.php?step=18');
	}
	
	//写log
	if(@$fp = fopen($lockfile, 'w')) {
		fwrite($fp, 'UCenter Home');
		fclose($fp);
	}
	updatetable('space', array('groupid'=>1), array('uid'=>$uid));
	show_msg('设置管理员成功，数据转换全部完成!<br>
		<font color=blue>请立即删除本转换文件!</font><br>
		<a href="space.php">登录全新的 UCenter Home 吧</a><br>
		登录后，推荐你进行一下操作：<br>
		进入 管理平台 => 统计更新 页面，对各项统计数据进行一下重新统计。');
}

//处理开始
if(empty($_GET['step'])) {
	//开始
	$_GET['step'] = 0;
	
	show_msg("<span style=\"font-size:14px;\">本程序会将X-Space 3.x/4.x系列转换到 UCenter Home。</span><br><br>
		<strong>数据转换说明，请务必阅读</strong><br>
		由于UCenter Home与SupeSite/X-Space功能的不同，本转换程序，只会进行以下数据信息的转换，其他数据不做转换，请认真了解：<br><br>
		<table width=\"100%\" class=\"datatable\">
		<tr style=\"font-weight:bold;\"><td width=\"100\">X-Space</td><td width=\"100\">UCenter Home</td><td>备注</td></tr>
		<tr><td>用户空间数据</td><td>用户空间数据</td><td>包括空间查看数、创建时间、积分、附件使用大小、居住城市</td></tr>
		<tr><td>日志</td><td>日志</td></td><td>其中，日志中的自定义字段不能转换</td></tr>
		<tr><td>图片主题</td><td>日志</td><td>图片主题中的图片、介绍等构成这篇日志的内容。同时，图片全部转换到用户的默认相册里面</td></tr>
		<tr><td>文件</td><td>日志</td><td>文件的介绍、下载地址等构成这篇日志的内容</td></tr>
		<tr><td>商品</td><td>日志</td><td>商品价格、数量、介绍等构成这篇日志的内容</td></tr>
		<tr><td>视频</td><td>日志</td><td>视频的播放器、视频介绍等构成这篇日志的内容</td></tr>
		<tr><td>书签</td><td>日志</td><td>书签的链接、书签的介绍等沟通这篇日志的内容</td></tr>
		<tr><td>好友</td><td>好友</td><td>好友转换后为申请好友状态，需要对方批准一下</td></tr>
		<tr><td>留言</td><td>留言</td><td>其中，悄悄话变为公开的</td></tr>
		<tr><td>日志分类</td><td>日志分类</td></td><td>&nbsp;</td></tr>
		<tr><td>评论</td><td>评论</td></td><td>&nbsp;</td></tr>
		</table>
		<br>
		<strong>不做转换的数据</strong>：<br>
		SupeSite/X-Space中的广告、公告、系统分类、频道、模型、模块、收藏、友情链接、投票、资讯、采集器、专题、用户风格、脚印等数据不会做转换。
		<br><br>
		
		根据您的站点规模，转换数据可能需要数分钟，甚至几小时的时间<br>在此期间，请关闭X-Space站点，禁止访问，并耐心等候!<br><br>
		<a href=\"$turl?step=100\">我已经认真阅读以上规则，开始数据转换操作</a>");

} elseif ($_GET['step'] == 1) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}userspaces LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;
		if($value['islock']) continue;
		$value = saddslashes($value);
	
		//未解决:额外的空间
		$setarr = array(
			'uid' => $value['uid'],
			'username' => $value['username'],
			'credit' => $value['credit'],//积分
			'domain' => $value['domain'],
			'viewnum' => $value['viewnum'],
			'dateline' => $value['dateline'],
			'updatetime' => $value['lastpost'],
			'attachsize' => $value['spacesize']
		);
		inserttable('space', $setarr, 0, true);
		
		$setarr = array(
			'uid' => $value['uid'],
			'resideprovince' => $value['province'],
			'residecity' => $value['city']
		);
		inserttable('spacefield', $setarr, 0, true);
	}
	
	show_next('用户空间数据');

} elseif ($_GET['step'] == 2) {
	
	//日志
	$next = false;
	$perpage = 5;
	$query = $_SGLOBAL['db']->query("SELECT i.*, ii.* FROM {$tpre}spaceitems i LEFT JOIN {$tpre}spaceblogs ii ON ii.itemid=i.itemid WHERE i.type='blog' AND i.folder<3 LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;

		//附件处理
		if($value['haveattach']) {
			$subquery = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments WHERE itemid='$value[itemid]'");
			while ($subvalue = $_SGLOBAL['db']->fetch_array($subquery)) {
				if(strexists($value['message'], $value['filepath']) || strexists($value['message'], $value['thumbpath'])) {
					continue;
				}
				if($subvalue['isimage']) {
					//图片
					$value['message'] .= "<div><img src=\"{$_SC[attachurl]}$subvalue[filepath]\"></div>";
				} else {
					$value['message'] .= "<div>文件: <a href=\"{$_SC[attachurl]}$subvalue[filepath]\">$subvalue[filename]</a></div>";
				}
			}
		}
		if($value['mood']) {
			$value['message'] = '<div><strong>心情</strong>: '.$value['mood'].'</div>'.$value['message'];
		}
		if($value['weather']) {
			$value['message'] = '<div><strong>天气</strong>: '.$value['weather'].'</div>'.$value['message'];
		}
		
		include_once(S_ROOT.'./source/function_cp.php');
		include_once(S_ROOT.'./source/function_blog.php');
		$value = saddslashes($value);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'uid' => $value['uid'],
			'username' => $value['username'],
			'subject' => $value['subject'],
			'classid' => $value['itemtypeid'],
			'viewnum' => $value['viewnum'],
			'replynum' => $value['replynum'],
			'dateline' => $value['dateline'],
			'noreply' => empty($value['allowreply'])?1:0,
			'friend' => $value['folder']>1?1:0
		);
		inserttable('blog', $setarr, 0, true);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'message' => message_replace($value['message']),
			'postip' => $value['postip']
		);
		inserttable('blogfield', $setarr, 0, true);
	}

	show_next('日志数据');
	
} elseif ($_GET['step'] == 3) {
	
	//图片
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;
		if(!$value['isimage']) {
			continue;
		}
		
		$value = saddslashes($value);
		$setarr = array(
			'picid' => $value['aid'],
			'uid' => $value['uid'],
			'dateline' => $value['dateline'],
			'filename' => $value['filename'],
			'title' => $value['subject'],
			'type' => $value['attachtype'],
			'size' => $value['size'],
			'filepath' => $value['filepath'],
			'thumb' => 0
		);
		inserttable('pic', $setarr, 0, true);
	}

	show_next('图片数据');
	
} elseif ($_GET['step'] == 4) {
	
	$next = false;
	$updateid = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}friends LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;
		$value = saddslashes($value);
		$updateid[] = $value['frienduid'];
		$setarr = array(
			'uid' => $value['uid'],
			'fuid' => $value['frienduid']
		);
		inserttable('friend', $setarr, 0, true);
	}
	if($updateid) {
		$_SGLOBAL['db']->query("UPDATE ".tname('friend')." f, ".tname('space')." s SET f.fusername = s.username where f.fuid IN ('".implode("','", $updateid)."') AND f.fuid=s.uid");
	}
	
	show_next('好友');

} elseif ($_GET['step'] == 5) {
	
	$cid_start = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT cid FROM {$tpre}spacecomments ORDER BY cid DESC LIMIT 1"), 0)+100;
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}guestbooks LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;
		$value = saddslashes($value);
		$setarr = array(
			'cid' => ($cid_start + $value['gid']),
			'uid' => $value['uid'],
			'id' => $value['uid'],
			'idtype' => 'uid',
			'author' => $value['author'],
			'authorid' => $value['authorid'],
			'ip' => $value['ip'],
			'message' => $value['message'],
			'dateline' => $value['dateline']
		);
		inserttable('comment', $setarr, 0, true);
	}
	
	show_next('留言');

} elseif ($_GET['step'] == 6) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}itemtypes LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;
		$value = saddslashes($value);
		
		$setarr = array(
			'classid' => $value['typeid'],
			'classname' => $value['typename'],
			'uid' => $value['uid'],
			'dateline' => $_SGLOBAL['timestamp']
		);
		inserttable('class', $setarr, 0, true);
	}
	
	show_next('个人分类');

} elseif ($_GET['step'] == 7) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}spacecomments WHERE type != 'news' LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;
		$value = saddslashes($value);
		if(empty($value['message'])) {
			continue;
		}
		$setarr = array(
			'cid' => $value['cid'],
			'id' => $value['itemid'],
			'idtype' => 'blogid',
			'uid' => $value['uid'],
			'authorid' => $value['authorid'],
			'author' => $value['author'],
			'ip' => $value['ip'],
			'dateline' => $value['dateline'],
			'message' => $value['message']
		);
		inserttable('comment', $setarr, 0, true);
	}
	
	show_next('评论');

} elseif ($_GET['step'] == 8) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT i.*, ii.* FROM {$tpre}spaceitems i LEFT JOIN {$tpre}spacefiles ii ON ii.itemid=i.itemid WHERE i.type='file' AND i.folder<3 LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;

		if($value['filesize']) {
			$value['message'] .= '<div><strong>大小</strong>: '.$value['filesize'].' '.$value['filesizeunit'].'</div>';
		}
		if($value['version']) {
			$value['message'] .= '<div><strong>版本</strong>: '.$value['version'].'</div>';
		}
		if($value['producer']) {
			$value['message'] .= '<div><strong>出品</strong>: '.$value['producer'].'</div>';
		}
		if($value['downfrom']) {
			$value['message'] .= '<div><strong>来源</strong>: '.$value['downfrom'].'</div>';
		}
		if($value['language']) {
			$value['message'] .= '<div><strong>语言</strong>: '.$value['language'].'</div>';
		}
		if($value['permission']) {
			$value['message'] .= '<div><strong>授权</strong>: '.$value['permission'].'</div>';
		}
	
		//附件如何处理?
		$i = 0;
		if($value['haveattach']) {
			$subquery = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments WHERE itemid='$value[itemid]'");
			while ($subvalue = $_SGLOBAL['db']->fetch_array($subquery)) {
				if(strexists($value['message'], $value['filepath']) || strexists($value['message'], $value['thumbpath'])) {
					continue;
				}
				if($subvalue['isimage']) {
					//图片
					$value['message'] .= "<div><img src=\"{$_SC[attachurl]}$subvalue[filepath]\"></div>";
				} else {
					$i++;
					$value['message'] .= "<div><strong>本地下载[$i]</strong>: <a href=\"{$_SC[attachurl]}$subvalue[filepath]\">$subvalue[filename]</a> (下载后，请将文件重命名为 $subvalue[filename] 方可正常使用)</div>";
				}
			}
		}
		
		$i = 0;
		if($value['remoteurl']) {
			$remoteurl = unserialize($value['remoteurl']);
			foreach ($remoteurl as $rs) {
				$i++;
				$value['message'] .= '<div><strong>远程下载['.$i.']</strong>: <a href="'.$rs['remoteurl'].'" target="_blank">'.$rs['remoteurlname'].'</a></div>';
			}
		}

		$value = saddslashes($value);
		$setarr = array(
			'blogid' => $value['itemid'],
			'uid' => $value['uid'],
			'username' => $value['username'],
			'subject' => $value['subject'],
			'classid' => $value['itemtypeid'],
			'viewnum' => $value['viewnum'],
			'replynum' => $value['replynum'],
			'dateline' => $value['dateline'],
			'noreply' => empty($value['allowreply'])?1:0,
			'friend' => $value['folder']>1?1:0
		);
		inserttable('blog', $setarr, 0, true);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'message' => message_replace($value['message']),
			'postip' => $value['postip']
		);
		inserttable('blogfield', $setarr, 0, true);
	}
	
	show_next('文件数据');

} elseif ($_GET['step'] == 9) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT i.*, ii.* FROM {$tpre}spaceitems i LEFT JOIN {$tpre}spacegoods ii ON ii.itemid=i.itemid WHERE i.type='goods' AND i.folder<3 LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;

		//附件如何处理?
		if($value['haveattach']) {
			$subquery = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments WHERE itemid='$value[itemid]'");
			while ($subvalue = $_SGLOBAL['db']->fetch_array($subquery)) {
				if(strexists($value['message'], $value['filepath']) || strexists($value['message'], $value['thumbpath'])) {
					continue;
				}
				if($subvalue['isimage']) {
					//图片
					$value['message'] .= "<div><img src=\"{$_SC[attachurl]}$subvalue[filepath]\"></div>";
				} else {
					$value['message'] .= "<div><strong>文件</strong>: <a href=\"{$_SC[attachurl]}$subvalue[filepath]\">$subvalue[filename]</a></div>";
				}
			}
		}
		if($value['price']) {
			$value['message'] .= '<div><strong>价格</strong>: '.$value['price'].'</div>';
		}
		if($value['province']) {
			$value['message'] .= '<div><strong>地区</strong>: '.$value['province'].' '.$value['city'].'</div>';
		}

		$value = saddslashes($value);
		$setarr = array(
			'blogid' => $value['itemid'],
			'uid' => $value['uid'],
			'username' => $value['username'],
			'subject' => $value['subject'],
			'classid' => $value['itemtypeid'],
			'viewnum' => $value['viewnum'],
			'replynum' => $value['replynum'],
			'dateline' => $value['dateline'],
			'noreply' => empty($value['allowreply'])?1:0,
			'friend' => $value['folder']>1?1:0
		);
		inserttable('blog', $setarr, 0, true);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'message' => message_replace($value['message']),
			'postip' => $value['postip']
		);
		inserttable('blogfield', $setarr, 0, true);
	}
	
	show_next('商品数据');
	
} elseif ($_GET['step'] == 10) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT i.*, ii.* FROM {$tpre}spaceitems i LEFT JOIN {$tpre}spacelinks ii ON ii.itemid=i.itemid WHERE i.type='link' AND i.folder<3 LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;

		//附件如何处理?
		if($value['haveattach']) {
			$subquery = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments WHERE itemid='$value[itemid]'");
			while ($subvalue = $_SGLOBAL['db']->fetch_array($subquery)) {
				if(strexists($value['message'], $value['filepath']) || strexists($value['message'], $value['thumbpath'])) {
					continue;
				}
				if($subvalue['isimage']) {
					//图片
					$value['message'] .= "<div><img src=\"{$_SC[attachurl]}$subvalue[filepath]\"></div>";
				} else {
					$value['message'] .= "<div><strong>文件</strong>: <a href=\"{$_SC[attachurl]}$subvalue[filepath]\">$subvalue[filename]</a></div>";
				}
			}
		}
		
		$value['message'] .= '<div><strong>链接</strong>: <a href="'.$value['url'].'" target="_blank">点击访问</a></div>';
		
		$value = saddslashes($value);
		$setarr = array(
			'blogid' => $value['itemid'],
			'uid' => $value['uid'],
			'username' => $value['username'],
			'subject' => $value['subject'],
			'classid' => $value['itemtypeid'],
			'viewnum' => $value['viewnum'],
			'replynum' => $value['replynum'],
			'dateline' => $value['dateline'],
			'noreply' => empty($value['allowreply'])?1:0,
			'friend' => $value['folder']>1?1:0
		);
		inserttable('blog', $setarr, 0, true);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'message' => message_replace($value['message']),
			'postip' => $value['postip']
		);
		inserttable('blogfield', $setarr, 0, true);
	}
	
	show_next('书签数据');

} elseif ($_GET['step'] == 11) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT i.*, ii.* FROM {$tpre}spaceitems i LEFT JOIN {$tpre}spacevideos ii ON ii.itemid=i.itemid WHERE i.type='video' AND i.folder<3 LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;

		//附件如何处理?
		if($value['haveattach']) {
			$subquery = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments WHERE itemid='$value[itemid]'");
			while ($subvalue = $_SGLOBAL['db']->fetch_array($subquery)) {
				if(strexists($value['message'], $value['filepath']) || strexists($value['message'], $value['thumbpath'])) {
					continue;
				}
				if($subvalue['isimage']) {
					//图片
					$value['message'] .= "<div><img src=\"{$_SC[attachurl]}$subvalue[filepath]\"></div>";
				} else {
					$value['message'] .= "<div><strong>文件</strong>: <a href=\"{$_SC[attachurl]}$subvalue[filepath]\">$subvalue[filename]</a></div>";
				}
			}
		}
		
		if($value['videosize']) {
			$value['videosize'] = formatsize($value['videosize']);
			$value['message'] .= "<div><strong>影音大小</strong>: $value[videosize]</div>";
		}
		
		if($value['file']) {
			$flvurl = getsiteurl().rawurlencode($value['file']);
			$value['message'] .= '<div>
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="photo" align="middle" height="315" width="420">
				<param name="movie" value="image/flv.swf?flvurl='.$flvurl.'">
				<param name="quality" value="high">
				<param name="allowFullScreen" value="true">
				<embed src="image/flv.swf?flvurl='.$flvurl.'" quality="high" name="photo" type="application/x-shockwave-flash" allowfullscreen="true" pluginspage="http://www.macromedia.com/go/getflashplayer" align="middle" height="315" width="420">
				</object>
				<br>'.$value['videoname'].'</a></div>';
		}

		if($value['remoteurl']) {
			$remoteurl = unserialize($value['remoteurl']);
			if($value['subtype'] == 'media') {
				foreach ($remoteurl as $rs) {
					$value['message'] .= '<div>
						<object id="PlayerEx2" classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="420" height="315">
						<param name="AutoStart" value="0">
						<param name="URL" value="'.$rs['remoteurl'].'">
						<embed autostart="false" src="'.$rs['remoteurl'].'" type="video/x-ms-wmv" width="420" height="315" controls="ImageWindow" console="cons"></embed>
						</object>
						<br>'.$rs['remoteurlname'].'</div>';
				}
			} elseif($value['subtype'] == 'real') {
				foreach ($remoteurl as $rs) {
					$value['message'] .= '<div>
						<object id="RVOCX" classid="CLSID:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="420" height="315">
						<param name="AUTOSTART" value="0">
						<param name="SRC" value="'.$rs['remoteurl'].'">
						<param name="CONTROLS" value="ControlPanel">
						<param name="CONSOLE" value="cons">
						<embed autostart="false" src="'.$rs['remoteurl'].'" type="audio/x-pn-realaudio-plugin" width="420" height="315" controls="ControlPanel" console="cons"></embed>
						</object>
						<br>'.$rs['remoteurlname'].'</div>';
				}
			} elseif($value['subtype'] == 'flash') {
				foreach ($remoteurl as $rs) {
					if(fileext($rs['remoteurl']) == 'flv') {
						$rs['remoteurl'] = 'image/flv.swf?flvurl='.$rs['remoteurl'];
					}
					$value['message'] .= '<div>
						<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="photo" align="middle" height="315" width="420">
						<param name="movie" value="'.$rs['remoteurl'].'">
						<param name="quality" value="high">
						<param name="allowFullScreen" value="true">
						<embed src="'.$rs['remoteurl'].'" quality="high" name="photo" type="application/x-shockwave-flash" allowfullscreen="true" pluginspage="http://www.macromedia.com/go/getflashplayer" align="middle" height="315" width="420">
						</object>
						<br>'.$rs['remoteurlname'].'</div>';
				}
			} else {
				foreach ($remoteurl as $rs) {
					$value['message'] .= '<div><a href="'.$rs['remoteurl'].'">'.$rs['remoteurlname'].'</a></div>';
				}
			}
		}
		
		$value = saddslashes($value);
		$setarr = array(
			'blogid' => $value['itemid'],
			'uid' => $value['uid'],
			'username' => $value['username'],
			'subject' => $value['subject'],
			'classid' => $value['itemtypeid'],
			'viewnum' => $value['viewnum'],
			'replynum' => $value['replynum'],
			'dateline' => $value['dateline'],
			'noreply' => empty($value['allowreply'])?1:0,
			'friend' => $value['folder']>1?1:0
		);
		inserttable('blog', $setarr, 0, true);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'message' => message_replace($value['message']),
			'postip' => $value['postip']
		);
		inserttable('blogfield', $setarr, 0, true);
	}
	
	show_next('视频数据');
	
} elseif ($_GET['step'] == 12) {
	
	$next = false;
	$query = $_SGLOBAL['db']->query("SELECT i.*, ii.* FROM {$tpre}spaceitems i LEFT JOIN {$tpre}spaceimages ii ON ii.itemid=i.itemid WHERE i.type='image' AND i.folder<3 LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$next = true;

		//附件如何处理?
		if($value['haveattach']) {
			$subquery = $_SGLOBAL['db']->query("SELECT * FROM {$tpre}attachments WHERE itemid='$value[itemid]'");
			while ($subvalue = $_SGLOBAL['db']->fetch_array($subquery)) {
				if(strexists($value['message'], $value['filepath']) || strexists($value['message'], $value['thumbpath'])) {
					continue;
				}
				if($subvalue['isimage']) {
					//图片
					$value['message'] .= "<div><img src=\"{$_SC[attachurl]}$subvalue[filepath]\"></div>";
				} else {
					$value['message'] .= "<div><strong>文件</strong>: <a href=\"{$_SC[attachurl]}$subvalue[filepath]\">$subvalue[filename]</a></div>";
				}
			}
		}
		
		$value = saddslashes($value);
		$setarr = array(
			'blogid' => $value['itemid'],
			'uid' => $value['uid'],
			'username' => $value['username'],
			'subject' => $value['subject'],
			'classid' => $value['itemtypeid'],
			'viewnum' => $value['viewnum'],
			'replynum' => $value['replynum'],
			'dateline' => $value['dateline'],
			'noreply' => empty($value['allowreply'])?1:0,
			'friend' => $value['folder']>1?1:0
		);
		inserttable('blog', $setarr, 0, true);
		
		$setarr = array(
			'blogid' => $value['itemid'],
			'message' => message_replace($value['message']),
			'postip' => $value['postip']
		);
		inserttable('blogfield', $setarr, 0, true);
	}
	
	show_next('图片主题数据');
	
} elseif ($_GET['step'] == 13) {
	$msg = <<<EOF
	<form method="post" action="convert.php">
	<table>
	<tr><td colspan="2">数据转换完成!<br><br>
	最后，请输入你的用户名，系统将您设为UCenter Home的管理员!
	</td></tr>
	<tr><td>您的用户名</td><td><input type="text" name="username" value="" size="30"></td></tr>
	<tr><td></td><td><input type="submit" name="opensubmit" value="设为管理员"></td></tr>
	</table>
	<input type="hidden" name="formhash" value="$formhash">
	</form>
EOF;
	show_msg($msg);
	
} elseif ($_GET['step'] == 100) {
	
	show_msg("<strong>首先，您需要将论坛升级到Discuz! 6.1 (这一步非常重要，而且必需进行的)。<br>接下来，下载并且全新安装 UCenter Home 程序</strong><br><font color=blue>警告：如果您的UCenter Home不是全新安装，本转换程序会覆盖您现有的所有数据。请做好数据备份！</font><br><br>
		<a href=\"$turl?step=110\">已经升级了论坛，并全新安装了UCenter Home，进入下一步</a>");
	
} elseif ($_GET['step'] == 110) {
	
	show_msg("<strong>确保当前 UCenter Home 使用的MySQL帐号，能够有权限连接操作之前的 X-Space 的数据库。</strong><br><br>
		<a href=\"$turl?step=120\">已经设置MySQL权限，进入下一步</a>");
	
} elseif ($_GET['step'] == 120) {
	
	show_msg("<strong>请将原SupeSite/X-Space存放附件的目录（一般为 ./attachments 目录）中的所有文件夹和文件，复制到当前的 UCenter Home 程序中的 ./attachment 目录下面。</strong><br><br>
		<a href=\"$turl?step=130\">已经复制好附件文件，进入下一步</a>");
	
} elseif ($_GET['step'] == 130) {
	
	show_msg("<strong>请将原SupeSite/X-Space存放影音的目录（一般为 ./video 目录）中的所有文件夹和文件，复制到 当前的 UCenter Home 程序中的 ./video 目录下面（请先创建该 video 文件夹）。</strong><br><br>
		<a href=\"$turl?step=200\">已经复制好视频文件，进入下一步</a>");
	
} elseif ($_GET['step'] == 200) {

	$msg = <<<EOF
	<strong>设置已有的X-Space的信息：</strong><br><br>
	<form method="post" action="convert.php">
	<table>
	<tr><td>所在数据库名：</td><td><input type="text" name="set[dbname]" value="supesite"></td></tr>
	<tr><td>表名前缀：</td><td><input type="text" name="set[tablepre]" value="supe_"></td></tr>
	<tr><td>之前的附件目录名：</td><td><input type="text" name="set[attach]" value="attachments"> (<strong>准确填写</strong>，一般为 attachments)</td></tr>
	<tr><td>之前的附件访问URL：</td><td><input type="text" name="set[purl]" value="http://localhost/xspace/attachments/" size="40"> (<strong>准确填写，末尾加“/”</strong>)</td></tr>
	<tr><td colspan="2">请准确填写之前X-Space的附件目录名和附件访问url地址。<br>本转换程序会根据这两个变量，将内容中的图片地址进行批量替换，<br>以避免出现图片无法访问的问题。
	</td></tr>
	<tr><td></td><td><input type="submit" name="setsubmit" value="提交保存"></td></tr>
	</table>
	<input type="hidden" name="formhash" value="$formhash">
	</form>
EOF;
	show_msg($msg);

} elseif ($_GET['step'] == 300) {
	
	if(!$query = $_SGLOBAL['db']->query("SELECT uid FROM {$tpre}userspaces LIMIT 1", 'SILENT')) {
		show_msg("不能正确连接 X-Space 的数据库，请检查X-Space设置，并且确保UCenter Home的MySQL帐号可以有权限操作X-Space的数据库。");
	} else {
		show_msg("检测 X-Space 的数据库，连接成功。<br><br><a href=\"$turl?step=1\">点击正式开始进入数据升级过程</a>");
	}
} 

//跳转
function show_next($batch) {
	global $turl, $next, $start, $perpage;
	
	$nowtime = gmdate('H:i:\<\b\>s\<\/\b\>', time()+8*3600);
	if($next) {
		$start = $start + $perpage;
		show_msg("第 $_GET[step] 步 / 共 13 步<br>OK,本次处理完成! ($start)<br><br><a href=\"$turl?step=$_GET[step]&start=$start\">进入下一批 <strong>$batch</strong> 处理, 请耐心等待 ...</a><br><br>Now Time: $nowtime ", "$turl?step=$_GET[step]&start=$start");
	} else {
		show_msg("第 $_GET[step] 步 / 共 13 步<br><strong>$batch</strong> 全部处理完毕! <br><br><a href=\"$turl?step=".($_GET['step']+1)."\">进入下一步处理</a><br><br>Now Time: $nowtime", "$turl?step=".($_GET['step']+1));
	}
}

//显示
function show_msg($message, $url_forward='') {
	global $_SGLOBAL;

	obclean();
	
	$_SGLOBAL['extrahead'] = $url_forward ? '<meta http-equiv="refresh" content="0; url='.$url_forward.'">' : '';
	show_header();
	print<<<END
	<table>
	<tr><td>$message</td></tr>
	</table>
END;
	show_footer();
	exit();
}

//复制文件
function scopy($source, $obj) {
	if(!@copy($source, $obj)) {
		$content = '';
		if(@$fp = fopen($source, 'rb')) {
			$content = fread($fp, filesize($source));
			fclose($fp);
		}
		if($content && @$fp = fopen($obj, 'wb')) {
			fwrite($fp, $content);
			fclose($fp);
		}
	}
}

//页面头部
function show_header() {
	global $_SGLOBAL, $_SC;

	$nowarr[$_GET['step']] = ' class="current"';
	
	if(empty($_SGLOBAL['extrahead'])) $_SGLOBAL['extrahead'] = '';
	
	print<<<END
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"> 
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=$_SC[charset]" />
	$_SGLOBAL[extrahead]
	<title> X-Space => UCenter Home 转换程序 </title>
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
	<h1>X-Space => UCenter Home 转换程序</h1>
	<div style="width:90%;margin:0 auto;">
	<table id="menu">
	<tr>
	<td{$nowarr[0]}>转换开始</td>
	<td{$nowarr[1]}>数据转换</td>
	<td>升级完成</td>
	</tr>
	</table>
	<br>
END;
}

//页面顶部
function show_footer() {
	print<<<END
	</div>
	<div id="footer">&copy; Comsenz Inc. 2001-2008 u.discuz.net</div>
	</div>
	<br>
	</body>
	</html>
END;
}

//内容替换
function message_replace($message) {
	global $_SC, $set;
	
	$searchs = array(
		'"'.$set['attach'].'/',
		'"/'.$set['attach'].'/',
		'href='.$set['attach'].'/',
		'href=/'.$set['attach'].'/',
		$set['purl']
	);
	$replaces = array(
		'"'.$_SC['attachurl'],
		'"'.$_SC['attachurl'],
		'href='.$_SC['attachurl'],
		'href='.$_SC['attachurl'],
		$_SC['attachurl']
	);
	$message = str_replace($searchs, $replaces, $message);
	
	return $message;
}

?>