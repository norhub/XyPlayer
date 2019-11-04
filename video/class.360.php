<?php 
//require_once("../include/global.inc.php");
//$url = "http://v.360kan.com/sv/boTrOWLkSxT7TC.html";
//$url = "http://m.v.360kan.com/sv/boTrOWLkSxT7TC.html";

//var_dump(ShortVideo360::parse("http://v.360kan.com/sv/boTrOWLkSxT7TC.html"));
/**
* 
*/
class ShortVideo360
{	
	public static function parse($url)
    {
        $params = parse_url($url);
    	if($params["host"]=="v.360kan.com"){
    		$url = str_replace("v.360kan.com", "m.v.360kan.com", $url);
    	}
    	$content = self::curl($url);     
    	preg_match("#'playurl'\s*:\s*'(.*?)',#",$content,$playurl);
	 $purl = $playurl[1];
		return self::get_video_url($purl);
    }
    public static function get_video_url($api)
    {
        $videoinfo = array('success' => 0, 'code' => 0);
        $content = self::curl($api);
     	$data = json_decode($content,true);
     	if(!empty($data["result"])){
	        $result = $data["result"];
	        $cover = $result["pic"];
	        $videoinfo["poster"] = $cover;
	        $videos = $result["videos"];
	        $vurl = $videos[0]["url"];
     		$videoinfo["video"]["file"] = $vurl;
		$videoinfo["video"]["type"] = "video/m3u8";
                
            $videoinfo['success'] = 1;
            $videoinfo['ext'] = "m3u8";
            $videoinfo['type'] = "hls";
            $videoinfo['code'] = 200;
            $videoinfo['url'] = $vurl;
            $videoinfo['pic'] = $cover;
  
	        return $videoinfo;
	    }
    }
    public static function curl($url)
    {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        return GlobalBase::curl($url,$params);
    }
}
