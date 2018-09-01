<?php

require_once './static/db_fun.php';

function xiu_get_current_user(){
    if(isset($GLOBALS['current_user'])){
        return $GLOBALS['current_user'];
    }

    session_start();
    if(empty($_SESSION['current-user-id'])){
        header('Location:/admin/login.php');
        exit;
    }
    xiu_connect();
    $GLOBALS['current_user']=xiu_query_assoc(sprintf('select * from users where id = %d limit 1',intval($_SESSION['current-user-id'])));
    return $GLOBALS['current_user'];
}

