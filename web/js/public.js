$(function () {

  var shopId = $('#shopId').val();
  var platformName = $('#platformName').val();
  var siteName = $('#siteName').val();
  var shopName = $('#shopName').val();
  var BRcount = $('#BRcount').val();

	//头部
	var head_xml="<div class='header'><div class='container-fluid'><div class='row'>"+
      "<div class='col-md-2'> <span class='logo'>  </div>"+
      "<div class='col-md-10'><div class='header-nav'><ul class='nav pull-right'>"+
            "<li> <a class='btn' href='/'> <i class='glyphicon glyphicon-home'></i> 首页 </a> </li>"+
            // "<li> <a class='btn' href='image.html'> <i class='glyphicon glyphicon-picture'></i> 图片 </a> </li>"+
            "<li class='dropdown'> <a class='btn dropdown-toggle' data-toggle='dropdown' href='#'> <i class='glyphicon glyphicon-user'></i> 个人中心 <span class='caret'></span> </a>"+
              "<ul class='dropdown-menu'><li><a href='/?r=user/index&shopId="+shopId+"'>用户信息</a></li><li> <li><a href='/index.php?r=site/logout' id='sign-out'>退出</a></li>"+
              "</ul></li></ul></div></div></div></div></div>";
    $("#headNav").html(head_xml)
    //底部信息
  var footer_xml="<div class='footer'><div class='container-fluid'><div class='row'>"+
      "<div class='col-md-4 col-sm-5'> 联系电话：4006-000-000 </div>"+
      "<div class='col-md-8 col-sm-7'>"+
      "<div style='float: right;'> <a href='#'>关于我们</a> <a href='#'>服务协议</a> <span>|</span> <a class='menu20' href='#' target='_blank'> 网站国际化平台 </a> </div>"+
      "</div></div></div></div>";
    $("#footerNav").html(footer_xml)

  //左边模块
  var modeLeft_xml="<div class='sidebar-nav'>"+
      "<div class='shop-info'>"+
        "<div class='name'> <i class='glyphicon glyphicon-home'></i> "+shopName+" </div>"+
        "<div class='text'> <span>"+platformName+"</span> <span>/</span> <span id='site-name'>"+siteName+"</span> </div>"+
        "<input type='hidden' value='"+shopId+"' id='shop-id'>"+
        "<div class='change'>"+
          "<button class='btn-link' data-toggle='modal' data-target='#myModal' id='change-shop'> [切换店铺] </button>"+
        "</div>"+
      "</div>"+
      "<ul>"+

        "<li class='normal'> <a href='/?r=product-online&shopId=" + shopId + "'> <i class='glyphicon glyphicon-barcode'></i> <span>在线产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=public-product&shopId=" + shopId + "&status=waiting'> <i class='glyphicon glyphicon-send'></i> <span>待发布产品</span> </a> </li>"+
        "<li class='active'> <a href='/?r=product/create-product&shopId=" + shopId + "'> <i class='glyphicon glyphicon-pencil'></i> <span>创建新商品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=monitor/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-screenshot'></i> <span>跟卖监控</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bad-review/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-thumbs-down'></i> <span>差评监控</span> <span id='BRWarning'></span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bidding&shopId=" + shopId + "'> <i class='glyphicon glyphicon-random'></i> <span>智能调价</span> </a> </li>"+
      "</ul>"+
    "</div>";
    $("#siderbarNav").html(modeLeft_xml)

    var modeLeft_xml="<div class='sidebar-nav'>"+
      "<div class='shop-info'>"+
        "<div class='name'> <i class='glyphicon glyphicon-home'></i> "+shopName+" </div>"+
        "<div class='text'> <span>"+platformName+"</span> <span>/</span> <span id='site-name'>"+siteName+"</span> </div>"+
        "<input type='hidden' value='" + shopId + "' id='shop-id'>"+
        "<div class='change'>"+
          "<button class='btn-link' data-toggle='modal' data-target='#myModal' id='change-shop'> [切换店铺] </button>"+
        "</div>"+
      "</div>"+
      "<ul>"+
        "<li class='normal'> <a href='/?r=product-online&shopId=" + shopId + "'> <i class='glyphicon glyphicon-barcode'></i> <span>在线产品</span> </a> </li>"+
        "<li class='active'> <a href='/?r=public-product&shopId=" + shopId + "&status=waiting'> <i class='glyphicon glyphicon-send'></i> <span>待发布产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=product/create-product&shopId=" + shopId + "'> <i class='glyphicon glyphicon-pencil'></i> <span>创建新商品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=monitor/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-screenshot'></i> <span>跟卖监控</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bad-review/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-thumbs-down'></i> <span>差评监控</span> <span id='BRWarning'></span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bidding&shopId=" + shopId + "'> <i class='glyphicon glyphicon-random'></i> <span>智能调价</span> </a> </li>"+
      "</ul>"+
    "</div>";
    $("#siderbarNav1").html(modeLeft_xml)

    var modeLeft_xml="<div class='sidebar-nav'>"+
      "<div class='shop-info'>"+
        "<div class='name'> <i class='glyphicon glyphicon-home'></i> "+shopName+" </div>"+
        "<div class='text'> <span>"+platformName+"</span> <span>/</span> <span id='site-name'>"+siteName+"</span> </div>"+
        "<input type='hidden' value='" + shopId + "' id='shop-id'>"+
        "<div class='change'>"+
          "<button class='btn-link' data-toggle='modal' data-target='#myModal' id='change-shop'> [切换店铺] </button>"+
        "</div>"+
      "</div>"+
      "<ul>"+

        "<li class='active'> <a href='/?r=product-online&shopId=" + shopId + "'> <i class='glyphicon glyphicon-barcode'></i> <span>在线产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=public-product&shopId=" + shopId + "&status=waiting'> <i class='glyphicon glyphicon-send'></i> <span>待发布产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=product/create-product&shopId=" + shopId + "'> <i class='glyphicon glyphicon-pencil'></i> <span>创建新商品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=monitor/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-screenshot'></i> <span>跟卖监控</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bad-review/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-thumbs-down'></i> <span>差评监控</span> <span id='BRWarning'></span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bidding&shopId=" + shopId + "'> <i class='glyphicon glyphicon-random'></i> <span>智能调价</span> </a> </li>"+
      "</ul>"+
    "</div>";
    $("#siderbarNav2").html(modeLeft_xml)

    var modeLeft_xml="<div class='sidebar-nav'>"+
      "<div class='shop-info'>"+
        "<div class='name'> <i class='glyphicon glyphicon-home'></i> "+shopName+" </div>"+
        "<div class='text'> <span>"+platformName+"</span> <span>/</span> <span id='site-name'>"+siteName+"</span> </div>"+
        "<input type='hidden' value=' " + shopId+ " ' id='shop-id'>"+
        "<div class='change'>"+
          "<button class='btn-link' data-toggle='modal' data-target='#myModal' id='change-shop'> [切换店铺] </button>"+
        "</div>"+
      "</div>"+
      "<ul>"+

        "<li class='normal'> <a href='/?r=product-online&shopId=" + shopId + "'> <i class='glyphicon glyphicon-barcode'></i> <span>在线产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=public-product&shopId=" + shopId + "&status=waiting'> <i class='glyphicon glyphicon-send'></i> <span>待发布产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=product/create-product&shopId=" + shopId + "'> <i class='glyphicon glyphicon-pencil'></i> <span>创建新商品</span> </a> </li>"+
        "<li class='active'> <a href='/?r=monitor/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-screenshot'></i> <span>跟卖监控</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bad-review/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-thumbs-down'></i> <span>差评监控</span> <span id='BRWarning'></span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bidding&shopId=" + shopId + "'> <i class='glyphicon glyphicon-random'></i> <span>智能调价</span> </a> </li>"+
      "</ul>"+
    "</div>";
    $("#siderbarNav3").html(modeLeft_xml)

    var modeLeft_xml="<div class='sidebar-nav'>"+
      "<div class='shop-info'>"+
        "<div class='name'> <i class='glyphicon glyphicon-home'></i> "+shopName+" </div>"+
        "<div class='text'> <span>"+platformName+"</span> <span>/</span> <span id='site-name'>"+siteName+"</span> </div>"+
        "<input type='hidden' value=' " + shopId+ " ' id='shop-id'>"+
        "<div class='change'>"+
          "<button class='btn-link' data-toggle='modal' data-target='#myModal' id='change-shop'> [切换店铺] </button>"+
        "</div>"+
      "</div>"+
      "<ul>"+

        "<li class='normal'> <a href='/?r=product-online&shopId=" + shopId + "'> <i class='glyphicon glyphicon-barcode'></i> <span>在线产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=public-product&shopId=" + shopId + "&status=waiting'> <i class='glyphicon glyphicon-send'></i> <span>待发布产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=product/create-product&shopId=" + shopId + "'> <i class='glyphicon glyphicon-pencil'></i> <span>创建新商品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=monitor/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-screenshot'></i> <span>跟卖监控</span> </a> </li>"+
        "<li class='active'> <a href='/?r=bad-review/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-thumbs-down'></i> <span>差评监控</span> <span id='BRWarning'></span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bidding&shopId=" + shopId + "'> <i class='glyphicon glyphicon-random'></i> <span>智能调价</span> </a> </li>"+
      "</ul>"+
    "</div>";
    $("#siderbarNav4").html(modeLeft_xml);

    var modeLeft_xml="<div class='sidebar-nav'>"+
      "<div class='shop-info'>"+
        "<div class='name'> <i class='glyphicon glyphicon-home'></i> "+shopName+" </div>"+
        "<div class='text'> <span>"+platformName+"</span> <span>/</span> <span id='site-name'>"+siteName+"</span> </div>"+
        "<input type='hidden' value=' " + shopId+ " ' id='shop-id'>"+
        "<div class='change'>"+
          "<button class='btn-link' data-toggle='modal' data-target='#myModal' id='change-shop'> [切换店铺] </button>"+
        "</div>"+
      "</div>"+
      "<ul>"+

        "<li class='normal'> <a href='/?r=product-online&shopId=" + shopId + "'> <i class='glyphicon glyphicon-barcode'></i> <span>在线产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=public-product&shopId=" + shopId + "&status=waiting'> <i class='glyphicon glyphicon-send'></i> <span>待发布产品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=product/create-product&shopId=" + shopId + "'> <i class='glyphicon glyphicon-pencil'></i> <span>创建新商品</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=monitor/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-screenshot'></i> <span>跟卖监控</span> </a> </li>"+
        "<li class='normal'> <a href='/?r=bad-review/list&shopId=" + shopId + "'> <i class='glyphicon glyphicon-thumbs-down'></i> <span>差评监控</span> <span id='BRWarning'></span> </a> </li>"+
        "<li class='active'> <a href='/?r=bidding&shopId=" + shopId + "'> <i class='glyphicon glyphicon-random'></i> <span>智能调价</span> </a> </li>"+
      "</ul>"+
    "</div>";
    $("#siderbarNav5").html(modeLeft_xml);

    var BRWarning = "<div style='width:24px;height:24px;border-radius:12px;background:#f44;color:white;font:SimHei;font-size:14px;line-height:24px;text-align:center;float:right;'>" + BRcount + "</div>";

    if(BRcount > 0){
      $("#BRWarning").html(BRWarning);
    }
})