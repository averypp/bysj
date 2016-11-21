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
  <title>创建新产品</title> 
  <link href="/css/bootstrap.min.css" rel="stylesheet" /> 
  <link href="/css/common/style.css" rel="stylesheet" /> 
  <link href="/css/layout/layout.css" rel="stylesheet" /> 
  <link href="/css/common/alert.css" rel="stylesheet" /> 
  <link href="/css/common/frame.css" rel="stylesheet" /> 
  <link rel="stylesheet" href="/css/shop/create.css" /> 
  <link rel="stylesheet" href="/css/shop/cropper.css" /> 
  <link rel="stylesheet" href="/css/shop/Huploadify.css" /> 
  <link rel="stylesheet" href="/js/kindeditor/themes/default/default.css" /> 
  <link rel="stylesheet" href="/css/shop/simple.css" /> 
  <link rel="stylesheet" href="/css/shop/prettify.css" /> 
  <link rel="stylesheet" href="/css/zTreeStyle/zTreeStyle.css" /> 
  <link rel="stylesheet" href="/css/shop/image_modal.css" /> 
  <link rel="stylesheet" href="/css/bootstrap-datetimepicker.css" /> 
  <link rel="stylesheet" href="/css/jquery-ui.min.css" /> 
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
  <input type="hidden" id="other-value" value="" /> 
  <input type="hidden" id="item-type" value="" /> 
  <input type="hidden" id="oil" value="[{u'sku': False, u'ShowType': u'List', u'required': True, u'values': [{u'v_name': u'Color', u'relation': [u'Color']}, {u'v_name': u'Size', u'relation': [u'Size']}, {u'v_name': u'Size-Color', u'relation': [u'Size', u'Color']}], u'name': u'VariationTheme'}, {u'sku': False, u'ShowType': u'String', u'name': u'UnitCount', u'required': True, u'values': u'', u'unit': [u'Count', u'Fl Oz', u'Gram', u'Ounce', u'Pound']}, {u'sku': True, u'ShowType': u'CheckBox', u'required': False, u'values': [u'Beige', u'Black', u'Blue', u'Bronze', u'Brown', u'Gold', u'Green', u'Grey', u'Metallic', u'Multicoloured', u'Off-White', u'Orange', u'Pink', u'Purple', u'Red', u'Silver', u'Transparent', u'Turquoise', u'White', u'Yellow'], u'name': u'Color'}, {u'sku': True, u'ShowType': u'CheckBox', u'required': False, u'values': [], u'name': u'Size'}]" /> 
  <input type="hidden" id="currency" value="USD" /> 
  <input type="hidden" value="<?= $shopId ?>" id="shopId" />
  <input type="hidden" value="<?= $shopInfo['platformName'] ?>" id="platformName" />
