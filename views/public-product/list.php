<?php
  use yii\widgets\LinkPager;

  $shopId = Yii::$app->request->get('shopId');
  $links = $pages->getLinks();
  $prevLink = isset($links['prev']) ? $links['prev'] : '';
  $nextLink = isset($links['next']) ? $links['next'] : '';

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title >商品发布_待发布</title>
<link href="/css/bootstrap.min.css" rel="stylesheet" />
<link href="/css/common/style.css" rel="stylesheet" />
<link href="/css/layout/layout.css" rel="stylesheet" />
<link href="/css/common/alert.css" rel="stylesheet" />
<link href="/css/common/frame.css" rel="stylesheet" />
<link rel="stylesheet" href="/css/shop/product.css" />
<link rel="stylesheet" href="/css/shop/create.css" />
<link rel="stylesheet" href="/css/bootstrap-datetimepicker.css" />
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
<input type="hidden" value="0" id="category-id" />
<input type="hidden" value="<?= $status ?>" id="status" />
<input type="hidden" value="<?= $shopId ?>" id="shopId" />
<input type="hidden" value="<?= $shopInfo['platformName'] ?>" id="platformName" />
<input type="hidden" value="<?= $shopInfo['siteName'] ?>" id="siteName" />
<input type="hidden" value="<?= $shopInfo['name'] ?>" id="shopName" />
<input type="hidden" value="<?= $BRcount ?>" id="BRcount">
<!-- edit sold Modal -->
<div class="modal fade" id="sold-base-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title"> 编辑商品跟卖信息 </h3>
      </div>
      <div class="modal-body" style="max-height: 600px">
        <form class="form-horizontal sold-info">
          <input type="text" class="form-control" id="Image" data-name="Image" value="" style="display: none" />
          <input type="text" class="form-control" id="PID" data-name="PID" value="" style="display: none" />
          <input type="text" class="form-control" id="Brand" data-name="Brand" value="" style="display: none" />
          <div class="form-group">
            <label class="col-md-2 control-label"> <span style=" color:red;">*</span>Asin: </label>
            <div class="col-md-9">
              <input type="text" class="form-control" id="Asin" data-name="Asin" value="" readonly />
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label"> <span style=" color:red;">*</span>SKU: </label>
            <div class="col-md-9">
              <input type="text" class="form-control m-required" id="Sku" data-name="Sku" value="" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label"> <span style=" color:red;">*</span>标题: </label>
            <div class="col-md-9">
              <input type="text" class="form-control m-required" id="Title" data-name="Title" value="" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label"> <span style=" color:red;">*</span>商品状况: </label>
            <div class="col-md-9">
              <select class="form-control m-required" id="Condition">
                <option value="">请选择</option>
                <option value="New">New</option>
                <option value="UsedLikeNew">UsedLikeNew</option>
                <option value="UsedVeryGood">UsedVeryGood</option>
                <option value="UsedGood">UsedGood</option>
                <option value="UsedAcceptable">UsedAcceptable</option>
                <option value="CollectibleLikeNew">CollectibleLikeNew</option>
                <option value="CollectibleVeryGood">CollectibleVeryGood</option>
                <option value="CollectibleGood">CollectibleGood</option>
                <option value="CollectibleAcceptable">CollectibleAcceptable</option>
                <option value="Refurbished">Refurbished</option>
                <option value="Club">Club</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label"> <span style=" color:red;">*</span>价格: </label>
            <div class="col-md-9">
              <input type="text" class="form-control m-required" id="Price" data-name="Price" value="" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label"> 促销价格: </label>
            <div class="col-md-9">
              <div class="material">
                <table style="margin-bottom: -10px">
                  <tbody>
                    <tr>
                      <td style="width:100px">促销价格</td>
                      <td style="padding: 4px"><input type="text" class="form-control" id="sale-price" value="" placeholder="促销价格" /></td>
                    </tr>
                    <tr>
                      <td style="width:100px">促销开始时间</td>
                      <td style="padding: 4px;position: relative"><input type="text" class="form-control date-choose" id="sale-date-from" value="" placeholder="促销开始时间" /></td>
                      <td style="width:100px; padding-left: 15px">促销结束时间</td>
                      <td style="padding: 4px;position: relative"><input type="text" class="form-control date-choose" id="sale-date-to" value="" placeholder="促销结束时间" /></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-2 control-label"> <span style=" color:red;">*</span>库存: </label>
            <div class="col-md-9">
              <input type="text" class="form-control m-required" id="Stock" data-name="Stock" value="" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary save" data-loading-text="处理中..." id="sold-save" data-name="base" autocomplete="off"> 提交 </button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!--模态对话框-->
