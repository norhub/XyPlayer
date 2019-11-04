<?php
/*##################################################
# xypaly 智能视频解析 X   by http://bbs.52jscn.com
# 官方网站: http://bbs.52jscn.com
# 源码获取：http://bbs.52jscn.com 
# 模块功能：公用文件
###################################################*/
//不显示读取错误
ini_set("error_reporting", "E_ALL & ~E_NOTICE");

header('Content-Type:text/html;charset=utf-8');

  // 检测PHP环境
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	die('PHP版本过低，最少需要PHP5.4，请升级PHP版本！');
}

class GlobalBase
{
    /**
     * [curl 网页数据获取]
     * @param  [type] $url    [访问 URL 地址]
     * @param  string $method [访问方式]
     * @param  string $fields [要提交的数据]
     * @param  string $ckname [cookie 文件名]
     * @return [type]         [返回访问结果字符串数据]
     */
    public static function curl($url, $params = array(), &$Headers = null)
    {

        $ip = empty($params["ip"]) ? self::rand_ip() : $params["ip"];
        $header = array('X-FORWARDED-FOR:' . $ip, 'CLIENT-IP:' . $ip);
        if (isset($params["httpheader"])) {
            $header = array_merge($header, $params["httpheader"]);
        }
        $referer = empty($params["ref"]) ? $url : $params["ref"];
        $user_agent = empty($params["ua"]) ? $_SERVER['HTTP_USER_AGENT'] : $params["ua"];

        $ch = curl_init();                                                      //初始化 curl
        curl_setopt($ch, CURLOPT_URL, $url);                                    //要访问网页 URL 地址
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                          //伪装来源 IP 地址
        curl_setopt($ch, CURLOPT_REFERER, $referer);                            //伪装网页来源 URL
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);                        //模拟用户浏览器信息
        curl_setopt($ch, CURLOPT_NOBODY, false);                                //设定是否输出页面内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                         //返回字符串，而非直接输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);                        //连接超时时间，设置为 0，则无限等待
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);                                //数据传输的最大允许时间超时,设为一小时
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                       //HTTP验证方法
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                        //不检查 SSL 证书来源
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);                        //不检查 证书中 SSL 加密算法是否存在
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);                         //跟踪爬取重定向页面
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);                            //当Location:重定向时，自动设置header中的Referer:信息
        curl_setopt($ch, CURLOPT_ENCODING, '');                                 //解决网页乱码问题
        curl_setopt($ch, CURLOPT_HEADER, empty($params["header"]) ? false : true);  //是否输出 header 部分
        if (!empty($params["fields"])) {
            curl_setopt($ch, CURLOPT_POST, true);                                  //设置为 POST 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params["fields"]);                //提交数据
        }
        if (!empty($params["cookie"])) {
            curl_setopt($ch, CURLOPT_COOKIE, $params["cookie"]);                  //从字符串传参来提交cookies
        }
        if (!empty($params["proxy"])) {
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);                  //代理认证模式
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);                  //使用http代理模式
            curl_setopt($ch, CURLOPT_PROXY, $params["proxy"]);                    //代理服务器地址 host:post的格式
            if (!empty($params["proxy_userpwd"])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $params["proxy_userpwd"]); //http代理认证帐号，username:password的格式
            }
        }

        $data = curl_exec($ch);
        $Headers = curl_getinfo($ch);

        //运行 curl，请求网页并返回结果
        curl_close($ch);                                                        //关闭 curl
        return $data;
    }

  
    /**
     * [rand_ip 生成随机 IP 地址]
     * @return [type] [返回 IPv4地址 字符串]
     */
    public static function rand_ip()
    {
        $ip_long = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889') //222.16.0.0-222.95.255.255
        );
        $rand_key = mt_rand(0, 9);
        $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        return $ip;
    }





    /**
     * [is_https 是否是安全连接访问]
     * @return boolean [description]
     */
    public static function is_https()
    {
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return "https://";
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return "https://";
        } elseif (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return "https://";
        } elseif (isset($_SERVER["REQUEST_SCHEME"]) && $_SERVER["REQUEST_SCHEME"] === 'https') {
            return "https://";
        }
        return "http://";
    }
    public static function is_dir()
    {
        $root = str_replace("\\", "/", filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'));
        if ($root == "") {
            return "/";
        }
        $dir = str_replace("\\", "/", str_replace("include", "", dirname(__FILE__)));
        if ($dir == "") {
            return "/";
        }
        $out = str_replace($root, "", $dir);
        //前后加"/"
        if (substr($out, 0, 1) !== "/") {
            $out = "/" . $out;
        }
        if (substr($out, -1, 1) !== "/") {
            $out .= "/";
        }
        return $out;
    }
    public static function is_root()
    {
        return self::is_https() . filter_input(INPUT_SERVER, 'HTTP_HOST') . self::is_dir();
    }

    public static function is_time($time)
    {
        if (preg_match("/^(\d+)(.*?)$/i", $time, $key)) {

            if (sizeof($key) < 2) {
                return 0;
            }

            switch ($key[2]) {
                case "d":
                    return $key[1] * 24 * 60 * 60 * 1000;
                case "h":
                    return $key[1] * 60 * 60 * 1000;
                case "m":
                    return $key[1] * 60 * 1000;
                case "s":
                    return $key[1] * 1000;
                case "ms":
                    return $key[1];
                default:
                    return $key[1];
            }
        } else {
            return 0;
        }
    }


    /**
     * [getdirs 取指定目录下的子目录数组]
     * @return array [dir]
     */
    public static function getdirs($dir)
    {

        if (is_dir($dir) && is_readable($dir)) {
            $handle = opendir($dir);
            $f_dir = array();
            while (($f_name = readdir($handle)) != false) {
                if (is_dir($dir . '/' . $f_name) && $f_name != "." && $f_name != "..") {
                    $f_dir[] = $f_name;
                }
            }
            closedir($handle);
            return $f_dir;
        } else {
            return false;
        }
    }
}


