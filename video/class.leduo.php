<?php
//XMMjM5MzEwMDAwXzE=
error_reporting(0);
class LEDUO {
    public static function parse($url) {
            $api ="https://api.leduotv.com/wp-api/glid.php?vid=";            
            return self::get_video($api.$url);
    }
   public static function get_video($url) {
        $videoinfo = array('success' => 0, 'code' => 0);
        $base = parse_url($url);
        $base["scheme"] = $base["scheme"] == '' ? GlobalBase::is_https() : $base["scheme"] . '://';
        $port = isset($base["port"]) ? ":" . $base["port"] : "";
        $host = $base["scheme"] . $base["host"] . $port;
        $content = self::curl($url); $_vurl = array();           
        $m3u8 = preg_match("{url\s*=\s*'(.*?)'}i", $content, $_vurl) ? $_vurl[1] : "";

        if ($m3u8 !== ""){
           // $m3u8 = GlobalBase::is_root()."/video/m3u8.php?url=" . base64_encode($m3u8) . "#.m3u8";
            $videoinfo['success'] = 1;
            $videoinfo['ext'] = "m3u8";
            $videoinfo['type'] = "hls";
            $videoinfo['code'] = 200;
            $videoinfo['url'] = $m3u8;
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
