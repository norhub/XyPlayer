<?php 
 include "config.php"; 
 require_once(dirname(__FILE__).'/../include/class.db.php');
 session_start(); 
 if(isset($_SESSION['lock_config'])){ $time=(int)$_SESSION['lock_config']-(int)time(); if($time>0){ exit(json_encode(array('success'=>0,'icon'=>5,'m'=>"请勿频繁提交，".$time."秒后再试！")));}}
 $_SESSION['lock_config']= time()+ $from_timeout;
  

 
 //基本设置；
 if(filter_has_var(INPUT_POST, "ROOT_PATH")){
     
     $CONFIG["ROOT_PATH"]=trim(filter_input(INPUT_POST, "ROOT_PATH")); //网站路径
     
     $CONFIG["sitetime"]=trim(filter_input(INPUT_POST, "sitetime"));   //成立日期
    
     $CONFIG["API_PATH"]=filter_input(INPUT_POST, "API_PATH");    //定义API路径；
     
     $CONFIG["timecookie"]=filter_input(INPUT_POST, "timecookie");  //有效期
     
     $CONFIG["timeout"]=filter_input(INPUT_POST, "timeout");   //访问超时
     
     $CONFIG["from_timeout"]=filter_input(INPUT_POST, "from_timeout");   //提交间隔
     
     //缓存设置
     $CONFIG["chche_config"]=array ( 'type' =>filter_input(INPUT_POST, "chche_type"),'prot' =>filter_input(INPUT_POST, "chche_prot"), 'time' =>filter_input(INPUT_POST, "chche_time"),); 
     
     //公告设置 
     $CONFIG["BOOK_INFO"]=array('off'=>filter_input(INPUT_POST, "BOOK_INFO_OFF"), 'info'=> filter_input(INPUT_POST, "BOOK_INFO_INFO") );
   
     //友情链接
     $CONFIG["FOOTER_LINK"]=array('off'=>filter_input(INPUT_POST, "FOOTER_LINK_OFF"), 'info'=> GetPostArr("FOOTER_LINK_INFO"));

     $CONFIG["HEADER_CODE"]=filter_input(INPUT_POST, "HEADER_CODE");   //页头代码
     
     $CONFIG["FOOTER_CODE"]=filter_input(INPUT_POST, "FOOTER_CODE");   //页尾代码
     
 }
 
//SEO设置
if(filter_has_var(INPUT_POST, "title")){   
   //SEO设置
    
    $CONFIG["TITLE"]=filter_input(INPUT_POST, "title");    //网站标题设置
    $CONFIG["keywords"]=filter_input(INPUT_POST, "keywords");  //站点关键词
    $CONFIG["description"]=filter_input(INPUT_POST, "description"); //站点描述
    $CONFIG["HEADER"]=filter_input(INPUT_POST, "HEADER");  //自定义
   
   //搜索页设置
   $CONFIG["socode"]["top_off"]=filter_input(INPUT_POST, "socode_top_off");  //热门搜索开关
   $CONFIG["socode"]["diy_off"]=filter_input(INPUT_POST, "socode_diy_off");  //自定义开关
   $CONFIG["socode"]["not_off"]=filter_input(INPUT_POST, "socode_not_off");  //屏蔽关键字开关

   $CONFIG["socode"]["diy_val"]=filter_input(INPUT_POST, "socode_diy_val");  //自定义内容
   $CONFIG["socode"]["not_val"]=filter_input(INPUT_POST, "socode_not_val");  //屏蔽关键字内容
  


  // $CONFIG["socode"]["not_val"]
  
    $CONFIG["SOCODE"]=filter_input(INPUT_POST, "SOCODE");  //自定义


  
   //模板设置
    $CONFIG["templets"]=array ( 
      'off' => filter_input(INPUT_POST, "templets_off"),
     'html' => filter_input(INPUT_POST, "templets_html"),
      'css' => filter_input(INPUT_POST, "templets_css"),
      'pc' => filter_input(INPUT_POST, "templets_pc"),
     'wap' =>filter_input(INPUT_POST, "templets_wap")
);
   //空URL设置
    $CONFIG["NULL_URL"]=array ( 
  'type' => filter_input(INPUT_POST, "NULL_URL_TYPE"),
  'url' =>filter_input(INPUT_POST, "NULL_URL_URL"),
  'info' => filter_input(INPUT_POST, "NULL_URL_INFO")
);
   
}

//链接设置
if(filter_has_var(INPUT_POST, "jx_link")){
    $input=trim(filter_input(INPUT_POST, "jx_link"));
      if($input===""){
         $CONFIG["jx_link"]=[];        
     } else{
	$arr=preg_split('/[\r\n]+/s', $input);	
	foreach($arr as $key){$val=explode("=>",$key); $array[$val[0]]=$val[1];}
        $CONFIG["jx_link"]=$array;
     }
    
}

