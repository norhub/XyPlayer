<?php
include "config.php";
include  dirname(__FILE__).'/../save/yun.data.php';
include dirname(__FILE__) . '/../include/class.db.php';
/*
 session_start(); 
 if(isset($_SESSION['lock_data'])){ $time=(int)$_SESSION['lock_data']-(int)time(); if($time>0){ exit(json_encode(array('success'=>0,'icon'=>5,'m'=>"请勿频繁提交，".$time."秒后再试！")));}}
 $_SESSION['lock_data']= time()+ $from_timeout;
*/

//检测参数
if (!filter_has_var(INPUT_POST, "type")) { exit(json_encode(array('success' => 0, 'icon' => 0, 'm' => "请勿非法调用！")));}

//传参初始化
$type = filter_input(INPUT_POST, 'type');
$id = filter_input(INPUT_POST, 'id');

//json 初始化
$info = array('success' => 0, 'icon' => 6);

switch ($type) {
    
    //跳转 添加
    case 'jmp_add' :
       
        $YUN_DATA["url_jmp"][] = array(
            'off' => filter_input(INPUT_POST, 'JMP_OFF'),
            'name' => filter_input(INPUT_POST, 'JMP_NAME'),
            'url' => filter_input(INPUT_POST, 'JMP_URL'),
            'title' => filter_input(INPUT_POST, 'JMP_TITLE'),
            'part' => filter_input(INPUT_POST, 'JMP_PART'),
            'href' => filter_input(INPUT_POST, 'JMP_HREF')    
            );
        $info['m'] = "添加成功";
        break;

    //跳转  修改    
    case 'jmp_edit' :
         isid();
       $YUN_DATA["url_jmp"][$id] = array(
            'off' => filter_input(INPUT_POST, 'JMP_OFF'),
            'name' => filter_input(INPUT_POST, 'JMP_NAME'),
            'url' => filter_input(INPUT_POST, 'JMP_URL'),
            'title' => filter_input(INPUT_POST, 'JMP_TITLE'),
            'part' => filter_input(INPUT_POST, 'JMP_PART'),
            'href' => filter_input(INPUT_POST, 'JMP_HREF')
     
            );
        $info['m'] = "修改成功";
        break;

//跳转  删除 
    case 'jmp_del':
        isid();
        $id=explode(",",$id);
        if (is_array($id)) {
            foreach ($id as $key) {
                unset($YUN_DATA["url_jmp"][$key]);   
            }
        } else {
             unset($YUN_DATA["url_jmp"][$id]);  
        }
        array_values($YUN_DATA["url_jmp"]);
        $info['id'] = filter_input(INPUT_POST, 'id');
        $info['m'] = "删除成功";
        break;
//跳转  停止
    case 'jmp_stop':
        isid();
        $YUN_DATA["url_jmp"][$id]['off'] = 0;
        $info['m'] = "已停用";
        $info['icon'] = 5;
        break;

  //跳转  启动
    case 'jmp_start':
         isid();
        $YUN_DATA["url_jmp"][$id]['off'] = 1;
        $info['m'] = "已启用";
        $info['icon'] = 6;
        break;
    
    
 
    default:
        exit(json_encode(array('success' => 0, 'icon' => 0, 'm' => "参数错误！")));
}

if (Main_db::save("../save/yun.data.php")) {

    $info['success'] = 1;
} else {

    $info['success'] = 0;
    $info['icon'] = 5;
    $info['m'] = "操作失败！";
}

exit(json_encode($info));

function isid(){   
    if(!filter_has_var(INPUT_POST, "id")){ exit(json_encode(array('success' => 0, 'icon' => 0, 'm' => "id错误，没有找到id！"))); }   
}

function curl($url, $ref = '') {
    $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $params['ref'] = $ref;
    return GlobalBase::curl($url, $params);
}
