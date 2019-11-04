<?php
/*##################################################
# xypaly 智能视频解析 X   by http://bbs.52jscn.com
# 官方网站: http://bbs.52jscn.com
# 源码获取：http://bbs.52jscn.com
# 模块功能：公用文件
###################################################*/

//不显示读取错误
ini_set("error_reporting","E_ALL & ~E_NOTICE");

/** 
 * js escape php 实现 
 * @param $string           the sting want to be escaped 
 * @param $in_encoding       
 * @param $out_encoding      
 */ 
function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') { 
    $return = ''; 
    if (function_exists('mb_get_info')) { 
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) { 
            $str = mb_substr ( $string, $x, 1, $in_encoding ); 
            if (strlen ( $str ) > 1) { // 多字节字符 
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) ); 
            } else { 
                $return .= '%' . strtoupper ( bin2hex ( $str ) ); 
            } 
        } 
    } 
    return $return; 
}


//防盗链判断，即授权域名
function is_referer(){
 if(defined('REFERER_URL')==false){return true;}else if(REFERER_URL==""){return true;} 
	@$host = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST);
	@$ymarr = explode("|",REFERER_URL);
    if(in_array($host,$ymarr)){return true;}
    return false;
}

//API数据加密，只能本站调用
function lsreferer(){
    global $play;		
    if(defined('ENCODE_URL')==false || $play['debug']!='0'){return true;}else if(ENCODE_URL==""){return true;}
	@$host = parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST);
	@$ymarr = explode("|",ENCODE_URL);
    if(in_array($host,$ymarr)){return true;}
    return false;
}
//检测字符串组的字符在字符串中是否存在,对大小写不敏感
function findstrs($str,$find,$separator="|"){
	$ymarr = explode($separator,$find);
	foreach ($ymarr as  $find) {  
     if(stripos($str,$find) !==false ){return true; }
    }  	 
    return false;
}

//设置url
function seturl($word,&$data){  
   if(preg_match("/\.(ogg|mp4|webm|m3u8)$/i", $word)){
     $data['success']=1;
     $data['type']="video";
     $data['url']=$word;
     return true;

    
   }else if(preg_match("/^BGM/i", $word)){
     $data['success']=1;
     $data['type']="url";
     $data['url']="http://api.jp255.com/api/?url=$word";
     return true;
   }
 return false;
};


//编码转换，转换为utf-8编码
function utf8($title) {						
		$encode = mb_detect_encoding($title, array('GB2312','GBK','UTF-8', 'CP936')); //得到字符串编码
		if ( $encode != 'CP936' && $encode != 'UTF-8') {
			$title=iconv($encode, 'UTF-8', $title);
		}			
		return $title;	
	}
	
class Main_Cache{        
    private $cachetype = 1;            //默认缓存类型,1为文件，2为Redis服务
	private $cacheprot = 6379;         //缓存服务端口，默认为Redis服务端口
    private $cacheTime = 3600;        //默认缓存时间,单位微秒。  
    private $cacheDir = './cache';    //缓存绝对路径   
    private $md5 = true;              //是否对键进行加密   
    private $suffix = "";         //设置文件后缀       
    private $cache;
    public function __construct($config){   
        		
		if($this->cachetype==0) {		
           return ;	   
	   }
				
		if( is_array( $config ) ){   
            foreach( $config as $key=>$val ){  
                $this->$key = $val;   
            }  
        }         
		
		if($this->cachetype==2){
			
		    $this->cache = new Redis();
            $this->cache->connect('127.0.0.1', $this->cacheprot);		        
		}        
	
	}
    //设置缓存   
    public function set($key,$val,$leftTime=null){ 
       
     if($this->cachetype==0) {
         return ;	  
	 }else if($this->cachetype==1){
		
        $key = $this->md5 ? md5($key) : $key;  
		$val=$this->md5 ? base64_encode($val) : $val; 
		$val = @gzcompress($val);
		
        $leftTime = $leftTime ? $leftTime : $this->cacheTime;   
        !file_exists($this->cacheDir) && mkdir($this->cacheDir,0777);   
        $file = $this->cacheDir.'/'.$key.$this->suffix;   
        //$val = serialize($val);   
       	 
	   @file_put_contents($file,$val) or $this->error(__line__,"文件写入失败");   
       // @chmod($file,0777)  or $this->error(__line__,"设定文件权限失败");  
       // @touch($file,time()+$leftTime) or $this->error(__line__,"更改文件时间失败");   
   
		}if($this->cachetype==2) {
           $key_md5 = $this->md5 ? md5($key) : $key; 
		   $val_base64 = $this->md5 ? base64_encode($val) : $val; 
		  
		   $val_base64 = @gzcompress($val_base64);	   
            $this->cache->set($key_md5,$val_base64);
		    if($this->cacheTime!=0){
			$this->cache->EXPIRE($key_md5,$this->cacheTime);
			}
		   // $this->cache->del($val_base64); 
	   }
   }   
  
