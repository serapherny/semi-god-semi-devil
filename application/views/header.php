<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title?></title>
	<?php foreach($css_files as $css_file) {?>
	<link rel="stylesheet" type="text/css" href="<?php echo STATIC_DIR.$css_file;?>" />
	<?php }?>
</head>
<body>
<div id="container">  
  <h1>拇指姑娘 - <?php echo $page_title?></h1>
  <?php $this->load->view('widget/search_box');?>
  <div id="main-area">
