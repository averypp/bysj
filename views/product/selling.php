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
<link href="/css/bootstrap.min.css" rel="stylesheet" />
<link href="/css/common/style.css" rel="stylesheet" />
<link href="/css/layout/layout.css" rel="stylesheet" />
<link href="/css/common/alert.css" rel="stylesheet" />
<link href="/css/common/frame.css" rel="stylesheet" />
<link rel="stylesheet" href="/css/shop/online.css" />
<link rel="stylesheet" href="/css/shop/group.css" />
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
<div class="modal fade" id="trans-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title" id="transModalLabel"> 请输入汇率以及翻译内容 </h4>
      </div>
      <div class="modal-body">
        <div id="repeat-content"> <span id="num-text"></span>
          <div class="form-group">
            <div class="radio">
              <label>
                <input type="radio" name="repeat-type" value="update" checked="" />
                替换目标店铺中的商品 </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="repeat-type" value="skip" />
                跳过重复的商品 </label>
            </div>
          </div>
        </div>
        <div id="trans-info">
          <div class="row">
            <label class="col-md-2 control-label"> 源货币: </label>
            <div class="col-md-4">
              <input class="form-control" type="text" value="USD" id="o-cur" readonly />
            </div>
            <label class="col-md-2 control-label"> 目标货币: </label>
            <div class="col-md-4">
              <input class="form-control" type="text" value="USD" id="t-cur" readonly />
            </div>
          </div>
          <div class="row"> <span class="trans-text" style="color: #eb3c00;"> 如果语言对应有误，请联系管理员。 </span> </div>
          <div class="row">
            <label class="col-md-2 control-label"> 源语言: </label>
            <div class="col-md-4">
              <select class="form-control" id="o-lan">
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
            <label class="col-md-2 control-label"> 目标语言: </label>
            <div class="col-md-4">
              <select class="form-control" id="t-lan">
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
          </div>
          <div class="row"> <span class="trans-text" style="color: #eb3c00"> 如果货币对应有误，请联系管理员。 </span> </div>
          <div class="form-group"> <span><span class="o-cur-text">USD</span> 对 <span class="t-cur-text">CNY</span> 的汇率为:&nbsp;&nbsp;</span>
            <input class="form-control rate" type="text" value="0.71" />
            <span class="trans-text" style="color:green"> 即: 100 <span class="o-cur-text">USD</span> = <span id="rate-text">0.71</span> <span class="t-cur-text">CNY</span> </span> </div>
          <div class="form-group trans-content">
            <p>请选择需要翻译的内容:</p>
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
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="trans-btn" autocomplete="off"> 提交 </button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- batch-specifics-modal -->
<div class="modal fade" id="batch-spec-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title"> 商品参数 </h3>
      </div>
      <div class="modal-body">
        <div id="pro-prop" class="form-horizontal">
          <div style="width: 32px; height: 32px; float:left"> <img src="static/image/spinner.gif" style="width: 100%; height: 100%" /> </div>
          <span style="line-height: 32px; margin-left: 10px">正在加载属性...</span> </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary batch-ensure-btn pull-right" data-loading-text="处理中" data-name="multi-props" style="margin-left: 10px">确定</button>
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">关闭</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- supply-link modal -->
<div class="modal fade" id="supply-link-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title"> 货源链接 </h3>
      </div>
      <div class="modal-body">
        <div class="supply-loading"> </div>
        <div class="source-link-div"> <span>此商品的货源链接为：</span>
          <div> <a href="" class="md-source-link" target="_blank"></a> </div>
        </div>
        <textarea class="form-control" placeholder="您尚未设置货源链接，可在此处填写。按下方确定按钮保存" id="supply-link-ipt" rows="2"></textarea>
      </div>
      <div class="modal-footer">
        <div class="link-tip pull-left" style="color: #eb3c00;display: none"> 请填写有效的货源链接 </div>
        <button type="button" class="btn btn-default" data-dismiss="modal" id="close-link-modal">关闭</button>
        <button type="button" class="btn btn-default" id="return-link-div">返回</button>
        <button type="button" class="btn btn-success" id="edit-supply-link">编辑</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="submit-supply-link" autocomplete="off"> 确定 </button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- 模态框（Modal） -->
<div class="modal fade" id="move-shop-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title" id="myModalLabel"> 将选中商品转移到以下店铺 </h4>
      </div>
      <div class="modal-body">
        <div class="row" id="move-shops"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="move-btn" autocomplete="off" disabled="disabled"> 提交 </button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- 模态框-->
