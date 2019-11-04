<?php 
class VIDEO
{	
	public static function parse($url)
	{
       $videoinfo=array('success'=>0,'code'=>0);        
	  if(stristr($url,".mp4")!==false){ 
               $videoinfo['title'] = 'MP4直链播放';
		       $videoinfo['url'] =$url;
			   $videoinfo['success'] = 1;
			   $videoinfo['type'] = "mp4";
			   $videoinfo['ext'] = "mp4";
			   $videoinfo['code'] = 200;		
		       return $videoinfo;
      }elseif (stristr($url,".m3u8")!==false){

               $videoinfo['title'] = 'M3U8直链播放';
		       $videoinfo['url'] =$url;
			   $videoinfo['success'] = 1;
			   $videoinfo['type'] = "hls";
			   $videoinfo['ext'] = "m3u8";
			   $videoinfo['code'] = 200;		
		       return $videoinfo;
	 
      }elseif (stristr($url,".flv")!==false){
      
               $videoinfo['title'] = 'FLV直链播放';
		       $videoinfo['url'] =$url;
			   $videoinfo['success'] = 1;
			   $videoinfo['type'] = "flv";
			   $videoinfo['ext'] = "flv";
			   $videoinfo['code'] = 200;		
		       return $videoinfo;		
      }
	        return $videoinfo;
	
	}

}