/** 
 * js escape php 实现 
 * @param $string           the sting want to be escaped 
 * @param $in_encoding       
 * @param $out_encoding      
 */
function escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2')
{
    $return = '';
    if (function_exists('mb_get_info')) {
        for ($x = 0; $x < mb_strlen($string, $in_encoding); $x++) {
            $str = mb_substr($string, $x, 1, $in_encoding);
            if (strlen($str) > 1) { // 多字节字符 
                $return .= '%u' . strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding)));
            } else {
                $return .= '%' . strtoupper(bin2hex($str));
            }
        }
    }
    return $return;
}

//文本加密函数
function strencode($string, $key = 'xyplay')
{
    $string = base64_encode($string);
    $len = strlen($key);
    $code = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $k = $i % $len;
        $code .= $string[$i] ^ $key[$k];
    }
    return base64_encode($code);
}

function lsUserAgen($key)
{
    return preg_match('/' . $key . "/i", @$_SERVER['HTTP_USER_AGENT']);
}

function lsReferer($key)
{
    return preg_match('/' . $key . "/i", parse_url(@$_SERVER['HTTP_REFERER'], PHP_URL_HOST));
}

//广告过滤类

class AdBlack
{
    static $path = "";

    public static function parse($list, $path)
    {

        $url = filter_input(INPUT_GET, $list["name"]);
        if (empty($url)) {
            return "";
        }
        $key =array();
        $path = preg_match("#^((http://|https://).*?)/#i", $url, $key) ? $key[1] : $url;
        self::$path = $path;


        $match = $list["match"];
        if (!sizeof($match) > 0) {
            return self::curl($url);
        }

        //规则按优先级降序排列  
        foreach ($match as $key => $row) {
            $num[$key] = $row['num'];
        }
        array_multisort($num, SORT_DESC, $match);


        foreach ($match as $m) {
            if ($m["off"] == "1" && preg_match("{" . Base64_decode($m["target"]) . "}", $url)) {

                $word = self::curl($url, $url);                                //原始内容
                $word = self::black_replace($word, $m["val"]);                //主体替换
                $word = self::frame_replace($word, $list["name"]);           //框架替换
                break;
            }
        }

        //file_put_contents("noad.html",$word);

        return  $word;
    }