    //得到缓存   
    public function get($key){   
     
      if($this->cachetype==0) {		
           return ;
		   
	  }else if($this->cachetype==1) {		  		   
		$this->clear();   	
        if( $this->_isset($key) ){   		
            $key_md5 = $this->md5 ? md5($key) : $key;  
            $file = $this->cacheDir.'/'.$key_md5.$this->suffix;             		
		   $val = file_get_contents($file);   
           $val=@gzuncompress($val);   // $val=unserialize($val);
		   $val =$this->md5 ? base64_decode($val) : $val; 
		   return $val;   
        }   
            return null;   
      }if($this->cachetype==2) {
           $key_md5 = $this->md5 ? md5($key) : $key; 
		    $val=$this->cache->get($key_md5);
			$val=@gzuncompress($val);	
			$val_base64=$this->md5 ? base64_decode($val) : $val; 		   
		    return $val_base64;
		   
	   }
	 
	}        
  
    //判断文件是否有效   
    public function _isset($key){           	
		$key = $this->md5 ? md5($key) : $key;         			
        $file = $this->cacheDir.'/'.$key.$this->suffix;   
        if( file_exists($file) ){   
            if( $this->cacheTime==0 || @filemtime($file) + $this->cacheTime >= time()){   
                return true;   
            }else{   
                @unlink($file);   
                return false;   
            }   
        }   
        return false;  
    }        
  
    //删除指定缓存  
    public function _unset($key){   
         if($this->cachetype==0) {		
           return ;	   
	  }elseif($this->cachetype==1){
	      if( $this->_isset($key) ){   
            $key_md5 = $this->md5 ? md5($key) : $key;  
            $file = $this->cacheDir.'/'.$key_md5.$this->suffix;  
            return @unlink($file);   
        }   
        return false;   	  
	}elseif($this->cachetype==2){
		$key_md5 = $this->md5 ? md5($key) : $key;  
		$val=$this->cache->del($key_md5);		
	}
}
    //清除过期缓存文件   
    public function clear(){       
	   $files = scandir($this->cacheDir);
       $cacheTime=$this->cacheTime;	   
        foreach ($files as $val){   
            if ( $cacheTime!=0 && @filemtime($this->cacheDir."/".$val) + $cacheTime < time()){ 
                			
                @unlink($this->cacheDir."/".$val);   
            }  
        }   
    }       
  
    //清除所有缓存文件   
    public function clear_all(){  
       if($this->cachetype==0) {
		   return ;
		}
      $files = scandir($this->cacheDir);  
        foreach ($files as $val){   
            @unlink($this->cacheDir."/".$val);   
        }  
    }        
  
    private function error($line,$msg){ 
  
        die("出错文件：".__file__."/n出错行：$line/n错误信息：$msg");   
    }   
}   

  

//简单SQL操作类

class db_class{
	public $server;           //连接地址
	public $dbname;             //数据库名
	public $username;        //连接用户名
	public $password;        //连接密码
    public $charset='utf8';	 //字符编码
		