<input type="hidden" value="<?= $shopInfo['siteName'] ?>" id="siteName" />
<input type="hidden" value="<?= $shopInfo['name'] ?>" id="shopName" />
<input type="hidden" id="tpl_id" value="">
<input type="hidden" id="site_id" value="">
<!-- <input type="hidden" id="level_id" value=""> -->
<input type="hidden" value="<?= $BRcount ?>" id="BRcount">
  <!-- 模态框（Modal） --> 
  <div class="modal fade" id="category-tree" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title" id="myModalLabel"> 为商品选择分类 </h3> 
     </div> 
     <div class="modal-body"> 
      <ul class="nav nav-tabs" id="sel-exit"> 
       <li class="active" data-target="#use-exist"> <a href="javascript:void(0)">使用店铺已有目录</a> </li> 
       <li data-target="#use-cates"> <a href="javascript:void(0)">使用新目录</a> </li> 
      </ul> 
      <div id="use-exist" style="padding: 20px 0"> 
       <div class="group-detail exist-cate"></div> 
      </div> 
      <div id="use-cates" hidden="hidden"> 
       <div>
         您选择的目录为:
        <span class="full-category-name"></span> 
       </div> 
       <div class="category-area"> 
        <ul class="category" data-level="1" id="data-category-tree"> 
         <div class="form-group search-div"> 
          <input type="text" class="cate-search form-control" placeholder="搜索....." /> 
          <span class="glyphicon glyphicon-search form-control-feedback"></span> 
         </div> 
         <?php  foreach($catgory as $key => $cat){    ?>
         <li class="has-leaf" data-id="<?php  echo $cat['node_id'] ;?>" data-en="<?php  echo $cat['keyword'] ;?>" data-cn="" data-level="<?php  echo $cat['level'] ;?>" data-leaf="<?php  echo $cat['leaf'] ;?>"  data-tpl="<?php  echo $cat['tpl_id'] ;?>" > <a href="javascript: void(0)"><?php  echo $cat['node_name'] ;?></a> </li> 
         <?php  } ?>
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
  <div class="modal fade" id="trans-control-modal" tabindex="-1" role="dialog" aria-labelledby="transLabel" aria-hidden="true"> 
   <div class="modal-dialog modal-lg"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title"> 设置翻译选项 </h3> 
     </div> 
     <div class="modal-body"> 
      <p>请选择翻译项，系统将根据选项翻译商品字段</p> 
      <input type="hidden" id="trans-pid" /> 
      <div class="form-group"> 
       <select class="form-control" id="src-lang"> <option value="">请选择源语言</option> <option value="fr"> 法语 (Fran&ccedil;ais) </option> <option value="en"> 英语 (English) </option> <option value="cn"> 简体中文 (简体中文) </option> <option value="de"> 德语 (Deutsch) </option> <option value="it"> 意大利语 (Italiano) </option> <option value="ja"> 日语 (日本語) </option> <option value="es"> 西班牙语 (Espa&ntilde;ol) </option> <option value="ru"> 俄语 (Pусский) </option> <option value="nl"> 荷兰语 (Nederlands) </option> <option value="pt"> 葡萄牙语 (Portugu&ecirc;s) </option> <option value="tw"> 繁体中文 (繁體中文) </option> <option value="ko"> 韩语 (한국의) </option> <option value="sv"> 瑞典语 (Svenska) </option> <option value="th"> 泰语 (ภาษาไทย) </option> <option value="pl"> 波兰语 (Polski) </option> </select> 
      </div> 
      <div class="form-group"> 
       <select class="form-control" id="tar-lang"> <option value="">请选择目标语言</option> <option value="fr"> 法语 (Fran&ccedil;ais) </option> <option value="en"> 英语 (English) </option> <option value="cn"> 简体中文 (简体中文) </option> <option value="de"> 德语 (Deutsch) </option> <option value="it"> 意大利语 (Italiano) </option> <option value="ja"> 日语 (日本語) </option> <option value="es"> 西班牙语 (Espa&ntilde;ol) </option> <option value="ru"> 俄语 (Pусский) </option> <option value="nl"> 荷兰语 (Nederlands) </option> <option value="pt"> 葡萄牙语 (Portugu&ecirc;s) </option> <option value="tw"> 繁体中文 (繁體中文) </option> <option value="ko"> 韩语 (한국의) </option> <option value="sv"> 瑞典语 (Svenska) </option> <option value="th"> 泰语 (ภาษาไทย) </option> <option value="pl"> 波兰语 (Polski) </option> </select> 
      </div> 
      <div class="form-group"> 
       <p style="color: #eb3c00;"> 强烈建议目标语言应符合当前店铺语言 </p> 
       <div class="checkbox"> 
        <label> <input type="checkbox" class="tr-title" checked="" />翻译商品标题 </label> 
       </div> 
       <div class="checkbox"> 
        <label> <input type="checkbox" class="tr-desc" checked="" />翻译商品描述 </label> 
       </div> 
       <div class="checkbox"> 
        <label> <input type="checkbox" class="tr-key" checked="" />翻译商品关键字 </label> 
       </div> 
       <div class="checkbox"> 
        <label> <input type="checkbox" class="tr-point" checked="" />翻译商品BulletPoints </label> 
       </div> 
      </div> 
      <div class="tips"></div> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="trans-control" autocomplete="off">确认</button> 
     </div> 
    </div> 
   </div> 
  </div> 
  <div class="modal fade" id="image-space-modal" tabindex="-1" role="dialog" aria-labelledby="img-select-Label" aria-hidden="true"> 
   <div class="modal-dialog modal-lg"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title" id="img-select-Label"> 从图片空间选择图片 </h3> 
     </div> 
     <div class="modal-body"> 
      <div class="row" id="img-content"> 
      </div> 
      <div class="row"> 
       <div class="col-md-12 "> 
        <button type="button" class="btn btn-primary" id="load-more">加载更多</button> 
       </div> 
      </div> 
     </div> 
     <div class="modal-footer"> 
      <span>最多可选</span>
      <span id="max-select" style="color:red"></span> 
      <span>张,已选</span>
      <span id="has-select" style="color:red"></span>
      <span>张。</span> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" id="img-ensure-btn">确认</button> 
     </div> 
    </div>
    <!-- /.modal-content --> 
   </div> 
  </div>
  <!-- /.modal --> 
  <!-- image-space-modal2 --> 
  <div class="modal fade" id="image-space-modal2" tabindex="-1" role="dialog" aria-labelledby="img-select-Label" aria-hidden="true"> 
   <div class="modal-dialog modal-lg modal-lg-img"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title" id=""> 从图片空间选择图片 </h3> 
     </div> 
     <div class="modal-body" style="position: relative"> 
      <div class="pic-sidebar" id="photoBankGroup"> 
       <div id="photoBankGroupList"> 
        <div id="capacity-box"> 
         <div id="capacity-value"> 
          <div class="percent-status"></div> 
          <div class="percent-value"></div> 
         </div> 
        </div> 
        <div id="groupList"> 
         <div class="groupTitle activeGroup" id="btnShowAllGroup"> 
          <span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;所有分组 
         </div> 
         <div id="groupTree" style="margin: 10px 0 0 5px"> 
          <ul id="tree" class="ztree" data-name="out-tree"></ul> 
         </div> 
        </div> 
        <div class="groupTitle" id="recycleBox" style="margin-bottom: 15px"> 
         <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;图片回收站 
        </div> 
       </div> 
      </div> 
      <div class="container-fluid wrap space-wrap"> 
       <div class="row" id="photoBankStatus"> 
        <div class="search-bar col-sm-6"> 
         <div class="input-group"> 
          <input type="text" class="form-control" id="search-input" placeholder="图片名称" /> 
          <span class="input-group-btn"> <button class="btn btn-success" type="button" data-loading-text="......" id="search-pic">搜索</button> </span> 
         </div> 
        </div> 
        <div class="col-md-6" style="margin-top: 7px"> 
         <button class="btn btn-warning" type="button" id="pic-filter" style="float: right;margin-right: -19px">筛选</button> 
         <div class="col-sm-4" style="float: right"> 
          <input type="text" id="end-time" class="form-control" /> 
         </div> 
         <div style="display:inline-block;float: right;">
          到
         </div> 
         <div class="col-sm-4" style="float: right"> 
          <input type="text" id="start-time" class="form-control" /> 
         </div> 
        </div> 
       </div> 
       <div class="row" id="photoBankContents"> 
        <div class="box" id="photoBankBox"> 
         <div id="photoArea"> 
          <div class="photoBoxTip" id="no-pic-tip">
           此分组下无图片。
          </div> 
          <div class="photoBoxTip" id="no-search-tip">
           找不到符合条件的信息。请重试。
          </div> 
          <div class="photoBoxTip loading-tip" id=""></div> 
          <div id="photoList"> 
          </div> 
         </div> 
        </div> 
       </div> 
       <div class="row img-temp-list ui-sortable" id="sortable"></div> 
      </div> 
     </div> 
     <div class="modal-footer"> 
      <span>最多可选</span>
      <span id="max-select2" style="color:red">6</span> 
      <span>张,</span>
      <span>已选</span>
      <span id="has-select2" style="color:red">0</span>
      <span>张。</span> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" id="img-ensure-btn2">确认</button> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!-- /.modal --> 
  <!-- 图片列表 --> 
  <div class="modal fade" id="upModal" tabindex="-1" role="dialog" aria-labelledby="upModalLabel"> 
   <div class="modal-dialog" role="document"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <h2 class="modal-title" id="upModalLabel" style="font-weight: 400">添加图片</h2> 
     </div> 
     <div class="modal-body"> 
      <div class="from-group" style="border: none"> 
       <div id="local-pic" class="pull-left local-pic" style="width: 108px;margin-right: 10px;">
        
       </div> 
       <ul class="nav nav-pills"> 
        <li role="presentation" class=""><a href="#" class="show-pic">图片展示</a></li> 
        <li role="presentation"><a href="#" class="web-pic">网络图片</a></li> 
        <!-- <li role="presentation"><a href="#" class="pic-space" data-self="amazon-sku">图片空间</a></li>  -->
        <div> 
         <div id="apply-sku"> 
          <!--<button type="button" class="btn btn-default" style="margin-bottom: 10px;float:right" id="apply-btn">确定</button> 
          <!-- <select class="form-control" id="sku_list" style="min-width:100px !important;max-width: 240px;float: right;margin-right:10px">
          </select> 
          <span style="float:right;line-height: 2.5;margin-right: 10px">应用至相同</span>  -->
         </div> 
        </div> 
       </ul> 
       <p style="color:red; display: none" id="pic-warning">采集图片仅供参考请谨慎使用，采集图片版权归原作者所有， 使用后引起的纠纷问题由卖家自己承担，与本站无关。</p> 
      </div> 
      <div class="form-group pic-url" style="display: none"> 
       <button type="button" class="btn btn-success" id="web-pic-ensure-button" style="margin-left: 878px;display:none">确定</button> 
      </div> 
      <div class="form-group pic-space-area" style="display: none;position: relative"> 
       <div class="pic-sidebar" id="photoBankGroup"> 
        <div id="photoBankGroupList"> 
         <div id="capacity-box"> 
          <div id="capacity-value"> 
           <div class="percent-status"></div> 
           <div class="percent-value"></div> 
          </div> 
         </div> 
         <div id="groupList"> 
          <div class="groupTitle activeGroup" id="btnShowAllGroup"> 
           <span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;所有分组 
          </div> 
          <div id="groupTree" style="margin: 10px 0 0 5px"> 
           <ul id="tree-sku" class="ztree" data-name="sku-tree"></ul> 
          </div> 
         </div> 
         <div class="groupTitle" id="recycleBox" style="margin-bottom: 15px"> 
          <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;图片回收站 
         </div> 
        </div> 
       </div> 
       <div class="container-fluid wrap space-wrap"> 
        <div class="row" id="photoBankStatus"> 
         <div class="search-bar col-sm-6"> 
          <div class="input-group"> 
           <input type="text" class="form-control" id="sku-search-input" placeholder="图片名称" /> 
           <span class="input-group-btn"> <button class="btn btn-success" type="button" data-loading-text="......" id="search-pic-sku">搜索</button> </span> 
          </div> 
         </div> 
         <div class="col-md-6" style="margin-top: 7px"> 
          <button class="btn btn-warning" type="button" id="pic-filter-sku" style="float: right;margin-right: -19px">筛选</button> 
          <div class="col-sm-4" style="float: right"> 
           <input type="text" id="sku-end-time" class="form-control" /> 
          </div> 
          <div style="display:inline-block;float: right;">
           到
          </div> 
          <div class="col-sm-4" style="float: right"> 
           <input type="text" id="sku-start-time" class="form-control" /> 
          </div> 
         </div> 
        </div> 
        <div class="row" id="photoBankContents"> 
         <div class="box" id="photoBankBox"> 
          <div id="sku-photoArea"> 
           <div class="photoBoxTip" id="no-pic-tip-sku">
            此分组下无图片。
           </div> 
           <div class="photoBoxTip" id="no-search-tip-sku">
            找不到符合条件的信息。请重试。
           </div> 
           <div class="photoBoxTip loading-tip" id=""></div> 
           <div id="sku-photoList"> 
           </div> 
          </div> 
         </div> 
        </div> 
        <div class="row img-temp-list ui-sortable" id="sortable-sku"></div> 
       </div> 
       <div style="overflow: hidden"> 
        <button type="button" class="btn btn-success" id="pic-space-ensure-button" style="float: right;margin-top: 10px;margin-right: 10px">确定</button> 
        <div style="float: right;line-height: 2px;margin-top: 25px;margin-right: 40px"> 
         <span>最多可选</span>
         <span id="max-select-mod" style="color:red"></span> 
         <span>张,已选</span>
         <span id="already-select-mod" style="color:red"></span>
         <span>张。</span> 
        </div> 
       </div> 
      </div> 
      <div class="pic-display" style="display: block;"> 
       <table> 
        <tbody>
         <tr> 
          <td> 
           <div class="" style="width: 181px;height: 260px;margin-right: 10px;"> 
            <div class="thumbnail" data-index="0" draggable="true"> 
             <img class="image" data-index="0" src="/image/add.png" draggable="false" /> 
             <div class="caption"> 
              <a href="javascript:void(0)" class="btn btn-warning del-pic-mod" style="margin-left: 60px" data-index="0">删除</a> 
             </div> 
            </div> 
           </div> </td> 
          <td> 

          <!--子商品图片-->
           <div class="row" id="feed_img-mod"> 

            <?php for ($i=1; $i < 9; $i++) { ?>
           
            <div class="col-lg-3 col-md-4 col-sm-4"> 
             <div class="thumbnail" data-index="<?php echo $i ?>" draggable="true" style="margin-bottom: 0;"> 
              <img class="image" data-index="<?php echo $i ?>" src="/image/add.png" draggable="false" /> 
              <div class="caption"> 
               <a href="javascript:void(0)" class="btn btn-success btn-xs set-main-pic-mod" style="margin-right: 30px;margin-left: 16px" data-index="<?php echo $i?>">设为主图</a> 
               <a href="javascript:void(0)" class="btn btn-warning btn-xs del-pic-mod" data-index="<?php echo $i?>">删除</a>
              </div> 
             </div> 
            </div> 

            <?php }?>
            

           </div> </td> 
         </tr> 
        </tbody>
       </table> 
      </div> 
      <div class="form-group collection-area" style="display: none;height: 550px;position: relative"> 
       <div class="collection-area-pic" style="min-height: 500px;max-height: 500px;overflow: auto"> 
        <div id="no-collection-tip" style="display: none;text-align: center;margin-top: 20px;font-size: 18px">
         暂无图片
        </div> 
       </div> 
       <div style="overflow: hidden;position: absolute;bottom: -10px;right: 20px"> 
        <button type="button" class="btn btn-success" id="collction-ensure-button" style="float: right;margin-top: 10px;margin-right: 10px">确定</button> 
        <div style="float: right;line-height: 2px;margin-top: 25px;margin-right: 40px"> 
         <span>最多可选</span>
         <span id="max-select-coll" style="color:red"></span> 
         <span>张,已选</span>
         <span id="already-select-coll" style="color:red"></span>
         <span>张。</span> 
        </div> 
       </div> 
      </div> 
     </div> 
     <div class="modal-footer my-modal-footer"> 
      <label id="upload-tips" style="display: none;margin-right: 220px;color:rgb(245, 109, 0);font-size: 15px">图片已达到九张，请删除后再进行上传!</label> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" data-dismiss="modal">保存</button> 
     </div> 
    </div> 
   </div> 
  </div> 
  <!--图片裁剪 --> 
  <div class="modal fade" id="edit-pic-modal" tabindex="-1" role="dialog" aria-labelledby="templateLabel" aria-hidden="true"> 
   <div class="modal-dialog"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title" id="info-template-Label"> 编辑图片 </h3> 
     </div> 
     <div class="modal-body"> 
      <div id="edit-img-content"></div> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" data-loading-text="处理中..." data-method="getCroppedCanvas" autocomplete="off">确认</button> 
     </div> 
    </div>
    <!-- /.modal-content --> 
   </div> 
  </div>
  <!-- /.modal --> 
  <!-- 图片进度已弃用 --> 
  <div class="modal fade" id="up-progress-modal" tabindex="-1" role="dialog" aria-labelledby="templateLabel" aria-hidden="true"> 
   <div class="modal-dialog modal-lg"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title"> 上传图片 </h3> 
     </div> 
     <div class="modal-body"> 
      <div class="progress"> 
       <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
         60% 
       </div> 
      </div> 
      <p>共上传 <span id="total-num" style="color:blue">10</span> 张图片,已上传 <span id="s-num" style="color:#1F8A1F">0</span> 张,失败 <span id="f-num" style="color:darkred">0</span> 张 </p> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
     </div> 
    </div>
    <!-- /.modal-content --> 
   </div> 
  </div>
  <!-- /.modal --> 
  <div class="modal fade" id="bulk-set-sale" tabindex="-1" role="dialog" aria-labelledby="groupLabel" aria-hidden="true"> 
   <div class="modal-dialog" style="width: 800px"> 
    <div class="modal-content"> 
     <div class="modal-header"> 
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button> 
      <h3 class="modal-title"> 设置促销信息 </h3> 
     </div> 
     <div class="modal-body"> 
      <form class="form-horizontal"> 
       <div class="form-group"> 
        <label class="col-md-2 control-label"> 促销设置: </label> 
        <div class="col-md-10"> 
         <div class="material"> 
          <table style="margin-bottom: -10px"> 
           <tbody> 
            <tr> 
             <td style="width:100px">促销价格</td> 
             <td style="padding: 4px"> <input type="text" class="form-control" id="bulk-sale-price" value="" placeholder="促销价格" /> </td> 
            </tr> 
            <tr> 
             <td style="width:100px">促销开始时间</td> 
             <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="bulk-sale-from" value="" placeholder="促销开始时间" /> </td> 
             <td style="width:100px; padding-left: 15px">促销结束时间</td> 
             <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="bulk-sale-to" value="" placeholder="促销结束时间" /> </td> 
            </tr> 
           </tbody> 
          </table> 
         </div> 
        </div> 
       </div> 
      </form> 
     </div> 
     <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button> 
      <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="commit-set-sale" autocomplete="off"> 提交 </button> 
     </div> 
    </div>
    <!-- /.modal-content --> 
   </div> 
  </div>
  <!-- /.modal --> 
  <div id="headNav"></div>
  <div id="siderbarNav"></div> 
  <div class="container-fluid"> 
   <div class="row"> 
    <div class="wrap"> 
     <div class="col-md-12"> 
      <div class="workspace"> 
       <ul class="breadcrumb"> 
        <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li> 
        <li><a href="javascript:void(0)">创建新产品</a></li> 
       </ul> 
       <input type="hidden" id="product-id" value="" /> 
       <input type="hidden" value="" id="root-id" /> 
       <form class="form-horizontal"> 
        <div class="box"> 
         <div class="priority high">
          <span>基本信息</span>
          <a style="margin-right: 20px;float: right;padding-top: 6px;
