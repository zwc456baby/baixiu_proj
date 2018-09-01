<?php 
$is_debug = true;
require 'static/console_log.php';
//初始化数据
if(!empty($_GET['id'])){
$sql = sprintf('delete from posts where id in (%s)',$_GET['id']);
console_log('sql:'.$sql);

require 'static/db_fun.php';

console_log('start delete func');
//sql语句执行
xiu_connect();
xiu_execute($sql);
xiu_close();

$target = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'posts.php';
console_head('target:'.$target);
header('Location:'.$target);

}else{
//id is null.header index.php
$target = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'posts.php';
console_log('error , id is null');
header('Location:'.$target);

}

?>