<?php  
    // var_dump($_SERVER['REQUEST_METHOD']);
    $message;
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        //表单提交数据
        if( empty($_POST['email']) || empty($_POST['password']) ){
           $message='数据不完整';
        }else{
            $email=$_POST['email'];
            $password=$_POST['password'];

            // $link=mysqli_connect('localhost','root','root','baixiu');
            //设置编码utf-8
            // mysqli_query($link,'set names utf8');
            $sql="select * from users where email='$email'";
            // $sql="select * from users";
            // // echo $sql;
            // $reader=mysqli_query($link,$sql);

            require'static/db_fun.php';
            xiu_connect();
            xiu_query('set names utf8');
            $data=xiu_query_assoc($sql);

            // $data=mysqli_fetch_assoc($reader);;
            xiu_close();
            // var_dump($data);
            if($data){        
                if($password==$data['password']){
                    session_start();
                    // $_SESSION['current_logged_in'] = true;
                    $_SESSION['current-user-id']=$data['id'];
                    // $_SESSION['current_logged_in_user_id'] = $data['id'];
                    // $message='登录成功!';
                    header('location:index.php');
                }else{
                    $message='用户名或密码错误!';
                }
            }else{
               $message='用户名或密码错误!';
            }
        }
    }else{
        //GET方式提交的数据,直接显示html部分内容
      // if(isset($_COOKIE['PHPSESSID'])){
      //   session_start();
      //   if(isset($_SESSION['current-user-id'])){
      //     header('location:index.php');
      //   }
      // }
    }
    
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" method="post">
      <img class="avatar" src="../assets/img/default.jpg">
       <!--有错误信息时展示 -->
       <?php if(isset($message)){ ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $message ?>
        </div> 
       <?php } ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" 
              type="email" 
              class="form-control" 
              placeholder="邮箱" 
              autofocus
              name="email"
              value="<?php echo isset($_POST['email']) ? $_POST['email']:''; ?>"
              >

      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" 
        type="password" 
        class="form-control" 
        placeholder="密码"
        name="password"
        >
      </div>   
      <input class="btn btn-primary btn-block" type="submit" value="登 录">
    </form>
  </div>
</body>
</html>
