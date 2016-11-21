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
<title >商品跟卖_监控</title>
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
<input type="hidden" value="<?= $shopId ?>" id="shopId" />
<input type="hidden" value="<?= $shopInfo['platformName'] ?>" id="platformName" />
<input type="hidden" value="<?= $shopInfo['siteName'] ?>" id="siteName" />
<input type="hidden" value="<?= $shopInfo['name'] ?>" id="shopName" />
<input type="hidden" value="<?= $BRcount ?>" id="BRcount">
<!-- /.modal -->
<div class="to-top" hidden="hidden"></div>
<div id="headNav"></div>
<div id="siderbarNav3"></div>
<div class="container-fluid">
  <div class="row">
    <div class="wrap">
      <div class="col-md-12">
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">商品跟卖 监控</a></li>
          </ul>
          <div class="box green">
            <div class="box-header">
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 商品跟卖 监控 </h2>
            </div>
            <div class="box-content">
              
              <div class="operation-bar">
                <div class="row">

                  <div class="" style="max-width: 500px;margin-top: 5px;">
                    <div class="input-group">
                      
                      <input type="text" class="form-control" id="asin-input" placeholder="(多个使用英文“,”分隔)" />
                      <span class="input-group-btn">
                      <button class="btn btn-success" type="button" id="asin-add-btn">添加ASIN</button>
                      </span> </div>
                    <!-- /input-group --> 
                  </div>

                </div>
              </div>
              <table class="table">
                <tbody>
                  <tr>
                    <th class="pro-5" style="min-width: 50px"> <div class="btn-group">
                    </th>
                    <th class="pro-10">产品图片</th>
                    <th class="pro-25">产品标题</th>
                    <th class="pro-10">排除卖家</th>
                    <th class="pro-10">最低总价</th>
                    <th class="pro-10">卖家数量</th>
                    <th class="pro-10">FBA数量</th>
                    <th class="pro-10">是否监控</th>
                    <th class="pro-10">最新监控时间</th>
                    <th class="pro-10">操作</th>

                  </tr>
                  <?php //var_dump($products);?>
                  <?php if(count($products)){?>
                    <?php foreach($products as $product){?>
                      <tr>
                        <td><input type="checkbox" class="sel-pro" data-feed-id="<?=$product['id']?>" /></td>
                        <td><img class="gallery" src="<?=$product['image_url']?>" /></td>
                        <td>
                          <a class="btn-link" href="https://www.amazon.com/dp/<?=$product['asin']?>" target="_blank">
                            <?=$product['item_name']?>
                          </a>
                          <br/><br/>
                          ASIN : <?=$product['asin']?>
                        </td>
                        <td class="v-seller"><?=$product['exclude_seller'];?></td>
                        <td>$<?=$product['low_price']?></td>
                        <td><?=$product['seller_count']?></td>
                        <td><?=$product['fba_count']?></td>
                        <td><?php if($product['is_monitor']){echo "监控";}else{echo "不监控";}?></td>
                        <td><?php if($product['last_monitor_at']){echo date('Y-m-d H:i:s', $product['last_monitor_at']);}else{echo '--';}?></td>
                        <td>
                          
                          <div class="btn-group" role="group">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> 其他操作 <span class="caret"></span> </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                              <li> 
                                <a href="/?r=monitor/detail&status=current&shopId=<?=$shopId?>&asin=<?=$product['asin']?>&id=<?=$product['id']?>"> 跟卖详情 
                                </a> 
                              </li>
                              <li>
                                <a target="_blank" href="https://www.amazon.com/gp/offer-listing/<?=$product['asin'];?>">商品链接
                                </a>
                              </li>
                              <li> 
                                <a class="edit-monitor" data-value="<?=$product['asin']?>" href="javascript: void(0)" data-id="<?=$product['id']?>"> 修改排除卖家列表 
                                </a> 
                              </li>
                              <?php if($product['is_monitor']){?>
                              <li> <a class="cancel-monitor" href="javascript: void(0)" data-id="<?=$product['id']?>"> 取消监控 </a> </li>
                              <?php }else{?>
                              <li> <a class="open-monitor" href="javascript: void(0)" data-id="<?=$product['id']?>"> 开启监控 </a> </li>
                              <?php }?>
                              <li>
                                <a class="del-monitor" href="javascript: void(0)" data-id="<?=$product['id']?>"> 
                                  删除监控 
                                </a>
                              </li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                    <?php }?>
                  <?php }?>

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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title" id="myModalLabel"> 修改监控ASIN信息 </h4>
        <input type="hidden" id="var-id" name="var-id" value="">
      </div>
      <div class="modal-body">
        <div>
          <form class="form-horizontal">
            <div class="form-group">
              <label class="col-md-3 control-label"> <span style=" color:red;">*</span>产品ASIN: </label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="var-Asin" data-name="Asin" value="" readonly />
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label"> <span style=" color:red;">*</span>产品图片: </label>
              <div class="col-md-9">
                <img style="width:65px;height:65px;" src="" id="var-img" />
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label">排除的Seller ID: </label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="var-seller" data-name="exclude-seller" value="" placeholder="(多个使用英文“,”分隔)" />
              </div>
            </div>
          </form>


        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭 </button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="edit-control" autocomplete="off">确认修改</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->

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
<script src="/js/create/template.js"></script> 
<script src="/js/monitor/followseller.js"></script> 
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>
</body>
</html>