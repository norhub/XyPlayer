<?php

//接收参数
$url = htmlspecialchars($_GET['url'] ? $_GET['url'] : $_GET['vid']);
if (empty($url))
{
    exit('<style type="text/css">
    H1{margin:10% 0 auto; color:#C7636C; text-align:center; font-family: Microsoft Jhenghei;}
    p{font-size: 1.2rem;/*1.2 × 10px = 12px */;text-align:center; font-family: Microsoft Jhenghei;}
    </style>  
  <h1>请填写url地址</h1>
  <p>本解析接口仅用于学习交流，盗用必究！~</p>');


}
?>
<!DOCTYPE html>
<html>
<head>
<title>海洋CMS专用解析</title>   
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> <meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="Content-language" content="zh-CN">
<meta http-equiv="pragma" content="no-cache"><meta http-equiv="expires" content="0">
<meta name="msapplication-tap-highlight" content="no">
<meta name="HandheldFriendly" content="true">
<meta name="x5-page-mode" content="app">

<script type="text/javascript" src="./ckplayer/ckplayer.min.js"></script>
<script type="text/javascript" src="./ckplayer/jquery.min.js"></script>
<style type="text/css">
html,body{
background-color:#000;
padding: 0;
margin: 0;
height:100%;
width:100%;
color:#999;
overflow:hidden;
}
#a1{
height:100%!important;
width:100%!important;
object-fit:fill;
}
</style>

<body style="overflow-y:hidden;">
<div id="loading" align="center"><strong><br><br><br><br><br><span class="tips">服务器正在解析中,请稍等....<font class="timemsg">0</font>s</span></strong><span class="timeout" style="display:none;color:#f90;">解析响应超时，请刷新重试！</span></div>
<div id="a1" class="content" style="display:none;"></div>
<div id="error" class="content" style="display:none;font-weight:bold;padding-top:90px;" align="center"></div>


<script type="text/javascript">    
var url='<?php echo $url ?>'; var cip='null';
var api="http://bbs.52jscn.com/?url=" ;
function tipstime(count){
    $('.timemsg').text(count);
    if (count === 20) {
       $('.tips').hide();
       $('.timeout').show();
    } else {
        count += 1;
        setTimeout(function () {
            tipstime(count);
        }, 1000);
    }
}
tipstime(0);

 function player(){$.post('api.php',{'url':url,'flag':'m3u8|27pan|ck|yun','cip':cip,},function(data){success(data);},'json')}; 
  
 function success(data){

  if (data.success){
	         
		if(data['url'].search(/\.(ogg|mp4|webm|m3u8)$/i)!==-1){			
		         
		         ckplay(data.url);
        
        }else if(data.type==='video' || data.type==='m3u8' || data.type==='mp4'  ){

               ckplay(data.url);
                
		 }else if(data.type==='url'  ){

             urlplay(data.url);

		 }else{
				
                // urlplay(api+url);
				var word='<br><br><a href="javascript:void(0);"  onclick="urlplay(api+url);">点击使用解析播放</a>';

				$("#loading").hide();$("#error").show();$("#error").html("解析失败,已上报至服务器" + word);		
									
         }  
   }else{

      // urlplay(api+url);
      var word='<br><br><a href="javascript:void(0);"  onclick="urlplay(api+url);">点击使用解析播放</a>';

      $("#loading").hide();$("#error").show();$("#error").html("解析失败,已上报至服务器" + word);
   	  
   }  

 }

   function urlplay(url){	  		
     
   	  $("#loading").hide();$("#a1").show();
      $("#a1").html('<iframe  width="100%" height="100%" src="' + url+'" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" ></iframe>');         		    

  }
   
  function ckplay(url){
	   $("#loading").hide();$("#a1").show();
	   var videoObject = {	
	   container: '#a1',
	   variable: 'player',
	   html5m3u8:true,	 
	   autoplay: true,	    	 
       video:url	 
	   };		    
	   var player=new ckplayer(videoObject);			    
  }
 
 function getcip(){$.get("https://data.video.iqiyi.com/v.f4v",function(cdnip){sip=cdnip.match(/\d+\.\d+\.\d+\.\d+/);cip=sip[0];player();});}
  
 if(url.search(/\.(ogg|mp4|webm|m3u8)$/i)!==-1){ckplay(url);}else{getcip();}
 
</script>
</body>
</html>