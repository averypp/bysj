<?php
  use yii\widgets\LinkPager;

  $shopId = Yii::$app->request->get('shopId');
?>

<!DOCTYPE html>
<html lang="zh-CN">
 <head> 
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
  <meta name="viewport" content="width=device-width, initial-scale=1" /> 
  <title>商品管理_在售</title> 
  <link href="css/bootstrap.min.css" rel="stylesheet" /> 
  <link href="css/common/style.css" rel="stylesheet" /> 
  <link href="css/layout/layout.css" rel="stylesheet" /> 
  <link href="css/common/alert.css" rel="stylesheet" /> 
  <link href="css/common/frame.css" rel="stylesheet" /> 
  <link rel="stylesheet" href="css/shop/online.css" /> 
  <link rel="stylesheet" href="css/shop/group.css" /> 
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
<input type="hidden" value="<?= $shopId ?>" id="shop-id" />
<input type="hidden" value="10000" id="status" />
<input type="hidden" value="<?= $shopId ?>" id="shopId" />
<input type="hidden" value="<?= $shopInfo['platformName'] ?>" id="platformName" />
<input type="hidden" value="<?= $shopInfo['siteName'] ?>" id="siteName" />
<input type="hidden" value="<?= $shopInfo['name'] ?>" id="shopName" />
<input type="hidden" value="<?= $BRcount ?>" id="BRcount">
  <!-- multi modal --> 
  <div class="modal fade" id="multi-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h4 class="modal-title"></h4> 
     </div> 
     <div class="modal-body"> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-primary batch-ensure-btn pull-right" data-loading-text="处理中" style="margin-left: 10px">确定</button> 
      <button type="button" class="btn btn-default pull-right" data-dismiss="modal">关闭</button> 
      <p class="invalid-tips"></p> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 
  <!-- sync-date Modal --> 
  <div class="modal fade" id="set-sync" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true"> 
   <div class="modal-dialog" style="width: 800px"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title"> 选择同步的时间 </h3> 
     </div> 
     <div class="modal-body"> 
      <form class="form-horizontal"> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> <span style="color:red;">*</span>起: </label> 
        <div class="col-md-10" style="position: relative"> 
         <input type="text" class="form-control date-choose" id="sync-start" placeholder="2015-01-01" value="" required="" /> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> <span style="color:red;">*</span>止: </label> 
        <div class="col-md-10" style="position: relative"> 
         <input type="text" class="form-control date-choose" id="sync-end" placeholder="2015-01-01" value="" required="" /> 
        </div> 
       </div> 
      </form> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="commit-sync-date" autocomplete="off"> 提交 </button> 
     </div> 
    </div>
    <!-- /.modal-content --> 
   </div> 
  </div>
  <!-- /.modal --> 
  <!-- batch-price Modal --> 
  <div class="modal fade" id="price-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h4 class="modal-title"> 批量修改价格 </h4> 
     </div> 
     <div class="modal-body form-horizontal"> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="replace" /> 直接替换原价格</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="直接替换原价格"> <span class="glyphicon glyphicon-pencil"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="add" /> 在原基础上增加</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在原基础上增加"> <span class="glyphicon glyphicon-plus"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="subtract" /> 在原基础上减少</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在原基础上减少"> <span class="glyphicon glyphicon-minus"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="multiply" /> 在原基础上乘以</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在原基础上乘以"> <span class="glyphicon glyphicon-remove"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="divide" /> 在原基础上除以</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在原基础上除以"> <span class="divide-icon">/</span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary batch-ensure-btn" data-loading-text="处理中">确定</button> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 
  <!-- batch-sale Modal --> 
  <div class="modal fade" id="sale-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h4 class="modal-title"> 批量修改促销价格 </h4> 
     </div> 
     <div class="modal-body form-horizontal"> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="replace" /> 设置促销价格</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="设置促销价格"> <span class="glyphicon glyphicon-pencil"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="subtract" /> 在价格上减少</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在价格上减少"> <span class="glyphicon glyphicon-minus"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="multiply" /> 在价格上乘以</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在价格上乘以"> <span class="glyphicon glyphicon-remove"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-10" id="sale-date"> 
        <table> 
         <tbody> 
          <tr> 
           <td style="width:100px">促销开始时间</td> 
           <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="bath-sale-form" value="" placeholder="促销开始时间" /> </td> 
           <td style="width:100px; padding-left: 15px">促销结束时间</td> 
           <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="bath-sale-to" value="" placeholder="促销结束时间" /> </td> 
          </tr> 
         </tbody> 
        </table> 
       </div> 
      </div> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary batch-ensure-btn" data-loading-text="处理中">确定</button> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 
  <!-- batch-stock Modal --> 
  <div class="modal fade" id="stoke-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h4 class="modal-title"> 批量修改库存 </h4> 
     </div> 
     <div class="modal-body form-horizontal"> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="replace" /> 直接替换原库存</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="直接替换原库存"> <span class="glyphicon glyphicon-pencil"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="add" /> 在原基础上增加</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在原基础上增加"> <span class="glyphicon glyphicon-plus"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
      <div class="form-group"> 
       <div class="col-md-offset-1 col-md-3"> 
        <div class="radio"> 
         <label><input class="operator-radio" type="radio" name="operator" data-name="subtract" /> 在原基础上减少</label> 
        </div> 
       </div> 
       <div class="col-md-6"> 
        <div class="input-group "> 
         <span class="input-group-addon" title="在原基础上减少"> <span class="glyphicon glyphicon-minus"></span> </span> 
         <input type="text" class="form-control oper-input" placeholder="请输入数字" disabled="" /> 
        </div> 
       </div> 
      </div> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary batch-ensure-btn" data-loading-text="处理中">确定</button> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 
  <!-- edit base Modal --> 
  <div class="modal fade" id="base-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true"> 
   <div class="modal-dialog" style="width: 800px"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title"> 修改商品基本信息 </h3> 
     </div> 
     <div class="modal-body" style="max-height: 600px;overflow: auto;"> 
      <form class="form-horizontal"> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> <span style=" color:red;">*</span>标题: </label> 
        <div class="col-md-9"> 
         <a class="btn btn-info spread-btn" href="javascript:void(0)">展开</a> 
         <div class="title-content" style="margin-top: 10px; display: none"> 
         </div> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> ItemType </label> 
        <div class="col-md-9"> 
         <input type="text" class="form-control" placeholder="参考amazon最新的目录信息（欧洲和加拿大是RecommendedBrowseNode)" id="ItemType" data-name="ItemType" value="" /> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> BulletPoint: </label> 
        <div class="col-md-9"> 
         <div id="BulletPoint"> 
          <input type="text" class="form-control" data-name="bullet-point" placeholder="BulletPoint的长度不应超过500个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" data-name="bullet-point" placeholder="BulletPoint的长度不应超过500个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" data-name="bullet-point" placeholder="BulletPoint的长度不应超过500个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" data-name="bullet-point" placeholder="BulletPoint的长度不应超过500个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" data-name="bullet-point" placeholder="BulletPoint的长度不应超过500个字符!" style="margin-bottom: 10px" value="" /> 
         </div> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> 关键词: </label> 
        <div class="col-md-9"> 
         <div id="SearchTerms"> 
          <input type="text" class="form-control" id="Desc" data-name="search-terms" placeholder="关键字的长度不应超过50个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" id="Desc" data-name="search-terms" placeholder="关键字的长度不应超过50个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" id="Desc" data-name="search-terms" placeholder="关键字的长度不应超过50个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" id="Desc" data-name="search-terms" placeholder="关键字的长度不应超过50个字符!" style="margin-bottom: 10px" value="" /> 
          <input type="text" class="form-control" id="Desc" data-name="search-terms" placeholder="关键字的长度不应超过50个字符!" style="margin-bottom: 10px" value="" /> 
         </div> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label class="control-label col-md-2">产品描述:</label> 
        <div class="col-md-9"> 
         <textarea class="form-control" id="Description" rows="5" data-name="pro-des" placeholder=""></textarea> 
        </div> 
       </div> 
      </form> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary single-ensure-btn" data-loading-text="处理中..." id="base-info-btn" data-name="base" autocomplete="off"> 提交 </button> 
     </div> 
    </div>
    <!-- /.modal-content --> 
   </div> 
  </div>
  <!-- /.modal --> 
  <!-- edit stock and price Modal --> 
  <div class="modal" id="price-stock-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h4 class="modal-title"> 更新价格和库存 </h4> 
     </div> 
     <div class="modal-body limit-height-modal"> 
      <div class="form-horizontal"> 
       <div class="form-group"> 
        <label for="" class="col-md-2 control-label"> SKU: </label> 
        <div class="col-md-10"> 
         <input type="text" class="form-control" id="var-sku" required="" value="" readonly="" /> 
         <input id="feed_id" hidden="" value="" /> 
        </div> 
       </div> 
       <hr noshade="noshade" /> 
       <div class="form-group"> 
        <label for="" class="col-md-2 control-label"> 库存: </label> 
        <div class="col-md-10"> 
         <input type="text" class="form-control" id="var-stock" required="" value="" /> 
        </div> 
       </div> 
       <div class="form-group"> 
        <div class="col-md-11"> 
         <button type="button" class="btn btn-info var-update stock" data-name="edit-vars" style="float: right">更新</button> 
        </div> 
       </div> 
       <hr noshade="noshade" /> 
       <div class="form-group"> 
        <label for="" class="col-md-2 control-label"> 价格: </label> 
        <div class="col-md-10"> 
         <input type="text" class="form-control" id="var-price" required="" value="" /> 
        </div> 
       </div> 
       <div class="form-group"> 
        <label for="" class="col-md-2 control-label"> 促销设置: </label> 
        <div class="col-md-10"> 
         <div class="material"> 
          <table style="margin-bottom: -10px"> 
           <tbody> 
            <tr> 
             <td style="width:100px">促销开始时间</td> 
             <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="var-sale-form" value="" placeholder="促销开始时间" /> </td> 
             <td style="width:100px; padding-left: 15px">促销结束时间</td> 
             <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="var-sale-to" value="" placeholder="促销结束时间" /> </td> 
            </tr> 
            <tr> 
             <td style="width:100px">促销价格</td> 
             <td style="padding: 4px"> <input type="text" class="form-control " id="var-sale" value="" placeholder="促销价格" /> </td> 
            </tr> 
           </tbody> 
          </table> 
         </div> 
        </div> 
       </div> 
       <div class="form-group"> 
        <div class="col-md-11"> 
         <button type="button" class="btn btn-info var-update price" data-name="edit-vars" style="float: right">更新</button> 
        </div> 
       </div> 
      </div> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 
  <div id="headNav"></div>
  <div id="siderbarNav2"></div>
  <div class="container-fluid"> 
   <div class="row"> 
    <div class="wrap"> 
     <div class="col-md-12"> 
      <input type="hidden" value="{&quot;Status&quot;:{&quot;$in&quot;:[&quot;10000&quot;]}}" id="con-value" /> 
      <div class="workspace"> 
       <ul class="breadcrumb"> 
        <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li> 
        <li><a href="javascript:void(0)">在线产品</a></li> 
       </ul> 
       <div class="condition-box" id="con-box"> 
        <div class="condition-box-hd"> 
         <a href="#" class="condition-box-nav