<div class="modal fade" id="category-tree" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title" id="myModalLabel"> 为商品选择分类 </h3>
      </div>
      <div class="modal-body">
        <div id="use-cates">
          <div> 您选择的目录为: <span class="full-category-name"></span> </div>
          <div class="category-area">
            <ul class="category" data-level="1" id="data-category-tree">
              <div class="form-group search-div">
                <input type="text" class="cate-search form-control" placeholder="搜索....." />
                <span class="glyphicon glyphicon-search form-control-feedback"></span> </div>

              <?php foreach ($categories as $category) { ?>
                <li class="<?= $category['leaf'] ? 'no' : 'has' ?>-leaf" data-id="<?= $category['node_id'] ?>" data-en="<?= $category['node_name'] ?>" data-cn="" data-level="<?= $category['level'] ?>" data-leaf="<?= $category['leaf'] ?>"> <a href="javascript: void(0)"><?= $category['node_name'] ?></a> </li>
              <?php } ?>

            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" disabled="disabled" id="choose-category">提交</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="modal fade" id="select-temp" tabindex="-1" role="dialog" aria-labelledby="tempLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title"> 选择模板信息 </h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group">
            <label for="" class="col-md-2 control-label"> <span style="color:red;">*</span> 运费模板: </label>
            <div class="col-md-10">
              <select class="form-control" required id="shipping-select">
                <option>---- 请到模板管理创建运费模板 ----</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-md-2 control-label"> <span style="color:red;">*</span> 服务模板: </label>
            <div class="col-md-10">
              <select class="form-control" required id="promise-select">
                <option>---- 请到模板管理创建服务模板 ----</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="" class="col-md-2 control-label"> 商品分组: </label>
            <div class="col-md-10">
              <select class="form-control" required id="group-select">
                <option val="">---- 请到模板管理同步商品分组 ----</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" id="choose-temp">提交</button>
        <button type="button" class="btn btn-primary" style="display: none" id="choose-temp-verify">提交</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="modal fade" id="trans-control-modal" tabindex="-1" role="dialog" aria-labelledby="transLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title" id="img-select-Label"> 设置翻译选项 </h3>
      </div>
      <div class="modal-body">
        <p>请选择翻译项，系统将根据选项翻译商品字段</p>
        <input type="hidden" id="trans-pid" />
        <div class="form-group">
          <select class="form-control" id="src-lang">
            <option value="">请选择源语言</option>
            <option value="fr"> 法语 (Fran&ccedil;ais) </option>
            <option value="en"> 英语 (English) </option>
            <option value="cn"> 简体中文 (简体中文) </option>
            <option value="de"> 德语 (Deutsch) </option>
            <option value="it"> 意大利语 (Italiano) </option>
            <option value="ja"> 日语 (日本語) </option>
            <option value="es"> 西班牙语 (Espa&ntilde;ol) </option>
            <option value="ru"> 俄语 (Pусский) </option>
            <option value="nl"> 荷兰语 (Nederlands) </option>
            <option value="pt"> 葡萄牙语 (Portugu&ecirc;s) </option>
            <option value="tw"> 繁体中文 (繁體中文) </option>
            <option value="ko"> 韩语 (한국의) </option>
            <option value="sv"> 瑞典语 (Svenska) </option>
            <option value="th"> 泰语 (ภาษาไทย) </option>
            <option value="pl"> 波兰语 (Polski) </option>
          </select>
        </div>
        <div class="form-group">
          <select class="form-control" id="tar-lang">
            <option value="">请选择目标语言</option>
            <option value="fr"> 法语 (Fran&ccedil;ais) </option>
            <option value="en"> 英语 (English) </option>
            <option value="cn"> 简体中文 (简体中文) </option>
            <option value="de"> 德语 (Deutsch) </option>
            <option value="it"> 意大利语 (Italiano) </option>
            <option value="ja"> 日语 (日本語) </option>
            <option value="es"> 西班牙语 (Espa&ntilde;ol) </option>
            <option value="ru"> 俄语 (Pусский) </option>
            <option value="nl"> 荷兰语 (Nederlands) </option>
            <option value="pt"> 葡萄牙语 (Portugu&ecirc;s) </option>
            <option value="tw"> 繁体中文 (繁體中文) </option>
            <option value="ko"> 韩语 (한국의) </option>
            <option value="sv"> 瑞典语 (Svenska) </option>
            <option value="th"> 泰语 (ภาษาไทย) </option>
            <option value="pl"> 波兰语 (Polski) </option>
          </select>
        </div>
        <div class="form-group">
          <p style="color: #eb3c00;"> 强烈建议目标语言应符合当前店铺语言 </p>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="tr-title" checked="" />
              翻译商品标题 </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="tr-desc" checked="" />
              翻译商品描述 </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="tr-spec" checked="" />
              翻译商品参数 </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="tr-key" checked="" />
              翻译商品关键字 </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" class="tr-point" checked="" />
              翻译商品BulletPoints </label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="trans-control" autocomplete="off">确认</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="modal fade" id="group-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title" id="img-select-Label"> 选择产品分类 </h3>
      </div>
      <div class="modal-body">
        <div class="group-detail">
          <div style="width: 32px; height: 32px; float:left"> <img src="/image/spinner.gif" style="width: 100%; height: 100%" /> </div>
          <span style="line-height: 32px; margin-left: 10px">正在加载分类...</span> </div>
      </div>
      <div class="modal-footer"> </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div class="to-top" hidden="hidden"></div>
