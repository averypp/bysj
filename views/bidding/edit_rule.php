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
  <title >智能调价</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/common/style.css" rel="stylesheet" />
  <link href="/css/layout/layout.css" rel="stylesheet" />
  <link href="/css/common/alert.css" rel="stylesheet" />
  <link href="/css/common/frame.css" rel="stylesheet" />
  <link rel="stylesheet" href="/css/shop/product.css" />
  <link rel="stylesheet" href="/css/shop/create.css" />
  <link rel="stylesheet" href="css/shop/online.css" /> 
  <link rel="stylesheet" href="css/shop/group.css" /> 
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
<!-- /.modal -->
<div class="to-top" hidden="hidden"></div>
<div id="headNav"></div>
<div id="siderbarNav5"></div>
<div class="container-fluid">
  <div class="row">
    <div class="wrap">
      <div class="col-md-12">
        <div class="workspace">
          <ul class="breadcrumb">
            <li> <i class="glyphicon glyphicon-home"></i> <a href="javascript:void(0)">店铺</a> </li>
            <li><a href="javascript:void(0)">智能调价</a></li>
          </ul>
          <div class="box green">
            <div class="box-header">
              <h2> <i class="glyphicon glyphicon-list"></i> <span class="break"></span> 调价规则 </h2>
            </div>
            <div class="box-content">
              <div class="operation-bar">
                <div class="row">
                  <a class="btn btn-primary" role="button" href="?r=bidding/rulelist&shopId=<?= $shopId ?>"> 返回规则列表</a>
                </div>
              </div>
              <div>
                <form class="form-horizontal" id="rule-form" method="post"> 
                  <input type="hidden" value="<?= $shopId ?>" name="shopId" id="shopId" />
                  <input type="hidden" value="<?= $shopInfo['platformName'] ?>" name="platformName" id="platformName" />
                  <input type="hidden" value="<?= $shopInfo['siteName'] ?>" name="siteName" id="siteName" />
                  <input type="hidden" value="<?= $shopInfo['name'] ?>" name="shopName" id="shopName" />
                  <input type="hidden" value="<?= $BRcount ?>" name="BRcount" id="BRcount">
                  <div class="box"> 
                    <!-- 基本信息 -->
                    <div class="priority high">
                      <span>基本信息</span>
                    </div> 
                    <div class="task high padding15"> 
                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> <span class="required">*</span>规则名称: </label> 
                        <div class="col-md-6"> 
                          <input class="form-control" name="rule-name"  value="<?php echo isset($ruleInfo) ? $ruleInfo['name'] : ''; ?>" >
                          <input class="form-control" name="rule-id" type="hidden" value="<?php echo isset($ruleInfo) ? $ruleInfo['id'] : ''; ?>" >
                        </div>
                      </div> 

                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 规则说明: </label> 
                        <div class="col-md-8"> 
                          <textarea class="form-control" rows="5" name="rule-description"><?= isset($ruleInfo) ? $ruleInfo['description'] : '' ?></textarea>
                        </div>
                      </div>
                    </div>
                    <!-- 黄金购物车设定 -->
                    <div class="priority medium">
                      <span>黄金购物车设定</span>
                    </div> 
                    <div class="task medium padding15">
                      <div class="form-group">
                        <div class="col-md-12">
                          <p class="form-control-static" style="color: #eb3c00">
                            当您在黄金购物车里，这是唯一将被使用的设置。<br>
                            备注：黄金购物车规则会套用在 Amazon、FBA 和 FBM 来达到最佳效果。
                          </p>
                        </div>
                      </div>

                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 黄金购物车设定: </label> 
                        <div class="col-md-4"> 
                          <select class="form-control" id="buybox-set" name="buybox[buybox_set]">
                            <option value="1">降低或提高黄金购物车价格</option>
                            <option value="2">提高我的购物车价格最大化利润</option>
                            <option value="3">降低我的黄金购物车内价格以保持竞争力</option>
                            <option value="4">不要改变我的黄金购物车价格</option>
                            <option value="5">暂停黄金购物车设定</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group buybox buybox-lower"> 
                        <label for="" class="col-md-2 control-label"></label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-2 control-label">
                            与最低价竞争时降低
                            </div>
                            <div class="col-md-3">
                              <input class="form-control" type="number" step="0.01" min="0.01" name="buybox[buybox_set_value1]" id=buybox_set_value1 value="0.01">
                            </div>
                            <div class="col-md-1">
                              <select class="form-control" id=buybox_set_math1 name="buybox[buybox_set_math1]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group buybox buybox-raise"> 
                        <label for="" class="col-md-2 control-label"></label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-2 control-label">
                            提高价格时降低
                            </div>
                            <div class="col-md-3">
                              <input class="form-control" type="number" step="0.01" min="0.01" name="buybox[buybox_set_value2]" id=buybox_set_value2 value="0.01">
                            </div>
                            <div class="col-md-1">
                              <select class="form-control" id=buybox_set_math2  name="buybox[buybox_set_math2]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group buybox buybox-item"> 
                        <label for="" class="col-md-2 control-label"> 当调整后的价格高于最大价格: </label> 
                        <div class="col-md-4">
                          <select class="form-control" id=buybox_item name="buybox[buybox_item]">
                            <option value="stop">不智能调价</option>
                            <option value="max">使用最大价格</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <!-- 黄金购物車设定 end -->

                    <!-- 智能调价设定 -->
                    <div class="priority low">
                      <span>智能调价设定</span>
                    </div>
                    <div class="task low padding15">
                      <div class="form-group">
                        <div class="col-md-12">
                          <p class="form-control-static" style="color: #eb3c00">
                            当您不是在黄金购物车里，这些都是将要使用的设置。<br>
                            备注：透过使用自动竞争，您将开始与选择价格最低的竞争者竞争，您可以与多达20名竞争对手进行竞争。
                          </p>
                        </div>
                      </div>

                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> <span class="required">*</span> 选择竞争对手: </label> 
                        <div class="col-md-10"> 
                          <!-- <label class="checkbox-inline" style="margin-right: 50px">
                            <input type="checkbox" name="competitors[]" value="Amazon"/>亚马逊
                          </label>  -->
                          <label class="checkbox-inline" style="margin-right: 50px">
                            <input type="checkbox" id="FBA" name="competitors[]" value="FBA" required=""/>FBA 
                          </label> 
                          <label class="checkbox-inline" style="margin-right: 50px">
                            <input type="checkbox" id="FBM" name="competitors[]" value="FBM" required=""/>FBM 
                          </label> 
                          <label class="checkbox-inline" style="margin-right: 50px">
                            <input type="checkbox" id="non_featured_sellers" name="competitors[]" value="non_featured_sellers" required=""/>非特色卖家 
                          </label> 
                        </div> 
                      </div> 
                      
                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 当竞争对手高于最小价格: </label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-3">
                              <select class="form-control symbol-change" id=basic_gt_item name="basic[gt][item]">
                                <option value="competitor">对手的竞争价格</option>
                                <option value="min">最小价格</option>
                                <option value="max">最大价格</option>
                              </select>
                            </div>
                            <div class="col-md-1">
                              <select class="form-control" id=basic_gt_symbol name="basic[gt][symbol]">
                                <option value="-">-</option>
                                <option value="+">+</option>
                              </select>
                            </div>
                            <div class="col-md-2">
                              <input class="form-control" id=basic_gt_value type="number" step="0.01" min="0.01" name="basic[gt][value]" value="0.01">
                            </div>
                            <div class="col-md-1">
                              <select class="form-control" id=basic_gt_math name="basic[gt][math]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div> 
                      </div> 
                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 当竞争对手低于最小价格: </label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-3">
                              <select class="form-control"  name="basic[lt][options]" id="basic-lt">
                                <option value="auto">使用自动竞争</option>
                                <option value="min">使用最小价格</option>
                                <option value="max">使用最大价格</option>
                                <option value="stop">不智能调价</option>
                                <option value="customize">自定您的价格</option>
                              </select>
                            </div>
                            <div class="col-md-3 basic-lt-temp">
                              <select class="form-control symbol-change" id=basic_lt_item name="basic[lt][item]">
                                <option value="competitor">对手的竞争价格</option>
                                <option value="min">最小价格</option>
                                <option value="max">最大价格</option>
                              </select>
                            </div>
                            <div class="col-md-1 basic-lt-temp">
                              <select class="form-control" id=basic_lt_symbol name="basic[lt][symbol]">
                                <option value="-">-</option>
                                <option value="+">+</option>
                              </select>
                            </div>
                            <div class="col-md-2 basic-lt-temp">
                              <input class="form-control" id=basic_lt_value type="number" step="0.01" min="0.01" name="basic[lt][value]" value="0.01">
                            </div>
                            <div class="col-md-1 basic-lt-temp" >
                              <select class="form-control" id=basic_lt_math name="basic[lt][math]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div> 
                      </div> 
                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 当竞争对手等于最小价格: </label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-3">
                              <select class="form-control"  name="basic[eq][options]" id="basic-eq">
                                <option value="auto">使用自动竞争</option>
                                <option value="min">使用最小价格</option>
                                <option value="max">使用最大价格</option>
                                <option value="stop">不智能调价</option>
                                <option value="customize">自定您的价格</option>
                              </select>
                            </div>
                            <div class="col-md-3 basic-eq-temp">
                              <select class="form-control symbol-change" id=basic_eq_item name="basic[eq][item]">
                                <option value="competitor">对手的竞争价格</option>
                                <option value="min">最小价格</option>
                                <option value="max">最大价格</option>
                              </select>
                            </div>
                            <div class="col-md-1 basic-eq-temp">
                              <select class="form-control" id=basic_eq_symbol name="basic[eq][symbol]">
                                <option value="-">-</option>
                                <option value="+">+</option>
                              </select>
                            </div>
                            <div class="col-md-2 basic-eq-temp">
                              <input class="form-control" id=basic_eq_value type="number" step="0.01" min="0.01" name="basic[eq][value]" value="0.01">
                            </div>
                            <div class="col-md-1 basic-eq-temp">
                              <select class="form-control" id=basic_eq_math name="basic[eq][math]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div> 
                      </div> 
                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 无竞争: </label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-3">
                              <select class="form-control"  name="basic[none][options]" id="basic-none">
                                <option value="max">使用最大价格</option>
                                <option value="stop">不智能调价</option>
                                <option value="customize">自定您的价格</option>
                              </select>
                            </div>
                            <div class="col-md-2 basic-none-temp">
                              <select class="form-control symbol-change" id=basic_none_item name="basic[none][item]">
                                <option value="min">最小价格</option>
                                <option value="max">最大价格</option>
                              </select>
                            </div>
                            <div class="col-md-1 basic-none-temp">
                              <select class="form-control" id=basic_none_symbol name="basic[none][symbol]">
                                <option value="-">-</option>
                                <option value="+">+</option>
                              </select>
                            </div>
                            <div class="col-md-2 basic-none-temp">
                              <input class="form-control" id=basic_none_value type="number" step="0.01" min="0.01" name="basic[none][value]" value="0.01">
                            </div>
                            <div class="col-md-1 basic-none-temp">
                              <select class="form-control" id=basic_none_math name="basic[none][math]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div> 
                      </div> 
                      <div class="form-group"> 
                        <label for="" class="col-md-2 control-label"> 当所有竞争者都低于最小价格 & 高于最大价格: </label> 
                        <div class="col-md-10"> 
                          <div class="row">
                            <div class="col-md-3">
                              <select class="form-control symbol-change"  name="basic[both][options]" id="basic-both">
                                <option value="stop">不智能调价</option>
                                <option value="min">最小价格</option>
                                <option value="max">最大价格</option>
                              </select>
                            </div>
                            <div class="col-md-1 basic-both-temp">
                              <select class="form-control" id=basic_both_symbol name="basic[both][symbol]">
                                <option value="-">-</option>
                                <option value="+">+</option>
                              </select>
                            </div>
                            <div class="col-md-2 basic-both-temp">
                              <input class="form-control" id=basic_both_value type="number" step="0.01" min="0.01" name="basic[both][value]" value="0.01">
                            </div>
                            <div class="col-md-1 basic-both-temp">
                              <select class="form-control" id=basic_both_math name="basic[both][math]">
                                <option value="$">$</option>
                                <option value="%">%</option>
                              </select>
                            </div>
                          </div>
                        </div> 
                      </div> 

                      <!-- 最小价格保障预设设定 -->
                      <div class="form-group"> 
                        <label for="" class="control-label"> </label>
                        <div class="col-md-12">
                          <div class="priority high">
                            <span>最小价格保障预设设定</span>
                          </div>
                          <div class="task high padding15">
                            <div class="form-group">
                              <div class="col-md-12">
                                <p class="form-control-static" style="color: #eb3c00">
                                  [智能调价设定]会调整您的价格等于或低于最小价格，您的价格反而会自动按照下面的设置重新调整。
                                </p>
                              </div>
                            </div>

                            <div class="form-group"> 
                              <label for="" class="col-md-2 control-label"> 当调整后的价格等于或低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="protected[after_le][options]" id="protected">
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 protected-temp">
                                    <select class="form-control symbol-change" id=protected_after_le_item name="protected[after_le][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 protected-temp">
                                    <select class="form-control" id=protected_after_le_symbol name="protected[after_le][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 protected-temp">
                                    <input class="form-control" id=protected_after_le_value type="number" step="0.01" min="0.01" name="protected[after_le][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 protected-temp">
                                    <select class="form-control" id=protected_after_le_math name="protected[after_le][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                          </div>
                        </div>
                      </div>
                      <!-- 最小价格保障预设设定 end -->

                      <!-- 进阶设定 -->
                      <div class="form-group">
                        <label for="" class="control-label"> </label>
                        <div class="col-md-12">
                          <div class="priority medium">
                            <span>进阶设定</span>
                          </div>
                          <div class="task medium padding15">
                            <div class="form-group">
                              <div class="col-md-12">
                                <p class="form-control-static" style="color: #eb3c00">
                                  您要如何进一步自订特定的竞争对手。 注：价格调整为'OFF'的情况，[智能调价设定]仍为优先的设定。 倘若您的'ON'的情况，[进阶设定]将被优先于[智能调价设定]。
                                </p>
                              </div>
                            </div>

                            <!-- FBA vs FBA -->
                            <div class="form-group">
                              <label for="" class="col-md-2 control-label"> FBA vs FBA: <br>
                              (当我的清单列表是FBA及竞争对手是FBA)</label> 
                              <div class="col-md-10">  
                                <p>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fba_vs_fba" name="fba_vs_fba" value="on"/>开启
                                  </label>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fba_vs_fba" name="fba_vs_fba" checked="checked" value="off"/>关闭
                                  </label>
                                  <input type="hidden" class="fba_vs_fba_is_open" name="fba_vs_fba[is_open]" value='0'/>
                                </p>
                              </div> 
                            </div>

                            <div class="form-group fba_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手高于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control symbol-change" id=fba_vs_fba_gt_item name="fba_vs_fba[gt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id=fba_vs_fba_gt_sumbol name="fba_vs_fba[gt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2">
                                    <input class="form-control" type="number" id=fba_vs_fba_gt_value step="0.01" min="0.01" name="fba_vs_fba[gt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id=fba_vs_fba_gt_math name="fba_vs_fba[gt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fba_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fba_vs_fba[lt][options]" id="fba_vs_fba_lt">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fba_vs_fba_lt_temp">
                                    <select class="form-control symbol-change" id="fba_vs_fba_lt_item"
                                    name="fba_vs_fba[lt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fba_vs_fba_lt_temp">
                                    <select class="form-control" id="fba_vs_fba_lt_symbol" name="fba_vs_fba[lt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fba_vs_fba_lt_temp">
                                    <input class="form-control" type="number" step="0.01" min="0.01" name="fba_vs_fba[lt][value]" id="fba_vs_fba_lt_value" value="0.01">
                                  </div>
                                  <div class="col-md-1 fba_vs_fba_lt_temp">
                                    <select class="form-control" id="fba_vs_fba_lt_math" git statuname="fba_vs_fba[lt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fba_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手等于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fba_vs_fba[eq][options]" id="fba_vs_fba_eq">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fba_vs_fba_eq_temp">
                                    <select class="form-control symbol-change" id="fba_vs_fba_eq_item" name="fba_vs_fba[eq][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fba_vs_fba_eq_temp">
                                    <select class="form-control" id="fba_vs_fba_eq_symbol" name="fba_vs_fba[eq][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fba_vs_fba_eq_temp">
                                    <input class="form-control" type="number" step="0.01" min="0.01" name="fba_vs_fba[eq][value]" id="fba_vs_fba_eq_value" value="0.01">
                                  </div>
                                  <div class="col-md-1 fba_vs_fba_eq_temp">
                                    <select class="form-control" id="fba_vs_fba_eq_math" name="fba_vs_fba[eq][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div> 
                            <div class="form-group fba_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当调整后的价格等于或低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fba_vs_fba[after_le][options]" id="fba_vs_fba_after_le">
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fba_vs_fba_after_le_temp">
                                    <select class="form-control symbol-change" id="fba_vs_fba_after_le_item" name="fba_vs_fba[after_le][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fba_vs_fba_after_le_temp">
                                    <select class="form-control" id="fba_vs_fba_after_le_symbol" name="fba_vs_fba[after_le][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fba_vs_fba_after_le_temp">
                                    <input class="form-control" type="number" id="fba_vs_fba_after_le_value" step="0.01" min="0.01" name="fba_vs_fba[after_le][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fba_vs_fba_after_le_temp">
                                    <select class="form-control" id="fba_vs_fba_after_math" name="fba_vs_fba[after_le][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div>
                            <hr/>
                            <!-- FBA vs FBA end -->

                            <!-- FBA vs FBM -->
                            <div class="form-group">
                              <label for="" class="col-md-2 control-label"> FBA vs FBM: <br>
                              (当我的清单列表是FBA及竞争对手是FBM)</label> 
                              <div class="col-md-10">  
                                <p>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fba_vs_fbm" name="fba_vs_fbm" value="on"/>开启
                                  </label>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fba_vs_fbm" name="fba_vs_fbm" checked="checked" value="off"/>关闭
                                  </label>
                                  <input type="hidden" class="fba_vs_fbm_is_open" name="fba_vs_fbm[is_open]" value='0'/>
                                </p>
                              </div> 
                            </div>

                            <div class="form-group fba_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手高于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control symbol-change" id="fba_vs_fbm_gt_item" name="fba_vs_fbm[gt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id="fba_vs_fbm_gt_symbol" name="fba_vs_fbm[gt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2">
                                    <input class="form-control" id="fba_vs_fbm_gt_value" type="number" step="0.01" min="0.01" name="fba_vs_fbm[gt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id="fba_vs_fbm_gt_math" name="fba_vs_fbm[gt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fba_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fba_vs_fbm[lt][options]" id="fba_vs_fbm_lt">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fba_vs_fbm_lt_temp">
                                    <select class="form-control symbol-change" id="fba_vs_fbm_lt_item" name="fba_vs_fbm[lt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fba_vs_fbm_lt_temp">
                                    <select class="form-control" id="fba_vs_fbm_lt_symbol" name="fba_vs_fbm[lt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fba_vs_fbm_lt_temp">
                                    <input class="form-control" type="number" id="fba_vs_fbm_lt_value" step="0.01" min="0.01" name="fba_vs_fbm[lt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fba_vs_fbm_lt_temp">
                                    <select class="form-control" id="fba_vs_fbm_lt_math" name="fba_vs_fbm[lt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fba_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手等于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fba_vs_fbm[eq][options]" id="fba_vs_fbm_eq">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fba_vs_fbm_eq_temp">
                                    <select class="form-control symbol-change" id="fba_vs_fbm_eq_item" name="fba_vs_fbm[eq][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fba_vs_fbm_eq_temp">
                                    <select class="form-control" id="fba_vs_fbm_eq_symbol" name="fba_vs_fbm[eq][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fba_vs_fbm_eq_temp">
                                    <input class="form-control" id="fba_vs_fbm_eq_value" type="number" step="0.01" min="0.01" name="fba_vs_fbm[eq][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fba_vs_fbm_eq_temp">
                                    <select class="form-control" id="fba_vs_fbm_eq_math" name="fba_vs_fbm[eq][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div> 
                            <div class="form-group fba_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当调整后的价格等于或低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fba_vs_fbm[after_le][options]" id="fba_vs_fbm_after_le">
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fba_vs_fbm_after_le_temp">
                                    <select class="form-control symbol-change" id="fba_vs_fbm_after_le_item" name="fba_vs_fbm[after_le][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fba_vs_fbm_after_le_temp">
                                    <select class="form-control" id="fba_vs_fbm_after_le_symbol" name="fba_vs_fbm[after_le][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fba_vs_fbm_after_le_temp">
                                    <input class="form-control" id="fba_vs_fbm_after_le_value" type="number" step="0.01" min="0.01" name="fba_vs_fbm[after_le][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fba_vs_fbm_after_le_temp">
                                    <select class="form-control" id="fba_vs_fbm_after_le_math" name="fba_vs_fbm[after_le][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div>
                            <hr/>
                            <!-- FBA vs FBM end -->

                            <!-- FBM vs FBA -->
                            <div class="form-group">
                              <label for="" class="col-md-2 control-label"> FBM vs FBA: <br>
                              (当我的清单列表是FBM及竞争对手是FBA)</label> 
                              <div class="col-md-10">  
                                <p>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fbm_vs_fba" name="fbm_vs_fba" value="on"/>开启
                                  </label>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fbm_vs_fba" name="fbm_vs_fba" checked="checked" value="off"/>关闭
                                  </label>
                                  <input type="hidden"  class="fbm_vs_fba_is_open" name="fbm_vs_fba[is_open]" value='0'/>
                                </p>
                              </div> 
                            </div>

                            <div class="form-group fbm_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手高于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control symbol-change" id="fbm_vs_fba_gt_item" name="fbm_vs_fba[gt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id="fbm_vs_fba_gt_symbol" name="fbm_vs_fba[gt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2">
                                    <input class="form-control" id="fbm_vs_fba_gt_value" type="number" step="0.01" min="0.01" name="fbm_vs_fba[gt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id="fbm_vs_fba_gt_math" name="fbm_vs_fba[gt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fbm_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fbm_vs_fba[lt][options]" id="fbm_vs_fba_lt">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fbm_vs_fba_lt_temp">
                                    <select class="form-control symbol-change" id="fbm_vs_fba_lt_item" name="fbm_vs_fba[lt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fbm_vs_fba_lt_temp">
                                    <select class="form-control" id="fbm_vs_fba_lt_symbol" name="fbm_vs_fba[lt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fbm_vs_fba_lt_temp">
                                    <input class="form-control" id="fbm_vs_fba_lt_value" type="number" step="0.01" min="0.01" name="fbm_vs_fba[lt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fbm_vs_fba_lt_temp">
                                    <select class="form-control" id="fbm_vs_fba_lt_math"  name="fbm_vs_fba[lt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fbm_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手等于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fbm_vs_fba[eq][options]" id="fbm_vs_fba_eq">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fbm_vs_fba_eq_temp">
                                    <select class="form-control symbol-change" id="fbm_vs_fba_eq_item" name="fbm_vs_fba[eq][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fbm_vs_fba_eq_temp">
                                    <select class="form-control" id="fbm_vs_fba_eq_symbol" name="fbm_vs_fba[eq][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fbm_vs_fba_eq_temp">
                                    <input class="form-control" id="fbm_vs_fba_eq_value" type="number" step="0.01" min="0.01" name="fbm_vs_fba[eq][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fbm_vs_fba_eq_temp">
                                    <select class="form-control" id="fbm_vs_fba_eq_math" name="fbm_vs_fba[eq][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div> 
                            <div class="form-group fbm_vs_fba_temp"> 
                              <label for="" class="col-md-2 control-label"> 当调整后的价格等于或低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fbm_vs_fba[after_le][options]" id="fbm_vs_fba_after_le">
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fbm_vs_fba_after_le_temp">
                                    <select class="form-control symbol-change" id="fbm_vs_fba_after_le_item" name="fbm_vs_fba[after_le][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fbm_vs_fba_after_le_temp">
                                    <select class="form-control" id="fbm_vs_fba_after_le_symbol" name="fbm_vs_fba[after_le][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fbm_vs_fba_after_le_temp">
                                    <input class="form-control" id="fbm_vs_fba_after_le_value" type="number" step="0.01" min="0.01" name="fbm_vs_fba[after_le][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fbm_vs_fba_after_le_temp">
                                    <select class="form-control" id="fbm_vs_fba_after_le_math" name="fbm_vs_fba[after_le][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div>
                            <hr/>
                            <!-- FBM vs FBA end -->

                            <!-- FBM vs FBM -->
                            <div class="form-group">
                              <label for="" class="col-md-2 control-label"> FBM vs FBM: <br>
                              (当我的清单列表是FBM及竞争对手是FBM)</label> 
                              <div class="col-md-10">  
                                <p>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fbm_vs_fbm" name="fbm_vs_fbm" value="on"/>开启
                                  </label>
                                  <label class="radio-inline" style="margin-right: 50px">
                                    <input type="radio" class="fbm_vs_fbm" name="fbm_vs_fbm" checked="checked" value="off"/>关闭
                                  </label>
                                  <input type="hidden" class="fbm_vs_fbm_is_open" name="fbm_vs_fbm[is_open]" value='0'/>
                                </p>
                              </div> 
                            </div>

                            <div class="form-group fbm_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手高于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control symbol-change" id="fbm_vs_fbm_gt_item" name="fbm_vs_fbm[gt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id="fbm_vs_fbm_gt_symbol" name="fbm_vs_fbm[gt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2">
                                    <input class="form-control" id="fbm_vs_fbm_gt_value" type="number" step="0.01" min="0.01" name="fbm_vs_fbm[gt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1">
                                    <select class="form-control" id="fbm_vs_fbm_gt_math" name="fbm_vs_fbm[gt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fbm_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fbm_vs_fbm[lt][options]" id="fbm_vs_fbm_lt">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fbm_vs_fbm_lt_temp">
                                    <select class="form-control symbol-change" id="fbm_vs_fbm_lt_item" name="fbm_vs_fbm[lt][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fbm_vs_fbm_lt_temp">
                                    <select class="form-control" id="fbm_vs_fbm_lt_symbol" name="fbm_vs_fbm[lt][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fbm_vs_fbm_lt_temp">
                                    <input class="form-control" id="fbm_vs_fbm_lt_value" type="number" step="0.01" min="0.01" name="fbm_vs_fbm[lt][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fbm_vs_fbm_lt_temp">
                                    <select class="form-control" id="fbm_vs_fbm_lt_math" name="fbm_vs_fbm[lt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group fbm_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当竞争对手等于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fbm_vs_fbm[eq][options]" id="fbm_vs_fbm_eq">
                                      <option value="auto">使用自动竞争</option>
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="stop">不智能调价</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fbm_vs_fbm_eq_temp">
                                    <select class="form-control symbol-change" id="fbm_vs_fbm_eq_item" name="fbm_vs_fbm[eq][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fbm_vs_fbm_eq_temp">
                                    <select class="form-control" id="fbm_vs_fbm_eq_symbol" name="fbm_vs_fbm[eq][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fbm_vs_fbm_eq_temp">
                                    <input class="form-control" id="fbm_vs_fbm_eq_value" type="number" step="0.01" min="0.01" name="fbm_vs_fbm[eq][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fbm_vs_fbm_eq_temp">
                                    <select class="form-control" id="fbm_vs_fbm_eq_math" name="fbm_vs_fbm[lt][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div> 
                            <div class="form-group fbm_vs_fbm_temp"> 
                              <label for="" class="col-md-2 control-label"> 当调整后的价格等于或低于最小价格: </label> 
                              <div class="col-md-10"> 
                                <div class="row">
                                  <div class="col-md-3">
                                    <select class="form-control" name="fbm_vs_fbm[after_le][options]" id="fbm_vs_fbm_after_le">
                                      <option value="min">使用最小价格</option>
                                      <option value="max">使用最大价格</option>
                                      <option value="customize">自定您的价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-3 fbm_vs_fbm_after_le_temp">
                                    <select class="form-control symbol-change" id="fbm_vs_fbm_after_le_item" name="fbm_vs_fbm[after_le][item]">
                                      <option value="competitor">对手的竞争价格</option>
                                      <option value="min">最小价格</option>
                                      <option value="max">最大价格</option>
                                    </select>
                                  </div>
                                  <div class="col-md-1 fbm_vs_fbm_after_le_temp">
                                    <select class="form-control" id="fbm_vs_fbm_after_le_symbol" name="fbm_vs_fbm[after_le][symbol]">
                                      <option value="-">-</option>
                                      <option value="+">+</option>
                                    </select>
                                  </div>
                                  <div class="col-md-2 fbm_vs_fbm_after_le_temp">
                                    <input class="form-control" id="fbm_vs_fbm_after_le_value" type="number" step="0.01" min="0.01" name="fbm_vs_fbm[after_le][value]" value="0.01">
                                  </div>
                                  <div class="col-md-1 fbm_vs_fbm_after_le_temp">
                                    <select class="form-control" id="fbm_vs_fbm_after_le_math" name="fbm_vs_fbm[after_le][math]">
                                      <option value="$">$</option>
                                      <option value="%">%</option>
                                    </select>
                                  </div>
                                </div>
                              </div> 
                            </div>
                            <hr/>
                            <!-- FBM vs FBM end -->
                          </div>
                        </div>
                      </div>
                      <!-- 进阶设定 end -->

                      <!-- 自订设置 -->
                      <!-- <div class="form-group"> -->
                        <!-- <label for="" class="control-label"> </label>
                        <div class="col-md-12">
                          <div class="priority low">
                            <span>自订设置</span>
                          </div>
                          <div class="task low padding15">
                            <div class="form-group">
                              <div class="col-md-12">
                                <p class="form-control-static" style="color: #eb3c00">
                                  自订您要排除, 或只想进行竞争的竞争对手（这会限制您的竞争对手)。此设置取代[智能调价设定]及所有其他设置。
                                </p>
                              </div>
                            </div>

                            <div class="form-group"> 
                              <label for="" class="col-md-2 control-label"> 排除卖家反馈评级低于: </label> 
                              <div class="col-md-4"> 
                                <div class="row">
                                  <div class="col-md-5"> 
                                    <input class="form-control" type="number" step="0.01" min="0.01" required="" value="" />
                                  </div>
                                  <div class="col-md-1 control-label"> % </div>
                                </div>
                              </div> 
                            </div> 
                            <div class="form-group"> 
                              <label for="" class="col-md-2 control-label"> 排除卖家总反馈数量小于: </label> 
                              <div class="col-md-4"> 
                                <div class="row">
                                  <div class="col-md-5"> 
                                    <input class="form-control" type="number" step="0.01" min="0.01" required="" value="" />
                                  </div>
                                </div>
                              </div> 
                            </div> 
                          </div>
                        </div> -->
                      <!-- </div> -->
                      <!-- 自订设置 end -->

                    </div> 
                    <!-- 智能调价设定 end -->
                  </div>

                  <div class="row"> 
                    <div class="col-md-12 text-right"> 
                      <div class="btn-group" role="group"> 
                        <div class="btn btn-primary" data-loading-text="处理中..." id="save-btn" autocomplete="off"> 保存 </div> 
                      </div>
                    </div> 
                  </div> 
                </form> 
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
<script src="/js/bidding/rules.js"></script>
<script src="/js/bidding/bidding.js"></script>
<script src="/js/moment-with-locales.js"></script> 
<script src="/js/bootstrap-datetimepicker.js"></script>

 
</body>
</html>