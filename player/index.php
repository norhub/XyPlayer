<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head> 
<title></title>    
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<meta http-equiv="X-UA-Compatible" content="IE=edge"><!-- IE内核 强制使用最新的引擎渲染网页 -->
<meta name="renderer" content="webkit">  <!-- 启用360浏览器的极速模式(webkit) -->
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0 ,maximum-scale=1.0, user-scalable=no"><!-- 手机H5兼容模式 -->
<meta name="x5-fullscreen" content="true" ><meta name="x5-page-mode" content="app" > <!-- X5  全屏处理 -->
<meta name="full-screen" content="yes"><meta name="browsermode" content="application">  <!-- UC 全屏应用模式 -->
<meta name="apple-mobile-web-app-capable" content="yes"> <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/> <!--  苹果全屏应用模式 -->
<!--必要样式-->
   <script type="text/javascript"  src="../include/class.main.js" ></script>
<?php include '../save/config.php'; if($CONFIG["play"]['style']['off']==1){echo '<link rel="stylesheet" href="../save/play.css">';} ?>
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
#player{
position: absolute;
left:0px;
top:0px;
z-index: 999;
height:100% !important;
width:100% !important;
}

</style>

</head>
<body>
<div class="fixed"></div> 
<div id="player"></div>

<script type="text/javascript">
function _GET(name,isurl) { 
    isurl=isurl || false;
	var word="(^|&)" + name + "=([^&]*)(&|$)";
	if(isurl){word="(^|&)" + name + "=(.*?)$";}	
	var reg = new RegExp(word, "i");
    var r = window.location.search.substr(1).match(reg);
    if (r !== null) {
        return decodeURI(r[2]);
    };
    return "";
}  
function load(){
	
var src=Base64.decode(_GET("url"));
document.getElementById('player').innerHTML  = '<iframe name="zzapi" id="zzapi" src="'+ src +'" scrolling="0" frameborder="0" width="100%" height="100%" allowfullscreen="allowfullscreen"  mozallowfullscreen="mozallowfullscreen"  msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>';

}
load();
</script>
</body>
</html>