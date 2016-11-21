/**
 * Created by yangguoli on 2016/2/29.
 */

$(function(){
    Inform.init();
    var $shopId = $('#shop-id').val();
    var is_all_check = false,
        count = 0,
        s_condition = "s-title";
    Inform.init();
    var Amazon = {
        init: function(){
            $(".product-content img").lazyload({
                 failurelimit: 3,
                 placeholder: "/static/image/grey.gif", //加载图片前的占位图片
                 effect: "fadeIn" //加载图片使用的效果(淡入)
            });
            $(".single-price-stock").click(Amazon.edit_var);
            $(".single-del").click(Amazon.del_pro);
            $(".single-base, .all-vars-base").click(Amazon.edit_base);
            $(".single-bidding").click(Amazon.add_bidding);
            $(".single-buybox").click(Amazon.render_buybox);
            $("html").on("click", ".update-var", Amazon.update_var);
            $(".product-table").on("click", "input[type=\"checkbox\"]", Amazon.check_pro);
            $(".all-check").find("input[type=\"checkbox\"]").on("click", Amazon.check_current_page);
            $(".more-choice").find("a").click(Amazon.check_all_pro);
            $("#search-options").find("a").click(Amazon.choose_option);
            $("#search-btn").click(Amazon.search_pro);
            $(".batch-op").click(Amazon.batch_operate);
            $(".operator-radio").click(Amazon.select_operator);
            $(".var-update").click(Amazon.update_var);
            $(".spread-btn").click(Amazon.spread_title);
            $(".batch-ensure-btn").click(Amazon.multi_ensure);
            $("#base-info-btn").click(Amazon.update_base);
            $("#start-sync").click(Amazon.sync_shop);
            Amazon.show_search_info();
        },
        // sync_shop: function(){
        //     var path = "/sync/shop",
        //         params = {};
        //     onlinePage.ajaxRequest(path, params, function(data){
        //         Inform.show(data.message)
        //     });
        // },
        check_title: function(){
            var flag = true;
            var title = $(".title-content tbody").find("tr");
            for(var i=0;i<title.length;i++){
                if(!$(title[i]).find("input").val()){
                    flag = false;
                    $(title[i]).find("input").css("border-color", "#FF0000");
                }else{
                    flag = true;
                    $(title[i]).find("input").css("border-color", "");
                }
            }
            return flag;
        },
        update_base: function(){
            var title = $(".title-content tbody").find("tr");
            var product_id = $(this).attr("data-id");
            if(!Amazon.check_title()){
                Inform.show("请将红框中的内容填写完整!");
                $(".spread-btn").text("收回");
                $(".title-content").show();
                return 0
            }else{
                var base_info = {};
                var bullet_points = [];
                var key_words = [];
                var category  = $(this).attr("data-type");
                var item_type = $("#ItemType").val().trim();
                var desc = $("#Description").val().trim();
                var bullet_object = $("#BulletPoint").find("input");
                var keys_object = $("#SearchTerms").find("input");
                for(i=0; i<keys_object.length; i++){
                    if($(keys_object[i]).val()){
                        key_words.push($(keys_object[i]).val());
                    }
                }
                base_info["KeyWords"] = key_words;
                for(j=0; j<bullet_object.length; j++){
                    if($(bullet_object[j]).val()){
                        bullet_points.push($(bullet_object[j]).val());
                    }
                }
                base_info["BulletPoints"] = bullet_points;
                var title_list = [];
                for(var i=0;i<title.length;i++){
                    title_list.push({"sku": $(title[i]).attr("data-sku"), "title": $(title[i]).find("input").val()})
                }
                base_info["Title"] = title_list;
                base_info["Description"] = desc;
                base_info["ItemType"] = item_type;
                var params = {"pid": product_id, "base": JSON.stringify(base_info), "category": category};
                onlinePage.ajaxRequest("?r=product-online/single-basic&shopId=" + $shopId, params, function(data){
                    if(data.status == 1){
                            $("#base-modal").modal("hide");
                            Inform.show(data.message);
                    }else{
                        Inform.show(data.message);
                    }
                });
           }
        },
        edit_var: function () {
            var tr_obj = $(this).closest("tr"),
                v_sku = tr_obj.find(".v-sku").attr("data-value").trim(),
                v_price = tr_obj.find(".v-price").attr("data-value").trim(),
                v_stock = tr_obj.find(".v-stock").attr("data-value").trim(),
                pid = $(this).closest("ul").attr("data-id").trim();
            $("#price-stock-modal").modal("show");
            $("#var-sku").val(v_sku);
            $("#var-stock").val(v_stock);
            $("#var-price").val(v_price);
            $("#feed_id").val(pid);
            $('.date-choose').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        },
        add_bidding: function () {
            var tr_obj = $(this).closest("tr"),
                sku_id = tr_obj.find(".single-bidding").attr("data-id").trim();

            onlinePage.ajaxRequest("/?r=product-online/add_bidding&shopId=" + $shopId, {"sku_id": sku_id}, function(data){
                if(data.status == 1){
                    Inform.show(data.message);
                    tr_obj.find(".single-bidding").parent().remove();
                }else{
                    Inform.show(data.message);
                }
            });
        },
        update_var: function(){
            var path = $(this).hasClass("price")?"?r=product-online/single-price&shopId="+$shopId:"?r=product-online/single-stock&shopId="+$shopId,
                pid = $("#feed_id").val().trim(),
                sku = $("#var-sku").val().trim(),
                stock = $("#var-stock").val().trim(),
                price = $("#var-price").val().trim(),
                sale = $("#var-sale").val().trim(),
                date_from = $("#var-sale-form").val().trim(),
                date_to = $("#var-sale-to").val().trim();
            if($(this).hasClass("price")){
                var params = {"pid": pid, "info": JSON.stringify({
                    "SKU": sku, "Price": price, "SalePrice": sale, "SaleDateFrom": date_from, "SaleDateTo": date_to
                })}
            }else{
                var params = {"pid": pid, "info": JSON.stringify({"SKU": sku, "Stock": stock})}
            }
            onlinePage.ajaxRequest(path, params, function(data){
                $("#price-stock-modal").modal("hide");
                Inform.show(data.message)
            });

        },
        // 单个商品checkbox点击事件
        check_pro: function(){
            var is_check = $(this).prop("checked"),
                all_check_div = $(".all-check"),
                all_checkbox = all_check_div.find("input[type=\"checkbox\"]"),
                check_status = true;
            if(is_check){
                $(".product-table").find("input[type=\"checkbox\"]").each(function(k, v){
                    $(v).prop("checked") || (check_status = false);
                })
            }else{
                check_status = false;
            }
            count = $(".product-table").find("input[type=\"checkbox\"]").filter(":checked").length;
            all_check_div.find(".important").each(function(k, v){
                var kv = $(v);
                kv.text(count);
            });
            all_checkbox.each(function(k, v){$(v).prop("checked", check_status);});
            Amazon.checkbox_change();
        },
        // 全选本页商品 click事件
        check_current_page: function(){
            var $dom = $(".more-choice"),
                all_check_div = $(".all-check"),
                is_check = $(this).prop("checked");
            // 全选后的遍历
            var check_execute = function(dom){
                var count = 0;
                dom.each(function(k, v){
                    var kv = $(v);
                    kv.prop("checked", is_check);
                    is_check && (count += 1)
                });
                return count
            };
            check_execute(all_check_div.find("input"));
            count = check_execute($(".product-table").find("input[type=\"checkbox\"]"));
            all_check_div.find(".important").each(function(k, v){
                var kv = $(v);
                kv.text(is_check ? count : "0");
            });
            Amazon.checkbox_change();
        },
        // 全选当前页checkbox状态改变
        checkbox_change: function(){
            var is_check = $(".all-check").find("input[type=\"checkbox\"]").eq(0).prop("checked"),
                $dom = $(".more-choice");
            $dom.css("height", is_check ? "30px" : "0");
            is_check && $(".already-select-sign").text("本页"+count);
            is_check && $dom.find("a").eq(0).show().siblings("a").hide();
            is_check || (is_all_check = false);
        },
        // 选择全部商品
        check_all_pro: function(){
            is_all_check = !is_all_check;
            var $dom = $(".more-choice"),
                all_check_div = $(".all-check");
            all_check_div.find(".important").each(function(k, v){
                var kv = $(v);
                kv.text(is_all_check ? $dom.find(".important").text() : count);
            });
            $dom.find("a").filter(":hidden").show().siblings("a").hide();
            $(".already-select-sign").text(is_all_check ? ("全部"+$dom.find(".important").text()) : ("本页"+count));
        },
        show_search_info: function(){
            var $search = location.search;
            var re = /^[ktp]\=[0-9a-zA-Z\%\_-]+/g,
                // re2 = /(?![hktp])[a-zA-Z]+\=[0-9a-zA-Z\%\_-]*/g;
                re2 = /([a-zA-Z_]{2,}|(?![hktp])[a-z]+)\=[0-9a-zA-Z\%\_-]*/g;
            if($search != ""){

                var search_arr = $search.split('&'),
                    search_text;
                for (x in search_arr) {
                    search_text = search_arr[x].match(re);
                    if (search_text) {
                        break;
                    }
                }

                if(search_text){
                    search_text = search_text.join("");
                    var str = '<div>{0}: {1}</div>',
                        $option = search_text[0],
                        $value = unescape(search_text.split("=")[1]);
                    if($option == "t"){
                        s_condition = "s-title";
                        $option = "标题";
                    }else if($option == "k"){
                         s_condition = "s-sku";
                        $option = "SKU";
                    }else if($option == "p"){
                         s_condition = "s-pid";
                        $option = "Asin";
                    }
                    str = str.format($option, $value);
                    var $href = location.pathname;
                    if($search.match(re2)){
                        $href = "?" + $search.match(re2).join("&");
                    }
                    $(".search-key").text($option);
                    $(".search-input").val($value);
                    $("#search-btn").after(' <a href="'+$href+'" class="btn btn-link" style="color: #eb3c00">去除搜索条件</a>');
                }
            }
        },
        // 选择搜索条件
        choose_option: function(){
            var $this = $(this);
            $(".search-key").text($this.text());
            s_condition = $this.attr("data-key");
        },
        // 搜索产品
        search_pro: function(){
            var s_value = $(".search-input").val().trim();
            if(s_value != ""){
                var param_str = "";
                var re = /[a-zA-Z_]+\=[0-9a-zA-Z\%\_-]*/g; // 非搜索条件和当前页的其他条件
                s_value = escape(s_value);
                if(s_condition=="s-title"){
                    param_str = "t=" + s_value;
                }else if(s_condition=="s-sku"){
                    param_str = "k="+s_value;
                }else if(s_condition=="s-pid"){
                    param_str = "p="+s_value;
                }
                var search_str = location.search;

                if(search_str == ""){
                    location.href = "?" + param_str;
                }else{
                    var new_str = search_str.match(re);
                    if (new_str) {
                        location.href = "?" + new_str.join("&") + "&" + param_str;
                    } else {
                        location.href = "?" + param_str;
                    }
                }
            }else{
                Inform.show("请输入搜索内容")
            }
        },
        // 弹出模态框
        batch_operate: function(){
            if(!Amazon.detect_check()){
                Inform.show("请至少勾选一件产品！");
                return false;
            }
            var $type = $(this).attr("id");
            var op_type = {
                "multi-price": $("#price-modal"),
                "multi-stock": $("#stoke-modal"),
                "multi-sale": $("#sale-modal"),
                "multi-offline": $("#offline-modal")
            };
            op_type[$type].find(".batch-ensure-btn").attr("data-name", $type);
            if($type == "multi-sale"){
                $('.date-choose').datetimepicker({
                    format: 'YYYY-MM-DD'
                })
            }
            op_type[$type].modal("show");
            op_type[$type].find(".oper-input").each(function(k, v){
                $(v).prop("disabled", true);
            });
            op_type[$type].find(".operator-radio").each(function(k, v){
                $(v).prop("checked", false);
            });
        },
        //运算符检测
        select_operator: function(){
            var $this = $(this);
            var $input = $this.closest(".form-group").find(".oper-input");
            $this.closest(".modal-body").find(".oper-input").each(function(k, v){$(v).prop("disabled", true)});
            $input.prop("disabled", false);
        },
        // 检查是否选中产品
        detect_check: function(){
            var num = 0;
            $(".product-table").find("input[type=\"checkbox\"]").each(function(k, v){
                $(v).prop("checked") && (num += 1);
            });
            return num;
        },
        render_buybox: function(){
            var tr_obj = $(this).closest("tr");
            var sku = tr_obj.find(".v-sku").attr("data-value").trim();
            var feed_id = $(this).closest("ul").attr("data-id").trim();
            var price_list = ["BPrice", "FBMPrice", "FBAPrice"];
            onlinePage.ajaxRequest("/?r=product-online/single-lowest-price&shopId=" + $shopId, {"sku": sku, "pid": feed_id}, function(data){
                if(data.status == 1){
                    var price = data["price"];
                    for(var i=0;i<price_list.length;i++){
                        var temp = tr_obj.find("." + price_list[i]);
                        temp.text(price[price_list[i]]["Price"]);
                        temp.parent().find(".ship_price").text(price[price_list[i]]["Ship"]);
                    }
                    Inform.show(data.message);
                }else{
                    Inform.show(data.message);
                }
            });
        },
        edit_base: function(){
            var feed_id = $(this).hasClass("single-base")?$(this).closest("ul").attr("data-id"):$(this).attr("data-id");
            var sku = $(this).closest("tr").find(".v-sku").attr("data-value").trim();
            var category = $(this).hasClass("single-base")?"single":"all";
            var params = {"pid": feed_id, "category": category, "sku": sku};
            onlinePage.ajaxRequest("/?r=product-online/single-feed&shopId=" + $shopId, params, function(data){
                if(data["status"]){
                        var table = $("<table/>").addClass("table table-bordered table-striped");
                        var t_list = ["<thead><tr><td class=\"pro-20\">SKU</th><td class=\"pro-80\">Title</th></thead>"];
                        var tbody = $("<tbody/>");
                        var info = data["json"]["title"];
                        for(var i=0;i<info.length;i++){
                            tbody.append("<tr data-sku=\"{0}\"><td style=\"vertical-align:inherit\">{1}</td><td><input class=\"form-control title\" value='{2}'></input></td></tr>".format(
                                info[i]["SKU"], info[i]["SKU"], info[i]["Title"]
                            ));
                        }
                        t_list.push(tbody.html());
                        table.append(t_list.join(""));
                        $(".title-content").html(table);
                        var bullet_point = data["json"]["bullets"];
                        var key_words = data["json"]["tags"];
                        var bullet_object = $("#BulletPoint").find(".form-control");
                        var key_object = $("#SearchTerms").find(".form-control");
                        $("#Description").val(data["json"]["description"]);
                        $("#ItemType").val(data["json"]["category"]);
                        for(var i=0; i<=bullet_object.length; i++){
                            $(bullet_object[i]).val(bullet_point[i]);
                        }
                        for(var i=0; i<=key_object.length; i++){
                            $(key_object[i]).val(key_words[i]);
                        }
                        $("#base-info-btn").attr({"data-id":feed_id, "data-type": category});
                        $("#base-modal").modal("show");
                    }else{
                        Inform.show(data.message);
                        // console.log("请求出错!")
                    }
            });
        },
        del_pro: function(){
            var $modal = $("#single-del-modal");
            $modal.find(".single-ensure-btn").attr("data-id", $(this).closest("ul").attr("data-id"));
            $modal.modal("show");
        },
        spread_title: function(){
            var $this = $(this);
            $this.siblings(".title-content").toggle("show",function(){
                $this.text($this.siblings(".title-content").is(":visible") ? "收回" : "展开");
            });
        },
        check_title: function(){
            var flag = true;
            var title = $(".title-content tbody").find("tr");
            for(var i=0;i<title.length;i++){
                if(!$(title[i]).find("input").val()){
                    flag = false;
                    $(title[i]).find("input").css("border-color", "#FF0000");
                }else{
                    flag = true;
                    $(title[i]).find("input").css("border-color", "");
                }
            }
            return flag;
        },
        check_sale: function(data){
            var flag = true,
                sale_price = data["value"],
                pattern = data["pattern"],
                from = data["sale_from"],
                to = data["sale_to"];
            if(sale_price&&pattern){
                if(!from||!to){
                    var span_ = "<span style=\"margin-left: 50px;color:red\">设置促销信息:需要将价格和促销日期一起设置,请将信息补充完整!</span>";
                    $("#sale-date").closest(".form-group").after(span_);
                    flag = false;
                }
            }
            return flag;
        },
        multi_ensure: function(){
            var $this = $(this),
                $modal = $(".modal-body").filter(":visible"),
                $verb = $this.attr("data-name");
            var $data = {}, ids = [], con = "{}";
            if(is_all_check){
                con = $("#con-value").val() || "{}";
            }else{
                $(".product-table").find(":checked").each(function(k, v){
                    ids.push($(v).val());
                })
            }
            $data.ids = JSON.stringify(ids);
            $data.con = con;
            if($verb =="multi-price" || $verb =="multi-stock"){
                $data["value"] = $modal.find(".oper-input").not(":disabled").val().trim();
                $data["pattern"] = $modal.find(".operator-radio").filter(":checked").attr("data-name");
            }else if($verb =="multi-sale"){
                $data["value"] = $modal.find(".oper-input").not(":disabled").val().trim();
                $data["pattern"] = $modal.find(".operator-radio").filter(":checked").attr("data-name");
                $data["sale_from"] = $("#bath-sale-form").val().trim();
                $data["sale_to"] = $("#bath-sale-to").val().trim();
                if(!Amazon.check_sale($data)){
                    return
                }
            }else{
                Inform.show("错误的请求,请重试!");
                return
            }
            // 请求完刷新页面 记得在回调里写
            $this.button("loading");

            // $verb = $verb.replace(/-/g,"/");
            $verb = $verb.substring($verb.indexOf('-') + 1);
            var $verb = '/?r=product-online/multi-modify&shopId=' + $shopId + '&type=' + $verb;

            onlinePage.ajaxRequest($verb, $data, function(data){
                $this.button("reset");
                $this.closest(".modal").modal("hide");
                Inform.show(data.message);
                Inform.enable(location.href)
            }, function(){Inform.show("未知错误")}, function(){$this.button("reset");$this.closest(".modal").modal("hide");}, true);
        }
    };
    Amazon.init();
});