    public static function black_replace($word, $match)
    {
        //规则替换
        foreach ($match as $key => $val) {
            $word = preg_replace("{" . $key . "}i", $val, $word);
        }

        //智能转换POST资源路径
        $word = preg_replace_callback(
            '{(\$\.post\()"(.*?)"}i',
            function ($matches) {
                return $matches[1] . '"'.self::put_url($matches[2]) . '"';
            },
            $word
        );

        //智能转换HTML资源路径
        $type = array("action", "src", "href");
        foreach ($type as $val) {
            $word = preg_replace_callback(
                "{($val)=" . '"(.*?)"}i',
                function ($matches) {
                    return $matches[1] . '="' . self::put_url($matches[2]) . '"';
                },
                $word
            );
        }


        //file_put_contents("noad.htm",$word);

        return $word;
    }

    public static function frame_replace($word, $jx)
    {
        if (preg_match_all('{<iframe.*?src="(.*?)".*?</iframe>}i', $word, $matchs)) {
            foreach ($matchs[1] as $val) {
                $word = preg_replace('{' . $val . '}', '/?jx=' . self::put_url($val), $word);
            }
        }
        return $word;
    }



    public static  function put_url($url, $path = "")
    {
        if (empty($path)) {
            $path = self::$path;
        }
        if (substr($url, 0, 4) == "http" || substr($url, 0, 2) == "//") {
            return $url;
        } else if (substr($url, 0, 1) == "/") {
            return $path . $url;
        } else {
            return $path . "/" . $url;
        }
    }

    public static function curl($url, $ref = '')
    {
        $params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $params['ref'] = $ref;
        return GlobalBase::curl($url, $params);
    }
}


//防火墙类
class Blacklist

{

    public static function parse($list)
    {

        if ($list['off'] == 1) {
            self::black($list);
        }
    }

    public static function shell($match, $list, $type = '')
    {

        switch ($type) {
                //来源域名
            case '0':
                //取出来源域名  

                $val = trim(filter_input(INPUT_SERVER, "HTTP_REFERER"));
                if ($val) {
                    $val = parse_url($val, PHP_URL_HOST);
                }
                //取出解析域名
                $host = filter_input(INPUT_SERVER, "HTTP_HOST");

                //调试选项
                if($_GET["dd"]==1){
                        echo "解析域名:".$_SERVER['HTTP_HOST']."<br>";
                        echo "来源域名:$val<br>";
                        echo "授权域名:".implode(",", $match['val']).'<br>';
                        echo "是否授权:".(in_array($val, $match['val'])? "是":"否");
                        exit;
                }
               //排除解析域名
                if ($host !== $val) {
                  
                    if (in_array($val, $match['val']) == $match['match']) {
                        self::shell($match, $list);
                    }
                }
                break;
                //目标域名
            case '1':
                $val = isset($_REQUEST['v']) ? $_REQUEST['v'] : $_REQUEST['url'];    //$val=parse_url( $val,PHP_URL_HOST);	//取出目标域名	
                if (preg_match('{' . implode("|", $match['val']) . "}i", $val) == $match['match']) {
                    self::shell($match, $list);
                }

                break;
                //浏览器标识
            case '2':
                $val = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';        //取出浏览器标识			
                if (preg_match('{' . implode("|", $match['val']) . "}i", $val) == $match['match']) {
                    self::shell($match, $list);
                }
                break;
                //客户IP
            case '3':
                $val = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';    //取出IP	
                if (preg_match('{' . implode("|", $match['val']) . "}i", $val) == $match['match']) {
                    self::shell($match, $list);
                }

                break;
            default:
                //取出脚本
                $shell = base64_decode($list['black'][$match['black']]['info']);
                //取出脚本类型
                $type = $list['black'][$match['black']]['type'];
                //取出脚本动作
                $action = $list['black'][$match['black']]['action'];
                //if($type=='0'){ if(!$all){echo $shell;}if($action=='1'){exit;}}else{eval($shell);if($action=='1'){exit;}}
                if ($type == '0') {
                    if ($action == '0') {
                        session_start();
                        $_SESSION['FOOTER_CODE'] = $shell;
                    } else {
                        exit($shell);
                    }
                } else {
                    eval($shell);
                    if ($action == '1') {
                        exit;
                    }
                }
                break;
        }
    }

