<?php 

error_reporting(0);
 
 //加载云配置文件

 require_once dirname(__FILE__).'/../save/yun.config.php'; 
 require_once dirname(__FILE__).'/../save/yun.match.php';
 require_once dirname(__FILE__).'/../save/yun.data.php';

class YUN
{
	public static function parse($val,$type=1)	
	{    
		global $YUN_MATCH;	 

	 switch ($type){				  	  
		 case 1 : //地址播放视频				 		    
                  $val=str_replace($YUN_MATCH["url_replace"],"",urldecode($val)); //url地址过滤		
		return self::getvideo($val);						 
		 case 2:	//标题播放视频		 
		    return self::getvideo($val,true);						
		case 3 :	//	搜索资源 使用ID   		
		   return self::getvideobyid($val["flag"],$val["id"]);
		  
		case 4 :   //搜索资源 使用标题
            return self::getnames(urldecode($val));  

		default:
		
		   return array('success'=>0,'code'=>0,'m'=>'input error!');
		
	  }	
		
   }
//检测链接跳转
 public static function getjmp($val,&$url,&$name,&$num)
 {
     global  $YUN_DATA;
     foreach ($YUN_DATA["url_jmp"] as $value) {
         if(preg_match('{'.$value['url'].'}i', $val)){ 
             $url=$value['href'];$name=$value['title'];$num=$value['part'];  
             return true;
         }
     }
    return false;
 }
   
//根据url或视频名称取视频信息
 public static function getvideo($val,$lswrod=false)
 {	  
	global $YUN_MATCH,$YUN_CONFIG; 
	;$num=1;$name="";$url="";$api=$YUN_CONFIG["API"];
	$videoinfo=array('success'=>0,'code'=>0);
    	if($lswrod){
                 $name=$val; 
         }else{
              if ($YUN_CONFIG["url_filter"]!="" && !preg_match('/'.$YUN_CONFIG["url_filter"]."/i",$val)){ $videoinfo['code']=0;$videoinfo['m']="url error!";return $videoinfo;  }
                 
				 
				 
              if(!self::getjmp($val,$url,$name,$num)){if(!self::getname($val,$name,$num)){$videoinfo['code']=0;$videoinfo['m']="getname error!";return $videoinfo;}}
              }	  
          if(filter_input(INPUT_GET, 'dd')){echo"{'name':$name,'num':$num}";}
	        //404判断,使用精确匹配  
          if( $YUN_MATCH["ERROR_404"]!="" &&  self::findstrs($name,$YUN_MATCH["ERROR_404"])){$videoinfo['code']=404;$videoinfo['m']="404 NOT FOUND";return $videoinfo;}
	  		
    for($i=0;$i<sizeof($api);$i++)
   {
	  $_api=explode("=>",$api[$i])[1];	 
	  $id=self::getid($_api,$name,$num);if(!$id){continue;}  	
	 
	  //取视频信息
          $data=self::curl($_api."?ac=videolist&ids=".$id);	
	  
	  if($data==''){continue;}  	  
	 	 	 
	  $xml = simplexml_load_string($data);  if(empty($xml)){continue;}$ret=true;
	  	 
	   foreach($xml->list->video->dl->dd as $video)	    
	   {   
	     $flag=(string)$video->attributes();    
             $vod=explode("#",(string)$video);
		 //检测是否存在名称，兼容苹果CMS		 
		 if(!strpos($vod[0],"$")){foreach($vod as &$mov){$mov="高清$".$mov;}}
		 if($YUN_CONFIG["flag_filter"]=="" ||preg_match('/'.$YUN_CONFIG["flag_filter"]."/i",$flag)){$info[]=array('flag'=>$flag,'flag_name'=>$YUN_CONFIG["flag_replace"][$flag] ? $YUN_CONFIG["flag_replace"][$flag]:$flag,'site'=>$i,'part'=>sizeof($vod),'video'=>$vod,);}             			 
		//匹配期数
		$matches=array();		
	        $vods = preg_match('!#'.(string)$num.'.*?\$(.*?)(?=\$|#)!i', $video, $matches) ? trim($matches[1]) : '';
		if ($vods!=''){$videoinfo['url']=$vods;$videoinfo['play']=$flag;}		   
	   
	    } 
		
	}  //for end 
    
  //检查集数或期数，如果未匹配则返回假
   if ($ret && ($vods||sizeof($info[0]['video'])>=$num))
   {		  		 
		 //结果先按集数降序排列再按资源站先后顺序排列。        
		foreach ( $info as $key => $row )
		{		   
           $num1[$key] = $row ['part'];
	   $num2[$key] = $row ['site'];		   
        } 
        array_multisort($num1,SORT_DESC ,$num2,SORT_ASC,$info);	
		    
		//检查集数，如果未匹配集数，设置为最后一个视频
		if(!$vods){$max=sizeof($info[0]['video']);if($max<$num){$num=$max;};}		 
		$vod=@$info[0]['video'][$num-1];$vod=explode('$', $vod); 
		//类型转换
		$type=$info[0]['flag'];		
		foreach($YUN_MATCH["type_match"] as $key => $val ){			
		    if (preg_match($key,$type,$matches)){			 
			    $type=$val;break;			 
		   }else if(preg_match($key,@$vod[1],$matches)){				 
			     $type=$val;break;				 
		    }				
		}
		 
		 //输出数据			 
		 $videoinfo['success']=1; 
		 $videoinfo['code']=200;
		 $videoinfo['title']=$name;
		 $videoinfo['part']=$num;
		 $videoinfo['url']=$url?$url:$vod[1];	
		 $videoinfo['type']=$type;			 
	     $videoinfo['info']=$info;
		 
    }else{
		
		 $videoinfo['m']="未找到资源!";
	}
	
  return $videoinfo;	
 
 
 }
//根据资源站序号及视频ID取视频信息
 public static function getvideobyid($flag,$id)
 {	 
      global  $YUN_MACTH,$YUN_CONFIG;
     
      $api=explode("=>",$YUN_CONFIG["API"][$flag])[1]; 
	  
	 $data=self::curl($api."?ac=videolist&ids=".$id);if($data==''){return false;}      
      $xml = simplexml_load_string($data);  if(empty($xml)){return false;}    	 		
	  $img=$xml->list->video->pic.""; $name=$xml->list->video->name."";	
	   foreach($xml->list->video->dl->dd as $video)	    
	   {        
	     $flag=(string)$video->attributes(); 
             $flag_name=$YUN_CONFIG["flag_replace"][$flag] ? $YUN_CONFIG["flag_replace"][$flag]:$flag;
              $vod=explode("#",(string)$video);         
		  //检测是否存在名称，兼容苹果CMS		 
		 if(!strpos($vod[0],"$")){foreach($vod as &$mov){$mov="高清$".$mov;}}		 		  	 		 			 			  				  			  
                 
                 if($YUN_CONFIG["flag_filter"]=="" || preg_match('/'.$YUN_CONFIG["flag_filter"]."/i",$flag)){$info[]=array('flag'=>$flag,'flag_name'=>$flag_name,'part'=>sizeof($vod),'video'=>$vod,);}
		
		}  
	 
		 //结果按集数降序排列。        
		 foreach ($info as $key => $row ){$num1[$key] = $row ['part'];} array_multisort($num1,SORT_DESC ,$info);
                	
               $vod=$info[0]['video'][0];$vod=explode('$', $vod); 
			
     	//类型转换
		$type=$info[0]['flag'];		
		foreach($YUN_MACTH["type_match"] as $key => $val ){			
		    if (preg_match($key,$type)){			 
			    $type=$val;break;			 
		   }else if(preg_match($key,$vod[1])){				 
			     $type=$val;break;				 
		    }				
		}
		
	
		//输出数据		 
		 $videoinfo['success']=1; 
		 $videoinfo['code']=200;
		 $videoinfo['url']=$vod[1];
         $videoinfo['pic']=$img;	 
		 $videoinfo['title']=$name;
		 $videoinfo['part']=1;	
		 $videoinfo['type']=$type;	         	 
	     $videoinfo['info']=$info;
	    return $videoinfo;

 }
 //视频搜索
  public static function getnames($name)
 {
    global $YUN_MACTH,$YUN_CONFIG;$api=$YUN_CONFIG["API"];
    $videoinfo=array('success'=>0,'code'=>0);
   
  for($i=0;$i<sizeof($api);$i++){ 
        $_api=explode("=>",$api[$i]); 
        $data=self::curl($_api[1]."?wd=".$name); 
              
        if(!$data){break;}		
		$xml = simplexml_load_string($data);  
		  foreach($xml->list->video as $video)	    
	    {   			 
			 $id=(string)$video->id;
                         $type=(string)$video->dt;
			 $title=(string)$video->name;
                         $flag_name=$YUN_CONFIG["flag_replace"][$type] ? $YUN_CONFIG["flag_replace"][$type]:$type;
                         //搜索资源过滤
                         if($YUN_CONFIG["flag_filter"]==='' || !preg_match('!'.$YUN_CONFIG["flag_filter"].'!i', $title)){
                             
                             $info[]=array('flag'=>$i,'flag_name'=>$flag_name,'from'=>$_api[0],'type'=>$type,'id'=>$id,'title'=>urlencode($title),'img:'=>'null');
                              
                         }
 
	    }      
  }
	  	if(isset($info)){$videoinfo['success']=1;  $videoinfo['info']= $info;}else{$videoinfo['code']=404;}
		 $videoinfo['title']=$name; 
		 
                
                 return $videoinfo;
 
 }
 //取视频ID
  public static function getid($api,$name,$num)
 {
	  
	
     //根据标题取ID
	 $data=self::curl($api."?wd=".urlencode($name)); if ($data==""){return false;};	 

         
	 $xml=simplexml_load_string($data);$forst=false;
	 
	 if(!$xml){return false;}
	
	 //匹配标题对应ID
	 foreach($xml->list->video as $video)	    
	    {   			 			 
			$id=(string)$video->id;						
			 $video=(string)$video->name;		  
			 if ($video==$name){ return $id;} 		
		} 	 
	//如果未找到，取集数匹配的视频	
	if($num>1){
     	foreach($xml->list->video as $video)	    
	    {   			 			 
			$id=(string)$video->id;	
			$ret=self::curl($api."?ac=videolist&ids=".$id);if($ret==''){return false;} 
			$xm = simplexml_load_string($ret); if(empty($xm)){return false;}  
			$ret=(string)$xm->list->video->dl->dd[0];
			$vod=explode("#",$ret);
			if(sizeof($vod)>=$num){return $id;}
		}
    }			  
	  //如果还未未匹配到就取第一个资源；	 
	  
	$matches=[];  
    $id = preg_match('!<id>(\d*?)</id>!i', $data, $matches) ? trim($matches[1]) : '';	
    if ($id==""){return false;}				 
	return $id;
	 
 
 }
 //取视频名称及集数
 public static function getname($url,&$name,&$num)
 {
      global $YUN_MATCH,$YUN_CONFIG;
     
     
     //$title_replace,$title_match,$name_match,$url_match;
      $title='';$name='';$num=1;
   
  //视频地址替换,用于移动转换为pc版本
     foreach ($YUN_MATCH["url_match"] as $val => $value) 
    {     	 	 
	 if (preg_match($val,$url,$matches)){		  		 		 		         
		  
		  for ($i=1;$i<sizeof($matches);$i++)
		    {		  	    
		     $value=str_replace('(?'.(string)$i.')',$matches[$i] ,$value);		   
		    }	       
		   $url=$value;
           break;	 
		}
     }
	
     $data=self::curl($url);
	 
	 
	
     
     if($data==''){if(function_exists('file_get_contents')){$data= file_get_contents($url);}} if($data==""){return false;}
	 
   
   
  //调用配置预设正则，获取视频标题。
  foreach($YUN_MATCH["title_match"] as  $val=>$value) 
  {    
	 if (preg_match($val, $url))		 
	  {	    
             foreach  ($value as $word) 
		 {	  
                 if (preg_match($word, $data, $matches))
                      {	                        
                           $title=$matches[1]?utf8(trim(strip_tags($matches[1]))):'';                   
                           break; 				   
	                 }	
		 }
          if($title!=""){break;} 	 
	  }  
  }
//过滤换行符
   $title=trim(str_replace(array("\r\n","\n","\r"),"",$title));
 

 //调用配置预设正则，获取视频名称和集数。
  foreach($YUN_MATCH["name_match"] as  $val=>$value) 
  {    
	 if (preg_match($val, $url))		 
	  {	     	 		  		    	 
           
             foreach  ($value as $word) 
		 {	
              if (preg_match($word, $title, $matches))	          
			 {	
			   $name=$matches[1] ? trim($matches[1]):'';
			   $num=$matches[2] ? trim($matches[2]):1;
			   break;	 				   
	          }	
		 }
        if($name!=""){break;} 	 
	  }  
  }
     $name=trim(str_replace($YUN_MATCH["title_replace"],"",$name));
        return ($name!== "");   
 } 	
	 //检测字符串组的字符在字符串中是否存在
    public static function findstrs($str,$find,$separator="|")
	{
	  $ymarr = explode($separator,$find);
	  foreach ($ymarr as  $find){ if(strcasecmp($str,$find)==0){return true;}}  	 
      return false;
    }
	 
	public static function curl($url,$ref='')
	{
    	$params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    	$params['ref'] = $ref;
      	return GlobalBase::curl($url,$params);
	}

	
}

