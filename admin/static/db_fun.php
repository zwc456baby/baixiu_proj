<?php 

$sql_connect = null;

require_once './static/config.php'; //配置文件

function xiu_connect(){
    if(isset($sql_connect)){
        xiu_close();
    }
    global $DB_HOST;
    global $DB_USER;
    global $DB_PASS;
    global $DB_NAME;
    global $sql_connect;
    // $DB_HOST = 'localhost';
    // $DB_USER = 'root';
    // $DB_PASS = 'root';
    // $DB_NAME = 'baixiu';
    $sql_connect = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    // $sql_connect = mysqli_connect('localhost','root','root','baixiu');
}

function xiu_close(){
  // 关闭数据库连接
  if(!isset($sql_connect)){
    return;
  }
  mysqli_close($sql_connect);
}
function xiu_query($sql){
  // 建立数据库连接
  global $sql_connect;

  if (!$sql_connect) {
    // 如果连接失败报错
    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }

  // 定义结果数据容器，用于装载查询到的数据
  $data = array();

  // 执行参数中指定的 SQL 语句

  if ($result = mysqli_query($sql_connect, $sql)) {
    // 查询成功，则获取结果集中的数据

    // 遍历每一行的数据
    while ($row = mysqli_fetch_array($result)) {
      // 追加到结果数据容器中
      $data[] = $row;
    }
    // 释放结果集
    mysqli_free_result($result);
  }
  // 返回容器中的数据
  return $data;
}

function xiu_query_assoc($sql){
  // 建立数据库连接
  
  global $sql_connect;
  
  if (!$sql_connect) {
    // 如果连接失败报错
    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }

  // 执行参数中指定的 SQL 语句
  if ($result = mysqli_query($sql_connect, $sql)) {
    // 查询成功，则获取结果集中的数据
    return mysqli_fetch_assoc($result);
    // 释放结果集
    mysqli_free_result($result);
  }

}

  /**
 * 执行一个非查询语句，返回执行语句后受影响的行数
 * @param  string  $sql 非查询语句
 * @return integer      受影响的行数
 */
function xiu_execute ($sql) {

  global $sql_connect;
  // 执行 SQL 语句，获取一个查询对象
  if ($result = mysqli_query($sql_connect, $sql)) {
    // 查询成功，获取执行语句后受影响的行数
    $affected_rows = mysqli_affected_rows($sql_connect);
  }

  // 返回受影响的行数
  return isset($affected_rows) ? $affected_rows : 0;
}