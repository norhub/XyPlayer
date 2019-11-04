<?php
/* html调用模块 用来加载html目录含有php代码的html文件*/
if( filter_has_var(INPUT_GET, "url"))
{
  $file="./html/".filter_input(INPUT_GET, 'url');
  if(file_exists($file)){include $file; exit;}	 
 }
 exit("404 not found");	
 ?>