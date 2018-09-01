<?php
//可以在任意界面，通过 is_debug 字段控制输出，而且可以单独传入boolean 进行单个输出控制

function console_log($data,$enable=null)
{
	if($enable==null){      //enable 的优先级更高
		if(isset($is_debug)&&!$is_debug){
			return;
		}
	}
	if($enable!=null&&!enable){
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