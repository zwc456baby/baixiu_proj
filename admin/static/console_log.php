<?php
//可以在任意界面，通过 is_debug 字段控制输出，而且可以单独传入boolean 进行单个输出控制

function enable_print_log($enable=null){
	if($enable==null){      //enable 的优先级更高
		global $is_debug;
		if(isset($is_debug)&&!$is_debug){
			return false;
		}
	}
	if($enable!=null&&!$enable){
		return false;
	}
	return true;
}

//php向 console 输出一个普通的调试信息
function console_log($data,$enable=null)
{
	if(!enable_print_log($enable)){
		return;
	}
	if (is_array($data) || is_object($data))
	{
		echo("<script>console.log('".json_encode($data)."');</script>");
	}
	else
	{
		echo("<script>console.log('".$data."');</script>");
	}
}

$temp_log = null;

//将当前页面的信息，延迟到下一个页面进行输出--方便在某些页面跳转的情况下进行日志输出
function console_head($data,$enable=null){
	if(!enable_print_log($enable)){
		return;
	}
	console_log($data);
	global $temp_log;
	$data_arr=array();
	if(isset($temp_log)&&$temp_log!=null){
		$data_arr=json_decode($temp_log);
	}
	array_push($data_arr, $data);
	$json_data=json_encode($data_arr);
	$temp_log=$json_data;
	setcookie("temp_log",$json_data);
}

if(isset($is_debug)&&!$is_debug){
		return;
}
if(isset($_COOKIE['temp_log'])){
	$data = json_decode($_COOKIE['temp_log']);
	setcookie("temp_log","",time()-1);
	foreach ($data as $value) {
		console_log($value,true);
	}
}
