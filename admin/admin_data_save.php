<?php 
include "config.php";
require_once '../include/class.db.php';

/*
session_start(); 
 if(isset($_SESSION['lock_yun_config'])){ $time=(int)$_SESSION['lock_yun_config']-(int)time(); if($time>0){ exit(json_encode(array('success'=>0,'icon'=>5,'m'=>"请勿频繁提交，".$time."秒后再试！")));}}
 $_SESSION['lock_yun_config']= time()+ $from_timeout;
*/

//搜索数据
if (filter_has_var(INPUT_POST, "socode_top_val")) {
    $file='../save/top.inc.php';  include  $file;
    $TOPDATA=arr_input("socode_top_val");
    if (Main_db::save($file)) {
        exit(json_encode(array('success' => 1, 'icon' => 1, 'm' => "保存成功!")));
    } else {
        exit(json_encode(array('success' => 0, 'icon' => 0, 'm' => "保存失败!请检测配置文件权限")));
    }
    exit;
}
 exit(json_encode(array('success' => 0, 'm' => "input error!")));
 function arr_input($name,$lsarr=false){
    $name=trim(filter_input(INPUT_POST, $name));
    if($name==""){return false;}
    $arr = preg_split('/[\r\n]+/s', $name);
    foreach ($arr as $key) {        
       $val = explode("=>", $key);
       if($lsarr){ $array[trim($val[0])] = explode(",", $val[1]); }else{$array[trim($val[0])] =  trim($val[1]);}
    }
    return $array;
}




