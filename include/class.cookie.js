/**
* Cookie操作类
* @nohacks   http://xydai.cn
* @version 1.0 
*/
var Cookie = new class {
    //设置浏览器缓存项值，参数：项名,值,其他参数{expires:"5s",path:"/",domain:"localhost",secure} 或 "5(d,h,m,s,ms)"
    set(key,value,other)
    {
     var expdate=new Date(); 
     var cookie=key+"=" +escape(value);
     
     if("object"===typeof other)
     {
        cookie+=[
         ("undefined" ===typeof other.expires) ?"":"; expires="+ (expdate.setTime(expdate.getTime() + this.getTime(other.expires)),expdate.toUTCString()), 
         ("undefined" ===typeof  other.path) ?"":"; path="+ other.path,
         ("undefined" ===typeof  other.domain) ?"":"; domain="+ other.domain,
         ("undefined" ===typeof  other.secure) ?"":"; secure"
        ].join("");       
    
      }else{ 
        cookie+=("undefined" ===typeof other) ?"":"; expires="+ (expdate.setTime(expdate.getTime() + this.getTime(other)),expdate.toUTCString());  
     }
      document.cookie = cookie;    
      //console.log("setcookie:"+cookie);
    }
   //获取浏览器缓存项值，参数：项名
    get(key)  {
    if (document.cookie.length > 0) {
          var r = new RegExp("(?:^|;)\\s*" + key + "=([^;]*)(?:;|$)").exec(document.cookie);
          return  (null===r)? "":unescape(r[1]);     
        }

    return "";
}

  getTime(time){
      if("undefined" !==typeof time && time!==null){
       var r = (/^(\d+)(.*?)$/i).exec(time);
       if(!r|| r.length < 2){return 0;}
       switch(r[2]){ 
          case "d":                   
            return r[1]*24*60*60*1000;
           case "h":                   
            return r[1]*60*60*1000; 
          case "m":                   
             return r[1]*60*1000;
          case "s":                   
             return r[1]*1000;       
          case "ms":                   
             return r[1];        
         default:  
              return r[1]*1000;  
       }    
      
        }else{
           return -1; 
       }
      
  }

  //删除浏览器缓存项值，参数：项名
    del(key) {
        this.set(key);
    }
 //更新文件头信息,便于PHP及时读取
    update(key,time) {
        
        key = key || "reload"; time=time||"5s";
        if (this.get(key)!== window.location.href)
        {
            this.set(key, window.location.href,{expires:"5s",path:window.location.pathname});
            window.location.reload();
        }
    }

};