color: #0DC2CE;" href="#">查看亚马逊商品创建帮助文档</a>
         </div> 
         <div class="task high padding15"> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> 货源链接: </label> 
           <div class="col-md-10"> 
            <div class="input-group">
             <input class="form-control" id="supply-link" value="" /> 
             <span class="input-group-btn"> <button type="button" class="btn btn-default" id="trigger-goto">访问链接</button> </span> 
            </div> 
           </div> 
           <a class="goto-source" href="javascript:void(0)" target="_blank" style="display: none"></a> 
           <div class="col-md-10 col-md-offset-2 source-tip">
            此链接记录货源链接，可编辑，非必填。
           </div> 
          </div> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>产品标题: </label> 
           <div class="col-md-10"> 
            <input type="text" id="product-title" placeholder="长度建议不超过200个字符" class="form-control" required="" value="" /> 
            <div class="title-btns"> 
             <a href="javascript: void(0)" id="t-upcase-btn" data-key="upCase" title="首字母大写"> <span class="glyphicon glyphicon-text-size"></span> </a> 
            </div> 
           </div> 
           <div class="col-md-10 col-md-offset-2" style="margin-top: 5px;color: #8e8e8e">
             已输入
            <span class="already-input">0</span>个字符，建议不超过200个字符。距200还能输入
            <span class="left-input">200</span>个字符。 
           </div> 
          </div> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>ParentSKU: </label> 
           <div class="col-md-10"> 
            <input type="text" class="form-control" id="parent-sku" required="" name="parent_sku" placeholder="如：BG0030BG 长度不超过40个字符" value="" /> 
           </div> 
          </div> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>产品分类: </label> 
           <div class="col-md-10"> 
            <p class="full-category-name form-control-static" id="CategoryID" data-id=""> 未设置分类

