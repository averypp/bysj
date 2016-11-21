/**
 * Created by xuhe on 15/5/23.
 */
$(function(){
    Inform.init();
    Checkbox.init(".all-select", ".sel-pro", "feed-id");
    var target_shop_ids = [],
        c_level = 0,
        category_id = $("#category-id").val(),
        all_delete = $("#all-delete"),
        all_upload = $("#all-upload"),
        all_trans = $("#all-trans"),
        all_edit = $("#all-edit"),
        all_check = $("#all-check"),
        shop_id = $("#shop-id").val(),
        product_ids = [],
        is_all = false,
        // status = $("#status").val().split(";"),
        status = $("#status").val(),
        condition = {"status": status},
        off_reason = "",
        pro_info = {},
        interval_id,
        set_cate_index,
        category_uid,
        s_option = "Title",
        group_dict = {},
        search_feeds = [];
    var Product = {
        group_selectable : true,
        init : function(){
            $("html").on("click",".upload-pro",Product.upload_click);
            all_delete.click(Product.delete_select);
            all_upload.click(Product.upload_select);
            all_edit.click(Product.edit_select);
            all_trans.click(Product.show_trans_modal);
            all_check.click(Product.check_items);
            $("#del-link").click(Product.del_link);
            $("#del-subject").click(Product.del_subject);
            $("#claim-ready").click(Product.claim_feed);
            $("#sync-products").click(Product.sync_products);
            $("html").on("click", ".all-select, .sel-pro",Product.detect_count);
            $("html").on("click", ".end-pro",Product.end_item);
            $("html").on("click", ".use-pro",Product.use_item);
            $("html").on("click", ".check-fees",Product.check_fees);
            $("#sel-group").click(Product.select_group);
            $("#group-ensure").click(Product.ensure_click);
            $("html").on("click", "#all-select", Product.all_click);
            $("#trans-control").click(Product.trans_words);
            $("#set-cate").click(Product.begin_set);
            $("#search-options").find("a").click(Product.choose_option);
            $("#search-btn").click(Product.start_search);
            $(".online-edit-p,.online-edit-s").click(Product.online_edit_p);
            $("#price-modal").on("change","input",Product.set_dirty);
            $("#stock-modal").on("change","input",Product.set_dirty);
            $(".category").find("li").click(Product.choose_category);
            $("#choose-category").click(Product.ch_category);
            $("#price-btn").click(Product.price_sub);
            $("#stock-btn").click(Product.stock_sub);
            $("html").on("click", ".del-group", Product.del_group);
            $(window).scroll(Product.scroll_listener);
            $(".to-top").click(function(e){
                e.preventDefault();
                $("body,html").animate({scrollTop:0},300);
            })
        },
        del_group: function(){
            var cate_id = $(this).attr("data-id"),
                this_div = $(this).closest("div");
            $.ajax({
                "url": "/product/" + shop_id + "/del/group",
                "data": {"cate_id": cate_id},
                "type": "POST",
                "dataType": "json",
                "success": function(data){
                    Product.del_group_div(this_div);
                    Inform.show(data.message);
                }
            })
        },
        del_group_div: function(dom){
            var pid = dom.remove().attr("data-pid"),
                categories = $("#group-modal").find(".category"),
                flag = categories.filter("[data-pid="+pid+"]").length,
                p_div = categories.filter("[data-id="+pid+"]");
            if(!flag){
                Product.del_group_div(p_div);
            }
        },
        start_search: function(){
            var search_input = $("#search-input");
            var search_content = search_input.val().trim() || "";
            if(!search_content){
                return 0;
            }
            var condition = {
                "status": status,
                "content": search_content,
                "option": s_option
            };
            $.ajax({
                "url": "/?r=public-product/search&shopId=" + shop_id,
                "data": {"condition": JSON.stringify(condition)},
                "type": "POST",
                "dataType": "json",
                "success": function(data){
                    if(data.success){
                        search_feeds = data.feeds;
                        var tb = $(".table"),
                            th = tb.find("tr").eq(0).clone(),
                            len = search_feeds.length;
                        var t = "<p>共搜索到<span style=\"padding: 4px;color: red;font-size: 16px;\">"+len+"</span>件商品</p>";
                        if(len>200){
                            t = "<p>共搜索到<span style=\"padding: 4px;color: red;font-size: 16px;\">"+len+"</span>件商品,"
                                +"商品数量过多,建议缩小搜索范围</p>";
                        }
                        tb.empty().prev("p").remove().end().append(th).before(t);
                        $(".footer-stat").remove();
                        if($(".render-more").length == 0){
                            var more_btn = $("<a/>").attr({
                                "class": "btn render-more"
                            }).text("加载更多商品").click(Product.render_products);
                            tb.after(more_btn);
                        }
                        Product.render_products();
                        $("#all-select").hide();
                    }
                }
            })
        },
        render_products: function(){
            var render_feeds = search_feeds.splice(0,200);
            for(var i=0; i<render_feeds.length; i++){
                $(".table").append(Product.create_tr(render_feeds[i],"3"));
            }
            $(".all-select").unbind();
            Checkbox.init(".all-select", ".sel-pro", "feed-id");
            search_feeds.length==0&&$(".render-more").unbind().text("没有更多了");
        },
        create_tr: function(feed, status){
            var option_dict = {
                "3": {
                    parts: ["check", "img", "title", "price", "time", "option" ],
                    btns: [{
                        class: "btn btn-info",
                        text: "编辑商品",
                        href: "/?r=product/edit-product&shopId="+shop_id + "&goodId=" + feed.Id
                    },{
                        class: "btn btn-success upload-pro",
                        text: "上传商品",
                        href: "javascript: void(0)"
                    }]}
            };
            var parts = option_dict[status].parts,
                tr = $("<tr/>");
            var td_check = $("<td/>").append($("<input/>").attr({
                    type: "checkbox",
                    class: "sel-pro",
                    "data-feed-id": feed.Id
                })),
                td_img = $("<td/>").append($("<img/>").attr({
                    class: "gallery",
                    src: feed.GalleryURL
                })),
                td_title = $("<td/>").append($("<a/>").attr({
                    class: "btn-link",
                    href: feed.Link,
                    target: "_blank"
                }).text(feed.Title)),
                td_price = $("<td/>").text(feed.StartPrice),
                td_stock = $("<td/>").text(feed.Quantity),
                td_time = $("<td/>").append(feed.UpdateTime),
                td_option = $("<td/>").attr("class","operate-pro"),
                btns = option_dict[status]["btns"]
            ;
            var part_dict = {
                "check": td_check,
                "img": td_img,
                "title": td_title,
                "price": td_price,
                "stock": td_stock,
                "time": td_time,
                "option": td_option
            };
            for(var i=0;i<btns.length;i++){
                td_option.append($("<a/>").attr({
                    class: btns[i].class,
                    href: btns[i].href,
                    "data-id": feed.Id
                }).text(btns[i].text)).append("<br/>")
            }
            for(var j=0;j<parts.length;j++){
                tr.append(part_dict[parts[j]])
            }
            return tr;
        },
        del_link: function(){
            if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
                if (condition.Ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
            }
            Inform.disable();
            Inform.show("", true, "正在删除链接...");
            $.ajax({
                url : "/product/"+shop_id+"/del/link",
                type :"post",
                dataType : "json",
                data:{
                    "condition" : JSON.stringify(condition)
                },
                success : function(data){
                    if (data.status == 1){
                        Inform.enable(location.pathname);
                        console.log(data);
                        Inform.show(data.message);
                    }else{
                        Inform.enable(location.pathname);
                        Inform.show(data.message);
                    }
                }
            })
        },
        del_subject: function(){
           if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
                if (condition.Ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
            }
            Inform.disable();
            Inform.show("", true, "正在删除...");
            $.ajax({
                url : "/product/"+shop_id+"/del/subject",
                type :"post",
                dataType : "json",
                data:{
                    "condition" : JSON.stringify(condition)
                },
                success : function(data){
                    if (data.status == 1){
                        Inform.enable(location.pathname);
                        console.log(data);
                        Inform.show(data.message);
                    }else{
                        Inform.enable(location.pathname);
                        Inform.show(data.message);
                    }
                }
            })
        },
        upload_click: function(){
            product_ids = [];
            product_ids.push($(this).attr("data-id"));
            Product.get_condition(product_ids);
            $("#choose-temp-verify").hide();
            $("#choose-temp").show();
            Product.upload_options("/?r=public-product/upload&shopId="+shop_id);
        },
        check_fees: function(){
            product_ids = [];
            product_ids.push($(this).attr("data-id"));
            Product.get_condition(product_ids);
            $("#choose-temp-verify").show();
            $("#choose-temp").hide();
            Product.upload_options("/?r=public-product/upload&shopId="+shop_id);
        },
        render_ebay_group: function (group, content, level) {
            for(var i=0; i<group.length; i++){
                var category = group[i],
                    childcategory = category.childGroup;
                if(childcategory&&level<2){
                    var op_group = $("<optgroup/>").attr("label" ,category.groupName).appendTo(content);
                    Product.render_ebay_group(childcategory, op_group);
                }else{
                    childcategory&&(group_dict[category.groupId]=childcategory);
                    $("<option/>").attr("value", category.groupId).text(category.groupName).appendTo(content);
                }
            }
        },
        choose_option: function(){
            $(".search-key").text($(this).text());
            s_option = $(this).attr("data-key");
        },
        upload_options: function (url){
            Inform.disable();
            Inform.show("", true, "正在检测并上传商品...");
            $.ajax({
                url:url,
                type:"post",
                dataType:"json",
                data:{
                    "condition" : JSON.stringify(condition)
                },
                success:function(data){
                    if (url.indexOf("verify") != -1){
                        if(data.status==1){
                            var fees = data.fees[0]["Fee"],
                                html = "<table class=\"table\"><tr><th>名称</th><th>价格</th><th>货币</th>";
                            for(var i = 0;i < fees.length; i++){
                                var fee = fees[i];
                                if(fee["Fee"].value != 0){
                                    html += "<tr><td>{0}</td><td>{1}</td><td>{2}</td></tr>".format(fee.Name,
                                        fee["Fee"].value, fee["Fee"]["_currencyID"])
                                }
                            }
                            html += "</table>";
                            Inform.enable();
                            Inform.show(html);
                        }else{
                            var errors = data.message,
                                html = "";
                            for(var i = 0;i<errors.length; i++){
                                var error = errors[i],
                                    num = i+1;
                                html += "{0}:{1};<br>".format('['+num+']',error["LongMessage"])
                            }
                            Inform.enable();
                            Inform.show(html);
                        }
                    }else{
                        if(data.status==1){
                            var upc_error_n = data["upc_error"].length;
                            var upc_str = upc_error_n == 0 ? "" : "<br/>有" + upc_error_n + "个商品因UPC或者商品编码不够无法上传";
                            var error_n = data["error_pid"].length;
                            var error_str = error_n == 0 ? "" : "<br/>有" + error_n + "个商品未通过上传检测，请修改";
                            var success_n = data["success_pid"].length;
                            var success_str = success_n == 0 ? "": "<br/>已成功将" + data["success_pid"].length + "个商品加入上传队列";
                            Inform.enable(window.location.href);
                            Inform.show("共提交了" + data["total"] + "个商品" + upc_str + error_str + success_str);
                            // Inform.show("共提交了" + data["total"] + "个商品" /*+ error_str + success_str*/);
                        }else{
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    }

                }
            })
        },
        option_value_change: function(){
            if ($(this).find(":selected").attr("value")){
                $(this).next().remove();
            }
        },
        detect_count: function(e){
            var count = Checkbox.get_length();
            if(count > 0){
                all_delete.prop("disabled", false);
                all_upload.prop("disabled", false);
                all_trans.prop("disabled", false);
                all_edit.prop("disabled", false);
            }else{
                all_delete.prop("disabled", true);
                all_upload.prop("disabled", true);
                all_trans.prop("disabled", true);
                all_edit.prop("disabled", true);
            }
            is_all = false;
            $("#product-count").text(count);
            e.stopPropagation();
        },
        end_item: function(){
            product_ids = [];
            product_ids.push($(this).attr("data-id"));
            Product.get_condition(product_ids);
            if( $(".shop-info").find(".text").text().split("/")[0].trim() == "eBay" ){
                $("#reason-modal").modal("show");
            }else{
                Product.end_option();
            }
        },
        use_item: function(){
            var product_id = $(this).attr("data-id");
            location.href = "/create/" + shop_id + "/edit?product_id=" + product_id+"&type=use";
        },
        end_product: function(){
            off_reason = $("#reason-modal").find("select").val();
            Product.end_option();
        },
        end_option: function(){
            $.ajax({
                url:"/product/"+shop_id+"/offline",
                type:"post",
                dataType:"json",
                data:{
                    "condition" : JSON.stringify(condition),
                    "reason": off_reason
                },
                success:function(data){
                    Inform.show("", true, "正在下架...");
                    if(data.status=="1"){
                        Inform.enable("/product/"+shop_id+"/offline");
                        Inform.show(data.message);
                    }else{
                        Inform.enable("/product/"+shop_id+"/offline");
                        Inform.show("下架失败");
                    }
                }
            })
        },
        delete_select: function(){
            if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
                if (condition.Ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
            }
            if (!confirm("是否删除选中商品")){
                return
            }
            Inform.disable();
            Inform.show("", true, "正在删除...");

            $.ajax({
                url : "/?r=public-product/delete&shopId="+shop_id,
                type :"post",
                dataType : "json",
                data:{
                    "condition" : JSON.stringify(condition)
                },
                success : function(data){
                    if (data.status == 1){
                        Inform.enable(window.location.href);
                        // console.log(data);
                        Inform.show(data.message);
                    }else{
                        Inform.enable(window.location.href);
                        Inform.show(data.message);
                    }
                }
            })
        },
        upload_select: function(){
            if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
                if (condition.Ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
            }
            Product.upload_options("/?r=public-product/upload&shopId="+shop_id);
        },
        edit_select: function(){
            var category_name = $('div[class="group"]').find('span').text();
            if (Checkbox.get_list().length == 0) {
                Inform.show("请至少选择一件商品!");
                return 0;
            }else if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
            }
            var productIds = [];
            $(".sel-pro:checked").each(function(n,v){
                productIds.push($(v).attr("data-feed-id"));
            });
            var condition = {};
            if(is_all){
                condition["productIds"] = [];
            }else{
                condition["productIds"] = productIds;
            }
            condition["category_name"] = category_name;
            condition["categoryId"] = category_id;
            condition["isAll"] = is_all;
            condition["status"] = status;
            $("#batch-condition").val(JSON.stringify(condition));
            $("#batch-form").submit();
        },
        select_group: function(){
            $("#group-modal").modal("show");
            if(Product.group_selectable){
                $.ajax({
                    "url": "/?r=public-product/group-select&shopId=" + shop_id,
                    "type": "POST",
                    "data": {
                        "status": JSON.stringify(status)
                    },
                    "dataType": "json",
                    "success": function(data){
                        Product.group_selectable = false;
                        if(data["group_list"].length == 0){
                            $(".group-detail").html("您的商品未设置分组");
                        }else{
                            $(".group-detail").html("");
                            Product.render_group(data["group_list"], 0);
                        }
                    },
                    "error": function(){
                    }
                })
            }
        },
        render_group: function(group_list, p_tag){
            var html;
            var gl = Product.search_group(group_list, p_tag);
            for(var i=0; i < gl.length; i++){
                if(!gl[i]["is_leaf"]){
                    html = "<div class=\"category\" "+
                        "style=\"margin-left: "+(gl[i]["level"]-1)*26+
                        "px\" data-leaf ="+gl[i]["is_leaf"]+" data-id="+
                        gl[i]["cid"]+" data-pid="+gl[i]["pid"]+">"+gl[i]["name"]+"</div>";
                    $(".group-detail").append(html);
                    Product.render_group(group_list, gl[i]["cid"]);
                }else{
                    html = "<div class=\"category\" "+
                        "style=\"margin-left: "+(gl[i]["level"]-1)*26+
                        "px;\" data-leaf ="+gl[i]["is_leaf"]+" data-id="+gl[i]["cid"]+" data-pid="+gl[i]["pid"]+">"+
                        "<a href="+location.pathname+"?category_id="+gl[i]["cid"]+">"+
                        gl[i]["name"]+"("+gl[i]["count"]+")</a>";
                    if(gl[i]["all_count"]=="0"){
                        html +="<span class=\"del-group\" data-id="+gl[i]["cid"]
                        +" data-count="+gl[i]["all_count"]+">删除</span>";
                    }
                    html += "</div>";
                    $(".group-detail").append(html);
                }
            }
        },
        search_group: function(group_list, p_tag){
            var gl = [];
            for(var j=0; j<group_list.length; j++){
                if(group_list[j]["pid"]==p_tag){
                    gl.push(group_list[j]);
                }
            }
            return gl;
        },
        ensure_click: function(){
            $("#group-modal").modal("hide");
            var cate_id = $("a[class=\"on\"]").closest("div").attr("data-id");
            var url = location.pathname+"?category_id="+cate_id;
            location.replace(url);
        },
        all_click: function(){
            $(".sel-pro").prop("checked", true);
            var count = Checkbox.get_length();
            if(count > 0){
                all_delete.prop("disabled", false);
                all_upload.prop("disabled", false);
                all_trans.prop("disabled", false);
                all_edit.prop("disabled", false);
            }else{
                all_delete.prop("disabled", true);
                all_upload.prop("disabled", true);
                all_trans.prop("disabled", true);
                all_edit.prop("disabled", false);
            }
            $("#product-count").text($(this).attr("data-count"));
            is_all = true;
        },
        sync_products: function(){
            $.ajax({
                "type": "POST",
                "url": "/product/" + $("#shop-id").val() + "/sync",
                "dataType": "json",
                "success": function (data) {
                    if(data.status == 1){
                        $("#to-sync").remove();
                        $("#sync-ing").css({"display": "block"});
                        setInterval((function(){
                            var n = 0;
                            var dot = $(".loading-txt");
                            function run(){
                                var txt = "商品正在同步";
                                n = (n + 1) % 4;
                                for(var i=0; i<n; i++){
                                    txt += " .";
                                }
                                dot.html(txt);
                            }
                            return run
                        })(), 500);
                        interval_id = setInterval(Product.view_status, 5000);
                    }else{
                        Inform.show(data.message);
                    }
                },
                "error": function(){
                    Inform.show("系统出错，请联系管理员");
                }
            })
        },
        claim_feed: function(){
            $.ajax({
                "type": "POST",
                "url": "/product/" + $("#shop-id").val() + "/other",
                "data": {},
                "dataType": "json",
                "success": function(data){
                    var shops = data["shops"];
                    var html_str = "";
                    for(var i=0;i<shops.length;i++){
                        var shop = shops[i];
                        html_str += "<div class=\"col-md-4\">"
                        + "<a href=\"javascript: void(0)\" class=\"thumbnail"
                        + " shop\" data-id=" + shop.shop_id + ">"
                        + "<span>店铺：" + shop.name + "</span><br/>"
                        + "<span>平台：" + shop.platform + "</span><br/>"
                        + "<span>站点：" + shop.site_name + "</span>"
                        + "</a></div>";
                    }
                    $("#claim-shops").html(html_str).find(".shop").click(Product.choose_shop);
                    $("#claim-btn").click(Product.claim_product);
                    $("#end-btn").click(Product.end_product);
                }
            })
        },
        choose_shop: function(){
            var shop_id = $(this).css({
                "background": "#337ab7",
                "color": "#fff"
            }).attr("data-id");
            for(var i=0;i<target_shop_ids.length;i++){
                if(target_shop_ids[i] == shop_id){
                    return 0;
                }
            }
            target_shop_ids.push(shop_id);
        },
        claim_product: function(){
            $(this).button("loading");
            Checkbox.get_data(function(feed_ids) {
                if (feed_ids.length == 0) {
                    $("#alert").html("请至少选中一件商品").css({"display": "block"});
                    return;
                }
                $.ajax({
                    "type": "POST",
                    "url": "/product/" + $("#shop-id").val() + "/claim",
                    "data": {
                        "feed_ids": JSON.stringify(feed_ids),
                        "target_shop_ids": JSON.stringify(target_shop_ids)
                    },
                    "dataType": "json",
                    "success": function (data) {
                        $("#claim-btn").button("reset");
                        if(data.status == 1){
                            $("#claim-modal").modal("hide");
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                        $("#claim-btn").button("reset");
                        $("#claim-modal").find(".modal-body").html("请求出错");
                    }
                })
            });
        },
        view_status: function(){
            $.ajax({
                "type": "POST",
                "url": "/product/" + $("#shop-id").val() + "/check",
                "dataType": "json",
                "success": function (data) {
                    if(data.status == 1){
                        clearInterval(interval_id);
                        var sync = $("#sync-progress").find(".progress-bar");
                        if(sync){
                            sync.css({"width": "100%"});
                            location.reload();
                        }
                    }
                    if(data.status == 0){
                        var progress = data.count/10;
                        progress = progress > 90 ? 90 : progress;
                        $("#sync-progress").find(".progress-bar").css({"width": progress + "%"});
                    }
                    if(data.status == -1){
                        Inform.show("同步失败，请稍候重试");
                        clearInterval(interval_id);
                    }
                },
                "error": function(){
                    $(".sync-products").html("请求出错");
                }
            })
        },
        get_condition: function(obj){
            if($.isArray(obj)){
                delete condition["CategoryUID"];
                condition["Ids"] = obj
            }else{
                delete condition["Ids"];
                condition["CategoryUID"] = parseInt(obj);
            }
        },
        show_trans_modal: function(){
            if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
                if (condition.Ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
            }
            $("#trans-control-modal").modal("show");
        },
        trans_words: function() {
            var trans_title = $(".tr-title").prop("checked");
            var trans_desc = $(".tr-desc").prop("checked");
            var trans_spec = $(".tr-spec").prop("checked") || false;
            var trans_key = $(".tr-key").prop("checked") || false;
            var trans_point = $(".tr-point").prop("checked") || false;
            var src_lang = $("#src-lang").val() || "";
            var tar_lang = $("#tar-lang").val() || "";
            if (!src_lang) {
                $("#src-lang").css("border-color", "red");
                alert("请选择源语言");
                return false
            }
            if (!tar_lang){
                $("#tar-lang").css("border-color", "red");
                alert("请选择目标语言");
                return false
            }
            Inform.disable();
            Inform.show("", true, "正在提交请求...");
            $(this).button("loading");
            $("#trans-control-modal").modal("hide");
            var request_body = {
                "Title": trans_title,
                "Description": trans_desc,
                "Specifics": trans_spec,
                "KeyWords": trans_key,
                "BulletPoints": trans_point,
                "condition": JSON.stringify(condition),
                "src_lang": src_lang,
                "tar_lang": tar_lang
            };
            $.ajax({
                "url": "/?r=public-product/translate&shopId=" + shop_id,
                "type": "POST",
                "dataType": "json",
                "data": request_body,
                "success": function (data) {
                    $(this).button("reset");
                    if (data.status == 1) {
                        Inform.enable("/?r=public-product&shopId=" + shop_id + "&status=dealing");
                        Inform.show("翻译请求已提交");
                    } else {
                        $(this).button("reset");
                        Inform.enable();
                        Inform.show("翻译请求被拒绝<br/>" + data.message);
                    }
                },
                "error": function () {
                    $(this).button("reset");
                    console.log("there is some error happened");
                }
            })
        },
        scroll_listener: function(){
            var to_top = $(window).scrollTop();
            var to_bottom = $(window).scrollTop()+$(window).height()-document.body.clientHeight;
            if(to_top>$(window).height()*0.5){
                $(".to-top").fadeIn(300);
            }else{
                $(".to-top").fadeOut(300);
            }
        },
        online_edit_p: function(){
            var $this = $(this);
            var p_id = $this.attr("data-id");
            if(pro_info[p_id]){
                $this.hasClass("online-edit-p")? Product.edit_show(pro_info[p_id],"p"):Product.edit_show(pro_info[p_id],"s");
            }else{
                $.ajax({
                    url: "/product/" + shop_id + "/simple",
                    type: "post",
                    data: { product_id: p_id },
                    success: function(data){
                        if(data["status"]){
                            pro_info[p_id] = data["info"];
                            $this.hasClass("online-edit-p")? Product.edit_show(data["info"],"p"):Product.edit_show(data["info"],"s");
                        }
                    }
                })
            }
        },
        edit_show: function(p_info,prop){
            var table = $("<table/>").attr("class","table table-striped table-bordered");
            var t_head = ["<tr>{0}<th>图片</th><th style='min-width:200px'>sku/标题</th>"];
            prop == "p"? t_head.push("<th>价格</th></tr>"):t_head.push("<th>库存</th></tr>");
            t_head = t_head.join("");
            var t_body = "";
            var t_spec_head = "";
            if(p_info["skus"].length>0){
                var v_spec = p_info["skus"][0]["VariationSpecifics"];
                t_spec_head += Product.get_sku_head(v_spec);
                t_body += Product.get_sku_body(p_info["skus"],p_info["Id"],p_info["i_id"],prop);
            }else{
                t_body = ["<tr data-sku='"+p_info["sku"]+"' data-p-id="+p_info["Id"]+" data-i-id="+p_info["i_id"]+"><td>{0}</td><td>{1}</td>"];
                if(prop == "p"){
                    t_body.push("<td><input type='text' class='form-control v-price' value={2}></td></tr>");
                    t_body = t_body.join("").format("<img src="+p_info["pic"][0]+">",p_info["title"],p_info["price"])
                }else{
                    t_body.push("<td><input type='text' class='form-control v-stock' value={2}></td></tr>");
                    t_body = t_body.join("").format("<img src="+p_info["pic"][0]+">",p_info["title"],p_info["stock"])
                }
            }
            t_head = t_head.format(t_spec_head);
            table.append(t_head).append(t_body);
            if(prop == "p"){
                $("#price-modal").find(".pro-detail").html(table).modal("show");
            }else{
                $("#stock-modal").find(".pro-detail").html(table).modal("show");
            }
        },
        get_sku_head: function(spec){
            var str = "";
            for(var i=0,l=spec.length;i<l;i++){
                str += "<th>"+spec[i]["Name"]+"</th>";
            }
            return str
        },
        get_sku_body: function(skus, p_id, i_id,prop){
            var str = [];
            for(var i=0;i<skus.length;i++){
                var $sku = skus[i];
                var tr_str = ["<tr data-sku='"+$sku["SKU"]+"' data-p-id="+p_id+" data-i-id="+i_id+">{0}<td><img src='"+$sku["PictureURL"]+"'></td><td>"+$sku["SKU"]+"</td>"],
                    p_str = "<td><input type='text' class='form-control v-price' value='"+$sku["Price"]+"'></td></tr>",
                    s_str = "<td><input type='text' class='form-control v-stock' value='"+$sku["Stock"]+"'></td></tr>";
                prop == "p"? tr_str.push(p_str):tr_str.push(s_str);
                var tr_str_add = "";
                var spec = $sku["VariationSpecifics"];
                for(var j=0;j<spec.length;j++){
                    tr_str_add += "<td>"+spec[j]["Value"]+"</td>";
                }
                tr_str = tr_str.join("").format(tr_str_add);
                str.push(tr_str);
            }
            return str.join("")
        },
        set_dirty: function(){
            $(this).closest("tr").addClass("dirty");
        },
        price_sub: function(){
            var price_info = [];
            var url = "/product/" + shop_id + "/edit/price";
            $("#price-modal").find(".dirty").each(function(){
                var $this = $(this);
                price_info.push({
                    ItemID: $this.attr("data-i-id"),
                    SKU: $this.attr("data-sku"),
                    StartPrice: $this.find(".v-price").val().trim()
                })
            });
            console.log(price_info);
            if(price_info.length){
                $.ajax({
                    url: url,
                    type: "post",
                    data:{
                        "price_info" : JSON.stringify(price_info)
                    },
                    success: function(data){
                    }
                })
            }
        },
        stock_sub: function(){
            var stock_info = [];
            var url = "/product/" + shop_id + "/edit/stock";
            $("#stock-modal").find(".dirty").each(function(){
                var $this = $(this);
                stock_info.push({
                    ItemID: $this.attr("data-i-id"),
                    SKU: $this.attr("data-sku"),
                    Quantity: $this.find(".v-stock").val().trim()
                })
            });
            console.log(stock_info);
            if(stock_info.length){
                $.ajax({
                    url: url,
                    type: "post",
                    data:{
                        "stock_info" : JSON.stringify(stock_info)
                    },
                    success: function(data){
                    }
                })
            }
        },
        begin_set: function(){
            $(".operation-bar,.table,.footer-stat").hide();
            $.ajax({
                url: "/?r=public-product/no-category&shopId="+shop_id,
                type: "post",
                dataType: "json",
                success: function(data){
                    if(data.status){
                        var row  = ["<div style='margin: 10px 0'>共有<span class='set-cate-num'>",data.result.length,
                                "</span>个分类需要手动配置<a class='f-right' id='return-list' href='javascript:void(0)'>",
                                    "返回商品列表</a><div/>"].join("");
                        $("#set-cate-con").empty().append(row);
                        $("#return-list").click(Product.return_list);
                        Product.render_cate(data.result);
                    }
                }
            });
            $("#set-cate-con").show();
        },
        return_list: function(){
            $(".operation-bar,.table,.footer-stat").show();
            $("#set-cate-con").hide();
        },
        render_cate: function(list){
            var content = $("#set-cate-con");
            for(var i=0;i<list.length;i++){
                var table= $("<table/>").attr("data-total",list[i]["total"]),
                    tr_1= "<tr><td>原目录:</td><td class='or-direct' data-id="+list[i]["Category.ID"]+" >{0}</td></td>".format(list[i]["Category.Name"].join(">")),
                    tr_2 = "<tr><td>目标目录:</td><td class='tar-direct'></td></td>";
                table.append(tr_1).append(tr_2);
                var btn_div = $("<div/>").addClass("row"),
                    btn_1 = $("<a/>").addClass("btn btn-default save-cate").text("保存").click(Product.save_cate),
                    btn_2 = $("<a/>").addClass("btn btn-default ch-cate")
                            .text("选择目录").attr("data-index",i).click(Product.begin_ch_cate);
                btn_div.append(btn_1).append(btn_2);
                $("<div/>").addClass("well").append(table).append(btn_div).appendTo(content)
            }
        },
        begin_ch_cate: function(){
            var $this = $(this);
            set_cate_index = $this.attr("data-index");
            $("#category-tree").modal("show");
        },
        choose_category: function(){
            var cate = $(this);
            var is_leaf = cate.attr("data-leaf") == "1";
            var level = cate.attr("data-level");
            var name = cate.find("a").text();
            var html_str = "";
            var pop_times, temp_level;
            level = parseInt(level);
            if(is_leaf){
                category_uid = cate.attr("data-id");
                if(c_level < level){
                }else{
                    pop_times = c_level - level + 1;
                    temp_level = level;
                    while(pop_times > 0){
                        temp_level += 1;
                        $(".category[data-level=" + temp_level + "]").remove();
                        pop_times -= 1;
                    }
                }
                c_level = level;
                cate.attr("class", "chosen");
                cate.siblings("li").attr("class", "");
                $("#choose-category").removeAttr("disabled");
            }else{
                $("#choose-category").attr("disabled","disabled");
                if(!cate.hasClass("chosen")){
                    if(c_level < level){
                    }else{
                        pop_times = c_level - level + 1;
                        temp_level = level;
                        while(pop_times > 0){
                            temp_level += 1;
                            $(".category[data-level=" + temp_level + "]").remove();
                            pop_times -= 1;
                        }
                    }
                    c_level = level;
                    var category_dom = $("<ul/>").attr({
                        "class": "category loading-cate",
                        "data-level": level + 1
                    }).appendTo(".category-area");
                    cate.attr("class", "chosen");
                    cate.siblings("li").attr("class", "");
                    $.ajax({
                        url: "/?r=api/category-get",
                        type: "GET",
                        data: {
                            shop_id: shop_id,
                            parent_id: cate.attr("data-id")
                        },
                        dataType: "json",
                        success: function(data) {
                            if(data["categories"].length > 0){
                                html_str = render_category(data["categories"]);
                                category_dom.html(html_str).removeClass("loading-cate");
                                category_dom.find("li").click(Product.choose_category);
                                Product.search_keyup();
                            }
                        },
                        error: function(){
                            /**
                            alert("请求出错");
                            **/
                            Inform.show("请求出错");
                        }
                    });
                }
            }
            function render_category(categories){
                var shop_name=$(".shop-info").find(".text").find("span")[0].innerText;
                if(shop_name == "AliExpress"){
                    var html_str = "<div class='form-group search-div'><input type='text' class='cate-search form-control'"
                            +" placeholder='请输入名称/拼音首字母'><span class='glyphicon glyphicon-search form-control-feedback'></span></div>";
                    for(var i=0;i<categories.length;i++){
                        var category = categories[i];
                        var class_name = category["leaf"] == 0 ? "has-leaf" : "no-leaf";
                            html_str += "<li class=\"" + class_name +"\" "
                                + "data-id=\"" + category["id"] + "\""
                                + "data-level=\"" + category["level"] + "\""
                                + "data-leaf=\"" + category["leaf"] + "\""
                                + "data-tag=\"" + category["tag"] + "\""
                                + "data-query=\"" + category["query"] + "\""
                                + "data-cn=\""+ category["name"] +"\""
                                + "data-en=\""+ category["pin"] +"\">"
                                + "<a href=\"javascript: void(0)\">"
                                + category["name"] +"</a></li>";
                        }
                    }else{
                    var html_str = "<div class='form-group search-div'><input type='text' class='cate-search form-control'"
                            +" placeholder='搜索.....'><span class='glyphicon glyphicon-search form-control-feedback'></span></div>";
                    for(var i=0;i<categories.length;i++){
                        var category = categories[i];
                        var class_name = category["leaf"] == 0 ? "has-leaf" : "no-leaf";
                         html_str += "<li class=\"" + class_name +"\" "
                            + "data-id=\"" + category["id"] + "\""
                            + "data-level=\"" + category["level"] + "\""
                            + "data-leaf=\"" + category["leaf"] + "\""
                            + "data-tag=\"" + category["tag"] + "\""
                            + "data-query=\"" + category["query"] + "\""
                            + "data-cn=\"\""
                            + "data-en=\""+ category["name"] +"\">"
                            + "<a href=\"javascript: void(0)\">"
                            + category["name"] +"</a></li>";
                    }
                }
                return html_str;
            }
        },
        search_keyup: function(){
            $(".cate-search").keyup(function(){
                $(this).closest("ul").find("li").show();
                var this_list = $(this).closest("ul");
                Product.clear_html(this_list);
                var en_cn = "en";
                var search_str_A = $(this).val().trim().toUpperCase();
                var str_len = search_str_A.length;
                for (var j=0;j<str_len;j++){
                    if(/[\u4e00-\u9fa5A-Za-z]/.test(search_str_A[j])){
                        if(/[\u4e00-\u9fa5]/.test(search_str_A[j])){
                            en_cn = "cn";
                            break
                        }
                    }
                }
                if(str_len){
                    this_list.find("a").each(function(n,ob){
                        var obj = $(ob);
                        var sear_tag =obj.closest("li").attr("data-"+en_cn).toUpperCase();
                        var index = sear_tag.indexOf(search_str_A);
                        if (index == -1){
                            obj.closest("li").hide();
                        }else{
                            var start_html = obj.text();
                            var replace_str = start_html.substring(index,index+str_len);
                            var tar_html = start_html.replace(replace_str,"<span style='color:red'>"+replace_str+"</span>");
                            obj.html(tar_html);
                        }
                    })
                }
            })
        },
        clear_html:function (obj){
            obj.find("a").each(function(n,o){
                o.innerHTML = o.text;
            })
        },
        ch_category: function(){
            var category_group = [];
            $(".chosen").each(function(){
               category_group.push($(this).text());
            });
            $(".well").eq(set_cate_index).find(".tar-direct").text(category_group.join(">>")).attr("data-id",category_uid);
            $("#category-tree").modal("hide");
        },
        save_cate: function(){
            $this_block = $(this).closest(".well");
            $.ajax({
                url: "/?r=public-product/set-category&shopId="+shop_id,
                type: "POST",
                dataType : "json",
                data:{
                    or_tag: $this_block.find(".or-direct").attr("data-id"),
                    tar_id: $this_block.find(".tar-direct").attr("data-id")
                },
                success: function(data){
                    if(data.n > 0){
                        Inform.enable();
                        Inform.show("修改完成,共修改"+data.n+"件商品");
                        $this_block.remove();
                        var num = $(".set-cate-num").text();
                        $(".set-cate-num").text(num-1);
                    }
                }
            })
        },
        check_items: function(){
            if (is_all) {
                Product.get_condition(category_id);
            }else{
                Product.get_condition(Checkbox.get_list());
                if (condition.Ids.length == 0) {
                    Inform.show("请至少选择一件商品!");
                    return 0;
                }
            }
            Inform.disable();
            Inform.show("", true, "正在检测商品信息...");
            $.ajax({
                url:"/?r=public-product/check&shopId="+shop_id,
                type: "post",
                data:{
                    "condition" : JSON.stringify(condition)
                },
                dataType: "json",
                success: function(data){
                    console.log(data);
                    Inform.enable(location.pathname);
                    if(data.status){
                        var error_num = data["error_pid"].length;
                        Inform.enable(window.location.href);
                        if(error_num > 0){
                            Inform.show("检测完成!<br/>" + error_num + "个商品未通过检测，请查看");
                        }else{
                            Inform.show("检测成功!");
                        }
                    }else{
                        Inform.enable();
                        Inform.show("检测失败!");
                    }
                }
            })
        }
    };
    Product.init();
    if($("#sync-status").val() == "0"){
        interval_id = setInterval(Product.view_status, 5000);
    }
});