    public static function black($list)
    {


        $match = $list['match'];

        //规则按优先级升序排列，数字越小，优先级越高     
        foreach ($match as $key => $row) {
            $num[$key] = $row['num'];
        }
        array_multisort($num, SORT_ASC, $match);

        foreach ($match as $key) {
            if ($key['off'] == 1 && preg_match("{" . $key['for'] . "}i", $_SERVER['PHP_SELF'])) {
                self::shell($key, $list, $key['type']);
            }
        }
    }
}


//检测字符串组的字符在字符串中是否存在,对大小写不敏感
function findstrs($str, $find, $strcmp = false, $separator = "|")
{
    $ymarr = explode($separator, $find);
    foreach ($ymarr as  $find) {

        if ($strcmp) {
            if (strcasecmp($str, $find) == 0) {
                return true;
            }
        } else {
            if (stripos($str, $find) !== false) {
                return true;
            }
        }
    }
    return false;
}



//获取远程内容
function geturl($url, $timeout = 10)
{
    $user_agent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $curl = curl_init();                                        //初始化 curl
    curl_setopt($curl, CURLOPT_URL, $url);                      //要访问网页 URL 地址
    curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);           //模拟用户浏览器信息 
    curl_setopt($curl, CURLOPT_REFERER, $url);               //伪装网页来源 URL
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);                //当Location:重定向时，自动设置header中的Referer:信息                   
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);             //数据传输的最大允许时间 
    curl_setopt($curl, CURLOPT_HEADER, 0);                     //不返回 header 部分
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);            //返回字符串，而非直接输出到屏幕上
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);             //跟踪爬取重定向页面
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');        //不检查 SSL 证书来源
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');        //不检查 证书中 SSL 加密算法是否存在
    curl_setopt($curl, CURLOPT_ENCODING, '');              //解决网页乱码问题
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}
function lsMobile()
{
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    return false;
}

//编码转换，转换为utf-8编码
function utf8($title)
{
    $encode = mb_detect_encoding($title, array('GB2312', 'GBK', 'UTF-8', 'CP936')); //得到字符串编码
    if ($encode != 'CP936' && $encode != 'UTF-8') {
        $title = iconv($encode, 'UTF-8', $title);
    }
    return $title;
}


