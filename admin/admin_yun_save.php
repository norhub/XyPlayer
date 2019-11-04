<?php 
include("config.php");
require_once('../include/class.db.php');
/*
session_start(); 
 if(isset($_SESSION['lock_yun_config'])){ $time=(int)$_SESSION['lock_yun_config']-(int)time(); if($time>0){ exit(json_encode(array('success'=>0,'icon'=>5,'m'=>"请勿频繁提交，".$time."秒后再试！")));}}
 $_SESSION['lock_yun_config']= time()+ $from_timeout;
*/

//云播 资源站设置
if (filter_has_var(INPUT_POST, "yun_config_api")) {
    require_once("../save/yun.config.php");
    $YUN_CONFIG["jmp_off"] = trim(filter_input(INPUT_POST, "yun_config_jmp_off"));
    $YUN_CONFIG["API"] = preg_split('/[\r\n]+/s', trim(filter_input(INPUT_POST, "yun_config_api")));
    $YUN_CONFIG["url_filter"] = trim(filter_input(INPUT_POST, "yun_config_url_filter"));
    $YUN_CONFIG["flag_filter"] = trim(filter_input(INPUT_POST, "yun_config_flag_filter"));
    $YUN_CONFIG["name_filter"] = trim(filter_input(INPUT_POST, "yun_config_name_filter"));
    $YUN_CONFIG["flag_replace"] = arr_input("yun_config_flag_replace");
 

    if (Main_db::save("../save/yun.config.php")) {

        exit(json_encode(array('success' => 1, 'icon' => 1, 'm' => "保存成功!")));
    } else {
        exit(json_encode(array('success' => 0, 'icon' => 0, 'm' => "保存失败!请检测配置文件权限")));
    }
    exit;
}




//云播 规则设置
if (filter_has_var(INPUT_POST, "yun_match_title_match")) {
    
    require_once("../save/yun.match.php");
   
   //404跳转设置
    $YUN_MATCH["ERROR_404"] = filter_input(INPUT_POST, "yun_match_error_404");
     
   //输出类型转换
    $YUN_MATCH["type_match"] = arr_input("yun_match_type_match");
     
    //视频标题过滤
    $YUN_MATCH["title_replace"] = preg_split('/[\r\n]+/s', trim(filter_input(INPUT_POST, "yun_match_title_replace")));
 
    //URL地址过滤
    $YUN_MATCH["url_replace"] = preg_split('/[\r\n]+/s', trim(filter_input(INPUT_POST, "yun_match_url_replace")));

    //视频地址转换
   $YUN_MATCH["url_match"] = arr_input("yun_match_url_match");

   //资源标题规则设置
  
    $YUN_MATCH["title_match"] = arr_input("yun_match_title_match",true);
    
    //视频名称和集数规则设置
 
    $YUN_MATCH["name_match"] = arr_input("yun_match_name_match",true);
   
    
    if (Main_db::save("../save/yun.match.php")) {

        exit(json_encode(array('success' => 1, 'icon' => 1, 'm' => "保存成功!")));
    } else {
        exit(json_encode(array('success' => 0, 'icon' => 0, 'm' => "保存失败!请检测配置文件权限")));
    }

     
}

 exit(json_encode(array('success' => 0, 'm' => "input error!")));

  function arr_input($name,$lsarr=false){
	      $name=trim(filter_input(INPUT_POST, $name));
		  if($name==""){return false;}
          $arr = preg_split('/[\r\n]+/s', $name);
          foreach ($arr as $key) {        
             $val = explode("=>", $key);
             if($lsarr){ $array[trim($val[0])] = explode(",", $val[1]); }else{$array[trim($val[0])] =  $val[1];}
          }
          return $array;
    }
    