</p> 
            <a class="btn btn-primary" role="button" href="#" data-toggle="modal" data-category="first"> 选择分类 </a> 
           </div> 
          </div> 
          <!--ProductType--> 
          <div class="form-group" hidden="hidden"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>ProductType: </label> 
           <div class="col-md-10"> 
            <select class="form-control" id="ProductType" required=""> <option>请选择</option> </select> 
           </div> 
          </div> 
          <!--变体模板--> 
          <div class="form-group" hidden="hidden"> 
           <label for="" class="col-md-2 control-label"> 变体模板: </label> 
           <div class="col-md-10"> 
            <select class="form-control" id="sku-tem"> <option>无</option> <!--<option data-name="Color" data-value="Color">Color</option> <option data-name="Size" data-value="Size">Size</option> <option data-name="Size;Color" selected="" data-value="Size-Color">Size-Color</option> --></select>
           </div> 
          </div> 
          <!--品牌入驻卖家--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>品牌入驻卖家: </label> 
           <div class="col-md-10"> 
            <label class="radio-inline" style="margin-right: 50px"> <input type="radio" name="is-brand" id="is-brand" value="1"  />是 </label> 
            <label class="radio-inline" style="margin-right: 50px"> <input type="radio" name="is-brand" id="is-brand" value="0"  checked=""/>否 </label> 
           </div> 
          </div> 
          <!--品牌必须属性--> 
          <div class="form-group brand-specifics"  style="display: none"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>必须属性: </label> 
           <div class="col-md-10"> 
            <select class="form-control" id="special-upc"> 
            <!--<option value="">请选择</option>
             <option data-name="MfrPartNumber" >MfrPartNumber</option>
             <option data-name="ModelNumber">ModelNumber</option>-->
              </select> 
           </div> 
          </div> 
          <!--产品描述--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>产品描述: </label> 
           <div class="col-md-10"> 
            
            <div id="desc-editor" class="form-control" style="display: none;"></div> 
            <div id="detailHtml" style="display:none;"></div> 
            <p style="margin: 10px 0;color: #aaa;"> <span class="attention">注意</span>编辑描述请先点击『<span class="ke-toolbar-icon ke-toolbar-icon-url ke-icon-source" style="margin: -3px 5px;
