<?php
/*##################################################
# xypaly 智能视频解析 X   by http://bbs.52jscn.com
# 官方网站: http://bbs.52jscn.com
# 源码获取：http://bbs.52jscn.com 
# 模块功能：PHP文本数据操作
###################################################*/
//不显示读取错误
ini_set("error_reporting","E_ALL & ~E_NOTICE");
//配置操作类
class Main_db
{
     //变量转文本
     public static function word($name) {global $$name; $key=var_export($$name,true);return "$$name=$key;\r\n";}
     //保存配置
     public static function save($file="../save/config.php")
     {     
        //排除注释和php标记
        $data = preg_replace('!\/\/.*?[\r\n]|\/\*[\S\s]*?\*\/!', '', preg_replace('/(?:\<\?php|\?\>)/', '', file_get_contents($file)));       
        //按php语句分组
        $lines = preg_split('/[;]+/s', $data,-1,PREG_SPLIT_NO_EMPTY);	 
        $word ="<?php\r\n";
       //更新变量
        foreach ($lines as $value){
          $value= trim($value);
          //检测是否PHP变量声明
          if($value!==''&&substr($value,0,1)==='$'){
              //分离 变量名和值
              $line=explode('=',$value,2);
              //取变量名
              $name = str_replace('$', '', trim($line[0]));
              //变量转文本 重新赋值
              $word.=self::word($name); }       
          }   
        return file_put_contents($file,$word);  	  
     }
}