<div class="modal fade" id="category-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title" id="img-select-Label"> 选择产品分类 </h3>
      </div>
      <div class="modal-body">
        <div class="category-detail">
          <div style="width: 32px; height: 32px; float:left"> <img src="static/image/spinner.gif" style="width: 100%; height: 100%" /> </div>
          <span style="line-height: 32px; margin-left: 10px">正在加载分类...</span> </div>
      </div>
      <div class="modal-footer"> </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- group modal -->
<div class="modal fade" id="group-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title" id="img-select-Label"> 选择产品分组 </h3>
      </div>
      <div class="modal-body">
        <div class="group-detail" style="position: relative;">
          <div style="width: 32px; height: 32px; float:left"> <img src="static/image/spinner.gif" style="width: 100%; height: 100%" /> </div>
          <span style="line-height: 32px; margin-left: 10px">正在加载分类...</span> </div>
      </div>
      <div class="modal-footer"> </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- move modal -->
<div class="modal fade" id="move-modal" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h3 class="modal-title" id="img-select-Label"> 转移产品分组 </h3>
      </div>
      <div class="modal-body">
        <div class="move-detail" style="position: relative;">
          <div style="width: 32px; height: 32px; float:left"> <img src="static/image/spinner.gif" style="width: 100%; height: 100%" /> </div>
          <span style="line-height: 32px; margin-left: 10px">正在加载分类...</span> </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary ensure-move" data-loading-text="处理中">确定</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal --> 
<!-- multi other modal-->
<div class="modal fade" id="multi-other-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title"> 编辑其他信息 </h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
          <div class="form-group">
            <div class=" col-md-offset-2 col-md-10" style="color: #eb3c00"> 信息不必全部填写,只需填写需要修改的内容即可 </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">标题:</label>
            <div class="col-md-10">
              <div class="material" data-id="title">
                <div class="row">
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input type="radio" data-position="head" name="modify-title" />
                        开头添加 </label>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <input type="text" class="form-control" data-id="head" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input type="radio" data-position="tail" name="modify-title" />
                        结尾添加 </label>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <input type="text" class="form-control" data-id="tail" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input type="radio" data-position="replace" name="modify-title" />
                        替换标题 </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <input type="text" class="form-control" data-id="before" />
                  </div>
                  <label class="control-label col-md-1"><span class="glyphicon glyphicon-chevron-right"></span></label>
                  <div class="col-md-4">
                    <input type="text" class="form-control" data-id="after" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">描述:</label>
            <div class="col-md-10">
              <div class="material" data-id="description">
                <div class="row">
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input type="radio" data-position="head" name="modify-des" />
                        开头添加 </label>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <input type="text" class="form-control" data-id="head" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input type="radio" data-position="tail" name="modify-des" />
                        结尾添加 </label>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <input type="text" class="form-control" data-id="tail" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input type="radio" data-position="replace" name="modify-des" />
                        替换描述 </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <input type="text" class="form-control" data-id="before" />
                  </div>
                  <label class="control-label col-md-1"><span class="glyphicon glyphicon-chevron-right"></span></label>
                  <div class="col-md-4">
                    <input type="text" class="form-control" data-id="after" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">有效期:</label>
            <div class="col-md-3">
              <div class="radio">
                <label>
                  <input type="radio" name="duration" value="1" checked="" />
                  不修改</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="radio">
                <label>
                  <input type="radio" name="duration" value="14" />
                  14天</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="radio">
                <label>
                  <input type="radio" name="duration" value="30" />
                  30天</label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">备货时间:</label>
            <div class="col-md-10">
              <input type="text" class="form-control" data-id="delivery" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">商品毛重:</label>
            <div class="col-md-10">
              <input type="text" class="form-control" data-id="gross" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">包装长度:</label>
            <div class="col-md-10">
              <input type="text" class="form-control" data-id="length" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">包装宽度:</label>
            <div class="col-md-10">
              <input type="text" class="form-control" data-id="width" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">包装高度:</label>
            <div class="col-md-10">
              <input type="text" class="form-control" data-id="height" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">运费模板:</label>
            <div class="col-md-10">
              <select class="form-control freight-select" data-id="freight">
                <option>请选择</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary batch-ensure-btn" data-loading-text="处理中" data-name="add-var">确定</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal --> 
