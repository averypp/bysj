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
<title >差评监控</title>
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
<div id="siderbarNav4"></div>
<div class="container-fluid">
  <div class="row">
    <div class="wrap">
      <div class="col-md-12">
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">差评监控</a></li>
          </ul>
          <div class="box green">
            <div class="box-header">
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 差评监控 </h2>
            </div>
            <div class="box-content">
              
              <div class="operation-bar">
                <div>

                  <div class="" style="max-width: 500px;margin-top: 5px;">
                    <div class="input-group">
                      <input type="text" class="form-control" id="asin-input" placeholder="(请填写需要查询的ASIN)" value="<?= $search_asin ?>"/>
                      <span class="input-group-btn">
                        <button class="btn btn-success" type="button" id="asin-search-btn">查询</button>
                      </span>
                    </div>
                    <div style="position:absolute;top:25px;left:550px;">
                        <button class="btn btn-success" type="button" id="asin-add-var">添加ASIN</button>
                    </div>
                    <div class="input-group" style="position:absolute;top:25px;line-height: 34px;right:30px;">
                        <a href="#" style="font-size:16px;color:red;">如何找出留差评的买家？</a>
                    </div>
                  </div>

                </div>
              </div>
              <table class="table">
                <tbody>
                  <tr>
                    <th class="pro-25" colspan="2">产品信息</th>
                    <th class="pro-10">评论星级 <i class='glyphicon glyphicon-sort orderby-star' data-id="star" data-value="<?= $sort ?>" style="color:#888;"></i></th>
                    <th class="pro-10">ProfileID</th>
                    <th class="pro-10">评论日期 <i class='glyphicon glyphicon-sort orderby-date' data-id="review_date" data-value="<?= $sort ?>" style="color:#888;"></i></th>
                    <th class="pro-10">操作</th>

                  </tr>
                  <?php //var_dump($reviews);?>
                    <?php if(count($reviews)){?>
                      <?php foreach($reviews as $review){?>
                      <tr>
                        <td><img src="<?= $review['image_url']?>" /></td>
                        <td>
                          <b><?= $review['title']?></b><br/>
                          <span><?= $review['review_info']?></span><br/>
                          <br/>
                          <a target="_blank" href="https://www.amazon.com/dp/<?= $review['m_asin']?>"><?= $review['m_asin']?></a> / <a target="_blank" href="https://www.amazon.com/gp/pdp/profile/<?= $review['profile_id']?>/ref=cm_cr_getr_d_pdp?ie=UTF8"><?= $review['buyer']?></a>
                        </td>
                        <td style="color:#ffb90f;">
                        <?php for($i = 0; $i<$review['star']; $i++ ){ ?>
                            <i class="glyphicon glyphicon-star"></i>
                        <?php }?>
                        </td>
                        <td><?= $review['profile_id']?></td>
                        <td><?= $review['review_date']?></td>
                        <td>
                          <div class="btn-group" role="group">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> 其他操作 <span class="caret"></span> </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                              <li> 
                                <a target="_blank" <?php if($review['reply_url'])echo "href='".$review['reply_url']."'";?>>评论回复
                                </a> 
                              </li>
                              <li> 
                                <a target="_blank" href="https://www.amazon.com/reviews/<?= $review['m_asin']?>">商品评论页
                                </a> 
                              </li>
                              <li>
                                <a target="_blank" <?php if($review['buyer_url'])echo "href='".$review['buyer_url']."'";?>>评论者首页
                                </a>
                              </li>
                              <li>
                                <a class="del-confirm" href="javascript: void(0)" data-id="<?= $review['m_id']?>"> 
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
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 每页显示<?= isset($_GET['page_no']) ? intval($_GET['page_no']) : 10 ?>个 <span class="caret"></span> </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="<?= $requestUri . '&page_no=10' ?>">10</a></li>
                      <li><a href="<?= $requestUri . '&page_no=50' ?>">50</a></li>
                      <li><a href="<?= $requestUri . '&page_no=100' ?>">100</a></li>
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
        <h4 class="modal-title" id="myModalLabel"> 添加ASIN </h4>
        <input type="hidden" id="var-id" name="var-id" value="">
      </div>

      <div class="modal-body">
        <div>
          <form class="form-horizontal">
            <div class="form-group">
              <div class="col-md-9">
                <textarea class="form-control" rows="10" id="add-asin" data-name="asin" placeholder="(一行请填写一个asin, )" value=""></textarea>
              </div>
            </div>
          </form>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭 </button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." id="asin-add-btn" autocomplete="off">确认添加</button>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
</div>
<!-- /.modal -->

<!-- 模态框（Modal） -->
<div class="modal fade" id="delConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 30%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
        <h4 class="modal-title" id="myModalLabel"> 提示 </h4>
      </div>
      <div class="modal-body">
        <div class="md-loading">
          <div class="md-loading-text" style="font-size:16px;">确认删除对该商品监控吗？</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" data-loading-text="处理中..." data-id="" id="del-monitor" autocomplete="off">确认</button>
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
<script src="/js/monitor/badreview.js"></script> 
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>
</body>
</html>