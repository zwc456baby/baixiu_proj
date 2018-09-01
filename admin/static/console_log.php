<?php
function console_log($data)
{
	if (is_array($data) || is_object($data))
	{
		echo("<script>console.log('".json_encode($data)."');</script>");
	}
	else
	{
		echo("<script>console.log('".$data."');</script>");
	}
}