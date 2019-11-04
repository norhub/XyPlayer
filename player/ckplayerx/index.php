<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>  
<title></title>    
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<meta http-equiv="X-UA-Compatible" content="IE=edge"><!-- IE内核 强制使用最新的引擎渲染网页 -->
<meta name="renderer" content="webkit">  <!-- 启用360浏览器的极速模式(webkit) -->
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0 ,maximum-scale=1.0, user-scalable=no"><!-- 手机H5兼容模式 -->

<!--必要样式-->
<script type="text/javascript"  src="../../include/jquery.min.js" ></script>
<script type="text/javascript"  src="../../include/class.main.js" ></script>
<script>
 if(_GET('p2pinfo')==="1"){
            document.write('<script type="text/javascript"  src="./p2p/ckplayer.min.js" ><\/script>'); 
        }else{
            document.write('<script type="text/javascript"  src="ckplayer.min.js" ><\/script>');
        
        }

</script>


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
#video,.video{
height:100%!important;
width:100%!important;
 
}

</style>
</head>
<body >
<div class="video">
	<video id="video" controls="controls" x5-video-player-type="h5" playsinline="true" webkit-playsinline="true" x-webkit-airplay="allow" x5-video-player-fullscreen="true"  preload="auto" > </video>
</div>
   <div id="stats"></div>
<script type="text/javascript">
         window.addEventListener('error', function (e) { window.location.href="../h5/"+ window.location.search;});   
         var xyplay=("undefined"!==typeof parent.xyplay) ? parent.xyplay :parent.parent.xyplay;  	 
	 var videoUrl=decodeURIComponent(_GET('url'));
	 var live =_GET('live')==="0" ? 0 : 1;
         var autoplay= _GET('autoplay')==="0" ? 0 : 1;
         var seektime=_GET('seektime')==="0" ? 0 : 1;
         var headtime= Number(getCookie("time_"+ videoUrl) || _GET('headtime'));
	 var video=document.getElementById('video');if(autoplay){video.autoplay="autoplay";}
     var videoObject = {	
	 container: '.video',
	 h5container:'#video',   //h5环境中使用自定义容器
	 variable: 'player',
	 mobileCkControls:true,//是否在移动端（包括ios）环境中显示控制栏
	 mobileAutoFull:false,//在移动端播放后是否按系统设置的全屏播放		 
	 html5m3u8:true,         //使用hls插件
	 loaded: 'loadedHandler',  //监听播放器加载成功
	 autoplay: autoplay,
     live: live,	 
     video:videoUrl,

	 };
  //h5播放信息补全
   if(is_mobile()){video.innerHTML ='<source src="'+videoUrl+'" type="application/x-mpegURL"><source src="'+videoUrl+'" type="video/mp4"><source src="'+videoUrl+'" type="video/webm"><source src="'+videoUrl+'" type="video/ogg">您的浏览器不支持此视频播放！';videoObject["html5m3u8"]=false;}
  //直播模式不使用html5m3u8模式
	if(live){videoObject["html5m3u8"]=false;}
	//智能显示图片及控件
  if(is_mobile()){videoObject["poster"]="loading_wap.gif";}
  if("undefined"!==typeof xyplay && "undefined"!==typeof xyplay.list_array){	  
	if(xyplay.list_array && xyplay.list_array.length>0  && xyplay.list_array[0]["video"].length >1  && live===0){		
		videoObject["next"]="video_next";
		videoObject["list"]="xyplay.onlist";
		if(!is_mobile()){videoObject["front"]="video_front";}
	}	
  }

 //调用CKplayer,API参考：http://www.ckplayer.com/manualX/39.html
 
   var player=new ckplayer(videoObject); player.notice=notice;
 
  //播放器加载成功
 function loadedHandler() {	 
		player.addListener('duration', durationHandler);  //监听播放总时间
		player.addListener('ended', endedHandler);        //监听播放结束
        player.addListener('error', errorHandler);	     //监听播放错误
  }  
 //监听播放总时间; 
 function durationHandler(time)
 {      
   if(seektime===1 && !live && headtime>0){
     setTimeout(function(){player.videoSeek(headtime);},100);
     player.notice("继续上次播放");	  
       
    }else{
       player.notice("视频已就绪");	
    }

     player.addListener('time',timeHandler);     //监听播放进度

 }
 
 //播放进度回调，用来监控播放即将结束 	
  function timeHandler(time) { 
         setCookie("time_" + videoUrl,time,24);

}
 // 播放错误回调
  function errorHandler(){ 
    xyplay.errorHandler();
  }
 
 // 播放结束回调		
  function endedHandler() {	
      setCookie("time_" + videoUrl,"",-1);
       player.notice("视频播放结束,自动播放下集");
	   setTimeout(function(){video_next();},2000);  	       
    }

  //播放下集
  function video_next() {		
	 if("undefined"!==typeof xyplay && "undefined"!==typeof xyplay.playlist_array )
		if (Number(xyplay.part) + 1 >= xyplay.playlist_array.length) {return false;}	
	    xyplay.part++;	  
            videoUrl=xyplay.playlist_array[xyplay.part];
            myplay(videoUrl);
	  
    }
 //播放上集	
	function video_front() {		
	 if("undefined"!==typeof xyplay && "undefined"!==typeof xyplay.playlist_array )		
	   if (Number(xyplay.part) <=0) {return false;}	
	    xyplay.part--;
            videoUrl=xyplay.playlist_array[xyplay.part];
            myplay(videoUrl);
	  
    }
  //调用播放
   function myplay(url,time){  
      videoUrl=url; headtime= getCookie("time_"+ videoUrl);
      videoObject['video']=url;player.newVideo(videoObject);    
	 if("undefined"!==typeof xyplay){	
       if(xyplay.title && !live){
		parent.parent.document.title = "正在播放:【" + xyplay.title + "】part " + (Number(xyplay.part) + 1) + "-- " + xyplay.mytitle;
	   }  
		
     }
   
   }      
    
 //输出信息	
  function notice(word,time,x,y){
      x=x||10;
      y=y||$(window).height()-100;
      time=time||2000;
      var attribute = {	list: [ //list=定义元素列表		
		 {
			type: 'text', //说明是文本
			text: word, //文本内容
			color: '0xFFFFFF', //文本颜色
			size: 14, //文本字体大小，单位：px
			font: '"Microsoft YaHei", YaHei, "微软雅黑",', //字体
			leading: 30, //文字行距
			alpha: 1, //文本透明度(0-1)
			paddingLeft: 10, //文本内左边距离
			paddingRight: 10, //文本内右边距离
			paddingTop: 0, //文本内上边的距离
			paddingBottom: 0, //文本内下边的距离
			marginLeft: 0, //文本离左边的距离
			marginRight: 10, //文本离右边的距离
			marginTop: 10, //文本离上边的距离
			marginBottom: 0, //文本离下边的距离
			backgroundColor: '0xff0000', //文本的背景颜色
			backAlpha: 0.5, //文本的背景透明度(0-1)
			backRadius: 30 //文本的背景圆角弧度
			//clickEvent: "actionScript->videoPlay"
		 }
		],
                x:x,
                y:y,
		//position:[0,0],  //位置[x轴对齐方式（0=左，1=中，2=右），y轴对齐方式（0=上，1=中，2=下）
		alpha: 0.5 //元件的透明度
	};

	var elementTemp = player.addElement(attribute);
        if(time>0){setTimeout(function(){player.deleteElement(elementTemp);},time); }
        
        return (player.getElement(elementTemp));
  }
    
	</script>
</body>
</html>