<!-- multi modal -->
<div class="modal fade" id="multi-modal" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body"> </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary batch-ensure-btn pull-right" data-loading-text="处理中" style="margin-left: 10px">确定</button>
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">关闭</button>
        <p class="invalid-tips"></p>
      </div>
    </div>
  </div>
</div>
<!-- /.modal --> 
<!-- single edit modal -->
<div class="modal fade" id="single-edit" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body"> </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary single-ensure-btn pull-right" data-loading-text="处理中" style="margin-left: 10px">确定</button>
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">关闭</button>
        <p class="invalid-tips"></p>
      </div>
    </div>
  </div>
</div>
<!-- /.modal --> 
<!-- single edit-all modal -->
<div class="modal fade" id="single-edit-all" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
          <div class="title-text-table"></div>
          <div class="form-group">
            <label class="control-label col-md-2">产品ID:</label>
            <div class="col-md-8">
              <input class="form-control" value="" disabled="" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-2">SKU:</label>
            <div class="col-md-8">
              <input class="form-control" value="" disabled="" />
            </div>
          </div>
          <div class="form-group"> </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary single-ensure-button" data-loading-text="处理中" data-name="">确定</button>
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
        <input type="hidden" value="{&quot;Status&quot;:&quot;10000&quot;}" id="con-value" />
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">商品管理</a></li>
          </ul>
          <div class="condition-box" id="con-box">
            <div class="condition-box-hd"> <a href="#" class="condition-box-nav on">在售(2)</a>
             <!-- <a href="shopsell1.html" class="condition-box-nav">已下架(1)</a> <a href="#" class="condition-box-nav">待审核(0)</a> <a href="#" class="condition-box-nav">审核失败(0)</a> <a href="#" class="condition-box-nav">处理中(0)</a> <a href="#" class="condition-box-nav">处理失败(0)</a>  -->
             </div>
          </div>
          <div class="alert alert-danger" role="alert" style="margin-top: 15px">
