<?php 

    //销毁对应session文件中的数据

    session_start();
    // session_destroy();//销毁session文件
    unset($_SESSION['current-user-id']);
    header('location:login.php');//跳转到登录页

?>