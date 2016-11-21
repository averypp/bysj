<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>用户信息</title>
<link href="/css/bootstrap.min.css" rel="stylesheet" />
<link href="/css/common/style.css" rel="stylesheet" />
<link href="/css/layout/layout.css" rel="stylesheet" />
<link href="/css/common/alert.css" rel="stylesheet" />
<link href="/css/index/my.css" rel="stylesheet" />
<link href="/css/common/frame.css" rel="stylesheet" />
<!--[if lt IE 9]>
	<script src="js/html5shiv.min.js"></script>
	<script src="js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="md-modal md-effect-1" id="global-inform">
  <div class="md-content">
    <h3 class="md-header">系统通知</h3>
    <div class="md-body">
      <div class="md-loading">
        <div class="md-loading-icon"></div>
        <div class="md-loading-text"></div>
      </div>
      <div class="md-panel"></div>
    </div>
    <div class="md-footer">
      <button class="btn btn-primary close-inform">关闭</button>
    </div>
  </div>
</div>
<div class="md-overlay"></div>
<div id="headNav"></div>
<div class="modal fade" id="auth-list-modal" tabindex="-1" role="dialog" aria-labelledby="delLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title">解除店铺授权</h3>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped" id="authorize-list">
          <tbody>
            <tr>
              <th>#</th>
              <th>用户名</th>
              <th>用户手机号</th>
              <th>操作</th>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer"> </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="modal fade" id="authorize-modal" tabindex="-1" role="dialog" aria-labelledby="delLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title">授权店铺</h3>
      </div>
      <div class="modal-body">
        <p>请输入您要授权的用户手机号码:</p>
        <input class="form-control" type="text" id="authorize-mobile" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消操作</button>
        <button type="button" class="btn btn-success" id="add-authorize">确认授权店铺</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="modal fade" id="del-modal" tabindex="-1" role="dialog" aria-labelledby="delLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title">删除店铺</h3>
      </div>
      <div class="modal-body">
        <p> 你确定要删除店铺<span id="del-label"></span>吗？ </p>
        <ol class="name-tip" style="color: #eb3c00;">
          <li>1.删除的店铺将无法恢复</li>
          <li>2.与店铺相关的所有数据都将被删除</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消操作</button>
        <button type="button" class="btn btn-danger" id="del-shop">删除店铺</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="modal fade" id="name-modal" tabindex="-1" role="dialog" aria-labelledby="modLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title">修改店铺名称</h3>
      </div>
      <div class="modal-body">
        <p style="color: #888">店铺名称有数字字母以及字符串构成，长度在6-16个字符之间</p>
        <input class="form-control" value="" placeholder="" />
        <p class="name-tip" style="color: #eb3c00;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消操作</button>
        <button type="button" class="btn btn-primary" id="submit-name">修改名称</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="section">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3"> 
        <!--个人信息-->
        <div class="box grey">
          <div class="box-header">
            <h2> <i class="glyphicon glyphicon-flag"></i> <span class="break"></span> 个人信息 </h2>
          </div>
          <div class="box-content">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="username">用户名:</label>
                  <input type="text" class="form-control" id="username" placeholder="输入用户名" value="<?php echo $userInfo['username']; ?>" readonly />
                </div>
                <div class="form-group">
                  <label for="email">邮箱:</label>
                  <input type="email" class="form-control" id="email" value="" readonly />
                </div>
                <div class="form-group">
                  <label for="phone">手机:</label>
                  <input type="text" class="form-control" id="phone" value="<?php echo $userInfo['mobile']; ?>" readonly />
                </div>
                <div class="form-group">
                  <label for="qq">QQ:</label>
                  <input type="text" class="form-control" id="qq" value="<?php echo $userInfo['qq']; ?>" readonly />
                </div>
                <div class="row" style="padding-top:7px">
                  <div class="col-md-3"> </div>
                  <div class="col-md-9" id="submit-btn">
                    <button type="button" class="btn btn-primary">编辑</button>
                    <button type="submit" class="btn btn-primary" style="display: none;">保存</button>
                    <button type="button" class="btn btn-default" style="display: none;">退出编辑</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--密码管理-->
        <div class="row">
          <div class="col-md-12">
            <div class="box grey">
              <div class="box-header">
                <h2> <i class="glyphicon glyphicon-flag"></i> <span class="break"></span> 密码管理 </h2>
              </div>
              <div class="box-content" id="modify-pw">
                <div class="row" style="padding-top:7px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="username">旧密码:</label>
                      <div>
                        <input type="password" class="form-control" id="old_pw" placeholder="请输入旧密码" />
                      </div>
                      <p class="tips"></p>
                    </div>
                    <div class="form-group">
                      <label for="email">新密码:</label>
                      <div>
                        <input type="password" class="form-control" id="new_pw" placeholder="请输入新密码" />
                      </div>
                      <p class="tips"></p>
                    </div>
                    <div class="form-group">
                      <label for="phone">重复新密码:</label>
                      <div>
                        <input type="password" class="form-control" id="conform_pw" placeholder="请再输入一遍新密码" />
                      </div>
                      <p class="tips"></p>
                    </div>
                  </div>
                </div>
                <div class="row" style="padding-top:7px">
                  <div class="col-md-3"> </div>
                  <div class="col-md-9">
                    <div class="input-group">
                      <button type="submit" class="btn btn-primary" id="changepw">修改</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--密码管理 end--> 
      </div>
      <div class="col-md-9"> 
        <!--已授权店铺-->
        <div class="box grey">
          <div class="box-header">
            <h2> <i class="glyphicon glyphicon-flag"></i> <span class="break"></span> 已授权店铺 </h2>
          </div>
          <div class="box-content">
            <table class="table table-hover table-striped">
              <thead>
                <tr>
                  <td><strong>#</strong></td>
                  <td><strong>店铺名</strong></td>
                  <td><strong>所在平台</strong></td>
                  <td><strong>所在站点</strong></td>
                  <td><strong>创建时间</strong></td>
                  <td><strong>操作</strong></td>
                  <!-- <td><strong>子账号操作</strong></td> -->
                </tr>
              </thead>
              <tbody>
              <?php if(!empty($shops)){
                  foreach($shops as $key =>$shop){
                    $shopId = $shop['id'];
                     ?>
                     <tr>
                        <td><?php echo $key+1; ?></td>
                        <td><?php echo $shop['store_name']; ?></td>
                        <td><?php echo $shop['platform']['platform_name']; ?></td>
                        <td><?php echo $shop['site']['platform_name']; ?></td>
                        <td><?php echo $shop['gmt_create']; ?></td>
                        <td>
                        <a href="javascript: void(0)" class="btn btn-primary mol-name" data-id="<?php echo $shopId; ?>">修改名称</a> 
                        <a href="javascript: void(0)" data-href="/?r=auth/retry&pa=<?php echo $shop['platform']['platform_name']; ?>&sp=<?php echo $shop['site']['id']; ?>&na=<?php echo $shop['store_name']; ?>" class="btn btn-success re-authorize" target="_blank" data-id="<?php echo $shopId; ?>">重新授权</a> 
                        <a href="javascript: void(0)" class="btn btn-danger release-shop" data-id="<?php echo $shopId;?>" data-master="1">删除店铺</a>
                        </td>
                        <!--  <td><a href="javascript: void(0)" class="btn btn-info add-authorize" data-id="4317">授权子账号</a> <a href="javascript: void(0)" class="btn btn-warning authorize-list" data-id="4317">解除授权</a></td>-->
                      </tr>
                  <?php } }else{
                   ?>
                  <tr>暂无店铺</tr>
                  <?php } ?>
                
              
              </tbody>
            </table>
          </div>
        </div>
        <!--已授权店铺 end--> 
        <!--消息通知-->
        <div class="box grey">
          <div class="box-header">
            <h2> <i class="glyphicon glyphicon-flag"></i> <span class="break"></span> 消息通知 </h2>
          </div>
          <div class="box-content"> 暂无通知 </div>
        </div>
        <!--消息通知 end--> 
      </div>
    </div>
  </div>
</div>
<div id="footerNav"></div>
<script src="/js/hm.js"></script> 
<script src="/js/jquery.min.js"></script> 
<script src="/js/public.js"></script> 
<script src="/js/bootstrap.min.js"></script> 
<script src="/js/common/logout.js"></script> 
<script src="/js/common/inform.js"></script> 
<script src="/js/my/my.js"></script>
</body>
</html>