<?php 
/*##################################################
# xypaly 智能视频解析 X3  by http://bbs.52jscn.com
# 官方网站: http://bbs.52jscn.com
# 源码获取：http://bbs.52jscn.com
# 模块功能：m3u8本地化
###################################################*/
error_reporting(0);
require_once(dirname(__FILE__).'/'."../include/class.main.php");
require_once(dirname(__FILE__).'/'."../save/config.php");
if(filter_input(INPUT_GET, "url")){$url=filter_input(INPUT_GET, 'url');}else{exit("input error！");}
define("ROOT_PATH", $ROOT_PATH ? $ROOT_PATH:GlobalBase::is_root());
define("M3U8", ROOT_PATH."video/m3u8.php?url=");
define("TS", ROOT_PATH."video/ts.php?url=");
parse(urldecode($url));
function parse($url)
{	
  $base=array();
  $name=preg_match("#/([\w]+\.m3u8)#",$url,$base)?$base[1]:"video.m3u8";
  $key=array();
  $path=preg_match("#^((http://|https://).*)/#i",$url,$key)?$key[1]:"";
  header('Access-Control-Allow-Origin:*');
  header('Content-type: application/vnd.apple.mpegurl;');
   header('Content-Disposition: attachment; filename='.$name);	
  //获取m3u8文件数据  
  $data = curl($url);if($data===""){return false;}
  $lines = preg_split('/[\r\n]+/s', $data); $m3u8=""; 

  foreach ($lines as  $key =>  $value) 
  {			 
       //判断是文件信息
	 if($value&&substr($value,0,1)!="#")
         {	

// 路径转换
             $purl=put_url($path,$value); 	                                           	 
	     $m3u8.=put_file($lines,$key).urlencode($purl)."\n";                           
	  //其他信息直接返回原信息
	  }else{
	     $m3u8.=$value."\n";			
         }    
    }
       exit(trim($m3u8));	
}
function put_file($lines,$key){  
     //取文件类型
     $i=$key; do {$i--;$front=$lines[$i];}while($front === ""&&!$i<0);   
    //路径转换
     if(strstr($front,"#EXT-X-STREAM-INF:")){ return M3U8;}else if(strstr($front,"#EXTINF:")){ return TS; }
    }

function put_url($path,$url){
	
  if(substr($url,0,4)=="http"){
	  return $url;			
   }else if(substr($url,0,1)=="/"){
	  return $path.$url;
   }else{
	 return $path."/".$url;
   }		
}

function curl($url,$cookie="")
{
	$params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
	$params["cookie"] = $cookie;
  	return GlobalBase::curl($url,$params);
}
