/**
 * Created by Administrator on 2014/12/9.
 */

$(function(){
    var CreateShop = {
        site: "",
        site_name: "",
        platform: "",
        shop_name: "",
        timestamp: "",
        init: function(){
            CreateShop.start_auth = $("#start-auth");
            CreateShop.site_area = $(".site-area");
            CreateShop.platform_list = $("#platform_list").find("li");
            CreateShop.platform_list.click(CreateShop.get_site);
            CreateShop.site_selector = $(".site");
            CreateShop.site_selector.click(CreateShop.choose_site);
            CreateShop.site_list = $("#site_list");
            CreateShop.platform = "Amazon";
            $("#shop_name").on("input",CreateShop.shop_name_bind);//店铺名输入
            $("#add-authorize").click(CreateShop.add_click);//开始授权点击事件
            $("#begin-authorize").click(CreateShop.set_listen);
            $("#auth-failed").click(CreateShop.reload);
            $("#auth-success").click(CreateShop.get_token);
            var notice_list = $("#notice-list").find("a");
            notice_list.click(function(){
                var no_id = $(this).attr("data-id");
                $("ul[data-id="+no_id+"]").slideToggle("fast").closest("li").siblings().find("ul").slideUp("fast");
            });
        },
        get_site : function(){
             CreateShop.platform = $(this).attr("data-name");
             $(this).addClass("active").siblings().removeAttr("class");
             if(CreateShop.platform == "Ensogo"){CreateShop.set_listen();return 0}
             $("#tag-body").css({"display":"none"});
             $("#loading-icon").css({"position":"relative","left":"50%","margin-top":"20px", "margin-left": "-16px"});
             $("#loading").css({"position":"relative"});
             Loading.show();
             $("#begin-authorize").attr("class", "btn btn-primary disabled");
             $.ajax({
                  url: "/?r=auth/site",
                  type: "POST",
                  data: "platform="+CreateShop.platform,
                  dataType: "json",
                  success: function(data){
                      if(data.success){
                          $("#loading").css({"display":"none","opacity": 0});
                          $("#tag-body").css({"display":"block"});
                          CreateShop.site_list.html("");
                          if(CreateShop.platform == "Joom"){
                              CreateShop.site_list.html("" +
                                  "<div style=\"color: #eb3c00\">特别注意：" +
                                  "此平台目前处于测试时期，请不要自行上传产品，如需了解可以与商务人员联系</div>"
                              )
                          }
                          var sites = data["sites"];
                          var site;
                          for(var i=0; i<sites.length; i++){
                               site = sites[i];
                               $("<a/>").attr({"class":"site", "href": "javascript:void(0)", "data-id": site.id})
                               .html(site.name).appendTo(CreateShop.site_list).className = "site";
                          }
                          CreateShop.site_selector = $(".site");
                          CreateShop.site_selector.click(CreateShop.choose_site);
                      }else{
                           alert(data.msg);
                      }
                  }
            })

        },
        choose_site: function(event){
            $(event.target).addClass("on");
            $(event.target).siblings().removeAttr("class");
            CreateShop.site = $(event.target).attr("data-id");
            CreateShop.site_name = $(event.target).html();
            if(CreateShop.check_shop_name(CreateShop.shop_name)){
                CreateShop.timestamp = new Date().getTime();
                $("#begin-authorize").attr("class","btn btn-primary")
                    .attr("href","/?r=auth/send&na="+CreateShop.shop_name
                        +"&sp="+CreateShop.site
                        +"&pa="+CreateShop.platform
                        +"&ts="+CreateShop.timestamp
                    ).attr("target","_blank");
            }
        },
        reload:function(){
            location.reload();
        },
        shop_name_bind : function(){
            var focus_num = $("#site_list").children(".on").length;
            CreateShop.shop_name = $("#shop_name_text").val().trim();
            if(CreateShop.check_shop_name(CreateShop.shop_name) && focus_num>0){
                CreateShop.timestamp = new Date().getTime();
                $("#begin-authorize").attr("class","btn btn-primary")
                    .attr("href","/?r=auth/send&na="+CreateShop.shop_name
                    +"&sp="+CreateShop.site
                    +"&pa="+CreateShop.platform
                    +"&ts="+CreateShop.timestamp
                ).attr("target","_blank");
            }
        },
        get_token: function(){
            if(CreateShop.platform == "Amazon"){
                var seller_id = $("#seller-id").val();
                var access_id = $("#access-id").val();
                var secret_key = $("#secret-key").val();
                if(!seller_id&&access_id&&secret_key){
                    return alert("请将带*的数据补充完整！");
                }
                if(!/^[A-Z0-9]{11,16}/.test(seller_id)){
                    return alert("请输入正确的卖家编号！");
                }else if(!/^[A-Z0-9]{20}$/.test(access_id)){
                    return alert("请输入正确的AWS Access Key！");
                }else if(!/^[\S]{40}$/.test(secret_key)){
                    return alert("请输入正确的密钥！");
                }
                seller_id = seller_id + "-" + access_id + "-" + secret_key;
                seller_id = seller_id.replace(/\+/g,"%2B");
                seller_id = seller_id.replace(/\&/g,"%26");
            }
            if(CreateShop.platform == "Lazada"){
                var email = $("#email").val().trim();
                var api_key = $("#api-key").val().trim();
                if(!email&&api_key){
                    return alert("请将带*的数据补充完整！");
                }
                if(!/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/.test(email)){
                    return alert("请输入正确的邮箱地址");
                }
                if(!/^\w{32}/.test(api_key)){
                    return alert("请输入正确的API Key");
                }
                seller_id = email + "#" + api_key;
            }
            $("#layer").css({"display": "none"});
            $(this).button("loading");
            $.ajax({
                url: "/?r=auth/token",
                type: "POST",
                data: "na="+ CreateShop.shop_name + "&session_id=" + $("#session-id").val()
                + "&platform="+CreateShop.platform + "&sp="+CreateShop.site + "&seller_id=" + seller_id,
                dataType: "json",
                success: function(data) {
                    if(data["success"]) {
                        $(this).button("reset");
                        $("#authorizing_model").find(".modal-body").html("授权成功");
                        location.href="/?r=public-product&shopId=" + data["shop_id"] + "&status=waiting";
                    }else{
                        $("#authorizing_model").find(".modal-body").html("授权失败<br/>" + data.message);
                        $("#auth-success").remove();
                    }
                }
            });
        },
        check_shop_name:function(shop_name){
            if (shop_name.length<=16&&shop_name.length>=4){
                if(shop_name.match(/^[A-Za-z0-9]+$/)!=null){
                     $(".tips").html("");
                    return true;
                }else if(shop_name.match(/[\s]/)!=null){
                    $("#begin-authorize").attr("class","btn btn-primary disabled");
                    $(".tips").html("店铺名不能包含空格");
                    return false;
                }else if(shop_name.match(/[\u4E00-\u9FA5]/)!=null){
                    $("#begin-authorize").attr("class","btn btn-primary disabled");
                    $(".tips").html("店铺名不能包含中文字符");
                    return false;
                }else if(shop_name.match(/[A-Za-z0-9]/)!=null){
                    $("#begin-authorize").attr("class","btn btn-primary disabled");
                    $(".tips").html("店铺名不能包含特殊字符");
                    return false;
                }else{
                    $("#begin-authorize").attr("class","btn btn-primary disabled");
                    $(".tips").html("店铺名由数字、字母构成,4-16个字符之间");
                    return false;
                }
            }else if(shop_name.length==0){
                $("#begin-authorize").attr("class","btn btn-primary disabled");
                $(".tips").html("请输入店铺名");
                return false;
            }else if(shop_name.length<4){
                $("#begin-authorize").attr("class","btn btn-primary disabled");
                $(".tips").html("店铺名不能少于4个字符");
                return false;
            }else if(shop_name.length>16){
                $("#begin-authorize").attr("class","btn btn-primary disabled");
                $(".tips").html("店铺名不能超过16个字符");
                return false;
            }else{
                $("#begin-authorize").attr("class","btn btn-primary disabled");
                $(".tips").html("店铺名由数字、字母构成,4-16个字符之间");
                return false;
            }
        },
        add_click:function(){
            $("#authorizing_model").modal('hide');
            $("#myModal").modal('show');
        },
        set_listen: function(){
            $(".external-area").css({"display": "none"});
            $("#myModal").modal('hide');
            var url = "#" + CreateShop.platform;
            $("#auth-help").attr("href",url).text(CreateShop.platform+"授权帮助");
            $("#authorizing_model").modal('show');
            // console.log(CreateShop);
            if(CreateShop.platform == "Amazon"){
                $("#amazon-mtl").css({"display": "block"});
                $("#auth-success").css({"display": ""});
            }else if(CreateShop.platform == "Lazada"){
                $("#lazada-mtl").css({"display": "block"});
                $("#auth-success").css({"display": ""});
            }else{
                $("#general-mtl").css({"display": "block"});
                $("#auth-success").css({"display": "none"});
            }
            var t = setInterval(function(){
                // console.log("listening");
                $.ajax({
                    url: "/?r=auth/listen",
                    data: {platform: CreateShop.platform, key: CreateShop.timestamp},
                    type: "POST",
                    dataType: "json",
                    success: function(data){
                        if(data["status"] == "1"){
                            clearInterval(t);
                            CreateShop.reload();
                        }else if(data["status"] == "-1"){
                            clearInterval(t);
                            clearTimeout(to);
                            $("#general-mtl").text("授权失败,请重试").css({"display": "block"});
                        }
                    }
                })
            },10000);
            var to = setTimeout(function(){
                clearInterval(t);
                $("#general-mtl").text("授权超时,请重试").css({"display": "block"});
            },120000)
        }
    };
    CreateShop.init();
});