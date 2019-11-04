<?php
/*
* TODO:PHP-验证码类
 * Author：nohacks (23453161@qq.com)
 * time:   2019-2-1
 * version:1.3
 * ready:

   $_vc = new ValidateCode();  //实例化一个对象
   $_SESSION['authnum_session'] = $_vc->getCode();//验证码保存到SESSION中

*/


function ShowMsg($msg,$gourl,$onlymsg=0,$limittime=0,$extraJs='')
{
	$htmlhead  = "<html>\r\n<head>\r\n<title>提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><meta name=\"viewport\" content=\"width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no\">\r\n";
	$htmlhead .= "<base target='_self'/>\r\n<style>body{background:#f9fafd;color:#818181}.msg_jump{width:90%;max-width:624px;min-height:60px;padding:20px 50px 50px;margin:5% auto 0;font-size:14px;line-height:24px;border:1px solid #cdd5e0;border-radius:10px;background:#fff;box-sizing:border-box;text-align:center}.msg_jump .title{margin-bottom:11px}.msg_jump .text{margin-bottom:11px}.msg_jump_tit{width:100%;height:35px;margin:25px 0 10px;text-align:center;font-size:25px;color:#0099CC;letter-spacing:5px}</style></head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
        $htmlfoot  = "</script>\r\n$extraJs</center>\r\n</body>\r\n</html>\r\n";
        $litime=($limittime==0)?($gourl=="-1"? 3000:1000) :$limittime;
        if($gourl=="-1"){$gourl = "javascript:history.go(-1);";$msg_color="F00";}else{$msg_color="0FF";}
	if($gourl==''||$onlymsg==1)
	{
		$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
	}else{
		$func = " var pgo=0;function JumpUrl(){ if(pgo==0){ location='$gourl'; pgo=1; } }\r\n";
		$rmsg = $func;
		$rmsg .= "document.write(\"<br /><div class='msg_jump'><div class='msg_jump_tit'>系统提示</div>";
	        $rmsg .= "<div class='text'>\");\r\n";

		$rmsg .= "document.write(\"<font style='color:$msg_color;'>".str_replace("\"","“",$msg)."</font>\");\r\n";
		$rmsg .= "document.write(\"";
		if($onlymsg==0)
		{
                        if($gourl!="javascript:;" && $gourl!=""){$rmsg .= "<br /><br /><a href='{$gourl}'><font style='color:#777777;'>如果你的浏览器没反应，请点击这里...</font></a>";}
			$rmsg .= "<br/></div></div>\");\r\n";
			if($gourl!="javascript:;" && $gourl!=''){$rmsg .= "setTimeout('JumpUrl()',$litime);";}
                }else{
                    $rmsg .= "<br/><br/></div></div>\");\r\n";
                }
		$msg  = $htmlhead.$rmsg.$htmlfoot;
	}
	echo $msg;
}

class ValidateCode {
 private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';//随机因子
 private $code;//验证码
 private $codelen = 4;//验证码长度
 private $width = 120;//宽度
 private $height = 50;//高度
 private $img;//图形资源句柄
 private $font;  //注意字体路径要写对
 private $fontsize = 20;//指定字体大小
 private $fontcolor;//指定字体颜色

 //构造方法初始化
   public function __construct() {
    $this->isgd();
    $this->font = dirname(__FILE__)."/data/Elephant.ttf";
    $this->doimg();
 }

//检测是否支持GD,如果不支持输出固定图片(ABCD)
 private function isgd(){
     if(!function_exists("imagecreate")){
		    $this->code="ABCD";
		    header('Content-type:image/png');
	            exit(file_get_contents(dirname(__FILE__)."/data/vdcode.png"));
      }
  }

 //生成随机码
 private function createCode() {

   $_len = strlen($this->charset)-1; for ($i=0;$i<$this->codelen;$i++) {$this->code .= $this->charset[mt_rand(0,$_len)];}

  //for($i=0; $i<$this->codelen; $i++)$this->code .= chr(mt_rand(65,90));
 }
 //生成背景
 private function createBg() {
  $this->img = imagecreatetruecolor($this->width, $this->height);
  $color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
  imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);
 }
 //生成文字
 private function createFont() {
  $_x = $this->width / $this->codelen;
  for ($i=0;$i<$this->codelen;$i++) {
     $this->fontcolor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
  if(!imagettftext($this->img,$this->fontsize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]))
     {
       imagestring($this->img, 5,$_x*$i+mt_rand(1,5),mt_rand(1,$this->height-20), $this->code[$i], $this->fontcolor);

     }
  }
 }
 //生成线条、矩阵
 private function createLine() {
  //线条
  for ($i=0;$i<6;$i++) {
   $color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
   imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
  }


   //雪花
  for ($i=0;$i<100;$i++) {
   $color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
   imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);
  }



 }
 //输出特定类型的图片格式，优先级为png -> jpg
 private function outPut() {

	if(function_exists("imagepng"))
	{
		 header("content-type:image/png\r\n");
		 imagepng($this->img);
	}else{
		header("content-type:image/jpeg\r\n");
		imagejpeg($this->img);

	}
       imagedestroy($this->img);
 }
 //对外生成
 public function doimg() {
  $this->createBg();
  $this->createCode();
  $this->createLine();
  $this->createFont();
  $this->outPut();
 }

 //获取验证码
 public function getCode(){ return $this->code; }

}