display:inline-block" title="HTML代码"></span>』进入编辑状态，再点击『<span class="ke-toolbar-icon ke-toolbar-icon-url ke-icon-source" style="margin: -3px 5px;
display:inline-block" title="HTML代码"></span>』是预览效果。默认是展示预览效果。 </p> 
           </div> 
          </div> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>备货时间: </label> 
           <div class="col-md-10"> 
            <input type="text" class="form-control" id="max-time" required="" name="max_time" value="" placeholder="单位:天 不超过30天" /> 
           </div> 
          </div> 
          <!--产品品牌--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>Brand: </label> 
           <div class="col-md-10"> 
            <input type="text" class="form-control" id="brand" required="" name="brand" value="" placeholder="Brand" /> 
           </div> 
          </div> 
          <!--产品生产商--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>商品生产商: </label> 
           <div class="col-md-10"> 
            <input type="text" class="form-control" id="Manufacture" required="" name="Manufacture" value="" placeholder="Manufacture" /> 
           </div> 
          </div> 
          <!--产品状态--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>商品状态: </label> 
           <div class="col-md-10"> 
            <select class="form-control" id="Condition"> <option value="">请选择</option> <option value="New" selected="">New</option> <option value="UsedLikeNew">UsedLikeNew</option> <option value="UsedVeryGood">UsedVeryGood</option> <option value="UsedGood">UsedGood</option> <option value="UsedAcceptable">UsedAcceptable</option> <option value="CollectibleLikeNew">CollectibleLikeNew</option> <option value="CollectibleVeryGood">CollectibleVeryGood</option> <option value="CollectibleGood">CollectibleGood</option> <option value="CollectibleAcceptable">CollectibleAcceptable</option> <option value="Refurbished">Refurbished</option> <option value="Club">Club</option> </select> 
           </div> 
          </div> 
          <!--生产商建议零售价--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> 制造商建议零售价: </label> 
           <div class="col-md-10"> 
            <input type="text" class="form-control" id="MSRP" name="MSRP" value="" placeholder="MSRP" /> 
           </div> 
          </div> 
          <!--普通商品编码类型--> 
          <div class="form-group common-upc"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span>商品编码类型: </label> 
           <div class="col-md-10"> 
            <select class="form-control" id="product-id-type"> <option value="" selected="">请选择</option> <option value="ISBN">ISBN</option> <option value="UPC" >UPC</option> <option value="EAN">EAN</option> <option value="PZN">PZN</option> <option value="GTIN">GTIN</option> <option value="GCID">GCID</option> </select> 
           </div> 
          </div> 
          <div id="spec-attr"> 
          </div> 
          <!--单体上传属性--> 
          <div id="single-attr" > 
           <!--生产商建议零售价--> 
           <div class="form-group"> 
            <label for="" class="col-md-2 control-label"> <span class="required">*</span>商品编码(UPC/EAN): </label> 
            <div class="col-md-10"> 
             <input type="text" class="form-control" id="UPC" required="" name="UPC" value="" placeholder="商品编码" /> 
            </div> 
           </div> 
           <!--价格--> 
           <div class="form-group"> 
            <label for="" class="col-md-2 control-label"> <span class="required">*</span>价格: </label> 
            <div class="col-md-10"> 
             <input type="text" class="form-control" id="StartPrice" required="" value="" placeholder="价格" /> 
            </div> 
           </div> 
           <!--产品促销--> 
           <div class="form-group"> 
            <label for="" class="col-md-2 control-label"> 促销设置: </label> 
            <div class="col-md-10"> 
             <div class="material"> 
              <table style="margin-bottom: -10px"> 
               <tbody> 
                <tr> 
                 <td style="width:100px">促销价格</td> 
                 <td style="padding: 4px"> <input type="text" class="form-control" id="SalePrice" name="Manufacture" value="" placeholder="促销价格" /> </td> 
                </tr> 
                <tr> 
                 <td style="width:100px">促销开始时间</td> 
                 <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="SaleDateFrom" name="Manufacture" value="" placeholder="促销开始时间" /> </td> 
                 <td style="width:100px; padding-left: 15px">促销结束时间</td> 
                 <td style="padding: 4px;position: relative"> <input type="text" class="form-control date-choose" id="SaleDateTo" name="Manufacture" value="" placeholder="促销结束时间" /> </td> 
                </tr> 
               </tbody> 
              </table> 
             </div> 
            </div> 
           </div> 
           <!--库存--> 
           <div class="form-group"> 
            <label for="" class="col-md-2 control-label"> <span class="required">*</span>库存: </label> 
            <div class="col-md-10"> 
             <input type="text" class="form-control" id="Quantity" required="" value="" placeholder="库存" /> 
             <div style="display: none; color: red; font-size: 13px" id="check-quantity">
              库存只能是大于0的数字
             </div> 
            </div> 
           </div> 
          </div> 
          <!--单品或者父体产品图片--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label" id="parent-image">父产品图片:</label> 
           <div class="col-md-10"> 
            <div class="row"> 
             <div class="col-md-12"> 
              <div id="upload" style="width: 110px;" class="pull-left"> 
               
              </div> 
              <div class="m-left pull-left"> 
               <a class="btn btn-primary" href="#image-net-collapse" data-toggle="collapse" aria-expanded="false" aria-controls="image-net-collapse"> 网络图片选取 </a> 
              </div> 
              <!-- <div class="m-left pull-left"> 
               <a class="btn btn-primary" id="image-space2" data-toggle="modal" data-self="product"> 图片空间选取 </a> 
              </div>  -->
             </div> 
            </div> 
            <div class="row"> 
             <div class="col-md-12"> 
              <div class="collapse" id="image-net-collapse"> 
               <div class="tip-tab"> 
                <div class="input-group"> 
                 <span class="input-group-addon" id="basic-addon1"> http:// </span> 
                 <input type="text" class="form-control" placeholder="输入一个外部链接" aria-describedby="basic-addon1" id="image-net-url" /> 
                </div> 
                <div style="margin-top: 10px;"> 
                 <a class="btn btn-primary" href="javascript:void(0)" id="image-net">确定</a> 
                </div> 
               </div> 
              </div> 
             </div> 
            </div> 
            <div class="form-control-static"> 
             <p> <span class="attention">注意</span> 支持.jpg，.png等格式图片，最长边大于500px，不能包含徽标或水印。推荐1000px以上白色背景，图片详细标准请参阅Amazon平台要求。 </p> 
             <p> <span class="tip">提示</span> 拖动图片，调节图片顺序 </p> 
            </div> 
              <!--父商品图片-->
            <div class="row" id="feed_img"> 

            <?php  for ($i=0; $i < 9 ; $i++) { ?>
             <div class="col-lg-3 col-md-4 col-sm-4"> 
              <div class="thumbnail" data-index="<?php echo $i ?>" draggable="true"> 
               <img class="image" src="/image/add.png" draggable="false" /> 
               <div class="caption"> 
                <a href="javascript:void(0)" class="btn btn-primary del-pic" data-index="<?php echo $i ?>">删除</a> 
               </div>
              </div>
             </div>
             <?php }?>
              <!-- <a href="javascript:void(0)" class="btn btn-warning edit-pic" data-index="<?php echo $i ?>">编辑</a>  -->

            </div> 
           </div> 
          </div> 
          <!--产品关键词--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span> 产品关键词: </label> 
           <div class="col-md-10"> 
            <div class="row" id="key"> 
             <div class="col-md-4"> 
              <input type="text" class="form-control" style="margin-top:10px" name="keyword" id="keyword" value="" placeholder="关键词" /> 
             </div> 
             <div class="col-md-4"> 
              <input type="text" class="form-control" style="margin-top:10px" name="keyword" id="keyword" value="" placeholder="关键词" /> 
             </div> 
             <div class="col-md-4"> 
              <input type="text" class="form-control" style="margin-top:10px" name="keyword" id="keyword" value="" placeholder="关键词" /> 
             </div> 
             <div class="col-md-4"> 
              <input type="text" class="form-control" style="margin-top:10px" name="keyword" id="keyword" value="" placeholder="关键词" /> 
             </div> 
             <div class="col-md-4"> 
              <input type="text" class="form-control" style="margin-top:10px" name="keyword" id="keyword" value="" placeholder="关键词" /> 
             </div> 
            </div> 
           </div> 
          </div> 
          <!--产品的BullentPoint--> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> <span class="required">*</span> BulletPoint: </label> 
           <div class="col-md-10"> 
            <div id="bullet-point"> 
             <input type="text" class="form-control" name="bullet_point" value="" placeholder="BullerPoints" /> 
             <input type="text" class="form-control" name="bullet_point" value="" placeholder="BullerPoints" /> 
             <input type="text" class="form-control" name="bullet_point" value="" placeholder="BullerPoints" /> 
             <input type="text" class="form-control" name="bullet_point" value="" placeholder="BullerPoints" /> 
             <input type="text" class="form-control" name="bullet_point" value="" placeholder="BullerPoints" /> 
            </div> 
            <span class="form-control-static" style="color: #eb3c00"> 5个bullet point不需要全部填写完整,可根据需求填写! </span> 
           </div> 
          </div> 
         </div> 
         <div class="priority medium">
          <span>商品参数</span>
         </div> 
         <div class="task medium padding15"> 
          <div id="pro-prop"> 

          <div class="form-group">
            <label for="" class="col-md-2 control-label">产品属性:
            </label>
            <div class="col-md-10">
            <p class="form-control-static" style="color: #eb3c00">
            请依次选择产品分类, (product type), 变体模板
            </p>
            </div>
          </div>
           <!--<div class="form-group" data-type="String" data-need="true"> 
            <label for="" class="col-md-2 control-label" data-name="UnitCount"> <span class="required">*</span> UnitCount: </label> 
            <div class="col-md-10 form-inline"> 
             <div class="row"> 
              <div class="col-md-3" style="padding-left:0"> 
               <input type="text" class="form-control" style="width:100%" /> 
              </div> 
              <div class="col-md-2"> 
               <select class="form-control"> <option>请选择</option> <option data-name="Count">Count</option> <option data-name="Fl Oz">Fl Oz</option> <option data-name="Gram">Gram</option> <option data-name="Ounce">Ounce</option> <option data-name="Pound">Pound</option> </select> 
              </div> 
             </div> 
            </div> 
           </div> -->
          </div> 
         </div> 
         <div class="priority low">
          <span>变体信息</span>
         </div> 
         <div class="task low padding15"> 
          <div id="sku-prop"> 

          <div class="form-group">
            <label for="" class="col-md-2 control-label">变体属性:
            </label>
            <div class="col-md-10">
            <p class="form-control-static" style="color: #eb3c00">
            请依次选择产品分类, (product type), 变体模板
            </p>
            </div>
          </div>

           <!--<div class="form-group" data-select="multi" data-type="CheckBox" data-need="false"> 
            <label for="" class="col-md-2 control-label" data-name="Color"><span class="required">*</span> Color: </label> 
            <div class="col-md-10 form-inline"> 
             <div class="row" style="padding-left:15px"> 
              <div class="checkbox" style="width: 210px"task medium padding15
               <label><input type="checkbox" class="kcb" name="Color" data-name="Beige" />Beige</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Black" />Black</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Blue" />Blue</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Bronze" />Bronze</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Brown" />Brown</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Gold" />Gold</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Green" />Green</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Grey" />Grey</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Metallic" />Metallic</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Multicoloured" />Multicoloured</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Off-White" />Off-White</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Orange" />Orange</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Pink" />Pink</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Purple" />Purple</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Red" />Red</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Silver" />Silver</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Transparent" />Transparent</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Turquoise" />Turquoise</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="White" />White</label>
              </div> 
              <div class="checkbox" style="width: 210px">
               <label><input type="checkbox" class="kcb" name="Color" data-name="Yellow" />Yellow</label>
              </div> 
             </div> 
            </div> 
            <div class="col-md-10 col-md-offset-2 form-inline"> 
             <input type="text" class="form-control" style="margin-right:8px" placeholder="输入自定义属性" /> 
             <button type="button" class="btn btn-primary" name="addSpec">添加自定义属性</button> 
            </div> 
           </div> 
           <div class="form-group" data-select="multi" data-type="CheckBox" data-need="false"> 
            <label for="" class="col-md-2 control-label" data-name="Size"><span class="required">*</span> Size: </label> 
            <div class="col-md-10 form-inline"> 
             <div class="row" style="padding-left:15px"> 
             </div> 
             <div class="checkbox" style="width: 210px;">
              <label><input type="checkbox" class="kcb" data-name="ffff7" checked="" />ffff7</label>
             </div>
            </div> 
            <div class="col-md-10 col-md-offset-2 form-inline"> 
             <input type="text" class="form-control" style="margin-right:8px" placeholder="输入自定义属性" /> 
             <button type="button" class="btn btn-primary" name="addSpec">添加自定义属性</button> 
            </div> 
           </div> -->
          </div> 
          <div id="sku-variation">


          <!-- <div class="form-group">
            <div class="col-md-12">
             <div class="form-inline">
              <span style="margin:8px">批量设置价格：<input type="text" class="form-control" id="batch-price" /></span>
              <a class="btn btn-primary batch-set-price" href="javascript:void(0)">确定</a>
              <span style="margin:8px">批量设置库存：<input type="text" class="form-control" id="batch-stock" /></span>
              <a class="btn btn-primary batch-set-stock" href="javascript:void(0)">确定</a>
              <span style="margin:8px">批量设置促销信息:</span>
              <a class="btn btn-success" href="javascript:void(0)" data-toggle="modal" data-target="#bulk-set-sale">一键设置</a>
              <span style="margin:8px">批量设置商品编码(只适用于品牌入驻卖家将商品编码与SKU设置一致):</span>
              <a class="btn btn-info bulk-special-upc" href="javascript:void(0)">一键设置</a>
             </div>
            </div>
            <div class="col-md-12">
             <table class="table table-striped table-bordered">
              <tbody>
               <tr class="variation-row">
                <th style="width: 100px">是否生效</th>
                <th class="variation-name" style="width: 150px" data-name="Color">Color</th>
                <th class="variation-name" style="width: 150px" data-name="Size">Size</th>
                <th class="variation-th">价格(USD)</th>
                <th class="variation-th">促销价格(USD)</th>
                <th>促销开始日期</th>
                <th>促销结束日期</th>
                <th class="variation-th">库存(件/个)</th>
                <th style="width: 150px">商品编码(UPC/EAN)</th>
                <th>图片URL</th>
                <th style="width: 200px">SKU编码<a class="one-btn-sku" id="onekey-SKU" href="javascript:void(0)">(一键生成SKU)</a></th>
               </tr>
               <tr class="variation-row">
                <td><label><input type="checkbox" class="sku-effect" checked="" /></label></td>
                <td><span data-name="Color" data-value="Pink" class="variation-attr" style="word-break: break-all">Pink</span></td>
                <td><span data-name="Size" data-value="ffff7" class="variation-attr" style="word-break: break-all">ffff7</span></td>
                <td><input type="text" class="form-control v-price" /></td>
                <td><input type="text" class="form-control v-sale-price sale" /></td>
                <td style="width:160px !important;position:relative"><input type="text" class="form-control v-sale-begin date-choose sale" /></td>
                <td style="width:160px !important;position:relative"><input type="text" class="form-control v-sale-end date-choose sale" /></td>
                <td><input type="text" class="form-control v-stock" /></td>
                <td><input type="text" class="form-control v-upc" /></td>
                <td style="width:268px !important;">
                 <div class="input-group">
                  <input type="text" class="form-control v-pic" readonly="readonly" "="" />
                  <div class="input-group-addon btn btn-primary display-button" data-toggle="modal" data-target="#upModal">
                   图片列表
                  </div>
                 </div></td>
                <td><input type="text" class="form-control v-sku" style="word-break: break-all" /></td>
               </tr>
               <tr class="variation-row">
                <td><label><input type="checkbox" class="sku-effect" checked="" /></label></td>
                <td><span data-name="Color" data-value="White" class="variation-attr" style="word-break: break-all">White</span></td>
                <td><span data-name="Size" data-value="ffff7" class="variation-attr" style="word-break: break-all">ffff7</span></td>
                <td><input type="text" class="form-control v-price" /></td>
                <td><input type="text" class="form-control v-sale-price sale" /></td>
                <td style="width:160px !important;position:relative"><input type="text" class="form-control v-sale-begin date-choose sale" /></td>
                <td style="width:160px !important;position:relative"><input type="text" class="form-control v-sale-end date-choose sale" /></td>
                <td><input type="text" class="form-control v-stock" /></td>
                <td><input type="text" class="form-control v-upc" /></td>
                <td style="width:268px !important;">
                 <div class="input-group">
                  <input type="text" class="form-control v-pic" readonly="readonly" "="" />
                  <div class="input-group-addon btn btn-primary display-button" data-toggle="modal" data-target="#upModal">
                   图片列表
                  </div>
                 </div></td>
                <td><input type="text" class="form-control v-sku" style="word-break: break-all" /></td>
               </tr>
              </tbody>
             </table>
            </div>
           </div>-->




          </div> 
         </div> 
         <div class="priority medium">
          <span>包装信息</span>
         </div> 
         <div class="task medium padding15" id="pack-info"> 
          <div class="form-group"> 
           <label for="" class="col-md-2 control-label"> 邮寄重量: </label> 
           <div class="col-md-10"> 
            <div class="row"> 
             <div class="col-md-4"> 
              <input type="text" class="form-control" value="" id="shipping-weight" /> 
             </div> 
             <div class="col-md-2"> 
              <select class="form-control" id="WeightUnit"> <option value="">请选择</option> <option value="GR">GR</option> <option value="KG">KG</option> <option value="LB">LB</option> <option value="OZ">OZ</option> </select> 
             </div> 
            </div> 
           </div> 
          </div> 
         </div> 
        </div> 
        <div class="row"> 
         <div class="col-md-12 text-right"> 
          <div class="btn-group" role="group"> 
           <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="save-btn" autocomplete="off"> 保存为草稿 </button> 
          </div> 
          <div class="btn-group" role="group"> 
           <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="submit-btn" autocomplete="off"> 保存到待发布 </button> 
          </div> 
          <div class="btn-group" role="group"> 
           <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="trans-sub-btn" autocomplete="off"> 翻译并保存商品 </button> 
          </div> 
         </div> 
        </div> 
       </form> 
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
  <script src="/js/bootstrap.min.js"></script> 
  <script src="/js/public.js"></script> 
  <script src="/js/common/logout.js"></script> 
  <script src="/js/common/inform.js"></script> 
  <script src="/js/util/ajaxupload.js"></script> 
  <script src="/js/util/swfupload.js"></script> 
  <script src="/js/shop/shop.js"></script> 
  <script src="/js/create/template.js"></script> 
  <script src="/js/kindeditor/kindeditor.js"></script> 
  <script src="/js/kindeditor/kindeditorEdit.js"></script> 
  <script src="/js/kindeditor/zh_CN.js"></script> 
  <script src="/js/util/cropper.js"></script> 
  <script src="/js/util/Huploadify.js"></script> 
  <script src="/js/create/amazon.js"></script> 
  <script src="/js/shop/create.js"></script> 
  <script src="/js/shop/imageModal.js"></script> 
  <script src="/js/jquery.ztree.all-3.5.js"></script> 
  <script src="/js/jquery.ztree.exedit-3.5.js"></script> 
  <script src="/js/moment-with-locales.js"></script> 
  <script src="/js/bootstrap-datetimepicker.js"></script> 
  <script src="/js/jquery-ui.min.js"></script> 
  <script type="text/javascript">
//加载富文本编辑器
KindEditor.ready(function(K) {
options.items = ['fullscreen','source']
window.editor = K.create('#desc-editor',options);
var detail = $("#detailHtml").text();
$("#detailHtml").empty();
editor.insertHtml(detail);
});
$(function(){
$('.date-choose').datetimepicker({
format: 'YYYY-MM-DD'
});
})
</script> 
 </body>
</html>