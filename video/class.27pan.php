<?php
//27panPacuvi4UtgqtPv0J
error_reporting(0);
class PAN27 {
    public static function parse($url) {
        if (strstr($url, "27pan")) {           
            $api ="https://512.jp277.com/share/";            
            $api.=str_replace("27pan","",$url);      
         
            return self::get_video($api);
  
        } else if (explode("/", parse_url($url)["path"])[1] == "share") {
            return self::get_video($url);
        }
        return array('success' => 0, 'code' => 403, 'm' => "视频未找到!");
    }
   public static function get_video($url) {
        $videoinfo = array('success' => 0, 'code' => 0);
        $base = parse_url($url);
        $base["scheme"] = $base["scheme"] == '' ? GlobalBase::is_https() : $base["scheme"] . '://';
        $port = isset($base["port"]) ? ":" . $base["port"] : "";
        $host = $base["scheme"] . $base["host"] . $port;
        $content = self::curl($url); $_vurl = array();$_pic = array();            
        $m3u8 = preg_match('#m3u8Url\s*=\s*"(.*?)";#', $content, $_vurl) ? $host . $_vurl[1] : "";
        $pic = preg_match('#picUrl\s*=\s*"(.*?)";#', $content, $_pic) ? $host . $_pic[1] : ""; 
        if ($m3u8 !== ""){
            $m3u8 = GlobalBase::is_root()."/video/m3u8.php?url=" . base64_encode($m3u8) . "#.m3u8";
            $videoinfo['success'] = 1;
            $videoinfo['ext'] = "m3u8";
            $videoinfo['type'] = "hls";
            $videoinfo['code'] = 200;
            $videoinfo['url'] = $m3u8;
            $videoinfo['pic'] = $pic;
        }else{
            return array('success' => 0, 'code' => 404, 'm' => "视频未找到!");
        }
        return $videoinfo;
    }
    public static function curl($url, $ref = '') {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $params['ref'] = $ref;
        return GlobalBase::curl($url, $params);
    }

}