温馨提示：单店铺SKU数超过1万的，在同步前请先联系管理员！
</div>
          <div class="sync-box">
            <div class="info-sign"> <i class="glyphicon glyphicon-info-sign"></i> </div>
            上次同步于2016-05-11 16:37:06，您可以 <a class="line-btn" id="start-sync" href="javascript:void(0)">&lt;再次同步&gt;</a> </div>
          <ul class="condition-box-bd">
            <!-- <li class="search-group">
              <label class="search-label">商品分类：</label>
              <span class="search-span">所有分类</span> <a href="javascript:void(0)" class="btn btn-link search-op" id="sel-category">更多分类&gt;</a> </li>
            <li class="search-group">
              <label class="search-label">商品分组：</label>
              <span class="search-span">所有分组</span> <a href="javascript:void(0)" class="btn btn-link search-op" id="sel-group">更多分组&gt;</a> </li>
            <li class="search-group">
              <label class="search-label">到期时间：</label>
              <a href="javascript:void(0)" class="search-title on">全部</a> <a href="#" class="search-title ">3天之内</a> <a href="#" class="search-title ">7天之内</a> <a href="#" class="search-title ">14天之内</a> </li> -->
            <li class="search-group">
              <label class="search-label">库存数量：</label>
              <a href="javascript:void(0)" class="search-title on">全部</a> <a href="#" class="search-title ">有货</a> <a href="#" class="search-title ">无货</a> </li>
            <li class="search-group form-group form-inline">
              <label class="search-label">内容搜索：</label>
              <div class="input-group">
                <div class="input-group-btn">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="search-key">标题</span> <span class="caret"></span> </button>
                  <ul class="dropdown-menu" id="search-options">
                    <li><a href="javascript: void(0)" data-key="s-title">标题</a></li>
                    <li><a href="javascript: void(0)" data-key="s-sku">SKU</a></li>
                    <li><a href="javascript: void(0)" data-key="s-pid">商品ID</a></li>
                  </ul>
                </div>
                <input class="form-control search-input" name="search" placeholder="标题名称、SKU、商品ID" />
              </div>
              <a href="javascript:void(0)" class="btn btn-success" id="search-btn">搜索</a> </li>
          </ul>
          <div class="content-manage row">
            <div style="margin: 5px 0; float: left"> 符合查询条件的商品有 <span class="important">0</span>件 </div>
            <ul class="manage-control">
              <li> 每页显示 <a class="page-num-control on" href="javascript:void(0)">50</a> <a class="page-num-control " href="#">100</a> <a class="page-num-control " href="#">200</a> </li>
            </ul>
          </div>
          <div class="product-content">
            <div class="checkbox all-check">
              <label>
                <input type="checkbox" />
                <span>已选择<span class="important">0</span>件商品</span> </label>
            </div>
            <div class="nav batch-area"> <a class="batch-op" id="multi-move" href="javascript:void(0)">批量转移</a> <a class="batch-op" id="multi-group" href="javascript:void(0)">调整分组</a> <a class="batch-op" id="multi-offline" href="javascript:void(0)">批量下架</a> <a class="batch-op" id="multi-price" href="javascript:void(0)">编辑价格</a> <a class="batch-op" id="multi-stock" href="javascript:void(0)">编辑库存</a> <a class="batch-op" id="multi-props" href="javascript:void(0)">编辑属性</a> <a class="batch-op" id="multi-other" href="javascript:void(0)">编辑更多信息</a> </div>
            <div class="more-choice">
              <div style="display: inline-block; vertical-align: middle"> 已选 <span class="already-select-sign"></span>件商品, </div>
              <a href="javascript:void(0)"> 勾选全部<span class="important">0</span>件商品 </a> <a href="javascript:void(0)" style="display: none"> 取消全选 </a> </div>
           <table class="table product-table"> 
         <tbody>
          <tr class="table-title"> 
           <th></th> 
           <th>产品</th> 
           <th class="pro-20">标题</th> 
           <th>分组</th> 
           <th>收藏</th> 
           <th>销售</th> 
           <th>SKU</th> 
           <th>价格</th> 
           <th>运费</th> 
           <th>库存</th> 
           <th>启用</th> 
           <th>操作</th> 
          </tr> 
          <tr> 
           <td rowspan="2"> <input type="checkbox" style="margin-left: 12px;" value="5732f0802471d01f3cd022b7" /> </td> 
           <td rowspan="2"> 
            <div class="img-cover"> 
             <img data-original="" src="static/image/1.jpg" style="display: inline;" /> 
            </div> </td> 
           <td rowspan="2"> 
            <div class="pro-title"> 
             <a href="#">Built-in Cables 10000 mAh Power Bank Portable Battery White or Black Color</a> 
            </div> <span class="badge success"> 审核通过 </span> </td> 
           <td rowspan="2">未分配</td> 
           <td rowspan="2">2</td> 
           <td rowspan="2">0</td> 
           <td class="pro-15">PB10000W</td> 
           <td>29.0</td> 
           <td>4.0</td> 
           <td>10</td> 
           <td> <i class="glyphicon glyphicon-ok pass"></i> </td> 
           <td rowspan="2"> 
            <div class="btn-group"> 
             <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
             <ul class="dropdown-menu pull-right" data-id="5732f0802471d01f3cd022b7"> 
              <li> <a class="edit-pro" href="javascript: void(0)">编辑</a> </li> 
              <li> <a class="check-supply-link" href="javascript:void(0)" data-toggle="modal" data-target="#supply-link-modal" data-value="None">查看货源链接</a> </li> 
              <li> <a class="put-off-vars" href="javascript: void(0)" data-toggle="modal" data-target="#single-offline-modal">下架此商品</a> </li> 
             </ul> 
            </div> </td> 
          </tr> 
          <tr> 
           <td class="pro-15">PB10000B</td> 
           <td>29.0</td> 
           <td>4.0</td> 
           <td>2</td> 
           <td> <i class="glyphicon glyphicon-ok pass"></i> </td> 
          </tr> 
          <tr> 
           <td rowspan="1"> <input type="checkbox" style="margin-left: 12px;" value="5732f0802471d01f3cd022b6" /> </td> 
           <td rowspan="1"> 
            <div class="img-cover"> 
             <img data-original="" src="static/image/1.jpg" style="display: inline;" /> 
            </div> </td> 
           <td rowspan="1"> 
            <div class="pro-title"> 
             <a href="#" >Jewelry &amp;amp; Kitchen Precision Scale</a> 
            </div> <span class="badge success"> 审核通过 </span> </td> 
           <td rowspan="1">未分配</td> 
           <td rowspan="1">4</td> 
           <td rowspan="1">0</td> 
           <td class="pro-15">sc0001</td> 
           <td>39.0</td> 
           <td>10.0</td> 
           <td>5</td> 
           <td> <i class="glyphicon glyphicon-ok pass"></i> </td> 
           <td rowspan="1"> 
            <div class="btn-group"> 
             <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
             <ul class="dropdown-menu pull-right" data-id="5732f0802471d01f3cd022b6"> 
              <li> <a class="edit-pro" href="javascript: void(0)">编辑</a> </li> 
              <li> <a class="check-supply-link" href="javascript:void(0)" data-toggle="modal" data-target="#supply-link-modal" data-value="None">查看货源链接</a> </li> 
              <li> <a class="put-off-vars" href="javascript: void(0)" data-toggle="modal" data-target="#single-offline-modal">下架此商品</a> </li> 
             </ul> 
            </div> </td> 
          </tr> 
          <tr> 
           <td rowspan="1"> <input type="checkbox" style="margin-left: 12px;" value="5732f0802471d01f3cd022b5" /> </td> 
           <td rowspan="1"> 
            <div class="img-cover"> 
             <img data-original=""  src="static/image/1.jpg" style="display: inline;" /> 
            </div> </td> 
           <td rowspan="1"> 
            <div class="pro-title"> 
             <a href="#" target="_blank">Power Bank 2000 mAh For iPhone 5/5s/6 Samsung Smart Phone</a> 
            </div> <span class="badge success"> 审核通过 </span> </td> 
           <td rowspan="1">未分配</td> 
           <td rowspan="1">0</td> 
           <td rowspan="1">0</td> 
           <td class="pro-15">ca0001</td> 
           <td>25.0</td> 
           <td>4.0</td> 
           <td>5</td> 
           <td> <i class="glyphicon glyphicon-ok pass"></i> </td> 
           <td rowspan="1"> 
            <div class="btn-group"> 
             <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"> 操作<span class="caret"></span> </a> 
             <ul class="dropdown-menu pull-right" data-id="5732f0802471d01f3cd022b5"> 
              <li> <a class="edit-pro" href="javascript: void(0)">编辑</a> </li> 
              <li> <a class="check-supply-link" href="javascript:void(0)" data-toggle="modal" data-target="#supply-link-modal" data-value="None">查看货源链接</a> </li> 
              <li> <a class="put-off-vars" href="javascript: void(0)" data-toggle="modal" data-target="#single-offline-modal">下架此商品</a> </li> 
             </ul> 
            </div> </td> 
          </tr> 
         </tbody>
        </table> 
            <div class="no-product"> 没有找到符合条件的商品信息 </div>
            <div class="gray-line"></div>
            <div class="checkbox all-check">
              <label>
                <input type="checkbox" />
                <span>已选择<span class="important">0</span>件商品</span> </label>
            </div>
            <div class="nav batch-area"> <a class="batch-op" id="multi-move" href="javascript:void(0)">批量转移</a> <a class="batch-op" id="multi-group" href="javascript:void(0)">调整分组</a> <a class="batch-op" id="multi-offline" href="javascript:void(0)">批量下架</a> <a class="batch-op" id="multi-price" href="javascript:void(0)">编辑价格</a> <a class="batch-op" id="multi-stock" href="javascript:void(0)">编辑库存</a> <a class="batch-op" id="multi-props" href="javascript:void(0)">编辑属性</a> <a class="batch-op" id="multi-other" href="javascript:void(0)">编辑更多信息</a> </div>
            <div class="product-footer">
              <ul class="footer-nav clearfix">
                <li>
                  <ul class="pagination page-bar">
                    <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">首页</span> </a> </li>
                    <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">上一页</span> </a> </li>
                    <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">下一页</span> </a> </li>
                    <li class="disabled"> <a href="javascript:void(0)"> <span aria-hidden="true">尾页</span> </a> </li>
                  </ul>
                </li>
                <li class="form-inline"> 跳到
                  <input type="text" class="form-control page-skip-input" placeholder="1" />
                  页 <a class="btn btn-default page-skip-btn" href="javascript:void(0)">Go!</a> </li>
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
<script src="/js/util/underscore.js"></script>
<script src="/js/shop/shop.js"></script> 
<script src="/js/online/smt.js"></script> 
<script src="/js/online/online.js"></script> 
<script src="/js/util/group.js"></script> 
<script src="/js/util/jquery.lazyload.js"></script>
</body>
</html>