//解析设置
if(filter_has_var(INPUT_POST, "jx_url")){ 
    $input=trim(filter_input(INPUT_POST, "jx_url"));
    if($input===""){$CONFIG["jx_url"]=array();}else{$CONFIG["jx_url"]=preg_split('/[\r\n]+/s',$input);}
       
}

//直播设置
if(filter_has_var(INPUT_POST, "live_url")){
     $input=trim(filter_input(INPUT_POST, "live_url"));
     if($input===""){
         $CONFIG["live_url"]=[];        
     } else{
        $arr=preg_split('/[\r\n]+/s',$input );	
        foreach($arr as $key){$val=explode("=>",$key); $array[$val[0]]=base64_encode($val[1]);}
        $CONFIG["live_url"]=$array;
    }
  
}


//防火墙开关设置
if(filter_has_var(INPUT_POST, "BLACKLIST_OFF")){	
    $CONFIG["BLACKLIST"]['off']=filter_input(INPUT_POST, "BLACKLIST_OFF");
    $CONFIG["BLACKLIST"]['type']=filter_input(INPUT_POST, "BLACKLIST_TYPE");
}
//防火墙白名单设置

//播放器开关配置

if(filter_has_var(INPUT_POST, "play_off_link")){	
        $CONFIG["play"]['off']['jmp']=filter_input(INPUT_POST, "play_off_jmp");
	$CONFIG["play"]['off']['link']=filter_input(INPUT_POST, "play_off_link");        
        $CONFIG["play"]['off']['yun']=filter_input(INPUT_POST, "play_off_yun");
        $CONFIG["play"]['off']['jx']=filter_input(INPUT_POST, "play_off_jx");
        $CONFIG["play"]['off']['live']=filter_input(INPUT_POST, "play_off_live");
        $CONFIG["play"]['off']['submit']=filter_input(INPUT_POST, "play_off_submit");
        $CONFIG["play"]['off']['mylink']=filter_input(INPUT_POST, "play_off_mylink");
        $CONFIG["play"]['off']['help']=filter_input(INPUT_POST, "play_off_help");
        $CONFIG["play"]['off']['debug']=filter_input(INPUT_POST, "play_off_debug");
        $CONFIG["play"]['off']['posterr']=filter_input(INPUT_POST, "play_off_posterr");
        $CONFIG["play"]['off']['log']= filter_input(INPUT_POST, "play_off_log");
        $CONFIG["play"]['off']['ckplay']=filter_input(INPUT_POST, "play_off_ckplay");
        $CONFIG["play"]['off']['autoline']=filter_input(INPUT_POST, "play_off_autoline");
        $CONFIG["play"]['off']['autoflag']=filter_input(INPUT_POST, "play_off_autoflag");
        $CONFIG["play"]['off']['lshttps']=filter_input(INPUT_POST, "play_off_lshttps");
        
        
}
//播放线路配置

if(filter_has_var(INPUT_POST, "play_line_pc_line")){	
	$CONFIG["play"]['line']['pc']['line']=filter_input(INPUT_POST, "play_line_pc_line");
        $CONFIG["play"]['line']['pc']['infotime']=filter_input(INPUT_POST, "play_line_pc_infotime");
       
        $CONFIG["play"]['line']['wap']['line']=filter_input(INPUT_POST, "play_line_wap_line");
        $CONFIG["play"]['line']['wap']['infotime']=filter_input(INPUT_POST, "play_line_wap_infotime");

        $CONFIG["play"]['line']['all']['autoline']['off']=filter_input(INPUT_POST, "play_line_all_autoline_off");
        $arr=preg_split('/[\r\n]+/s', trim(filter_input(INPUT_POST, "play_line_all_autoline_val")));
        foreach($arr as $key){$val=explode("=>",$key); $array[trim($val[0])]=trim($val[1]);}
        $CONFIG["play"]['line']['all']['autoline']['val']=$array;    
}

//播放 播放配置
 
