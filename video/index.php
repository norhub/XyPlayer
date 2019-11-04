<?php
error_reporting(0);
header('Content-Type: text/json; charset=utf-8');

//运行目录
define("FCPATH", str_replace("\\", "/",dirname(__FILE__)));

//加载核心类
 require_once FCPATH.'/../include/class.main.php';

//加载配置文件
require_once FCPATH.'/../save/config.php';


//加载防火墙规则
Blacklist::parse($CONFIG["BLACKLIST"]);

$url=filter_input(INPUT_GET, "url")?filter_input(INPUT_GET, "url"):filter_input(INPUT_GET, "v");

$type=filter_input(INPUT_GET, 'type');

$info=array('success'=>0,'code'=>0);


//获取数据
if($url!="" ){ 	
    //mp4 m3u8 flv 直链	
    if (stristr($url,'.mp4')!==false || stristr($url,'.m3u8')!==false|| stristr($url,'.flv')!==false) {    
         
		  require_once 'class.video.php';
		  
	      $info=VIDEO::parse($url);
	  
   //27盘资源处理
	 }else if(stristr($url,'27pan')!==false || explode("/",parse_url($url)['path'])[1]=='share'){   
   
         require_once 'class.27pan.php';
		 
	     $info=PAN27::parse($url);	
     
                
    // 360看看处理
	}else if(stristr($url,'v.360kan.com')!==false){ 
             
           require_once  'class.360.php';

	       $info=ShortVideo360::parse($url);	
           
	   
    // QQ空间处理
	}else if(stristr($url,'qzone.qq.com')!==false){ 
             
		require_once  'class.qzone.php';

		$info=QZONE::parse($url);	
		  
	/*	

   	/*
      # 乐多资源处理
	}else if(strstr($url,'XMM')!==false || $type==="leduo"){ 

           require_once  'class.leduo.php';

	       $info=LEDUO::parse($url);        

      #咪咕视频
	}else if(stristr($url,'miguvideo.com')!==false){ 
             
		require_once  'class.migu.php';
		$info=MIGU::parse($url);	
    */ 


	 //添加更多   
	      
	  
    }else{
	
	     $info['m']='暂不支持直解';
	
    }
	
}else{
	
	 $info["m"]="input err!";

}

// 调用第三方资源
if($info["success"]==0 && (stristr($url,'http://')!==false || stristr($url,'https://')!==false ) ){

	 require_once 'class.yun.php' ;
		
	 $info=YUN::parse($url);	

}else{

	$info['m']='暂不支持此站点';
}

exit(json_encode($info)) ;

