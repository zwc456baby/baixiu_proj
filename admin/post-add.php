<?php
$is_debug=true;
$current_page="post-add";
require 'static/console_log.php';
require 'static/db_fun.php';
//初始化数据
console_log('add posts');

$test_array=array('value1','value2','value3');
console_log($test_array);
xiu_connect();

//处理和执行代码
$categories = xiu_query('select * from categories');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  console_log('进行数据校验');
  // 数据校验
  // ------------------------------
  if (empty($_POST['slug'])
    || empty($_POST['title'])
    || empty($_POST['created'])
    || empty($_POST['content'])
    || empty($_POST['status'])
    || empty($_POST['category'])) {
    // 缺少必要数据
    console_log('某个数据没有填写');
    $message = '请完整填写所有内容';
  } else if (xiu_query(sprintf("select count(1) from posts where slug = '%s'", $_POST['slug']))[0][0] > 0) {
    // slug 重复
    console_log('已经存在相同的别名');
    $message = '别名已经存在，请修改别名';
  } else {
    console_log('数据合法');
    console_log($_POST);
    // 数据合法,所以接收数据,
    $slug = $_POST['slug'];
    $title = $_POST['title'];
    $feature = '';
    $created = $_POST['created'];
    $content = $_POST['content'];
    $status = $_POST['status']; // 作者 ID 可以从当前登录用户信息中获取
    $user_id = $current_user['id'];
    $category_id = $_POST['category'];
    // 接收文件并保存
    // ------------------------------

    // 如果选择了文件 $_FILES['feature']['error'] => 0
    if (empty($_FILES['feature']['error'])) {
      console_head('上传了文件');
      // PHP 在会自动接收客户端上传的文件到一个临时的目录
      $temp_file = $_FILES['feature']['tmp_name'];
      // 我们只需要把文件保存到我们指定上传目录
      $target_file = '../uploads/' . $_FILES['feature']['name'];
      if (move_uploaded_file($temp_file, $target_file)) {
        $image_file = '../uploads/' . $_FILES['feature']['name'];
        console_head('img_req_path:'.$image_file);
        var_dump($image_file);
      }
    }

    $feature = isset($image_file) ? $image_file : '';

    // 拼接查询语句
    $sql = sprintf(
      "insert into posts values (null, '%s', '%s', '%s', '%s', '%s', 0, 0, '%s', %d, %d)",
      $slug,
      $title,
      $feature,
      $created,
      $content,
      $status,
      $user_id,
      $category_id
    );
    console_head($sql);
    // 执行 SQL 保存数据
    if (xiu_execute($sql) > 0) {
      // 保存成功
      console_head('保存数据到sql 成功');
      header('Location:posts.php');
    } else {
      // 保存失败，请重试
      $message='保存数据失败,请重试';
      console_log('保存数据到 sql 失败');
    }
  }
}else{
  console_log('current request is GET');
}
xiu_close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="../assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <nav class="navbar">
      <button class="btn btn-default navbar-btn fa fa-bars"></button>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><i class="fa fa-user"></i>个人中心</a></li>
        <li><a href="login.html"><i class="fa fa-sign-out"></i>退出</a></li>
      </ul>
    </nav>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <?php if(isset($message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
        </div>
      <?php endif; ?>
      <form class="row" action="post-add.php" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题" value="<?php echo isset($_POST['title'])?$_POST['title']:''; ?>">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"><?php echo isset($_POST['content'])?$_POST['content']:''; ?></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php isset($_POST['slug'])?$_POST['slug']:'' ?>">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category" value="<?php echo isset($_POST['category'])?$_POST['category']:'' ?>">
              <?php foreach($categories as $item){ ?>
                <option value="<?php echo $item['id']; ?>"><?php echo $item['name'] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php require 'inc/sidebar.php' ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="../assets/vendors/moment/moment.js"></script>
  <script type="text/javascript">
    // 发布时间初始值
    $('#created').val(moment().format('YYYY-MM-DDTHH:mm'))

  </script>
  <script>NProgress.done()</script>
</body>
</html>