<div id="headNav"></div>
<div id="siderbarNav1"></div>
<div class="container-fluid">
  <div class="row">
    <div class="wrap">
      <div class="col-md-12">
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">待发布产品</a></li>
          </ul>
          <div class="box green">
            <div class="box-header">
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 待发布产品 </h2>
              <ul class="pager box-pager">
                <li class="<?= $prevLink ? '' : 'disabled' ?>"> <a href="<?= $prevLink ?: 'javascript:void(0);' ?>"> <span aria-hidden="true">&laquo;</span>上一页 </a> </li>
                <li class="<?= $nextLink ? '' : 'disabled' ?>"> <a href="<?= $nextLink ?: 'javascript:void(0);' ?>"> 下一页<span aria-hidden="true">&raquo;</span> </a> </li>
              </ul>
            </div>
            <div class="box-content">
              <ul class="nav nav-tabs">
                <li role="presentation" class="<?= $status == 'waiting' ? 'active' : '' ?>"> <a href="?r=public-product&shopId=<?= $shopId ?>&status=waiting"> 待发布 <span class="badge"><?= $totalCount['waiting'] ?: 0 ?></span> </a> </li>
                <li role="presentation" class="<?= $status == 'draft' ? 'active' : '' ?>"> <a href="?r=public-product&shopId=<?= $shopId ?>&status=draft"> 草稿箱 <span class="badge"><?= $totalCount['draft'] ?: 0 ?></span> </a> </li>
                <li role="presentation" class="<?= $status == 'dealing' ? 'active' : '' ?>"> <a href="?r=public-product&shopId=<?= $shopId ?>&status=dealing"> 处理中 <span class="badge"><?= $totalCount['dealing'] ?: 0 ?></span> </a> </li>
                <li role="presentation" class="<?= $status == 'failed' ? 'active' : '' ?>"> <a href="?r=public-product&shopId=<?= $shopId ?>&status=failed"> 发布失败 <span class="badge"><?= $totalCount['failed'] ?: 0 ?></span> </a> </li>
                <li role="presentation" class="<?= $status == 'success' ? 'active' : '' ?>"> <a href="?r=public-product&shopId=<?= $shopId ?>&status=success"> 发布成功 <span class="badge"><?= $totalCount['success'] ?: 0 ?></span> </a> </li>
              </ul>
              <div class="operation-bar">
                <div class="row">
                  <div class="group"> 商品分类： <span>未指定</span> <a href="javascript: void(0)" id="sel-group">选择分类</a> <a href="" class="f-right">查看全部商品</a> <a href="javascript: void(0)" class="f-right" id="set-cate">手动配置分类</a> </div>
                </div>
                <div class="row">
                <?php if ($status == 'waiting') { ?> 

                  <div class="search-bar">
                    <div class="input-group">
                      <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="search-key">标题</span> <span class="caret"></span> </button>
                        <ul class="dropdown-menu" id="search-options">
                          <li><a href="javascript: void(0)" data-key="Title">标题</a></li>
                          <li><a href="javascript: void(0)" data-key="PID">产品ID</a></li>
                          <li><a href="javascript: void(0)" data-key="SKU">商品SKU</a></li>
                        </ul>
                      </div>
                      <input type="text" class="form-control" id="search-input" />
                      <span class="input-group-btn">
                      <button class="btn btn-success" type="button" id="search-btn">搜索</button>
                      </span> </div>
                    <!-- /input-group --> 
                  </div>
                <?php } ?>

                  已选中 <span id="product-count">0</span>件商品

                  <?php if ($status != 'success') { ?>

                  <?php if (in_array($status, ['waiting', 'failed'])) { ?>
                    <form id="batch-form" action="#" method="post">
                      <input type="hidden" id="batch-condition" name="condition" />
                      <div class="btn-group" style="margin: 0 0 0 8px;">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 批量操作 <span class="caret"></span> </button>
                        <ul class="dropdown-menu">
                        <?php if (!in_array($status, ['dealing'])) { ?>
                        <?php if (0) { ?>

                          <li> <a href="javascript: void(0)" id="all-edit"> <?= $status == 'dealing' ? '重新' : '批量' ?>编辑 </a> </li>
                        <?php } ?>

                          <li> <a href="javascript: void(0)" id="all-delete"> <?= $status == 'dealing' ? '重新' : '批量' ?>删除 </a> </li>
                        <?php } ?>
                          
                        <?php if (!in_array($status, ['failed'])) { ?>
                          <!-- <li> <a href="javascript: void(0)" id="all-trans"> 批量翻译 </a> </li> -->
                        <?php } ?>
                          <li> <a href="javascript: void(0)" id="all-upload"> 批量上传 </a> </li>
                         <!-- <li> <a href="javascript:void(0)" id="del-link">去除链接</a> </li> -->
                        </ul>
                      </div>
                    </form>
                  <?php } ?>

                    <!-- <a class="btn btn-success" href="javascript:void(0)" id="del-subject">去除推广产品</a>  -->

                    <?php if (in_array($status, ['waiting'])) { ?>
                      <a class="btn btn-warning" id="all-check">批量检测</a>
                    <?php } ?>

                    <?php if ($status == 'draft') { ?>
                      <div class="btn-group" style="margin-left: 8px">
                        <button class="btn btn-warning" id="all-delete" disabled="">批量删除</button>
                      </div>
                    <?php } ?>

                  <?php } ?>

                </div>
              </div>
              <table class="table">
                <tbody>
                  <tr>
                    <th class="pro-10" style="min-width: 100px"> <div class="btn-group">
                        <div class="btn btn-default all-select"> 
                          <!--<input type="checkbox" class="all-select"/>--> 全选 </div>
                        <div class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> </div>
                        <ul class="dropdown-menu">
                          <li><a href="javascript:void(0)" class="all-select">选取本页商品</a></li>
                          <li><a href="javascript:void(0)" id="all-select" data-count="2">选取全部商品</a></li>
                        </ul>
                      </div>
                    </th>
                    <th class="pro-15">产品</th>
                    <th class="pro-15">标题</th>

                  <?php if (in_array($status, ['failed'])) { ?>
                    <th class="pro-30"> 错误消息</th>
                  <?php } else { ?>
                    <th class="pro-10">价格(USD)</th>
                  <?php } ?>

                  <?php if (in_array($status, ['dealing', 'success'])) { ?>
                    <th class="pro-10"><?= $status == 'dealing' ? '' : '产品' ?>状态</th>
                  <?php } ?>

                    <th class="pro-20"><?= in_array($status, ['dealing', 'failed', 'success']) ? '提交' : '更新' ?>时间</th>

                  <?php if (!in_array($status, ['dealing'])) { ?>
                    <th class="pro-15">操作</th>
                  <?php } ?>

                  </tr>
                  <?php foreach ($products as $product) { ?>
                      <tr>
                      <td><input type="checkbox" class="sel-pro" data-feed-id="<?= $product['id'] ?>" /></td>
                      <td><img class="gallery" src="<?= $product['mainImages'] ?>" /></td>
                      <td><a class="btn-link" href="javascript:void(0);" target="_blank"> <?= $product['item_name'] ?> </a>
                      <?php foreach ($product['check_error_msg'] as $errMsg) { ?>
                        <div style="color: #eb3c00"> [错误]<?= $errMsg ?> </div>
                      <?php } ?>
                      </td>
                      <td>

                      <?php if ($status == 'failed') { ?>
                        <?= $product['results'] ?>
                      <?php } else {?>

                        <?= $product['price'] ?>
                      <?php } ?>
                        
                      </td>
                      <?php if (in_array($status, ['dealing', 'success'])) { ?>
                        <td>
                          <span style="color: #5cb85c"><?= $product['statusMsg'] ?></span>
                        </td>
                      <?php } ?>
                      <td><?= $product['gmt_modified'] ?></td>

                    <?php if (!in_array($status, ['dealing'])) { ?>
                      <td>

                        <?php if (in_array($status, ['waiting', 'draft', 'failed'])) { ?>
                          <a class="btn btn-info" href="/?r=product/edit-product&shopId=<?= $shopId ?>&goodId=<?= $product['id'] ?>"> <?= $status != 'draft' ? '编辑商品' : '继续编辑' ?> </a><br />
                        <?php } ?>

                        <?php if (in_array($status, ['waiting', 'failed'])) { ?> 
                          <a class="btn <?=  $status == 'waiting' ? 'btn-success' : 'btn-danger' ?> upload-pro" href="javascript: void(0)" data-id="<?= $product['id'] ?>"> 上传商品 </a><br />
                        <?php } ?>
                        <?php if ($status == 'waiting') { ?>
                          <div class="btn-group" role="group">
                            <!--<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> 其他操作 <span class="caret"></span> </button> -->
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                              <li> <a class="use-pro" href="javascript: void(0)" data-id="<?= $product['id'] ?>"> 引用产品 </a> </li>
                            </ul>
                          </div>
                        <?php } ?>

                        <?php if ($status == 'success') { ?>
                            <a class="btn btn-success /*use-pro*/" href="javascript: void(0)" data-feed-id="<?= $product['id'] ?>">引用产品</a>
                        <?php } ?>

                      </td>
                    <?php } ?>
                    </tr>
                  <?php } ?>

                  <?php if (0) { ?>

                    <tr>
                      <td><input type="checkbox" class="sel-pro" data-feed-id="5755241d2471d04c07e658f4" /></td>
                      <td><img class="gallery" src="/image/1.jpg" /></td>
                      <td><a class="btn-link" href="javascript: void(0)" target="_blank"> nihao </a></td>
                      <td>12</td>
                      <td>2016-06-07 10:06:41</td>
                      <td><a class="btn btn-info" href="shopfound.html"> 编辑商品 </a><br />
                        <a class="btn btn-success upload-pro" href="javascript: void(0)" data-id="5755241d2471d04c07e658f4"> 上传商品 </a><br />
                        <div class="btn-group" role="group">
                          <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> 其他操作 <span class="caret"></span> </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li> <a class="use-pro" href="javascript: void(0)" data-id="5755241d2471d04c07e658f4"> 引用产品 </a> </li>
                          </ul>
                        </div></td>
                    </tr>

                    <tr>
                      <td><input type="checkbox" class="sel-pro" data-feed-id="574000ca2471d0385fd58edd" /></td>
                      <td><img class="gallery" src="/image/1.jpg" /></td>
                      <td><a class="btn-link" href="#" target="_blank"> 2016新款纯色短袖圆领t恤 男装 运动工装体恤大码 </a>
                        <div style="color: #eb3c00"> [错误]商品标题存在中文字符 </div>
                        <div style="color: #eb3c00"> [错误]商品标题存在非法字符 </div>
                        <div style="color: #eb3c00"> [错误]商品计量单位未设置 </div>
                        <div style="color: #eb3c00"> [错误]必要的商品参数未填写 </div>
                        <div style="color: #eb3c00"> [错误]商品未设置运费模板 </div>
                        <div style="color: #eb3c00"> [错误]商品未设置售后服务模板 </div></td>
                      <td>2.76</td>
                      <td>2016-06-06 15:18:01</td>
                      <td><a class="btn btn-info" href="shopfound.html"> 编辑商品 </a><br />
                        <div class="btn-group" role="group">
                          <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> 其他操作 <span class="caret"></span> </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li> <a class="use-pro" href="javascript: void(0)" data-id="574000ca2471d0385fd58edd"> 引用产品 </a> </li>
                          </ul>
                        </div></td>
                    </tr>
                   <?php } ?>

                </tbody>
              </table>
              <div class="row footer-stat">
                <ul class="nav pull-right">
                  <li>
                    <?php 
                        echo LinkPager::widget([
                            'pagination' => $pages,
                            'options' => [
                                'class' => 'pagination page-bar',
                            ],
                            'hideOnSinglePage' => false,
                        ]);
                    ?>
                  </li>
                  <li class="btn-group page-n-ctrl">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 每页显示<?= isset($_GET['page_no']) ? intval($_GET['page_no']) : 30 ?>个 <span class="caret"></span> </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?= $requestUri . '&page_no=30' ?>">30</a></li>
                      <li><a href="<?= $requestUri . '&page_no=50' ?>">50</a></li>
                      <li><a href="<?= $requestUri . '&page_no=100' ?>">100</a></li>
                      <li><a href="<?= $requestUri . '&page_no=200' ?>">200</a></li>
                    </ul>
                  </li>
                  <li>共<?= $pages->getPageCount() ?>页</li>
                </ul>
              </div>
              <div id="set-cate-con"> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title" id="myModalLabel"> 切换店铺 </h4>
      </div>
      <div class="modal-body">
        <div class="row" id="shops"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭 </button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->
<div id="footerNav"></div>
<script src="/js/hm.js"></script> 
<script src="/js/jquery.min.js"></script> 
<script src="/js/public.js"></script> 
<script src="/js/bootstrap.min.js"></script> 
<script src="/js/common/logout.js"></script> 
<script src="/js/common/inform.js"></script> 
<script src="/js/shop/shop.js"></script> 
<script src="/js/util/checkbox.js"></script> 
<script src="/js/shop/product.js"></script> 
<script src="/js/create/template.js"></script> 
<script src="/js/shop/sold.js"></script> 
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>
</body>
</html>