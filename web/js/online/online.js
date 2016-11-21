/**
 * Created by xuhe on 16/1/19.
 */

var onlinePage = (function($, _){
    var $shopId = $('#shop-id').val();
    Checkbox.init("", ".product-table input[type='checkbox']", "");
    $("#start-sync").click(function(){
        Inform.show("正在同步...");
        Inform.disable();
        ajaxRequest("/?r=product-online/sync-shop&shopId=" + $shopId, {}, function(data){
            if(data.status == 1){
                Inform.show(data.message);
                console.log(location.href);
                Inform.enable(location.href);
            }else{
                Inform.enable();
                Inform.show("同步失败，请稍后再试");
            }
        },function(data){
            Inform.enable();
            Inform.show(data.message);
        });
    });
    $("#change-shop").click(function(){
        $.ajax({
            "type": "POST",
            "url": "/shop/"+$("#shop-id").val()+"/change",
            "dataType": "json",
            "success": function(data){
                if(data.status==1) {
                    render_modal(data["shops"]);
                }
            },
            "error": function(){
                //location.href = "/"
            }
        });
    });
    $(".page-skip-btn").click(function(){
        var to_page = $(".page-skip-input").val().trim(),
            page_pattern = /^\d+$/g;
        if(to_page){
            if(page_pattern.test(to_page)){
                var link = location.search;
                to_page = parseInt(to_page);
                if(to_page>0){
                    if(link){
                        var re = /[^(page)][a-zA-Z_]+\=[0-9a-zA-Z\%\_-]*/g;
                        var $search = link.match(re);
                        if($search.length != 0){
                            link = location.pathname + $search.join("") + "&page=" + to_page;
                            // console.log(link);return;
                        }else{
                            link = location.pathname + "?page=" + to_page;
                        }
                    }else{
                        link = location.pathname + "?page=" + to_page;
                    }
                    location.href = link;
                }
            }
        }
    });
    function render_modal(shops){
        var html_str = "";
        for(var i=0;i<shops.length;i++){
            var shop = shops[i];
            html_str += "<div class=\"col-md-4\">";
            if(shop.platform != "Amazon"){
                html_str += "<a href=\"/online/" + shop.shop_id +"/selling\"";
            }else{
                html_str += "<a href=\"/product/" + shop.shop_id +"/waiting\"";
            }
            html_str += " class=\"thumbnail-btn";
            html_str += " p-" + shop.platform.toLowerCase() + "\"";
            html_str += ">"
            + "<span>店铺：" + shop.name + "</span><br/>"
            + "<span>平台：" + shop.platform + "</span><br/>"
            + "<span>站点：" + shop.site_name + "</span>"
            + "</a></div>"
        }
        $("#shops").html(html_str);
    };
    var baseConfig = {
        shopId: $("#shop-id").val(),
        platform: $(".shop-info").find(".text").text().split("/")[0].trim(),
        inform: Inform
    };
    var pageConfig = {
        ent: 50,
        cPg: 1,
        cId: 0,
        gId: 0,
        q: ""
    };
    var ajaxConfig = {
        type: "POST",
        dataType: "json"
    };
    var setPageConfigToCookie = function(){
        $()
    };
    var renderFunc;
    var http_status_views = {
        "400": {"msg": "该请求无效(400)"},
        "403": {"msg": "请求被拒绝(403)"},
        "404": {"msg": "请求未找到(404)"},
        "401": {"msg": "登录信息失效(401)"},
        "500": {"msg": "服务器异常(500)"},
        "504": {"msg": "请求超时(504)"}
    };
    var ajaxRequest = function (verb, data, successCal, errorCal, completeCal, isAsync) {
        if(isAsync == null){
            isAsync = true
        }
        var req_body = {
            async: isAsync,
            url: verb,
            data: data,
            success: function(data){
                if(data != null){
                    if(data.status == 1){
                        successCal(data);
                    }else if(data.action){
                        location.href = data.action
                    }else{
                        Inform.enable();
                        Inform.show(data.message);
                    }
                }
            },
            error: function(data, XMLHttpRequest){
                if(http_status_views[data.status]){
                    Inform.show(http_status_views[data.status].msg);
                }else{
                    errorCal(data);
                }
            },
            complete: completeCal
        };
        // console.log(req_body);
        req_body = _.extend(req_body, ajaxConfig);
        $.ajax(req_body);
    };
    var t_shop_id = 0,
        t_shop_name = "",
        sl = "",
        tl = "",
        st_rate = "";
    var $data = {};
    var part_move = {
        move_option: function(){
            Checkbox.get_data(function (product_ids) {
                if (product_ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
                $.ajax({
                    "type": "POST",
                    "url": "other/shop",
                    "data": {},
                    "dataType": "json",
                    "success": function (data) {
                        var shops = data["shops"];
                        var html_str = "";
                        for (var i = 0; i < shops.length; i++) {
                            var shop = shops[i];
                            if(shop.platform == baseConfig.platform&&shop.shop_id != baseConfig.shop_id){
                                html_str += "<div class=\"col-md-3\">"
                                    + "<a href=\"javascript: void(0)\" class=\"thumbnail"
                                    + " shop\" data-id=" + shop.shop_id + ">"
                                    + "<span class=\"shop-name\"><i class=\"glyphicon glyphicon-home\">"
                                    + "</i>" + shop.name + "</span><br/><span class=\"shop-data\">"
                                    + "<i class=\"glyphicon glyphicon-shopping-cart\"></i>" + shop.platform
                                    + " / " + shop.site_name + "</span></a></div>";
                            }
                        }
                        $("#move-shops").html(html_str).find(".shop").click(part_move.choose_shop);
                        $("#move-shop-modal").modal("show");
                    }
                })
            });
        },
        choose_shop: function(){
            var $this = $(this),
                btn = $("#move-btn");
            t_shop_id = $this.attr("data-id");
            t_shop_name = $this.text();
            $this.addClass("on").parent().siblings().find(".shop").removeClass("on");
            t_shop_id?btn.prop("disabled", false):btn.prop("disabled", true);
        },
        ensure_shop: function(name){
            var ids = [], con = "{}";
            var more_choice = $(".more-choice");
            if(more_choice.css("height") == "30px" && more_choice.find("a").eq(1).is(":visible")){
                con = $("#con-value").val() || "{}";
            }else{
                $(".product-table").find(":checked").each(function(k, v){
                    ids.push($(v).val());
                })
            }
            $data.ids = JSON.stringify(ids);
            $data.con = con;
            $data.tid = t_shop_id;
            $("#move-shop-modal").modal("hide");
            Inform.disable();
            Inform.show("", true, "正在检测商品信息...");
            $.ajax({
                url:"multi/move/check",
                type:"post",
                dataType:"json",
                data:$data,
                success:function(data){
                    var num = data.repeat_num,
                        reg = data.site_reg;
                    $("#repeat-content").hide();
                    $("#trans-info").show();
                    part_move.get_info();
                    Inform.hide();
                    if(reg==1 && num==0){
                        part_move.trans_items();
                    }else{
                        num&&$("#num-text").text("有"+num+"个商品已存在于目标店铺["+t_shop_name.split("/")[0]+"]").closest("#repeat-content").show();
                        reg==1&&$("#trans-info").hide()&&$("#repeat-content").attr("style","border-bottom: 0");
                        $("#trans-modal").modal("show");
                    }

                }
            })
        },
        get_info: function(){
            $.ajax({
                url: "move/info",
                type: "post",
                dataType: "json",
                data:{
                    "tid": t_shop_id
                },
                success: function(data){
                    if(data){
                        sl = data.lan;
                        tl = data.t_lan;
                        $("#t-cur").val(data.t_currency);
                        $(".t-cur-text").text(data.t_currency);
                        $("#o-cur").val(data.currency);
                        $(".o-cur-text").text(data.currency);
                        $("#o-lan").val(sl);
                        $("#t-lan").val(tl);
                        part_move.get_rate(data.currency,data.t_currency);
                    }
                }
            })
        },
        get_rate: function(cur, t_cur){
            $.ajax({
                url: "/api/rate/rate?sc="+cur+"&tc="+t_cur,
                success: function(data){
                    st_rate = data.sc_rate;
                    $(".rate").val((data.sc_rate/100).toFixed(4));
                    $("#rate-text").text(data.sc_rate);
                }
            })
        },
        trans_items: function(){
            var trans_control = {
                    "SL": sl,
                    "TL": tl,
                    "st_rate": st_rate,
                    "Specifics": $(".tr-spec").prop("checked"),
                    "Description": $(".tr-desc").prop("checked"),
                    "Title": $(".tr-title").prop("checked")
                };
            $("#trans-modal").modal("hide");
            Inform.disable();
            Inform.show("", true, "正在提交商品信息...");
            // console.log(trans_control);
            $.ajax({
                url: "multi/move/start",
                type: "post",
                dataType: "json",
                data: {
                    "con" : $data.con,
                    "ids": $data.ids,
                    "tid": $data.tid,
                    "repeat_type": $("#repeat-content").is(":visible")?$("[name='repeat-type']:checked").val():"",
                    "trans_control": JSON.stringify(trans_control)
                },
                success: function(data){
                    if(data.status){
                        var url = "/product/" + t_shop_id + "/waiting";
                        Inform.enable();
                        Inform.show( "共有"+data.success+"个商品转移到目标店铺<a href="+url+">[待发布]</a>");
                    }else{
                        Inform.enable();
                        Inform.show(data.message);
                    }
                }
            })
        }
    };
    $("#multi-move").click(part_move.move_option);
    $("#move-btn").click(part_move.ensure_shop);
    $("#trans-btn").click(part_move.trans_items);
    $(".check-supply-link").click(function(){
        var pro_id = $(this).closest("ul").attr("data-id"),
            $link = "",
            $ipt = $("#supply-link-ipt"),
            $btn = $("#submit-supply-link"),
            link_div = $(".source-link-div"),
            link_a = $(".md-source-link"),
            loading_icon = $(".supply-loading"),
            edit_btn = $("#edit-supply-link");
        $ipt.val("");
        loading_icon.show();
        $(".link-tip").hide();
        $ipt.hide();
        link_div.hide();
        edit_btn.hide();
        $("#close-link-modal").show();
        $("#return-link-div").hide();
        $btn.attr("data-id", pro_id);
        $.ajax({
            url: "supply/get",
            data: {
                pid: pro_id
            },
            type: "post",
            success: function(data){
                loading_icon.hide();
                if(data.status == 1){
                    $link = data.json.link;
                }
                if($link == ""){
                    $ipt.show();
                    $btn.show();
                }else{
                    $ipt.hide();
                    $btn.hide();
                    edit_btn.show();
                    link_a.attr("href", $link).text($link);
                    link_div.show();
                }
            },
            complete: function(){
                loading_icon.hide();
            }
        });
    });
    $("#edit-supply-link").click(function(){
        $(this).hide();
        $("#return-link-div").show();
        $("#close-link-modal").hide();
        $("#supply-link-ipt").show();
        $("#submit-supply-link").show();
        $(".source-link-div").hide();
    });
    $("#return-link-div").click(function(){
        $(this).hide();
        $("#return-link-div").hide();
        $("#close-link-modal").show();
        $("#supply-link-ipt").hide();
        $("#submit-supply-link").hide();
        $(".source-link-div").show();
        $("#edit-supply-link").show();
        $(".link-tip").hide();
    });
    $("#supply-link-ipt").on("input propertychange",function(){$(".link-tip").hide();});
    $("#submit-supply-link").click(function(){
        var $ipt = $("#supply-link-ipt"),
            $link = $ipt.val(),
            $btn = $(this),
            re = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
            is_valid = re.test($link),
            link_div = $(".source-link-div"),
            loading_icon = $(".supply-loading"),
            tip = $(".link-tip");
        if($link && is_valid){
            $("#supply-link-modal").modal("hide");
            link_div.hide();
            loading_icon.show();
            $ipt.hide();
            tip.hide();
            Inform.disable();
            Inform.show("",true,"正在修改...");
            $.ajax({
                url: "supply/set",
                data: {
                    pid: $btn.attr("data-id"),
                    link: $link
                },
                type: "post",
                success: function(data){
                    if(data.status == 1){
                        Inform.enable();
                        Inform.show("货源链接设置成功");
                    }else{
                        Inform.enable();
                        Inform.show("设置失败，请稍后重试");
                    }
                },
                error: function(){
                    Inform.enable();
                    Inform.show("设置失败，请稍后重试");
                }
            })
        }else{
            tip.show();
        }
    });
    return {
        nextPage: function () {
            pageConfig.cPage < pageConfig.pages && (pageConfig.cPage += 1);
            renderFunc();
        },
        prevPage: function () {
            pageConfig.cPage > 1 && (pageConfig.cPage -= 1);
            renderFunc();
        },
        changeEntry: function (entry) {
            entry == 50 || entry == 100 || entry == 200 && (pageConfig.entry = entry);
            renderFunc();
        },
        ajaxRequest: ajaxRequest,
        loading: function(loadingText){
            Inform.disable();
            Inform.show("", true, loadingText);
        },
        showInform: function(content){
            Inform.enable();
            Inform.show(content);
        },
        hideInform: function(){
            Inform.hide();
        },
        setRenderFunc: function(func){
            renderFunc = func;
        }
    }
})($, _);