	public function db_getconn(){  //连接数据库
		$this->db_conn= mysqli_connect($this->server,$this->username,$this->password,$this->dbname);
      
      
		if (!$this->db_conn)
		{
			return false;
		}				
		mysqli_query($this->db_conn,"SET NAMES ".$this->charset);
		mysqli_query($this->db_conn,"set character_set_client=".$this->charset); 
		mysqli_query($this->db_conn,"set character_set_results=".$this->charset);
	}
	
	public function __construct($config){  //构造方法赋值
		//全部赋值
		if( is_array( $config ) ){   
            foreach( $config as $key=>$val ){  
                $this->$key = $val;   
            }  
        }   		
		$this->db_getconn();		
	}
	
	
	
  /*  query方法：执行sql语句	  */
	public function query($sql){  		
		$result = mysqli_query($this->db_conn,$sql);		
		if($result&& mysqli_num_rows($result)>0){		
		  while($row = mysqli_fetch_assoc($result)){			
			$arr[]=$row;
		  }				
		   return $arr;		
		}else{			 
			 return false;
			  
			  
		}		 
	
	
	}
	
/*  get方法：执行sql语句,只获取一条记录  */		 
  public function get($sql){  		
		$result = mysqli_query($this->db_conn,$sql." LIMIT 0,1");     
		if($result && mysqli_num_rows($result)>0){		 
		  $row = mysqli_fetch_assoc($result);
          return $row;
		}else{
			return false;
		}		   
	}

	
/*  getname方法：正则搜索表名  */		 
  public function getname($name){  		
		$result = mysqli_query($this->db_conn,"select table_name from information_schema.tables where table_schema='".$this->dbname."'");     
		
		if($result && mysqli_num_rows($result)>0){	
		 
		  while($row = mysqli_fetch_array($result)){
          
		    $table=$row['table_name'];
		   //echo "/$name/i";
		     if(preg_match("/$name/i",$table)){return $table;}
		  	   
		  }		   
		  
		}else{
			return '';
		}		   
	}


  	
 /*  quote方法：字符串转义(过滤)  */
  public function quote($val) {  
      $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);       
      $search = 'abcdefghijklmnopqrstuvwxyz'; 
      $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';  
      $search .= '1234567890!@#$%^&*()'; 
      $search .= '~`";:?+/={}[]-_|\'\\'; 
    for ($i = 0; $i < strlen($search); $i++) { 
      // @ @ search for the hex values 
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ; 
      // @ @ 0{0,7} matches '0' zero to seven times  
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 
   }     
   // now the only remaining whitespace attacks are \t, \n, and \r 
     $ra1 = Array('_GET','_POST','_COOKIE','_REQUEST','if:','javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base', 'eval', 'passthru', 'exec', 'assert', 'system', 'chroot', 'chgrp', 'chown', 'shell_exec', 'proc_open', 'ini_restore', 'dl', 'readlink', 'symlink', 'popen', 'stream_socket_server', 'pfsockopen', 'putenv', 'cmd','base64_decode','fopen','fputs','replace','input','contents'); 
     $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 
     $ra = array_merge($ra1, $ra2); 
    
     $found = true; // keep replacing as long as the previous round replaced something 
     while ($found == true) { 
      $val_before = $val; 
      for ($i = 0; $i < sizeof($ra); $i++) { 
         $pattern = '/'; 
         for ($j = 0; $j < strlen($ra[$i]); $j++) { 
            if ($j > 0) { 
               $pattern .= '(';  
               $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
               $pattern .= '|';  
               $pattern .= '|(&#0{0,8}([9|10|13]);)'; 
               $pattern .= ')*'; 
            } 
            $pattern .= $ra[$i][$j]; 
         } 
         $pattern .= '/i';  
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag  
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags  
         if ($val_before == $val) {  
            // no replacements were made, so exit the loop  
            $found = false;  
         }  
       }  
    }  
   return stripslashes($val);  
 } 
	
	
		
	public function __destruct(){  //析构方法关闭连接
		mysqli_close($this->db_conn);
              
	}
}


