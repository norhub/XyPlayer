<?php 
if(function_exists("opcache_reset")){opcache_reset();} //清除PHP脚本缓存
require_once dirname(__FILE__).'/../include/class.main.php';
if(file_exists(dirname(__FILE__).'/../save/config.php')){include dirname(__FILE__).'/../save/config.php';}
session_start(); 
$username=isset($_SESSION['username'])?$_SESSION['username']:'admin';
if(empty($_SESSION['hashstr']) || $_SESSION['hashstr']!==md5((isset($CONFIG["user"])?$CONFIG["user"]:"admin").(isset($CONFIG["pass"])?$CONFIG["pass"]:MD5("admin888")))){header("location:load.php?url=login.htm");exit();}


