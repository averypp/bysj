/**
 * Created by GF on 2016/1/28.
 */
$(function(){
    Inform.init();
    var group_btn, group_text_td;
    var is_all_check = false;
    var s_condition = "s-title",
        shop_id = $("#shop-id").val(),
        status = $("#status").val().split(";"),
        condition ={},
        count = 0;
    var move_modal = $("#move-modal"),
        insert_target =  $(".move-detail");
    var temp_sku_value = []; // 记录价格/库存值是否修改
    var SMT = {
        category_selectable : true,
        group_selectable : true,
        init: function(){
            $(".product-table img").lazyload({
                 failurelimit: 3,
                 placeholder: "/static/image/grey.gif", //加载图片前的占位图片
                 effect: "fadeIn" //加载图片使用的效果(淡入)
            });
            $("#sel-group").gfGroup({
                is_request: true,
                is_add: false,
                is_edit: false,
                is_delete: false,
                group_data: null,
                init_url: "/group/"+shop_id+"/init"
            });
            $(".single-group").gfGroup({
                is_checkbox: true,
                is_request: true,
                is_add: false,
                is_edit: false,
                is_delete: false,
                is_link: false,
                insert_target: insert_target,
                group_modal: move_modal,
                init_url: "/group/"+shop_id+"/init"
            });
            $(".single-group").click(function(){
                var $this = $(this);
                move_modal.attr("data-id", SMT.get_pro_info($this)["pro_id"]);
                group_btn = "single";
                group_text_td = $this.closest("tr").find("td").eq(3);
            });
            $("#multi-group").gfGroup({
                is_checkbox: true,
                is_request: true,
                is_add: false,
                is_edit: false,
                is_delete: false,
                is_link: false,
                insert_target: insert_target,
                init_url: "/group/"+shop_id+"/init",
                group_modal: null
            });
            $("#search-options").find("a").click(SMT.choose_option);
            $(".product-table").on("click", "input[type=\"checkbox\"]", SMT.check_pro);
            $(".all-check").find("input[type=\"checkbox\"]").on("click", SMT.check_current_page);
            $(".more-choice").find("a").click(SMT.check_all_pro);
            $("#search-btn").click(SMT.search_pro);
            $(".batch-op").click(SMT.batch_operate);
            $("#sel-category").click(SMT.select_category);
            $(".single-edit-menu").on("click", "a", SMT.single_edit);
            $(".single-ensure-btn").click(SMT.single_ensure);
            $(".batch-ensure-btn").click(SMT.batch_ensure);
            $("#multi-modal").on("click", ".operator-radio", SMT.select_operator);
            $("input[name=\"modify-des\"]").click(function(){
                if($(this).attr("data-position") == "mold"){
                    $("input[name=\"des-mold-pos\"]").each(function(k, v){
                        $(v).prop("disabled", false);
                    })
                }else{
                    $("input[name=\"des-mold-pos\"]").each(function(k, v){
                        $(v).prop("disabled", true).prop("checked", false);
                    })
                }
            });
            $("#pro-prop").on("change", "select", function () {
                var this_g = $(this).closest(".form-group"),
                    text = this_g.find(":text");
                if (this_g.attr("data-type") == "select") {
                    $(this).find(":selected").attr("data-id") == 4 ? text.show() : text.hide();
                }
            });
            move_modal.on("click", ".ensure-move", SMT.ensure_move);
            SMT.show_search_info();
        },
        show_search_info: function(){
            var $search = location.search;
            var re = /[ktp]\=[0-9a-zA-Z\%\_-]+/g,
                re2 = /(?![hktp])[a-z]\=[0-9a-zA-Z]+/g;
            if($search != ""){
                var search_text = $search.match(re);
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
                        $option = "产品ID";
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
            SMT.checkbox_change();
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
            SMT.checkbox_change();
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
        // 搜索产品
        search_pro: function(){
            var s_value = $(".search-input").val().trim();
            if(s_value != ""){
                var param_str = "";
                var re = /(?![hktp])[a-z]\=[0-9a-zA-Z\%\_-]*/g; // 非搜索条件和当前页的其他条件
                s_value = escape(s_value);
                if (s_condition == "s-title") {
                    param_str = "t=" + s_value;
                } else if (s_condition == "s-sku") {
                    param_str = "k=" + s_value;
                } else if (s_condition == "s-pid") {
                    param_str = "p=" + s_value;
                }
                var search_str = location.search;
                if (search_str == "") {
                    location.href = "?" + param_str;
                } else {
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
        // 检查是否选中产品
        detect_check: function(){
            var num = 0;
            $(".product-table").find("input[type=\"checkbox\"]").each(function(k, v){
                $(v).prop("checked") && (num += 1);
            });
            return num;
        },
        //生成商品属性
        create_attributes: function(attribute){
            var name_id = attribute["name_id"];
            var name = attribute["name"];
            var name_en = attribute["name_en"];
            var units = attribute["units"];
            var frame = '<div class="col-md-10 form-inline">{0}</div>';
            var elements = "", content = "", label = "", value_id, value, value_en;
            label = '<label for class="col-md-2 control-label" data-id="{0}" data-name="{1}" data-en="{3}">{2}:</label>'
                .format(name_id, name, name, name_en);
            switch (attribute["show_type"]) {
                case "check_box":
                {
                    (name_id == 200007763) && (elements += "<div class=\"form-control-static\" style=\"display: block;color: #eb3c00;\">[ 注意 ] 发货地仅供具有海外仓的卖家进行选择</div>");
                    for (var i = 0; i < attribute.values.length; i++) {
                        value_en = attribute.values[i]["value_en"];
                        value_id = attribute.values[i]["value_id"];
                        value = attribute.values[i]["value"] == "undefined" ? value_en : attribute.values[i]["value"];
                        elements += ('<div class="checkbox" style="width: 150px">'
                        + '<label><input type="checkbox" class="kcb"'
                        + 'data-name="{0}" data-en="{3}" data-id="{1}">{2}</label></div>')
                            .format(value, value_id, value, value_en);
                    }
                    content = frame.format(elements);
                    return '<div class="form-group" data-type="checkbox" data-pic = ' + attribute["need_pic"]
                        + ' data-need-name=' + attribute["need_name"] + '>'
                        + '{0}{1}</div>'.format(label, content);
                }
                case "list_box":
                {
                    elements += '<select class="form-control"><option>不修改</option>';
                    for (var j = 0; j < attribute.values.length; j++) {
                        value_en = attribute.values[j]["value_en"];
                        value_id = attribute.values[j]["value_id"];
                        value = attribute.values[j]["value"] == "undefined" ? value_en : attribute.values[j]["value"];
                        elements += '<option data-name="{0}" data-id="{1}" data-en="{3}">{2}</option>'
                            .format(value, value_id, value, value_en);
                    }
                    var other_text = "<input class=\"form-control\" type=\"text\" style=\"margin-left: 10px;display:none\">";
                    elements += "</select>" + other_text;
                    content = frame.format(elements);
                    return '<div class="form-group" data-type="select">'
                        + '{0}{1}</div>'.format(label, content);
                }
                case "input":
                {
                    elements += '<input type="text" name="{0}" class="form-control">'.format(name_id);
                    if (units) {
                        var unit_str = "<select class=\"form-control unit\" style=\"margin-left:10px\">";
                        for (var n = 0; n < units.length; n++) {
                            unit_str += "<option value=\"" + units[n]["unitName"] + "\">" + units[n]["unitName"] + "<\/option>";
                        }
                        unit_str += "<\/select>";
                        elements += unit_str;
                    }
                    content = frame.format(elements);
                    return '<div class="form-group" data-type="input">'
                        + '{0}{1}</div>'.format(label, content);
                }
                default:
                {
                    return false;
                }
            }
        },
        // 弹出模态框
        batch_operate: function(){
            var $this = $(this),
                title_str = $this.text(),
                $type = $this.attr("id");
            if($type == "multi-props"){
                var re = /c\=[0-9]*/;
                var cate = location.search.match(re);
                if(cate){
                    if(!SMT.detect_check()){
                        Inform.show("请至少勾选一件产品！");
                        return false;
                    }
                    var $loading = $("#loading-spec");
                    $("#batch-spec-modal").modal("show");
                    $loading.show();
                    $.ajax({
                        url: "/ali/" + $("#shop-id").val() + "/api/attribute",
                        type: "GET",
                        data: "category_uid=" + cate[0].split("=")[1],
                        dataType: "json",
                        success: function (data) {
                            var pro_part = $("#pro-prop");
                            var element;
                            pro_part.html("");
                            var pro_specifics = data["specifics"]["pro"];
                            console.log(pro_specifics);
                            for (var j = 0; j < pro_specifics.length; j++) {
                                element = pro_specifics[j];
                                pro_part.append(SMT.create_attributes(element));
                            }
                            if (pro_part.html() == "") {
                                pro_part.html('<div class="form-group"><label for class="col-md-2 control-label">'
                                    + '商品属性: </label><div class="col-md-10"><p class="form-control-static" '
                                    + 'style="color: #eb3c00">没有可供选择的商品属性</p></div></div>');
                            }
                            $loading.hide()
                        }
                    });
                }else{
                    Inform.show("请先选择商品分类！");
                    return false;
                }
            }else if($type != "multi-move"){
                if(!SMT.detect_check()){
                    Inform.show("请至少勾选一件产品！");
                    return false;
                }
                var op_type = {
                    "multi-other": $("#multi-other-modal")
                };
                var $modal, body_str;
                if($type == "multi-group"){
                    group_btn = "multi";
                    $modal = move_modal;
                }else{
                    if(op_type[$type]){
                        $modal = op_type[$type];
                    }else{
                        $modal = $("#multi-modal");
                        $modal.find(".invalid-tips").text("");
                        if($type == "multi-price" || $type == "multi-stock"){
                            body_str = '<div class="form-horizontal">{0}</div>';
                            var add_str = "";
                            var oper_list = [["", "replace"], ["在原基础上增加", "add"],
                                ["在原基础上减少", "subtract"], ["在原基础上乘以", "multiply"], ["在原基础上除以", "divide"]];
                            var loop_time = $type == "multi-price" ? 5 : 3;
                            oper_list[0][0] = $type == "multi-price" ? "直接替换原价格" : "直接替换原库存";
                            var icon_list = ["glyphicon-pencil", "glyphicon-plus", "glyphicon-minus", "glyphicon-remove", "divide-icon"],
                                icon_str = "";
                            for(var i=0;i<loop_time;i++){
                                icon_str = '<span class="glyphicon '+icon_list[i]+'"></span>';
                                if(i == 4){
                                    icon_str = '<span class="'+icon_list[i]+'">/</span>';
                                }
                                add_str += '<div class="form-group">'+
                                            '<div class="col-md-offset-1 col-md-3">'+
                                            '<div class="radio">'+
                                            '<label><input class="operator-radio" type="radio" name="operator" data-name="'+oper_list[i][1]+'">'+
                                            oper_list[i][0]+'</label>'+
                                            '</div>'+
                                            '</div>'+
                                            '<div class="col-md-6">'+
                                            '<div class="input-group ">'+
                                            '<span class="input-group-addon" title="'+oper_list[i][0]+'">'+
                                            icon_str+
                                            '</span>'+
                                            '<input type="text" class="form-control oper-input" placeholder="请输入数字" disabled>'+
                                            '</div>'+
                                            '</div>'+
                                            '</div>';
                            }
                            body_str = body_str.format(add_str);
                        }else if($type == "multi-online" || $type == "multi-offline"){
                            body_str = '<div class="text-center">确定要'+title_str+'吗</div>'
                        }
                        $modal.find(".modal-body").empty().append(body_str);
                    }
                    $modal.find(".batch-ensure-btn").attr("data-name", $type);
                    $modal.find(".modal-title").text(title_str);
                }
                $modal.modal("show");
                if($type == "multi-other"){
                    $(".freight-select").html('<option>请选择</option>');
                    $.ajax({
                        url: "/template/" + $("#shop-id").val() + "/list/shipping",
                        type: "POST",
                        success: function(data){
                            if(data.status == 1){
                                for(var i=0;i<data["json"].length;i++){
                                    var opt = '<option value="'+data["json"][i]["template_id"]+'">'+data["json"][i]["template_name"]+'</option>';
                                    $(".freight-select").append(opt);
                                }
                            }
                        }
                    });
                }
            }
        },
        ////选择分类
        select_category: function(){
            $("#category-modal").modal("show");
            if(SMT.category_selectable){
                $.ajax({
                    "url": "category/init",
                    "type": "POST",
                    "data": {
                        "status": JSON.stringify(status)
                    },
                    "dataType": "json",
                    "success": function(data){
                        SMT.category_selectable = false;
                        if(data.json.length == 0){
                            $(".category-detail").html("您的商品未设置分类");
                        }else{
                            $(".category-detail").html("");
                            var all_category = "<div class='category' data-leaf ='1' data-id=''><a href='"
                                + SMT.change_url("c", 0)+"'>所有分类</a></div>";
                            $(".category-detail").append(all_category);
                            SMT.render_category(data.json, 0);
                        }
                    },
                    "error": function(){
                    }
                })
            }
        },
        ////渲染分类
        render_category: function(group_list, p_tag){
            var html = "";
            var gl = SMT.search_category(group_list, p_tag);
            for(var i=0; i < gl.length; i++){
                if(!gl[i]["is_leaf"]){
                    html = "<div class=\"category\" "+
                        "style=\"margin-left: "+(gl[i]["level"]-1)*26+
                        "px\" data-leaf ="+gl[i]["is_leaf"]+" data-id="+
                        gl[i]["cid"]+" data-pid="+gl[i]["pid"]+">"+gl[i]["name"]+"</div>";
                    $(".category-detail").append(html);
                    SMT.render_category(group_list, gl[i]["cid"]);
                }else{
                    html = "<div class=\"category\""+
                        "style=\"margin-left: "+(gl[i]["level"]-1)*26+
                        "px;\" data-leaf ="+gl[i]["is_leaf"]+" data-id="+gl[i]["cid"]+" data-pid="+gl[i]["pid"]+">"+
                        "<a href='"+SMT.change_url("c", gl[i]["cid"])+"'>"+
                        gl[i]["name"]+"</a>";
                    html += "</div>";
                    $(".category-detail").append(html);
                }
            }
        },
        search_category: function(group_list, p_tag){
            var gl = [];
            for(var j=0; j<group_list.length; j++){
                if(group_list[j]["pid"]==p_tag){
                    gl.push(group_list[j]);
                }
            }
            return gl;
        },
        del_category_div: function(dom){
            var pid = dom.remove().attr("data-pid"),
                categories = $("#group-modal").find(".category"),
                flag = categories.filter("[data-pid="+pid+"]").length,
                p_div = categories.filter("[data-id="+pid+"]");
            if(!flag){
                SMT.del_category_div(p_div);
            }
        },
        ////选择分组
        select_group:function(){
            $("#group-modal").modal("show");
            if(SMT.group_selectable){
                $.ajax({
                    "url": "/product/" + shop_id + "/group/choose",
                    "type": "POST",
                    "dataType": "json",
                    "success": function(data){
                        SMT.group_selectable = false;
                        if( !data["group"]){
                            $(".group-detail").html("您的商品未设置分组");
                        }else{
                            $(".group-detail").html("");
                            SMT.render_group(data["group"]);
                        }
                    },
                    "error": function(){
                    }
                })
            }
        },
        render_group:function(group_list){
            var gl = group_list,
                all_group = "<div class='category'data-id=''><a class='group_a' href='"
                    + SMT.change_url("g", 0)+"'>所有分组</a></div>",
                html = "";
            $(".group-detail").append(all_group);
            for (var i=0; i<gl.length; i++){
                var child = gl[i]["childGroup"];
                if( !child){
                    html = "<div class='category'"+
                        " data-id="+gl[i]["groupId"]+
                        " ><a class='group_a' href='"+SMT.change_url("g", gl[i]["groupId"])+"'>"+gl[i]["groupName"]+"(0)</a></div>";
                    $(".group-detail").append(html);
                }else{
                    html = "<div class='category'"+
                        " data-id="+
                        gl[i]["groupId"]+" >"+gl[i]["groupName"]+"</div>";
                    $(".group-detail").append(html);
                    for(var j=0; j<child.length; j++) {
                        html = "<div class='category'"+
                        "style='margin-left: 26px' data-id="+child[j]["groupId"]+
                        " ><a class='group_a' href='"+ SMT.change_url("g",child[j]["groupId"]) +"'>"+child[j]["groupName"]+"(0)</a></div>";
                        $(".group-detail").append(html);
                    }
                }
            }
        },
        ////拼接url
        change_url: function(key, value){
            var string_url = "?", url ="";
            if(key == 'c' && value != 0){
                condition[key] = value;
                for( var s in condition ){
                    string_url += s +"="+ condition[s] + "&";
                }
                url = string_url.substring(0,string_url.length-1);
            }else{
                url = "?";
            }
            var $search = location.search;
            if($search != ""){
                var re = /(?![chktp])[a-z]\=\w+/g;
                var con_list = $search.match(re);
                if(con_list){
                    if(url !="?"){
                        url += "&" + con_list.join("&");
                    }else{
                        url += con_list.join("&");
                    }
                }
            }
            return url == "?" ? location.pathname : url;
        },
        /*GF SMT JS*/
        get_pro_info: function($this){
            var $tds = $this.closest("tr").find("td");
            var title = $tds.eq(2).find("a").text(),
                pro_id = $this.closest("ul").attr("data-id");
            return {
                "title": title,
                "pro_id": pro_id
            }
        },
        // 单品编辑
        single_edit: function(){
            var $modal,
                $this = $(this),
                $type = $this.attr("class"),
                info = SMT.get_pro_info($this),
                $title = info["title"],
                $pro_id = info["pro_id"];
            if($type == "edit-pro"){

            }else if($type == "check-supply-link"){

            }else{
                $modal = $("#single-edit");
                $modal.find(".single-ensure-btn").attr("data-name", $type).attr("data-id", $pro_id);
                var title_str = {
                    "single-online": "上架商品",
                    "single-offline": "下架商品",
                    "single-price": "修改价格",
                    "single-stock": "修改库存",
                    "single-group": "转移分组"
                };
                $modal.find(".modal-title").text(title_str[$type]);
                var body_str = "", pro_name_str = "";
                if($type == "single-online" || $type == "single-offline"){
                    body_str = '<div class="text-center">确定要'+title_str[$type]+'吗</div>';
                }else{
                    $.ajax({
                        "url": "single/feed",
                        "type": "POST",
                        "async":false,
                        "data": {
                            "pid": $pro_id
                        },
                        success: function(data){
                            if(data.status == 1){
                                var $product = data.json;
                                if($type == "single-group"){

                                }else{
                                    body_str = SMT.generate_table(data.json, $type);
                                    if(body_str == false){
                                        Inform.show("此商品信息有误");
                                        return
                                    }else{
                                        pro_name_str = '<div class="title-text-table">'+$title+'</div>';
                                    }
                                }
                            }else{
                                Inform.show(data.message);
                            }
                        }
                    });
                }
                $modal.find(".modal-body").empty().append(pro_name_str).append(body_str);
            }
            if(body_str){
                $modal.modal("show").find(".invalid-tips").empty();
            }
        },
        get_sku_thead: function(spec){
            var head_str = "", key;
            for(key in spec){
                if($.inArray(key, ["sku", "price", "stock", "sku_id"]) == -1){
                    head_str += '<th>'+key+'</th>';
                }
            }
            return head_str
        },
        get_sku_tbody:function(variants, type){
            var body_str = [];
            for(var i=0;i<variants.length;i++){
                var $sku = variants[i];
                var sku_text = $sku["sku"] || "--";
                var tr_str = ["<tr data-sku='"+$sku["sku"]+"' data-sku-id='"+$sku["sku_id"]+"'>{0}"
                            +"<td>"+sku_text+"</td>"],
                    p_str = "<td><input type='text' class='form-control v-price' value='"+$sku["price"]+"'></td></tr>",
                    s_str = "<td><input type='text' class='form-control v-stock' value='"+$sku["stock"]+"'></td></tr>";
                tr_str.push(type == "single-price"? p_str : s_str);
                var value_str = "";
                for(var key in $sku){
                    if($.inArray(key, ["sku", "price", "stock", "sku_id"]) == -1){
                        value_str += "<td>"+$sku[key]+"</td>";
                    }
                }
                tr_str = tr_str.join("").format(value_str);
                body_str.push(tr_str);
                temp_sku_value.push(type == "single-price"? $sku["price"] : $sku["stock"]);
            }
            return body_str.join("")
        },
        generate_table: function(data, type){
            var table = $("<table/>").attr("class","table table-striped table-bordered"),
                t_head = ["<tr>{0}<th style='min-width:200px'>SKU(商品编码)</th>"],
                t_body = "",
                t_spec_head = "";
            t_head.push(type == "single-price" ? "<th>价格</th></tr>" : "<th>库存</th></tr>");
            t_head = t_head.join("");
            temp_sku_value = [];
            if(data["variants"].length > 1){
                var spec = data["variants"][0];
                t_spec_head = SMT.get_sku_thead(spec);
                t_body = SMT.get_sku_tbody(data["variants"], type);
            }else if(data["variants"].length == 1){
                var $sku_text = data["variants"][0]["sku"] || "--";
                t_body = '<tr><td>'+$sku_text+'</td><td>{0}</td></tr>';
                if(type == "single-price"){
                    t_body = t_body.format('<input type="text" class="form-control v-price" value={0}></td>');
                    t_body = t_body.format(data["variants"][0]["price"]);
                    temp_sku_value = [data["variants"][0]["price"]];
                }else{
                    t_body = t_body.format('<input type="text" class="form-control v-stock" value={0}></td>');
                    t_body = t_body.format(data["variants"][0]["stock"]);
                    temp_sku_value = [data["variants"][0]["stock"]];
                }
            }else{
                return false
            }
            t_head = t_head.format(t_spec_head);
            table.append(t_head).append(t_body);
            return table
        },
        // 批量操作模态框确定按钮点击
        batch_ensure: function(){
            var $this = $(this),
                $modal = $(".modal-body").filter(":visible"),
                $verb = $this.attr("data-name");
            var $data = {}, ids = [], con = "{}";
            if(is_all_check){
                con = $("#con-value").val() || "{}"; // 记得改
            }else{
                $(".product-table").find(":checked").each(function(k, v){
                    ids.push($(v).val());
                })
            }
            $data.ids = JSON.stringify(ids);
            $data.con = con;
            if($verb =="multi-move"){

            }else if($verb =="multi-group"){

            }else if($verb =="multi-price" || $verb =="multi-stock" || $verb =="multi-ship-cost"){
                $data["value"] = $modal.find(".oper-input").not(":disabled").val().trim();
                $data["pattern"] = $modal.find(".operator-radio").filter(":checked").attr("data-name");
            }else if($verb == "multi-online" || $verb == "multi-offline"){

            }else if($verb == "multi-other"){
                var title_checked = $("input[name=modify-title]").filter(":checked"),
                    des_checked = $("input[name=modify-des]").filter(":checked");
                // 生成修改标题/描述的字符串
                var get_modify_str = function(dom){
                    var title_pos = dom.attr("data-position"),
                        title_panel = dom.closest(".material"),
                        title_str = "";
                    if(title_pos == "replace"){
                        var be_str = title_panel.find("input[data-id=\"before\"]").val().trim(),
                            af_str = title_panel.find("input[data-id=\"after\"]").val().trim();
                        be_str != "" && (title_str = "replace;" + be_str + ";" + af_str + ";" + be_str.length);
                    }else if(title_pos == "mold"){
                        var mold_value = $(".mold-select").val();
                        if(mold_value != "请选择"){
                            var $position = $("input[name=des-mold-pos]").filter(":checked").attr("data-position") || "head";
                            title_str = "template" + $position + mold_value;
                        }
                    }else{
                        var add_str = title_panel.find("input[data-id=\""+title_pos+"\"]").val().trim();
                        add_str != "" && (title_str = title_pos + ";" + add_str);
                    }
                    return title_str
                };
                if(title_checked.length > 0){
                    var title_str = get_modify_str(title_checked);
                    title_str != "" && ($data["title"] = title_str);
                }
                if(des_checked.length > 0){
                    var des_str = get_modify_str(des_checked);
                    des_str != "" && ($data["description"] = des_str);
                }
                var check_is_void = function(){
                    for(var i=0;i<arguments.length;i++){
                        var $data_id = arguments[i],
                            $value = $("input[data-id=\""+$data_id+"\"]").val().trim();
                        if($value != ""){
                            $data[$data_id] = $value;
                        }
                    }
                };
                check_is_void("delivery", "gross", "length", "width", "height");
                var $duration = $("input[name=\"duration\"]").filter(":checked").val();
                if($duration != "1"){
                    $data["duration"] = parseInt($duration);
                }
                var $freight = $(".freight-select").val();
                if($freight != "请选择"){
                    $data["freight"] = $freight;
                }
            }else if($verb == "multi-props"){
                var pro_specifics = [];
                $("#pro-prop").find(".form-group").each(function (k, v) {
                    var cur = $(v);
                    var type = cur.attr("data-type");
                    var label = cur.find(".control-label");
                    var label_name = label.attr("data-en");
                    var label_id = parseInt(label.attr("data-id"));
                    if (type == "checkbox") {
                        var flag = false;
                        cur.find("input[type=checkbox]").each(function (m, n) {
                            var cur2 = $(n);
                            if (cur2.prop("checked")) {
                                flag = true;
                                pro_specifics.push({
                                    "NameID": label_id,
                                    "Name": label_name,
                                    "Value": cur2.attr("data-en"),
                                    "ValueID": parseInt(cur2.attr("data-id"))
                                })
                            }
                        });
                    }
                    if (type == "select") {
                        var option = cur.find("option:selected");
                        var option_id = parseInt(option.attr("data-id"));
                        var option_name = option.attr("data-en");
                        if (option_name != "不修改" && option_id) {
                            var specific = {
                                "NameID": label_id,
                                "Name": label_name,
                                "Value": option_name,
                                "ValueID": option_id
                            };
                            option_id == 4 && (specific["Value"] = cur.find(":text").val().trim());
                            pro_specifics.push(specific)
                        }
                    }
                    if (type == "input") {
                        var input = cur.find("input[class=form-control]"),
                            sel = cur.find("select"),
                            value = input.val().trim();
                        if (sel.length > 0) {
                            var unit = sel.val();
                            value = value + " " + unit;
                        }
                        if (input.val()) {
                            pro_specifics.push({
                                "NameID": label_id,
                                "Name": label_name,
                                "Value": value,
                                "ValueID": ""
                            });
                        }
                    }
                });
                $data.props = JSON.stringify(pro_specifics);
            }else{
                Inform.show("错误的请求,请重试!");
                return
            }
            // 请求完刷新页面 记得在回调里写
            $this.button("loading");
            onlinePage.ajaxRequest($verb.replace(/-/g,"/"), $data, function(data){
                $this.button("reset");
                $this.closest(".modal").modal("hide");
                Inform.show(data.message);
                Inform.enable(location.href)
            }, function(){Inform.show("未知错误")}, function(){$this.button("reset");$this.closest(".modal").modal("hide");}, true);
        },
        // 单体编辑确认按钮
        single_ensure: function(){
            var $this = $(this),
                $modal = $(".modal").filter(":visible"),
                $verb = $this.attr("data-name"),
                $data = {},
                valid_status = true;
            if($verb == "single-price" || $verb == "single-stock"){
                var value_list = [];
                var sku_tr = $modal.find("tr").filter(":gt(0)");
                var total_value = 0;
                for(var i=0;i<temp_sku_value.length;i++){
                    total_value += parseFloat(temp_sku_value[i]);
                }
                sku_tr.each(function(k, v){
                    var kv = $(v),
                        $value = kv.find("input").val();
                    if(!isNaN($value) && 999999>= parseFloat($value) >= 0){
                        if(parseFloat($value) != parseFloat(temp_sku_value[k])){
                            value_list.push(kv.attr("data-sku-id")+"="+$value);
                            total_value += parseFloat($value);
                            total_value -= parseFloat(temp_sku_value[k]);
                        }
                    }else{
                        $modal.find(".invalid-tips").text("无效的输入值");
                        valid_status = false;
                        return false
                    }
                });
                $verb == "single-price" ? $data["sku_prices"] = value_list.join("&") : $data["sku_stocks"] = value_list.join("&");
                if(valid_status){
                    if(value_list.length == 0){
                        $modal.find(".invalid-tips").text("请至少修改一个值再提交");
                        valid_status = false;
                        return false
                    }
                    if($verb == "single-stock"){
                        if(total_value > 999999 || total_value <= 0){
                            $modal.find(".invalid-tips").text("总库存值需要在1~999999之间");
                            valid_status = false;
                            return false
                        }
                    }
                    $data["pid"] = $this.attr("data-id");
                    $this.button("loading").siblings(".invalid-tips").text("");
                    onlinePage.ajaxRequest($verb.replace(/-/g, "/"), $data, function(data){
                        $this.button("reset");
                        $this.closest(".modal").modal("hide");
                        Inform.show(data.message);
                        // 更新页面显示值 区间
                    },
                    function(){
                        Inform.show("未知错误")
                    },
                    function () {
                        $this.button("reset").siblings(".invalid-tips").text("");
                        $this.closest(".modal").modal("hide");
                    }, true);
                }
            }else if($verb == "single-online" || $verb == "single-offline"){
                $this.button("loading").siblings(".invalid-tips").text("");
                onlinePage.ajaxRequest($verb.replace(/-/g,"/"), {"pid": $this.attr("data-id")}, function(data){
                        Inform.show(data.message);
                        Inform.enable(location.href);
                    },
                    function () {
                        Inform.show("未知错误")
                    }, function () {
                        $this.button("reset");
                        $this.closest(".modal").modal("hide");
                    }, true);
            }else{

            }
        },
        // 选择运算符
        select_operator: function(){
            var $this = $(this);
            var $input = $this.closest(".form-group").find(".oper-input");
            $this.closest(".modal-body").find(".oper-input").each(function(k, v){$(v).prop("disabled", true)});
            $input.prop("disabled", false);
        },
        // 转移分组
        move_group: function(){
            var $this = $(this),
                group_id = $this.closest(".gf-category").attr("data-id");
            var url, data = {}, ids = [], con = {};
            if(group_btn == "single"){
                url = "single/group";
                data = {
                    "pid": $this.closest(".modal").attr("data-id").trim(),
                    "group_id": group_id
                }
            }else if(group_btn == "multi"){
                url = "multi/group";
                data["group_id"] = group_id;
                if(is_all_check){
                    con = {}; // 记得改
                }else{
                    $(".product-table").find(":checked").each(function(k, v){
                        ids.push($(v).val());
                    });
                }
                data["con"] = JSON.stringify(con);
                data["ids"] = JSON.stringify(ids);
            }else{
                return
            }
            if(group_id){
                var res = confirm("确定要转移到此分组下吗？");
                if(res){
                    $("#move-modal").find(".gf-global-tip").show().find(".gf-tip-text").text("转移中，请稍候");
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: data,
                        success: function(data){
                            if(data.status == 1){
                                if(group_btn == "single"){
                                    $(".gf-tip-text").filter(":visible").text("转移成功").css("color", "green");
                                    group_text_td.text($this.text());
                                }else{
                                    $("#move-modal").modal("hide");
                                    setTimeout(Inform.show("批量转移成功"), 500);
                                    Inform.enable(location.href);
                                }
                            }else{
                                $("#move-modal").find(".gf-global-tip").show().find(".gf-tip-text").text("转移失败，请重试").css("color", "#eb3c00");
                            }
                        },
                        error: function(){
                            $("#move-modal").find(".gf-global-tip").show().find(".gf-tip-text").text("转移失败，请重试").css("color", "#eb3c00");
                        }
                    });
                }
            }
        },
        ensure_move: function(){
            var $checked = $(this).closest(".modal").find(".check-gf-group:checked"), group_id;
            if($checked.length != 0){
                group_id = $checked.closest(".gf-category").attr("data-id");
            }
            var $this = $(this);
            var data = {}, ids = [], con = "{}";
            if(group_btn == "single"){
                data = {
                    "ids": JSON.stringify([$this.closest(".modal").attr("data-id").trim()]),
                    "group_id": group_id
                }
            }else if(group_btn == "multi"){
                data["group_id"] = group_id;
                if(is_all_check){
                    con = $("#con-value").val() || "{}";
                }else{
                    $(".product-table").find(":checked").each(function(k, v){
                        ids.push($(v).val());
                    });
                }
                data["con"] = con;
                data["ids"] = JSON.stringify(ids);
            }
            if(group_id) {
                $("#move-modal").find(".gf-global-tip").show().find(".gf-tip-text").text("转移中，请稍候");
                $.ajax({
                    url: "multi/group",
                    type: "POST",
                    data: data,
                    success: function (data) {
                        if (data.status == 1) {
                            $("#move-modal").modal("hide");
                            setTimeout(Inform.show("转移分组成功"), 500);
                            Inform.enable(location.href);
                        } else {
                            $("#move-modal").find(".gf-global-tip").show().find(".gf-tip-text").text("转移失败，请重试").css("color", "#eb3c00");
                        }
                    },
                    error: function () {
                        $("#move-modal").find(".gf-global-tip").show().find(".gf-tip-text").text("转移失败，请重试").css("color", "#eb3c00");
                    }
                });
            }
        }
    };
    SMT.init();
});