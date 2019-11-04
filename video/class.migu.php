<?php
define("MIGU_COOKIE", '');
define("ROOT_PATH", $ROOT_PATH ? $ROOT_PATH:GlobalBase::is_root());
define("M3U8", ROOT_PATH."video/m3u8.php?url=");
class MIGU
{
	public static function parse($url)
    {
    //header('Content-type: text/json;charset=utf-8');
   //直链解析/?url=http://http://www.miguvideo.com/wap/resource/pc/detail/miguplay.jsp?cid=638224850
   //ID解析/?url=638224850

    $videoinfo=array('success'=>0,'code'=>0); 

    $cid = explode("cid=", $url);if($cid[1] != ""){$cid=$cid[1];}else{ $cid=$url;}

    $ref="http://www.miguvideo.com/wap/resource/pc/detail/miguplay.jsp?cid=".$cid;

    $urlx=self::curl($ref); preg_match('#id="sessionID"\s*value="(.*?)">#',$urlx,$clientId);

    //$url="http://www.miguvideo.com/playurl/v1/play/playurlh5?contId=".$cid."&rateType=1,2,3&clientId=".$clientId[1];

    $url="http://www.miguvideo.com/gateway/playurl/v3/play/playurl?contId=".$cid."&rateType=2%2C3&clientId=".$clientId[1];


 //exit($url);

    $content = json_decode(self::curl($url,$ref,MIGU_COOKIE),true);
    if(!is_array($content)){ return array('success' => 0, 'code' => 404, 'm' => "解析失败!");}
   
   $content0 =$content['body']['urlInfos'][0]['url'];//0为标清 1为高清 2为超清
   $content1 =$content['body']['urlInfos'][1]['url'];//0为标清 1为高清 2为超清
   $content2 =$content['body']['urlInfos'][2]['url'];//0为标清 1为高清 2为超清
   
   if($content2 != ""){
       $m3u8=$content2;
   }elseif($content1 != ""){
       $m3u8=$content1;
   }else{
       $m3u8=$content0;
   }
   
   $m3u8 = str_replace("h5vod.gslb.cmvideo.cn","vod.hcs.cmvideo.cn:8088", $m3u8);//mgzb.vod.miguvideo.com:8088 

     if($m3u8!=""){
        $videoinfo["success"]=1;
        $videoinfo['type'] = "m3u8";
        $videoinfo['ext'] = "m3u8";
        $videoinfo['code'] = 200;
        $videoinfo["url"]= M3U8.urlencode($m3u8) ;

     }else{
        return array('success' => 0, 'code' => 404, 'm' => "视频未找到!");

     }
     return $videoinfo;

    }
        
    public static function curl($url, $ref = '',$cookie='') {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
		$params["ref"] = $ref;
		$params["cookie"] = $cookie;
        return GlobalBase::curl($url, $params);
    }

}

