<?php
error_reporting(0);
define("qqzone_cookie","");  //填写你的cookie
class QZONE
{

	public static function parse($vid)
	{
		$videoinfo=array('success'=>0,'code'=>0);   
		$url = "https://h5.qzone.qq.com/proxy/domain/taotao.qq.com/cgi-bin/video_get_data?g_tk=519728470&picKey={$vid}&hostUin=44232132131&getMethod=3";
		$H5 = "";
		$C = 0;

		while ($H5 == "") {
			$H5 = self::QQzone($url);
			$mp4 = str_ireplace(');', '', str_ireplace('_Callback(', '', $H5));
			$mp4 = json_decode($mp4, true);
			$mp4 = $mp4['data']['photos'][0]['url'];
			$C++;
			if ($C > 5) {
				break;
			}
		}

        if($mp4!=""){
			$videoinfo["success"]=1;
			$videoinfo['type'] = "mp4";
			$videoinfo['ext'] = "mp4";
			$videoinfo['code'] = 200;
			$videoinfo["url"]= $mp4;
		}else{
			return array('success' => 0, 'code' => 404, 'm' => "视频未找到!");
		}

		return $videoinfo;

	}


	public static function QQzone($url)
	{
		preg_match("#p_uin=(.*?);#",qqzone_cookie, $p_uin);
		preg_match("#pt4_token=(.*?);#", qqzone_cookie, $pt4_token);
		preg_match("#p_skey=(.*?);#", qqzone_cookie, $p_skey);
		preg_match("#p_skey=(.*?)_#", qqzone_cookie, $p_skey2);
		if ($p_skey[0] == "") {
			$p_skeyx = $p_skey2[0];
		} else {
			$p_skeyx = $p_skey[0];
		};
		return  self::curl($url,$p_uin[0] . $pt4_token[0] . $p_skeyx);
	}

    public static function curl($url, $cookie = '') {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
		$params["cookie"] = $cookie;
		
        return GlobalBase::curl($url, $params);
    }

}




