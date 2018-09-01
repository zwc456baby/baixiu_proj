<?php
$current_page="posts";

require 'static/db_fun.php';
require 'static/console_log.php';

//------------------初始化数据
// 处理分页
// ========================================
// 定义每页显示数据量（一般把这一项定义到配置文件中）
$size = 10;

// 获取分页参数 没有或传过来的不是数字的话默认为 1
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? intval($_GET['p']) : 1;

if ($page <= 0) {
  // 页码小于 1 没有任何意义，则跳转到第一页
  header('Location: /admin/posts.php?p=1');
  exit;
}

// 处理筛选逻辑
// ========================================

// 数据库查询筛选条件（默认为 1 = 1，相当于没有条件）
$where = '1 = 1';

// 状态筛选
if (isset($_GET['s']) && $_GET['s'] != 'all') {
  $where .= sprintf(" and posts.status = '%s'", $_GET['s']);
}

// 记录本次请求的查询参数
$query = '';

// 状态筛选
if (isset($_GET['s']) && $_GET['s'] != 'all') {
  $where .= sprintf(" and posts.status = '%s'", $_GET['s']);
  $query .= '&s=' . $_GET['s'];
}

// 分类筛选
if (isset($_GET['c']) && $_GET['c'] != 'all') {
  $where .= sprintf(" and posts.category_id = %d", $_GET['c']);
  $query .= '&c=' . $_GET['c'];
}

//执行数据库操作
xiu_connect();

// 查询总条数
$total_count = intval(xiu_query('select count(1)
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id
where '.$where)[0][0]);

// 计算总页数
$total_pages = ceil($total_count / $size);

if ($page > $total_pages) {
  // 超出范围，则跳转到最后一页
  header('Location: /admin/posts.php?p=' . $total_pages);
  exit;
}

// $posts = xiu_query('select * from posts');
$posts = xiu_query(sprintf('select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  categories.name as category_name,
  users.nickname as author_name
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id
where %s
order by posts.created desc
limit %d, %d',$where, ($page - 1) * $size, $size));



xiu_close();


//-------------------------自定义方法
/**
 * 将英文状态描述转换为中文
 * @param  string $status 英文状态
 * @return string         中文状态
 */
function convert_status ($status) {
  switch ($status) {
    case 'drafted':
      return '草稿';
    case 'published':
      return '已发布';
    case 'trashed':
      return '回收站';
    default:
      return '未知';
  }
}

/**
 * 格式化日期
 * @param  string $created 时间字符串
 * @return string          格式化后的时间字符串
 */
function format_date ($created) {
	// 设置默认时区
	date_default_timezone_set('UTC');
	// 转换时间戳
	$timestamp = strtotime($created);
	return date('Y年m月d日 <b\r> H:i:s',$timestamp);
}


// /**
//  * 根据 ID 获取分类信息
//  * @param  integer $id 分类 ID
//  * @return array       分类信息关联数组
//  */
// function get_category ($id) {
//   $sql = sprintf('select * from categories where id = %d', $id);
//   return xiu_query($sql)[0];
// }

// /**
//  * 根据 ID 获取用户信息
//  * @param  integer $id 用户 ID
//  * @return array       用户信息关联数组
//  */
// function get_author ($id) {
//   $sql = sprintf('select * from users where id = %d', $id);
//   return xiu_query($sql)[0];
// }

/**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 */
function xiu_pagination ($page, $total, $format, $visible = 5) {
  // 计算起始页码
  // 当前页左侧应有几个页码数，如果一共是 5 个，则左边是 2 个，右边是两个
  $left = floor($visible / 2);
  // 开始页码
  $begin = $page - $left;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;
  // 结束页码
  $end = $begin + $visible - 1;
  // 确保结束不能大于最大值 $total
  $end = $end > $total ? $total : $end;
  // 如果 $end 变了，$begin 也要跟着一起变
  $begin = $end - $visible + 1;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;

  // 上一页
  if ($page - 1 > 0) {
    printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page - 1));
  }

  // 省略号
  if ($begin > 1) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 数字页码
  for ($i = $begin; $i <= $end; $i++) {
    // 经过以上的计算 $i 的类型可能是 float 类型，所以此处用 == 比较合适
    $activeClass = $i == $page ? ' class="active"' : '';
    printf('<li%s><a href="%s">%d</a></li>', $activeClass, sprintf($format, $i), $i);
  }

  // 省略号
  if ($end < $total) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 下一页
  if ($page + 1 <= $total) {
    printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page + 1));
  }
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="/baixiu/admin/posts.php">
          <select name="c" class="form-control input-sm">
            <option value="all">所有分类</option>
            <option value="uncategorized">未分类</option>
            <option value="funny">奇趣事</option>
            <option value="living">会生活</option>
            <option value="travel">去旅行</option>
          </select>
          <select name="s" class="form-control input-sm">
		    <option value="all">所有状态</option>
		    <option value="drafted">草稿</option>
		    <option value="published">已发布</option>
		    <option value="trashed">回收站</option>
		  </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>

        <?php require 'inc/sidebar.php' ?>

        <tbody>
        	<?php foreach ($posts as $item) { ?>
	          <tr>
	            <td class="text-center"><input type="checkbox"></td>
	            <td><?php echo $item['title']?></td>

	            <td><?php echo $item['author_name']; ?></td>
	            <td><?php echo $item['category_name']; ?></td>
	            <td class="text-center"><?php echo format_date($item['created']); ?></td>
	            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
	            <td class="text-center">
	              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
	              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
	            </td>
	          </tr>
        	<?php }; ?>
        </tbody>
      </table>



      <ul class="pagination pagination-sm pull-right">
			<?php xiu_pagination($page,$total_count,'?p=%d') ?>
	  </ul>
    </div>
  </div>


  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