if(filter_has_var(INPUT_POST, "play_play_pc_player")){	
   
	$CONFIG["play"]['play']['pc']['player']=filter_input(INPUT_POST, "play_play_pc_player");
        $CONFIG["play"]['play']['pc']['player_diy']= filter_input(INPUT_POST, "play_play_pc_player_diy");
        $CONFIG["play"]['play']['pc']['autoplay']=filter_input(INPUT_POST, "play_play_pc_autoplay");
        $CONFIG["play"]['play']['wap']['player']= filter_input(INPUT_POST, "play_play_wap_player");
        $CONFIG["play"]['play']['wap']['player_diy']= filter_input(INPUT_POST, "play_play_wap_player_diy");
        
        $CONFIG["play"]['play']['wap']['autoplay']=filter_input(INPUT_POST, "play_play_wap_autoplay");
        
        $CONFIG["play"]['play']['all']['ver']=filter_input(INPUT_POST, "play_play_all_ver"); 
        $CONFIG["play"]['play']['all']['seektime']=filter_input(INPUT_POST, "play_play_all_seektime");
        $CONFIG["play"]['play']['all']['p2pinfo']=filter_input(INPUT_POST, "play_play_all_p2pinfo");
        $CONFIG["play"]['play']['all']['logo_off']=filter_input(INPUT_POST, "play_play_all_logo_off");
        $CONFIG["play"]['play']['all']['logo_style']=base64_encode(filter_input(INPUT_POST, "play_play_all_logo_style"));
        $CONFIG["play"]['play']['all']['danmaku']=filter_input(INPUT_POST, "play_play_all_danmaku");
       
        $CONFIG["play"]['play']['all']['contextmenu']['off']=filter_input(INPUT_POST, "play_play_all_contextmenu_off");
        $CONFIG["play"]['play']['all']['contextmenu']['val']=GetPostArr("play_play_all_contextmenu_val");   
       
        $CONFIG["play"]['play']['all']['autoline']['off']=filter_input(INPUT_POST, "play_play_all_autoline_off");
        $CONFIG["play"]['play']['all']['autoline']['val']=GetPostArr("play_play_all_autoline_val");   
}

//播放 其他配置

if(filter_has_var(INPUT_POST, "play_all_ver")){	     
	$CONFIG["play"]['all']['AppName']= filter_input(INPUT_POST, "play_all_AppName");
        $CONFIG["play"]['all']['ver']=filter_input(INPUT_POST, "play_all_ver");
        $CONFIG["play"]['all']['by']=filter_input(INPUT_POST, "play_all_by");
        $CONFIG["play"]['all']['info']=filter_input(INPUT_POST, "play_all_info");
        $CONFIG["play"]['all']['yun_title']=filter_input(INPUT_POST, "play_all_yun_title");   
        $CONFIG["play"]['all']['load_info']=filter_input(INPUT_POST, "play_all_load_info");
        $CONFIG["play"]['all']['defile_info']=filter_input(INPUT_POST, "play_all_defile_info");
        $CONFIG["play"]['all']['decode']= filter_input(INPUT_POST, "play_all_decode");  
}

//播放 样式配置

if(filter_has_var(INPUT_POST, "play_style_logo_show")){	
            
	$CONFIG["play"]['style']['logo_show']=filter_input(INPUT_POST, "play_style_logo_show");
        $CONFIG["play"]['style']['line_show']=filter_input(INPUT_POST, "play_style_line_show");
        $CONFIG["play"]['style']['list_show']= filter_input(INPUT_POST, "play_style_list_show");
        $CONFIG["play"]['style']['flaglist_show']=filter_input(INPUT_POST, "play_style_flaglist_show");
        $CONFIG["play"]['style']['playlist_show']=filter_input(INPUT_POST, "play_style_playlist_show");
        $CONFIG["play"]['style']['off']=filter_input(INPUT_POST, "play_style_off");
         file_put_contents("../save/play.css",trim(filter_input(INPUT_POST, "play_style_css")));
         
}


//播放 筛选配置 
if(filter_has_var(INPUT_POST, "play_match_yunflag")){	     
	$CONFIG["play"]['match']['yunflag']=filter_input(INPUT_POST, "play_match_yunflag");
        $CONFIG["play"]['match']['video']= filter_input(INPUT_POST, "play_match_video");
        $CONFIG["play"]['match']['urljmp']=filter_input(INPUT_POST, "play_match_urljmp");
        $CONFIG["play"]['match']['flagjmp']=filter_input(INPUT_POST, "play_match_flagjmp");
        $CONFIG["play"]['match']['urlurl']=filter_input(INPUT_POST, "play_match_urlurl");
        $CONFIG["play"]['match']['urlflag']=filter_input(INPUT_POST, "play_match_urlflag");
        $CONFIG["play"]['match']['playflag']= filter_input(INPUT_POST, "play_match_playflag");    
}

if( Main_db::save()){
	
	     exit(json_encode(array('success'=>1,'icon'=>1,'m'=>"保存成功!")));
	
	 }else{
		 exit(json_encode(array('success'=>0,'icon'=>0,'m'=>"保存失败!请检测配置文件权限")));
	} 
  

  function GetPostArr($word,$BASE64=false,$delimiter="=>")
  
  {
                $val=trim(filter_input(INPUT_POST, $word)); 
                if($BASE64){$val= base64_decode($val);}
                if($val==""){return null;}   
                $arr=preg_split('/[\r\n]+/s', $val);
                foreach($arr as $key){
                  $val=explode($delimiter,$key);
                  $key=trim($val[0]);
                  if($key!==""){$array[$key]= trim($val[1]);}
                }
                return $array;
  }      