//缓存操作类	
class Main_Cache
{
    private $cachetype = 1;            //默认缓存类型,1为文件，2为Redis服务
    private $cacheprot = 6379;         //缓存服务端口，默认为Redis服务端口
    private $cacheTime = 3600;        //默认缓存时间,单位微秒。  
    private $cacheDir = './cache';    //缓存绝对路径   
    private $md5 = true;              //是否对键进行加密   
    private $suffix = "";         //设置文件后缀       
    private $cache;
    public function __construct($config)
    {

        if ($this->cachetype == 0) {
            return;
        }

        if (is_array($config)) {
            foreach ($config as $key => $val) {
                $this->$key = $val;
            }
        }

        if ($this->cachetype == 2) {

            $this->cache = new Redis();
            $this->cache->connect('127.0.0.1', $this->cacheprot);
        }
    }
    //设置缓存   
    public function set($key, $val, $leftTime = NULL)
    {

        if ($this->cachetype == 0) {

            return  false;
        } else if ($this->cachetype == 1) {
            $key = $this->md5 ? md5($key) : $key;
            $val = $this->md5 ? base64_encode($val) : $val;
            if (function_exists("gzcompress")) {
                $val = @gzcompress($val);
            }
            !file_exists($this->cacheDir) && mkdir($this->cacheDir, 0777);
            $file = $this->cacheDir . '/' . $key . $this->suffix;
            $leftTime = empty($leftTime) ? $this->cacheTime / 1000 : $leftTime;
            $ret = file_put_contents($file, $val) or $this->error(__line__, "文件写入失败");
            $ret = touch($file, time() + $leftTime) or $this->error(__line__, "更改文件时间失败");
        } else if ($this->cachetype == 2) {
            $key_md5 = $this->md5 ? md5($key) : $key;
            $val_base64 = $this->md5 ? base64_encode($val) : $val;
            $val_base64 = @gzcompress($val_base64);
            $ret = $this->cache->set($key_md5, $val_base64);
            if ($leftTime != 0) {
                $this->cache->EXPIRE($key_md5, $leftTime);
            }
            // $this->cache->del($val_base64); 
        }
        return   $ret;
    }

    //得到缓存   
    public function get($key)
    {

        if ($this->cachetype == 0) {
            return;
        } else if ($this->cachetype == 1) {
            //$this->clear();   

            if ($this->_isset($key)) {


                $key_md5 = $this->md5 ? md5($key) : $key;
                $file = $this->cacheDir . '/' . $key_md5 . $this->suffix;
                $val = file_get_contents($file);
                $val = @gzuncompress($val);
                $val = $this->md5 ? base64_decode($val) : $val;
                return $val;
            }
            return null;
        }
        if ($this->cachetype == 2) {
            $key_md5 = $this->md5 ? md5($key) : $key;
            $val = $this->cache->get($key_md5);
            if (function_exists("gzuncompress")) {
                $val = @gzuncompress($val);
            }
            $val_base64 = $this->md5 ? base64_decode($val) : $val;
            return $val_base64;
        }
    }

    //判断文件是否有效   
    public function _isset($key)
    {
        $key = $this->md5 ? md5($key) : $key;
        $file = $this->cacheDir . '/' . $key . $this->suffix;
        if (file_exists($file)) {
            if ($this->cacheTime == 0 || filemtime($file) >= time()) {
                return true;
            } else {
                @unlink($file);
                return false;
            }
        }
        return false;
    }

    //删除指定缓存  
    public function _unset($key)
    {
        if ($this->cachetype == 0) {
            return;
        } elseif ($this->cachetype == 1) {
            if ($this->_isset($key)) {
                $key_md5 = $this->md5 ? md5($key) : $key;
                $file = $this->cacheDir . '/' . $key_md5 . $this->suffix;
                return @unlink($file);
            }
        } elseif ($this->cachetype == 2) {
            $key_md5 = $this->md5 ? md5($key) : $key;
            return $this->cache->del($key_md5);
        }
    }
    //清除过期缓存文件   
    public function clear()
    {
        $files = scandir($this->cacheDir);
        $cacheTime = $this->cacheTime;

        foreach ($files as $val) {
            if ($cacheTime != 0 && filemtime($this->cacheDir . "/" . $val)  < time()) {
                $ret = @unlink($this->cacheDir . "/" . $val);
            }
        }
        return $ret;
    }

    //清除所有缓存文件   
    public function clear_all()
    {
        $ret = true;
        if ($this->cachetype == 0) {
            return $ret;
        } elseif ($this->cachetype == 1) {
            if (!is_writable($this->cacheDir)) {
                return false;
            }
            $files = scandir($this->cacheDir);
            foreach ($files as $val) {
                @unlink($this->cacheDir . "/" . $val);
            }
        } elseif ($this->cachetype == 2) {
            $ret = $this->cache->flushAll();
        }
        return $ret;
    }
    private function error($line, $msg)
    {

        die("出错文件：" . __file__ . "/n出错行：$line/n错误信息：$msg");
    }
}
