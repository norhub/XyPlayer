/*轻量级搜索自动完成处理插件 jquery.autocomplete.js
jquery  autocomplete plus v1.6 by http://bbs.52jscn.com time: 2017.10.4  
jsonp : "$cb["s":{"",""}];​"  注：不包括引号，jsonp服务必须$_GET["cb"]返回. 
exp:
    <link href="jquery.autocomplete.css" rel="stylesheet">
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript" src="jquery.autocomplete.js"></script>

	<input id="wd" type="text" name="searchword" placeholder="请输入关键词">			
	<input type="submit"  id="submit"  value="搜索"/>
	<div id="word"></div>
*/


$(function(){
    'use strict';   //严格模式
    var $searchInput = $('#wd');              //设置显示控件
    var $autocomplete  =$('#word');           //设置输入控件
    var $api  ='./api.php?tp=jsonp&wd=';                //设置api
	
    $searchInput.attr('autocomplete','off'); 
    var selectedItem = null; 
    var timeoutid = null; 
    var setSelectedItem = function(item){ 
        selectedItem = item ; 

        if(selectedItem < 0){ 
          selectedItem = $autocomplete.find('div').length - 1; 
          } 
    else if(selectedItem > $autocomplete.find('div').length-1 ) { 
          selectedItem = 0; 
         } 
//首先移除其他列表项的高亮背景，然后再高亮当前索引的背景 
      $autocomplete.find('div').removeClass('click_hover'); 
      $autocomplete.find('div').eq(selectedItem).addClass('click_hover'); 
}; 
	
   var removeHTMLTag = function(str){      
             str=str.replace(/&quot;/g, '"');  //引号html编码转换
             str=str.replace(/\+/g," ");//恢复转码的"+"为空格            
             str = str.replace(/[ | ]*\n/g,'\n'); //去除行尾空白
             str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行 	
             //str=str.replace(/ /ig,'');//去掉所有空格      
            // str.replace(/<[^>]+>/g,"");//去除HTML tag
            str = str.replace(/<\/?.*?$/g,''); //去除HTML及后面内容
            return str;
    };
	
//键盘键被松开时事件处理程序
  $searchInput.keyup(function(event){
			var keywords = $(this).val();
			var ajax_request = function(){ 
			if (keywords==='') { $autocomplete.hide();return;}
			$.ajax({
				url: $api+ keywords,
				dataType: 'jsonp',
				jsonp: 'cb', //回调函数的参数名(发送请求到服务器的key，例如 &key=Callback ) 
				// jsonpCallback: 'fun', //回调函数名(值：value，如果为空，默认会用success()处理) 
				//  另外还有 beforeSend() ,error:function() 事件处理，这里不需要，被省略。
                
				success:function(data){															
				    if (data.success){
					  $autocomplete.empty().show();
					  $.each(data.info, function(index,term){ 
						var title=removeHTMLTag(decodeURIComponent(term.title));
						$('<div ></div>').text(title).appendTo($autocomplete)						
						//设置鼠标悬停效果
						.addClass('click_work')
						.hover(function(){ $(this).siblings().removeClass('click_hover'); $(this).addClass('click_hover');selectedItem = index; },function(){ $(this).removeClass('click_hover');selectedItem = -1;})  
						//注册点击事件
						.click(function(){ $searchInput.val(title); $autocomplete.empty().hide(); $('#submit').trigger('click');});				
					  });
				      //内容添加完毕
				     setSelectedItem(0); 
				   }
				}	

			});
			
		 }  //ajax_request end
		
		
        //输入合法字符  
        if(event.keyCode > 40 || event.keyCode === 8 || event.keyCode ===32) { 
            $autocomplete.empty().hide(); 
            clearTimeout(timeoutid); 
            timeoutid = setTimeout(ajax_request,100); 
		
	 //上光标
	 }else if(event.keyCode === 38){ 	
           //selectedItem = -1 代表鼠标离开 
           if(selectedItem === -1){ setSelectedItem($autocomplete.find('div').length-1); } else { setSelectedItem(selectedItem - 1); }   
	   event.preventDefault(); 
		
        //下光标 
         }else if(event.keyCode === 40) { 
	       if(selectedItem === -1){ setSelectedItem(0); } else { setSelectedItem(selectedItem + 1);} 
               event.preventDefault(); 
         } 

		
});   //keyup end
	
	//处理enter键消息 	
    $searchInput.keypress(function(event){ 
        if(event.keyCode === 13)
        { 
          if($autocomplete.find('div').length === 0 || selectedItem === -1) { return;} 
          $searchInput.val($autocomplete.find('div').eq(selectedItem).text()); 
          $autocomplete.empty().hide();
          $('#submit').trigger('click');
          event.preventDefault(); 
        } 
    });
	
	//处理ESC键消息
    $searchInput.keydown(function(event){ 
       if(event.keyCode === 27 ){ 
         $autocomplete.empty().hide(); 
         event.preventDefault(); 
       } 
    }); 
		
 //失去焦点时隐藏	
        $("input").blur(function(){setTimeout(function(){$autocomplete.hide();},500);});			
});
	





	
	