on">在售(1)</a> 
        </div> 
       </div> 
       <div class="alert alert-danger" role="alert" style="margin-top: 15px">
         温馨提示：单店铺SKU数超过1万的，在同步前请先联系管理员！ 
       </div> 
       <div class="sync-box"> 
        <div class="info-sign"> 
         <i class="glyphicon glyphicon-info-sign"></i> 
        </div>
        <?php if ($syncStatus && $syncStatus['status'] == 0) { ?>
            正在同步产品，请等待...
        <?php } else { ?>
          <?php if ($syncStatus) { ?>
          上次同步于<?= date('Y-m-d H:i:s', strtotime($syncStatus['gmt_modified'])) ?>，您可以 
            <a class="line-btn" id="start-sync" href="javascript:void(0)">&lt;再次同步&gt;</a>
          <?php } else { ?>
            您的店铺中没有产品，您可以<a class="line-btn" id="start-sync" href="javascript:void(0)">&lt;同步在线产品&gt;</a> 
          <?php } ?>
        <?php  } ?>
       </div> 
       <ul class="condition-box-bd"> 
        <li class="search-group"> <label class="search-label">库存状态：</label> <a href="<?= $stockUri ?>&stoc=0" class="search-title <?= $params['is_stock'] == '0' ? 'on' : '' ?>">全部</a> <a href="<?= $stockUri ?>&stoc=1" class="search-title <?= $params['is_stock'] == '1' ? 'on' : '' ?>">有货</a> <a href="<?= $stockUri ?>&stoc=-1" class="search-title <?= $params['is_stock'] == '-1' ? 'on' : '' ?>">无货</a> </li> 
        <li class="search-group form-group form-inline"> <label class="search-label">内容搜索：</label> 
         <div class="input-group"> 
          <div class="input-group-btn"> 
           <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="search-key">标题</span> <span class="caret"></span> </button> 
           <ul class="dropdown-menu" id="search-options"> 
            <li><a href="javascript: void(0)" data-key="s-title">标题</a></li> 
            <li><a href="javascript: void(0)" data-key="s-sku">SKU</a></li> 
            <li><a href="javascript: void(0)" data-key="s-pid">Asin</a></li> 
           </ul> 
          </div> 
          <input class="form-control search-input" name="search" placeholder="标题名称、SKU、Asin" /> 
         </div> <a href="javascript:void(0)" class="btn btn-success" id="search-btn">搜索</a> </li> 
       </ul> 
       <div class="content-manage row"> 
        <div style="margin: 5px 0; float: left">
          符合查询条件的商品有
         <span class="important"><?= $totalCount ?></span>件 
        </div> 
        <ul class="manage-control"> 
         <li> 每页显示 <a class="page-num-control <?= $params['page_no'] == 10 ? 'on' : '' ?>" href="<?= $pageUri ?>&page_no=10">10</a> 
         <a class="page-num-control <?= $params['page_no'] == 50 ? 'on' : '' ?>" href="<?= $pageUri ?>&page_no=50">50</a> 
         <a class="page-num-control <?= $params['page_no'] == 100 ? 'on' : '' ?>" href="<?= $pageUri ?>&page_no=100">100</a> </li> 
        </ul> 
       </div> 
       <div class="product-content"> 
        <div class="checkbox all-check"> 
         <label> <input type="checkbox" /> <span>已选择<span class="important">0</span>件商品</span> </label> 
        </div> 
        <div class="nav batch-area"> 
         <a class="batch-op" id="multi-price" href="javascript:void(0)">修改价格</a> 
         <a class="batch-op" id="multi-sale" href="javascript:void(0)">修改促销价格</a> 
         <a class="batch-op" id="multi-stock" href="javascript:void(0)">修改库存</a> 
        </div> 
        <div class="more-choice"> 
         <div style="display: inline-block; vertical-align: middle">
          已选
          <span class="already-select-sign"></span>件商品,
         </div> 
         <a href="javascript:void(0)"> 勾选全部<span class="important">1</span>件商品 </a> 
         <a href="javascript:void(0)"> 取消全选 </a> 
        </div> 
        <table class="table table-condensed product-table"> 
         <tbody>
          <tr class="table-title"> 
           <th></th> 
           <th>产品</th> 
           <th class="pro-20" style="width: 15%;">标题</th> 
           <th>SKU</th> 
           <th>价格</th> 
           <th>当前价格</th> 
           <th>BuyBox</th> 
           <th>FBA</th> 
           <th>FBM</th> 
           <th>库存</th> 
           <th>销售排名</th> 
           <th>Fulfilled By</th> 
           <th>操作</th> 
          </tr> 
        <?php if ($products) { ?>
            <?php foreach ($products as $one) { ?>

              <?php
                  $itemsCount = count($one['skus']);
                  $firstSku = array_shift($one['skus']);

                  if ($firstSku['sales_end_date'] > date('Y-m-d H:i:s')) {
                      $firstSku['current_price'] = $firstSku['sale_price'];
                  }
              ?>

              <tr> 
               <td rowspan="<?= $itemsCount ?>"> <input type="checkbox" style="margin-left: 12px;" value="<?= $one['id'] ?>" /> </td> 
               <td rowspan="<?= $itemsCount ?>"> 
                <div class="img-cover"> 
                 <img data-original="#" style="width: 100%;" src="<?= $one['image_url'] ?>" /> 
                </div> </td> 
               <td rowspan="<?= $itemsCount ?>"> 
                <div class="pro-title"> 
                 <a href="http://www.amazon.com/dp/<?= $one['asin'] ?>" target="_blank"><?= $one['title'] ?></a> 
                </div> <span class="badge success"> <?= $one['asin'] ?> - <?= $itemsCount ?> Item(s) </span> 
                <button type="button" class="btn btn-warning btn-xs all-vars-base" data-id="<?= $one['id'] ?>" style="border-radius: 10px"> 
                <span class="glyphicon glyphicon-pencil"></span> 修改所有变体基本信息 </button> 
                </td> 
               <td class="pro-15 v-sku" data-value="<?= $firstSku['sku'] ?>"><?= $firstSku['sku'] ?></td> 
               <td class="v-price" data-value="<?= $firstSku['price'] ?>"> 
                <div>
                 <?= $firstSku['price'] ?>
                </div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span"></span>
                </div> </td> 
               <td class="v-now" data-value="<?= $firstSku['current_price'] ?>"> 
                <div>
                 <?= $firstSku['current_price'] ?>
                </div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span"><?= ($one['shipping_fee'] + $firstSku['shipping_fee']) > 0 ? ($one['shipping_fee'] + $firstSku['shipping_fee']) : '0.00' ?></span>
                </div> </td> 
               <td class="v-sale hidden" data-value=""></td> 
               <td class="v-sale-from hidden" data-value=""></td> 
               <td class="v-sale-to hidden" data-value=""></td> 
               <td class="buybox-td"> 
                <div class="BPrice"></div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span ship-price"></span>
                </div> </td> 
               <td class="fba-td"> 
                <div class="FBAPrice"></div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span ship-price"></span>
                </div> </td> 
               <td class="fbm-td"> 
                <div class="FBMPrice">
                </div>
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span ship-price"></span>
                </div> </td> 
               <td class="v-stock" data-value="<?= $firstSku['stock'] ?>"><?= $firstSku['stock'] ?></td> 
               <td><?= $firstSku['sales_rank'] ?: '' ?></td> 
               <td><?= $firstSku['fulfillment_channel'] ?></td> 
               <td> 
                <div class="btn-group"> 
                 <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
                 <ul class="dropdown-menu pull-right single-edit-menu" data-id="<?= $one['id'] ?>"> 
                  <li> <a class="single-price-stock" href="javascript: void(0)">更新价格和库存</a> </li>
                <?php if (0) { ?>
                  <li> <a class="single-buybox" href="javascript: void(0)">查看BuyBox信息</a> </li> 
                <?php } ?>

                  <li> <a class="single-base" href="javascript:void(0)" data-toggle="modal" data-target="#base-modal"> 修改基本信息 </a> </li> 
                  <?php if (!$firstSku['is_adjustment_price']) { ?>
                  <li> <a class="single-bidding" href="javascript:void(0)" data-id="<?= $firstSku['id'] ?>"> 添加到智能调价 </a> </li>
                  <?php } ?>
                 </ul> 
                </div> </td> 
              </tr> 
            
            <?php foreach ($one['skus'] as $oneSku) { ?>
                <?php
                  if ($oneSku['sales_end_date'] > date('Y-m-d H:i:s')) {
                      $oneSku['current_price'] = $oneSku['sale_price'];
                  }
                ?>
              <tr> 
               <td class="pro-15 v-sku" data-value="<?= $oneSku['sku'] ?>"><?= $oneSku['sku'] ?></td> 
               <td class="v-price" data-value="<?= $oneSku['price'] ?>"> 
                <div>
                 <?= $oneSku['price'] ?>
                </div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span"></span>
                </div> </td> 
               <td class="v-now" data-value="<?= $oneSku['current_price'] ?>"> 
                <div>
                 <?= $oneSku['current_price'] ?>
                </div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span"><?= $oneSku['shipping_fee'] > 0 ? $oneSku['shipping_fee'] : $one['shipping_fee'] ?: '' ?></span>
                </div> </td> 
               <td class="v-sale hidden" data-value=""></td> 
               <td class="v-sale-from hidden" data-value=""></td> 
               <td class="v-sale-to hidden" data-value=""></td> 
               <td class="buybox-td"> 
                <div class="BPrice"></div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span ship-price"></span>
                </div> </td> 
               <td class="fba-td"> 
                <div class="FBAPrice"></div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span ship-price"></span>
                </div> </td> 
               <td class="fbm-td"> 
                <div class="FBMPrice"></div> 
                <div>
                 <span class="ship-span">+</span>
                 <span class="ship-span ship-price"></span>
                </div> </td> 
               <td class="v-stock" data-value="<?= $oneSku['stock'] ?>"><?= $oneSku['stock'] ?></td> 
               <td><?= $oneSku['sales_rank'] ?: '' ?></td> 
               <td><?= $oneSku['fulfillment_channel'] ?></td> 
               <td> 
                <div class="btn-group"> 
                 <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
                 <ul class="dropdown-menu pull-right single-edit-menu" data-id="<?= $one['id'] ?>"> 
                  <li> <a class="single-price-stock" href="javascript: void(0)">更新价格和库存</a> </li> 
                  <?php if (0) { ?>
                    <li> <a class="single-buybox" href="javascript: void(0)">查看BuyBox信息</a> </li> 
                  <?php } ?>
                  <li> <a class="single-base" href="javascript:void(0)" data-toggle="modal" data-target="#base-modal"> 修改基本信息 </a> </li> 
                  <?php if (!$oneSku['is_adjustment_price']) { ?>
                  <li> <a class="single-bidding" href="javascript:void(0)" data-id="<?= $oneSku['id'] ?>"> 添加到智能调价 </a> </li>
                  <?php } ?>
                 </ul> 
                </div> </td> 
              </tr> 
            <?php } ?>
          <?php } ?>
        <?php } else { ?>
            <div class="no-product"><?= (!$products && !($syncStatus && $syncStatus['status'] == 0)) ? '没有找到符合条件的产品信息' : '正在同步产品，请等待...' ?></div>
        <?php  } ?>

         </tbody>
        </table> 
        <div class="checkbox all-check"> 
         <label> <input type="checkbox" /> <span>已选择<span class="important">0</span>件商品</span> </label> 
        </div> 
        <div class="nav batch-area"> 
         <a class="batch-op" id="multi-price" href="javascript:void(0)">修改价格</a> 
         <a class="batch-op" id="multi-sale" href="javascript:void(0)">修改促销价格</a> 
         <a class="batch-op" id="multi-stock" href="javascript:void(0)">修改库存</a> 
        </div> 
        <div class="product-footer"> 
         <ul class="footer-nav clearfix"> 
          <li> 
          <?php if ($pageString) { ?>
            <?= $pageString ?>
          <?php } else { ?>
           <ul class="pagination page-bar"> 
            <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">首页</span> </a> </li> 
            <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">上一页</span> </a> </li> 
            <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">下一页</span> </a> </li> 
            <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">尾页</span> </a> </li> 
           </ul> </li> 
          <?php } ?>
          <li class="form-inline"> 跳到<input type="text" class="form-control page-skip-input" placeholder="<?= $params['page'] ?>" />页 <a class="btn btn-default page-skip-btn" href="javascript:void(0)">Go!</a> </li> 
         </ul> 
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
  <script src="/js/util/checkbox.js"></script>
  <script src="/js/shop/shop.js"></script> 
<script src="/js/util/underscore.js"></script>
<script src="/js/online/amazon.js"></script>
<script src="/js/online/online.js"></script>
<script src="/js/moment-with-locales.js"></script>
<script src="/js/bootstrap-datetimepicker.js"></script>
<script src="/js/util/jquery.lazyload.js"></script>
 </body>
</html>