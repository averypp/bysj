<?php

use yii\helpers\Html;

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>跨境电商服务平台</title>
<link href="/css/bootstrap.min.css" rel="stylesheet" />
<link href="/css/common/style.css" rel="stylesheet" />
<link href="/css/layout/layout.css" rel="stylesheet" />
<link href="/css/common/alert.css" rel="stylesheet" />
<link href="/css/index/index.css" rel="stylesheet" />
<link href="/css/common/loading.css" rel="stylesheet" />
<link href="/css/common/frame.css" rel="stylesheet" />
<link href="/css/font-awesome.min.css" rel="stylesheet" />
<link href="/css/index/notice.css" rel="stylesheet" />
<!--[if lt IE 9]>
<script src="js/html5shiv.min.js"></script>
    <script src="js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="md-modal md-effect-1" id="global-inform">
  <div class="md-content">
    <h3 class="md-header"></h3>
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
<div class="section">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">
        <div class="row">
          <div class="col-md-12">
            <div class="box grey">
              <div class="box-header">
                <h2> <i class="glyphicon glyphicon-home"></i> <span class="break"></span> 社区 </h2>
              </div>
              <div class="box-content">
                <div class="row announcement-shell">
                  <div class="col-md-12">
                  <?php foreach ($communitys as $community) { ?>
                    <div class="announcement ali"> <strong><?php echo $community['name'] ?></strong> </div>
                    <div class="announcement-content"><?php echo $community['content'] ?><br /></div>
                  <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="box grey">
              <div class="box-header">
                <h2> <i class="glyphicon glyphicon-stats"></i> <span class="break"></span> 常见问题 </h2>
              </div>
              <div class="box-content">
                <div class="row announcement-shell">
                  <ul class="dashboard-list metro question-list">
                    <li><a href="/set/account.html" target="_blank"> 如何进行店铺授权？ </a></li>
                    <li><a href="#" target="_blank"> 什么是商品采集？ </a></li>
                    <li><a href="#" target="_blank"> 如何进行店铺搬家？ </a></li>
                    <li><a href="#" target="_blank"> 如何发布/刊登商品？ </a></li>
                    <li><a href="#" target="_blank"> 如何进行模板管理？ </a></li>
                    <li><a href="#" target="_blank"> 如何创建亚马逊商品？ </a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="row">
          <div class="col-md-12">
            <div class="box grey">
              <div class="box-header">
                <h2> <i class="glyphicon glyphicon-saved"></i> <span class="break"></span> 已授权店铺(<?= Html::encode(count($stores)) ?>/10) </h2>
                <div class="box-icon" hidden="hidden"> <a href="#" class="" data-toggle="modal" data-target="#myModal"> 新增店铺授权 </a> </div>

              <?php
                  $colors = ['#521f54','#9b0533','#ffc40d','#f37021','#40bddb','#2b5797','#86ac3d','#e3301b',];
              ?>
              <?php foreach ($platforms as $k => $platform) { ?>
                 <div class="plat-label"> &nbsp;<?= Html::encode($platform->platform_name) ?> </div>
                 <div style="background-color:<?= array_shift($colors) ?>" class="plat-color"></div>
              <?php } ?>

              </div>
              <div class="box-content">
                <div style="height:20px"></div>
                <div class="row shop-row">
                <?php foreach ($stores as $store) { ?>
                  
                  <div class="col-lg-3 col-md-4 col-sm-4"> <a href="/?r=public-product&shopId=<?= Html::encode($store['id']) ?>&status=waiting">
                    <div class="statbox ali-box">
                      <div class="number"> <?= Html::encode($store['goodsCount']) ?>件 <i class="glyphicon glyphicon-shopping-cart"></i> </div>
                      <div class="title"> <?= Html::encode($store['platform']['platform_name']) ?>&gt;&gt;<?= Html::encode($store['site']['platform_name']) ?> </div>
                      <div class="footer"> <?= Html::encode($store['store_name']) ?> </div>
                    </div>
                    </a> </div>

                <?php } ?>
                  <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="statbox " style="border:1px dashed #bbb;text-align:center"> <span id="add-authorize" style="color:#000;line-height:100px"> +新增店铺授权 </span> </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="box grey">
              <div class="box-header">
                <h2> <i class="glyphicon glyphicon-bullhorn"></i> <span class="break"></span> 公告 </h2>
              </div>
              <div class="box-content" id="inform-detail">
              <?php if ($notices) { ?>
                <ul class="dashboard-list metro" id="notice-list">
                <?php foreach ($notices as $notice) { ?>
                  <li> <a href="javascript:void(0)" data-id="<?= Html::encode($notice['id']) ?>"> <span><?= Html::encode($notice['name']) ?></span> </a> <span style="float:right">[<?= Html::encode($notice['gmt_create']) ?>]</span>

                    <ul data-id="<?= Html::encode($notice['id']) ?>" hidden="hidden" class="notice-content">
                    <?php foreach ($notice['content'] as $one) { ?>
                      <li><?= Html::encode($one) ?></li>
                    <?php } ?>
                    </ul>

                  </li>
                <?php } ?>
                  
                </ul>
              <?php } else { ?>
                <ul class="dashboard-list metro" id="notice-list"><li>暂无内容</li></ul>
              <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times; </button>
        <h4 class="modal-title" id="myModalLabel"> 添加授权 </h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="platform_list">
        <?php foreach ($platforms as $k => $platform) { ?>
          <li role="presentation" data-name="<?= Html::encode($platform->platform_name) ?>" class="<?php if (!$k) echo 'active' ?>"> 
            <a href="javascript: void(0)"> <?= Html::encode($platform->platform_name) ?> </a> 
          </li>
        <?php } ?>

          <!--备用-->
          <!-- <li role="presentation" data-name="DHgate" style="width: 115px"> <a href="javascript: void(0)"> <span class="sandbox">Beta</span>DHgate </a> </li>
          <li role="presentation" data-name="Ensogo" style="width: 115px"> <a href="#" target="_blank"> <span class="sandbox">Beta</span>Ensogo </a> </li>
          <li role="presentation" data-name="Joom" style="width: 100px"> <a href="javascript:void(0)" target="_blank"> <span class="sandbox">Beta</span>Joom </a> </li> -->

        </ul>
        <div id="loading">
          <div id="loading-icon"> </div>
        </div>
        <div id="tag-body">
          <div style="color: #00A300"> 请选择该平台的站点 </div>
          <div id="site_list">
          <?php foreach ($sites as $site) { ?>
             <a class="site" href="javascript:void(0)" data-id="<?= Html::encode($site->id) ?>"><?= Html::encode($site->platform_name) ?></a> 
          <?php } ?>
          </div>
          <div id="shop_name">
            <div style="color: #00A300"> 请自定义一个店铺名称 <span style="color:red"> (自由填写，格式为4-16个字母或者数字)</span> </div>
            <input type="text" class="form-control" id="shop_name_text" placeholder="不能包含空格、中文以及其他特殊字符" />
            <div class="tips" id=""></div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="modal-footer">
        <div class="authorize_button"> <a type="button" class="btn btn-primary disabled" id="begin-authorize"> 开始授权 </a> </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="authorizing_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times; </button>
        <h4 class="modal-title" id="authorizing_model_label"> 授权处理中 </h4>
      </div>
      <input type="hidden" id="session-id" placeholder="" />
      <div class="modal-body tab-content">
        <div class="material external-area" id="amazon-mtl">
          <table>
            <tbody>
              <tr>
                <td><span class="required">*</span>Seller ID </td>
                <td><input type="text" class="form-control" id="seller-id" placeholder="卖家编号" /></td>
              </tr>
              <tr>
                <td><span class="required">*</span>AWS Access Key ID </td>
                <td><input type="text" class="form-control" id="access-id" placeholder="AWS Access Key" /></td>
              </tr>
              <tr>
                <td><span class="required">*</span>Secret Key </td>
                <td><input type="text" class="form-control" id="secret-key" placeholder="密钥" /></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="material external-area" id="lazada-mtl">
          <table>
            <tbody>
              <tr>
                <td><span class="required">*</span>Email </td>
                <td><input type="text" class="form-control" id="email" placeholder="邮箱地址" /></td>
              </tr>
              <tr>
                <td><span class="required">*</span>API Key </td>
                <td><input type="text" class="form-control" id="api-key" placeholder="API Key" /></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="external-area" id="general-mtl"> 在新的页面填写授权信息，不要关闭此页面, 请在2分钟之内完成授权操作 </div>
      </div>
      <div class="modal-footer"> <a id="auth-help" style="float: left" target="_blank" href="javascript:void(0)">授权帮助</a>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="auth-success" autocomplete="off"> 完成授权 </button>
        <button type="button" class="btn btn-danger" id="auth-failed"> 授权遇到问题 </button>
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
<script src="/js/my/authorize.js"></script> 
<script type="text/javascript" src="/js/my/noticeModal.js"></script> 
<script type="text/javascript" src="/js/common/loading.js"></script>
</body>
</html>