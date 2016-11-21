/**
 * Created by xuhe on 15/5/27.
 */

$(function () {
    Inform.init();
    var rows = "",
        shop_id = $("#shop-id").val(),
        need_trans = false,
        origin_lot_num = $("#LotNum").val() || 1,
        delivery_time = 30,
        cache = $('#detailHtml'),
        button,
        group_info = [],
        option_type = get_query_string("type");
    var has_render = [];
    var switch_count = [0, 0, 0, 0]; // 词源按钮开关
    var final_title_text = ""; // 存储去重前标题
    var title_length = 0; // 标题长度计数
    var temp_switch = { // 记录模板nav是否第一次点击
        "description": 0,
        "relation": 0,
        "size": 0
    };
    var temp_list = []; // 记录选中的模板
    self_weight_listener();
    var group_name = [];
    //初始化分组数据
    $("[data-group]").gfGroup({
        is_sync: false,
        is_checkbox: true,
        is_request: true,
        is_link: false,
        is_add: false,
        is_edit: false,
        is_delete: false,
        show_no_group: true,
        init_url: "/group/" + shop_id + '/init',
        insert_target: $("#group-select"),
        group_modal: null
    });
    //加载富文本编辑器
    KindEditor.ready(function (K) {
        window.editor = K.create('#desc-editor', options);
        var detail = cache.text(),
            mem = $("<div/>");
        mem.html(detail);
        mem.find("[data-widget-type]").each(function (k, v) {
            var vv = $(v);
            var src_str = "https://www.actneed.com/static/image/{0}?id={1}&ti={2}&ty={3}";
            src_str = src_str.format(
                vv.attr("type") == "custom" ? "widget1.png" : "widget2.png",
                vv.attr("id"), vv.attr("title"), vv.attr("type"));
            var image_url = "<img src=\"" + src_str + "\" class=\"actneed-temp\" />";
            var origin_str = '<kse:widget data-widget-type="{0}" id="{1}" title="{2}" type="{3}"></kse:widget>';
            origin_str = origin_str.format(
                vv.attr("type") == "custom" ? "customText" : "relatedProduct",
                vv.attr("id"), vv.attr("title"), vv.attr("type"));
            detail = detail.replace(origin_str, image_url);
            console.log(detail);
        });
        editor.insertHtml(detail);
        cache.empty();
    });
    var AliEdit = {
        addSpecifics: function () {
            var str = '<div class="row" name="spec">' +
                '<div class="col-md-4">' +
                '<input type="text" placeholder="属性名 - 例如：Color" class="form-control self-name" style="margin-bottom:15px">' +
                '</div>' +
                '<div class="col-md-4">' +
                '<input type="text" placeholder="属性值 - 例如：Red" class="form-control self-value" style="margin-bottom:15px">' +
                '</div>' + '<div class="col-md-4"><div class="form-control-static"><a href="javascript:void(0)" class="rem-spec">' +
                '<i class="glyphicon glyphicon-remove"></i></a></div></div></div>';
            $(this).before(str);
            $(".rem-spec").click(AliEdit.remove_property);
        },
        remove_property: function () {
            $(this).closest("div[name='spec']").remove();
        },
        get_size_temp: function(category_id){
            $("#size-temp").closest(".form-group").remove();
            $.ajax({
                url: "/ali/" + $("#shop-id").val() + "/api/size",
                type: "GET",
                data: "category_uid=" + category_id,
                dataType: "json",
                success: function(data){
                    if(data["need_size"] && data["templates"].length != 0){
                        var size_str = $("<div>").attr("class", "form-group"),
                            select_str = '<label for class="col-md-2 control-label">尺码模板:</label><div class="col-md-6">' +
                                '<select id="size-temp" class="form-control"><option value="">---- 请选择尺码模板 ----</option>{0}</select></div>',
                            option_str = '';
                        for(var i=0;i<data["templates"].length;i++){
                            option_str += '<option value="'+data["templates"][i]["template_id"]+'">'+data["templates"][i]["template_name"] +
                                '</option>';
                        }
                        select_str = select_str.format(option_str);
                        size_str.append(select_str);
                        $("#template-info").prepend(size_str);
                        var s_id = $("#size-chart-id").val();
                        if(s_id){
                            $("#size-temp").val(s_id);
                        }
                    }
                }
            })
        }
    };
    $("#ali-sync-temp").click(sync_info_temp);
    $('#info-template-new').on('show.bs.modal', function (e) {
        $(this).find("a[data-id=\"description\"]").trigger("click");
        $(this).find("input[type=\"checkbox\"]").each(function(k, v){
            $(v).prop("checked", false);
        });
        temp_list = [];
    }).on("change", "input[type=\"checkbox\"]",function(){
        var $this = $(this),
            $id = $this.attr("data-id");
        if($this.prop("checked")){
            temp_list.push($id);
        }else{
            var index = $.inArray($id, temp_list);
            if(index != -1){
                temp_list.splice(index, 1);
            }
        }
    });
    $("#info-template-new").on("click", "a[data-toggle=\"tab\"]", function () {
        var $this = $(this);
        var $id = $this.attr("data-id"),
            target = $('.temp-area[data-id="'+$id+'"]'),
            err_sp = target.find(".error-span");
        err_sp.length > 0 && err_sp.hide();
        target.show().siblings().hide();
        if(temp_switch[$id] == 0){
            target.empty();
            $(".load-info").show();
            err_sp = target.find(".error-span");
            $.ajax({
                "url": "/template/" + $("#shop-id").val() + "/list/" + $id,
                "type": "POST",
                "success": function(data){
                    target.show().siblings().hide();
                    if(data.status == 0){
                        if(err_sp.length == 1){
                            err_sp.show().html(data.message);
                        }else {
                            target.append("<div class=\"error-span\">" + data.message + "</div>");
                        }
                        return 0;
                    }
                    if(data["json"].length == 0){
                        if(err_sp.length == 1){
                            err_sp.show().html(data.message);
                        }else {
                            target.append("<div class=\"error-span\">您尚未设置模板</div>");
                        }
                        return 0;
                    }else{
                        temp_switch[$id] = 1;
                        render_info_temps(target, data["json"],$id);
                    }
                }
            });
        }
    });
    $("#add-info-temp").click(function () {
        var temp_str = "";
        var $this = $(this);
        if(temp_list.length > 0){
            $this.prop("disabled", true);
            $this.text("处理中");
        }
        for(var i=0;i<temp_list.length;i++){
            var $id = temp_list[i],
                $checkbox = $('[data-id="'+$id+'"]'),
                is_custom = !($checkbox.attr("data-custom") == "false"),
                $type = $checkbox.attr("data-type");
            if(is_custom){
                $.ajax({
                    "async": false,
                    "url": "/template/" + $("#shop-id").val() + "/single",
                    "type": "POST",
                    "data": {
                        "id": $id
                    },
                    "success": function (data) {
                        if (data.status == 1) {
                            var temp_data = data["json"];
                            if($type == "size"){
                                if (temp_data["details"]["is_guide"]) {
                                    temp_str += temp_data["size_chart_html"] + temp_data["size_guide_html"];
                                } else {
                                    temp_str += temp_data["size_chart_html"];
                                }
                            }else if($type == "description") {
                                if (temp_data["details"]["title_hide"] == "true") {
                                    temp_str += temp_data["template_html_no_title"];
                                } else {
                                    temp_str += temp_data["template_html"];
                                }
                            }else{
                                temp_str += temp_data["template_html"];
                            }
                        } else {
                            alert("添加失败")
                        }
                    }
                })
            }else{
                var image_url = "https://www.actneed.com/static/image/{0}?id={1}&ti={2}&ty={3}";
                image_url = image_url.format(
                    $type == "custom" ? "widget1.png" : "widget2.png",
                    $id, $checkbox.attr("data-title"), $type);
                temp_str += "<img src=\"{0}\" class=\"actneed-temp\"/>".format(encodeURI(image_url));
            }
        };
        $this.prop("disabled", false);
        $this.text("确认");
        editor.insertHtml(temp_str.replace(/\n/g, "").replace(/<a class="delete" href="#" style="position: absolute; top: 0px; right: 1px; display: none;">×<\/a>/g, ""));
        $("#info-template-new").modal("hide");
    });
    function render_info_temps(target, templates, type){
        var table_head = "<table class=\"table table-hover " +
            "table-striped\"><tr><td>模块名称</td>" +
            "<td>模块类型</td>" + "<td>是否添加模块</td></tr>";
        var table_tail = "</table>";
        var rows = "";
        for(var i=0; i<templates.length;i++) {
            var template = templates[i];
            var check_box = "<input type=\"checkbox\" data-type=\""+type+"\" data-custom=\""+template["is_custom"]+"\"" +
                " data-title=\"" + template["template_name"] + "\" data-id=\"" + template["template_oid"] + "\" />";
            rows += "<tr><td>{0}</td><td>{1}</td><td>{2}</td>"
                    .format(template["template_name"], template["mold_name"], check_box);
        }
        target.html(table_head + rows + table_tail);
    }
    function sync_info_temp(){
        temp_list = [];
        var $this = $(this);
        $this.closest(".modal").find("input[type=\"checkbox\"]").each(function(k, v){
            $(v).prop("checked", false);
        });
        $this.closest(".modal").find(".load-info").show().siblings().hide();
        $.ajax({
            url: "/ali/" + shop_id + "/api/information",
            type: "POST",
            success: function(){
                temp_switch = { // 重置
                    "description": 0,
                    "relation": 0,
                    "size": 0
                };
                $this.closest(".modal").find("a[data-id=\"description\"]").trigger("click");
            }
        })
    }
    $("#group-select").on("click", "input[type=checkbox]", group_check);
    $("#group-tree").on("click", "#choose-group",choose_group);
    $("#sku-prop").on("change", ".self-def-name", function () {
        var $this = $(this),
            text = $this.val().trim(),
            len = text.length;
        len > 20 && $this.val(text.substr(0, 20));
    });
    $("#pro-prop").on("change", "select", function () {
        var this_g = $(this).closest(".form-group"),
            text = this_g.find(":text");
        if (this_g.attr("data-type") == "select") {
            $(this).find(":selected").attr("data-id") == 4 ? text.show() : text.hide();
        }
    });
    $("#addSpecifics").click(AliEdit.addSpecifics);
    $(".bulk").click(function () {
        $("#support-bulk-sell").collapse("show");
    });
    $(".piece").click(function () {
        $("#support-bulk-sell").collapse("hide");
    });
    if ($(".bulk").prop("checked") == true) {
        $("#support-bulk-sell").collapse("show");
    }
    var GenerateVariation = {
        get_specifics: function (category_id) {
            $.ajax({
                url: "/ali/" + $("#shop-id").val() + "/api/attribute",
                type: "GET",
                data: "category_uid=" + category_id,
                dataType: "json",
                success: function (data) {
                    var sku_part = $("#sku-prop");
                    var pro_part = $("#pro-prop");
                    var element;
                    sku_part.html("");
                    pro_part.html("");
                    delivery_time = data["delivery_time"];
                    var sku_specifics = data["specifics"]["sku"];
                    var pro_specifics = data["specifics"]["pro"];
                    console.log(sku_specifics);
                    console.log(pro_specifics);
                    for (var i = 0; i < sku_specifics.length; i++) {
                        element = sku_specifics[i];
                        sku_part.append(GenerateVariation.create_attribute(element));
                    }
                    sku_part.find("input").click(GenerateVariation.choose_variation);
                    for (var j = 0; j < pro_specifics.length; j++) {
                        element = pro_specifics[j];
                        pro_part.append(GenerateVariation.create_attribute(element));
                    }
                    if (sku_part.html() == "") {
                        sku_part.html('<div class="form-group"><label for class="col-md-2 control-label">'
                            + '变体属性: </label><div class="col-md-10"><p class="form-control-static" '
                            + 'style="color: #eb3c00">没有可供选择的变体属性</p></div></div>');
                    }
                    if (pro_part.html() == "") {
                        pro_part.html('<div class="form-group"><label for class="col-md-2 control-label">'
                            + '变体属性: </label><div class="col-md-10"><p class="form-control-static" '
                            + 'style="color: #eb3c00">没有可供选择的商品属性</p></div></div>');
                    }
                }
            });
        },
        create_attribute: function (attribute) {
            var name_id = attribute["name_id"];
            var name = attribute["name"];
            var name_en = attribute["name_en"];
            var units = attribute["units"];
            var frame = '<div class="col-md-10 form-inline">{0}</div>';
            var elements = "", content = "", label = "", value_id, value, value_en;

            if (attribute["required"]) {
                label = ('<label for class="col-md-2 control-label" data-id="{0}" data-name="{1}" data-en="{3}">'
                + '<span class="required">*</span>{2}:</label>').format(name_id, name, name, name_en);
            } else if (attribute["key_attr"]) {
                label = ('<label for class="col-md-2 control-label" data-id="{0}" data-name="{1}" data-en="{3}">'
                + '<span class="key-attr">! </span>{2}:</label>').format(name_id, name, name, name_en);
            } else {
                label = '<label for class="col-md-2 control-label" data-id="{0}" data-name="{1}" data-en="{3}">{2}:</label>'
                    .format(name_id, name, name, name_en);
            }

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
                    elements += '<select class="form-control"><option>请选择</option>';
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
        choose_variation: function () {
            var sku_structure = [];
            var this_checkbox = $(this);
            $("#sku-prop").find(".form-group").each(function (k, v) {
                var kv = $(v);
                var label = kv.find(".control-label");
                var checked_values = [];
                kv.find("input[type=checkbox]").each(function (m, n) {
                    var mn = $(n);
                    if (mn.prop("checked")) {
                        checked_values.push({
                            "value": mn.attr("data-name"),
                            "value_id": mn.attr("data-id"),
                            "value_en": mn.attr("data-en")
                        })
                    }
                });
                if (checked_values.length > 0) {
                    sku_structure.push({
                        name_id: label.attr("data-id"),
                        name: label.attr("data-name"),
                        name_en: label.attr("data-en"),
                        values: checked_values
                    });
                }
            });
            console.log(sku_structure);
            if (sku_structure.length > 0) {
                var variation_html = GenerateVariation.variation_title(sku_structure);
                $("#sku-variation").html(variation_html);
                $(".single-area").hide();
                $("#onekey-SKU").click(GenerateVariation.onekey_sku);
                $("#onekey-clear").click(GenerateVariation.onekey_clear);
                $(".batch-set-price").click(GenerateVariation.batch_set_price);
                $(".batch-set-stock").click(GenerateVariation.batch_set_stock);
            } else {
                var product_id = $("#product-id").val(),
                    info = product["ProductSKUs"][0];
                $("#sku-variation").html("");
                $(".single-area").show();
                if(product_id){
                    $("#StartPrice").val(info["Price"]);
                    $("#quantity").val(info["Stock"]);
                }
            }
            $("#sku-prop").find(".form-group").each(function (k, v) {
                var checkbox_div = $(v);
                var next_div = checkbox_div.next(".form-group");
                var pic_tab = next_div.find("table");
                var checked = checkbox_div.find(":checkbox:checked");
                var need_pic = (checkbox_div.attr("data-pic") || "").toLowerCase();
                var need_name = (checkbox_div.attr("data-need-name") || "").toLowerCase();
                if (checked.length == 0) {
                    pic_tab.closest(".form-group").remove();
                } else if (pic_tab.length > 0 && (need_pic != "false" || need_name != "false")) {
                    for (var i = 0; i < checked.length; i++) {
                        var data_name = checked.eq(i).attr("data-name");
                        var data_id = checked.eq(i).attr("data-id");
                        var value_en = checked.eq(i).attr("data-en");
                        if (pic_tab.find("td[data-id=" + data_id + "]").length > 0) {
                            continue
                        } else {
                            var other_pic = '<div  class="m-left pull-left">'
                                + '<a class="btn btn-primary"  data-sku="sku-net" data-toggle="modal"'
                                + 'aria-expanded="false" >网络图片选取</a>'
                                + '</div>'
                                + '<div class="m-left pull-left">'
                                + '<a class="btn btn-primary" data-toggle="modal" data-self="ali-sku">图片空间选取</a>'
                                + '</div>';
                            var pic_btn = "<a class=\"pull-left\" href=\"javascript:void(0)\" data-name=\"pic-btn\"></a>";
                            var str_td = "<td data-id=\"" + data_id + "\" data-en=\"" + value_en + "\">" + data_name + "</td>",
                                add_str_name = "<td><input type=\"text\" class=\"form-control\"></td>",
                                add_str_pic = "<td>" + pic_btn + other_pic
                                    + "<a style = \"float: right;width: 40px;height: 34px;\" data-pic=\"pic0\" target=\"_blank\" href=\"javascript:void(0)\"></a></td>"
                            need_name != "false" && (str_td += add_str_name);
                            need_pic != "false" && (str_td += add_str_pic);
                            $("<tr/>").html(str_td).appendTo(pic_tab);
                        }
                    }
                    if (this_checkbox) {
                        var this_data_name = this_checkbox.attr("data-name");
                        var this_data_id = this_checkbox.attr("data-id");
                        if (!this_checkbox.is(":checked")) {
                            pic_tab.find("td[data-id=\"" + this_data_id + "\"]").closest("tr").remove()
                        }
                    }
                } else {
                    if (need_pic != "false" || need_name != "false") {
                        var content_div = $("<div/>").attr("class", "col-md-10 col-md-offset-2");
                        var name = checkbox_div.children("label").attr("data-name");
                        var id = checkbox_div.children("label").attr("data-id");
                        var table_div = $("<div/>").attr("class", "form-group").attr("data-name", name).attr("data-id", id);
                        var pro_table = $("<table/>").attr("class", "table table-striped table-bordered");
                        var str_th = "<th>" + name + "</th>",
                            add_th_name = "<th>请输入自定义名称</th>",
                            add_th_pic = "<th>图片（无图片可以不填）</th>";
                        need_name != "false" && (str_th += add_th_name);
                        need_pic != "false" && (str_th += add_th_pic);
                        $("<tr/>").html(str_th).appendTo(pro_table);
                        for (var i = 0; i < checked.length; i++) {
                            var data_name = checked.eq(i).attr("data-name");
                            var data_id = checked.eq(i).attr("data-id");
                            var value_en = checked.eq(i).attr("data-en");
                            var other_pic = '<div  class="m-left pull-left">'
                                + '<a class="btn btn-primary"  data-sku="sku-net"  data-toggle="modal"'
                                + 'aria-expanded="false" >网络图片选取</a>'
                                + '</div>'
                                + '<div class="m-left pull-left">'
                                + '<a class="btn btn-primary"  data-toggle="modal" id="image-space"  data-self="ali-sku">图片空间选取</a>'
                                + '</div>';
                            var pic_btn = "<a class=\"pull-left\"  href=\"javascript:void(0)\"  data-name=\"pic-btn\"></a>";
                            var str_td = "<td data-id=\"" + data_id + "\" data-en=\"" + value_en + "\">" + data_name + "</td>",
                                add_td_name = "<td><input type=\"text\" class=\"form-control self-def-name\"></td>",
                                add_td_pic = "<td>" + pic_btn + other_pic
                                    + "<a style = \"float: right;width: 40px;height: 34px;\" data-pic=\"pic0\"  target=\"_blank\" href=\"javascript:void(0)\"></a></td>";
                            need_name != "false" && (str_td += add_td_name);
                            need_pic != "false" && (str_td += add_td_pic);
                            $("<tr/>").html(str_td).appendTo(pro_table);
                        }
                        pro_table.appendTo(content_div);
                        content_div.appendTo(table_div);
                        $(this).closest(".form-group").after(table_div);
                    }
                }
                ;
                $("a[data-name='pic-btn']").each(function (a, b) {
                    var $this = $(this);
                    var obj = $(b),
                        this_a = obj.parent().find("a[data-pic='pic0']"),
                        this_del_a = obj.next("a[data-name='del-pic']");
                    $this.Huploadify({
                        auto: true,
                        fileTypeExts: '*.JPG;*.jpeg*.jpg;*.png;*.gif',
                        multi: false,
                        fileObjName: 'Filedata',
                        fileSizeLimit: 9999,
                        showUploadedPercent: true,//是否实时显示上传的百分比，如20%
                        showUploadedSize: true,
                        removeTimeout: 2000,
                        buttonText: '点此上传图片',//上传按钮上的文字
                        uploader: "/picture/upload/local",
                        onUploadStart: function () {
                            console.log('开始上传');
                        },
                        onInit: function () {
                            $this.removeAttr("data-name");
                            console.log(" $(this)==" + $this.html() + "初始化！");
                        },
                        onUploadComplete: function (file, data) {
                            //插入图片预览
                            $("#s-num").text(parseInt($("#s-num").text()) + 1);
                            data = eval("(" + data + ")");
                            if (data["error"] == 0) {
                                this_a.find("img").remove();
                                var img_dom = $("<img/>").attr("style", "width:100%;height:100%").attr("src", data["url"]);
                                this_a.removeAttr("href").attr("href", data["url"]);
                                img_dom.appendTo(this_a);
                                var del_btn = $("<a/>").attr("href", "javascript:void(0)").attr("data-name", "del-pic")
                                    .attr("style", "float:right;padding:inherit").text("删除");
                                this_del_a.remove();
                                this_a.before(del_btn);
                                $("a[data-name='del-pic']").each(function (x, y) {
                                    var del_btn = $(y);
                                    del_btn.click(function () {
                                        $(this).closest("td").find("img")
                                            .closest("a").removeAttr("href").attr("href", "javascript:void(0)");
                                        $(this).closest("td").find("img").remove();
                                        $(this).remove();
                                    })
                                });
                            }
                        },
                        onUploadError: function () {
                            alert("上传失败，请稍后重试。");
                        },
                        onDelete: function (file) {
                            console.log('删除的文件：' + file);
                            console.log(file);
                        }
                    });
                });
            });

            if ($("#product-id").val()) {
                RenderPage.render_sku_value();
                RenderPage.render_sku_img();
            }
        },
        batch_set_price: function () {
            var price = $("#batch-price").val();
            if ((/^([\d\.])+$/g).test(price) && parseFloat(price) > 0) {
                $("#sku-variation").find(".v-price").each(function (k, v) {
                    $(v).val(price);
                });
            } else {
                Inform.show("价格不合法");
            }
        },
        batch_set_stock: function () {
            var stock = $("#batch-stock").val();
            if ((/^(\d)+$/g).test(stock) && parseInt(stock) > 0) {
                $("#sku-variation").find(".v-stock").each(function (k, v) {
                    $(v).val(stock);
                });
            } else {
                Inform.show("价格不合法");
            }
        },
        variation_title: function (sku_structure) {
            var frame = '<div class="form-group">'
                + '<div class="col-md-10 col-md-offset-2"><div class="form-inline">'
                + '<span style="margin:8px">批量设置价格：<input type="text" class="form-control" id="batch-price"/>'
                + '</span><a class="btn btn-primary batch-set-price" href="javascript:void(0)">确定</a>'
                + '<span style="margin:8px">批量设置库存：<input type="text" class="form-control" id="batch-stock"/>'
                + '</span><a class="btn btn-primary batch-set-stock" href="javascript:void(0)">确定</a>'
                + '</div></div><div class="col-md-10 col-md-offset-2">{0}</div></div>';
            var sku_attr_str = '<tr class="variation-row">{0}<th>价格(USD)</th><th>库存(件/个)</th><th>SKU编码'
                + '<a class="one-btn-sku" id = "onekey-SKU">(一键生成SKU/</a>' +
                '<a class="one-btn-sku" id = "onekey-clear">清除SKU)</a></th></tr>';
            var sku_attr = "";
            for (var i = 0; i < sku_structure.length; i++) {
                sku_attr += '<th class="variation-name" data-id="{0}" data-en="{2}">{1}</th>'
                    .format(sku_structure[i].name_id, sku_structure[i].name, sku_structure[i].name_en);
            }
            sku_attr_str = sku_attr_str.format(sku_attr);
            rows = sku_attr_str;
            GenerateVariation.variation_row([], 0, sku_structure.length - 1, sku_structure);
            var table = '<table class="table table-striped table-bordered">{0}</table>'.format(rows);
            return frame.format(table);
        },
        variation_row: function (record, level, max_level, struc) {
            for (var i = 0; i < struc[level].values.length; i++) {
                record[level] = {
                    name: struc[level]["name"],
                    name_id: struc[level]["name_id"],
                    value: struc[level].values[i]["value"],
                    value_id: struc[level].values[i]["value_id"],
                    value_en: struc[level].values[i]["value_en"]
                };
                if (level == max_level) {
                    console.log("!!!!!!!");
                    var row = '<tr class="variation-row">{0}'
                        + '<td><input type="text" class="form-control v-price"></td>'
                        + '<td><input type="text" class="form-control v-stock"></td>'
                        + '<td><input type="text" class="form-control v-sku"></td>'
                        + '</tr>';
                    var sku_attr_str = "";
                    for (var j = 0; j < record.length; j++) {
                        console.log(record[j].value_id);
                        sku_attr_str += '<td><span data-id="{0}" data-en="{2}" class="variation-attr">{1}</span></td>'
                            .format(record[j].value_id, record[j].value, record[j].value_en);
                    }
                    rows += row.format(sku_attr_str);
                } else {
                    GenerateVariation.variation_row(record, level + 1, max_level, struc);
                }
            }
        },
        onekey_clear: function () {
            $("#sku-variation").find(".v-sku").val("");
        },
        onekey_sku: function () {
            var sku_table_tr = $("#sku-variation").find("tr");
            var num = sku_table_tr.eq(0).find("th:contains('价格')").index();
            var length = sku_table_tr.length;
            for (var i = 1; i < length; i++) {
                var v_sku = $("#ParentSKU").val().trim(),
                    o_sku = v_sku;
                for (var j = 0; j < num; j++) {
                    v_sku += ("-" + sku_table_tr.eq(i).find("td").eq(j).find("span").attr("data-en").replace(" ", ""));
                }
                if (v_sku.length > 20) {
                    v_sku = v_sku.substr(0, 20);
                }
                sku_table_tr.eq(i).find(".v-sku").val(v_sku);
            }
        }
    };
    $("#DispatchTimeMax").keyup(function () {
        var $this = $(this);
        parseInt($this.val().trim()) > delivery_time && $this.val(delivery_time);
    });
    $("#self-define-attr").on("blur", ":text", check_self_attr);
    $("#Title").on("blur", check_title);
    $("#StartPrice").keyup(check_price);
    $(".pack-int").keyup(pack_int);
    $(".pack-float").keyup(pack_float);
    $("#quantity").keyup(check_quantity);
    $("#sku-prop").find("input").click(GenerateVariation.choose_variation);
    $("#choose-category").click(function () {
        if ($("#use-exist").is(":hidden")) {
            var cate_id = $(".category-area").find("li[class='chosen'][data-leaf='1']").attr("data-id");
        } else {
            var cate_id = $(".on").parent().attr("data-id");
        }

        GenerateVariation.get_specifics(cate_id);
        $("#sku-variation").find(".form-group").remove();
        $("#category-tree").modal("hide");
        $(".single-area").show();
        AliEdit.get_size_temp(cate_id);
    });
    $("#del-style").click(function () {
        editor.html(editor.html().replace(/[\u4e00-\u9fa5\$]/gm, ""));
    });
    // 产品分组
    $(".workspace").on("click", "[data-group]", group_show);
    //同步分组
    $("#sync-cate").click(group_click);
    $("html").on("blur", ":text", function () {
        var $this = $(this);
        if (this.id != "Title") {
            $this.val($this.val().replace(/[^\x00-\x7F]+?/g, ""));
        }
    });
    $("#pack-sell").change(function () {
        var status = $(this).prop("checked");
        if (status) {
            $("#LotNum").prop("disabled", false).val(origin_lot_num);
        } else {
            $("#LotNum").val(1).prop("disabled", true);
        }
    });
    $("#title-final-input").on("keydown", check_str_length).on("change", check_str_length).on("keyup", check_str_length)
        .on("keyup", check_words_in_options).on("change", check_words_in_options);
    $("#t-upcase-btn").click(function () {
        var $dom = $("#Title"),
            $title = $dom.val().trim();
        if ($title != "") {
            var new_title = $title.replace(/\s[a-z]/g, function ($1) {
                return $1.toLocaleUpperCase()
            }).replace(/^[a-z]/, function ($1) {
                return $1.toLocaleUpperCase()
            }).replace(/\sOr[^a-zA-Z]|\sAnd[^a-zA-Z]|\sOf[^a-zA-Z]|\sAbout[^a-zA-Z]|\sFor[^a-zA-Z]|\sWith[^a-zA-Z]|\sOn[^a-zA-Z]/g, function ($1) {
                return $1.toLowerCase()
            });
            $dom.val(new_title);
        }
    });
    $("#save_new_title").click(function () {
        $("#Title").val($("#title-final-input").val().trim());
    });
    $("#t-modal-btn").click(function () {
        switch_count = [0, 0, 0, 0];
        $(".tags-box").each(function (k, v) {
            $(v).empty()
        });
        $("#no-prop-tip").hide();
        $("#options-area").empty();
        $("#title-final-input").val($("#Title").val().trim());
        $("#keyword-btn-1").trigger("click");
        check_str_length();
    });
    $(".btn-collapse").click(function () {
        var $id = $(this).attr("id");
        var tags_list = [];
        var no_prop_tip = $("#no-prop-tip");
        $("#" + $id.replace("keyword-btn", "tags-area")).show().siblings().hide();
        if ($id == "keyword-btn-1") {
            no_prop_tip.hide();
            tags_list = [];
            var tags_list_no_white = [];
            if ($("#Title").val().trim() != "") {
                tags_list = $("#Title").val().trim().split(" ");
                for (var i = 0; i < tags_list.length; i++) {
                    if (tags_list[i] != "") {
                        tags_list_no_white.push(tags_list[i]);
                    }
                }
            }
            if (switch_count[0] == 0 && tags_list_no_white.length > 0) {
                insert_tags(tags_list_no_white, "tags-area-1");
                switch_count[0] = 1
            }
        } else {
            tags_list = [];
            var prop_div = $("#pro-prop,#sku-prop");
            prop_div.find("input[type=checkbox]").each(function (k, v) {
                var kv = $(v);
                kv.prop("checked") && tags_list.push(kv.attr("data-en"));
            });
            prop_div.find("option:selected").each(function (k, v) {
                var kv = $(v);
                kv.attr("data-en") == undefined || tags_list.push(kv.attr("data-en"));
            });
            $("#self-define-attr").find("input[type=text]").not(".self-name").each(function (k, v) {
                var kv = $(v);
                kv.val().trim() == "" || tags_list.push(kv.val().trim());
            });
            if ($("#CategoryID").attr("data-id") == "") {
                no_prop_tip.text("你尚未选择商品分类").show();
            } else {
                no_prop_tip.hide();
                if ($id == "keyword-btn-2") {
                    if (tags_list.length == 0) {
                        no_prop_tip.text("您尚未设置商品属性").show();
                    } else {
                        if (switch_count[1] == 0) {
                            insert_tags(tags_list, "tags-area-2");
                            switch_count[1] = 1;
                        }
                    }
                } else if ($id == "keyword-btn-3") {
                    if ($("#CategoryID").attr("data-id") != "" && switch_count[2] == 0) {
                        request_title_tags("hot");
                        switch_count[2] = 1;
                    }
                } else if ($id == "keyword-btn-4") {
                    if ($("#CategoryID").attr("data-id") != "" && switch_count[3] == 0) {
                        request_title_tags("up");
                        switch_count[3] = 1;
                    }
                }
            }
        }
    });
    $(".tags-area").on("click", ".title-tags-li", check_tags);
    $("#options-area").on("click", ".title-tags-li", check_options)
        .on("click", ".tag-del", del_option);
    $(".panel-heading").find(".glyphicon").click(function () {
        var $this = $(this);
        if ($this.attr("class") == "glyphicon glyphicon-plus") {
            $this.removeClass("glyphicon-plus").addClass("glyphicon-minus");
        } else {
            $this.removeClass("glyphicon-minus").addClass("glyphicon-plus");
        }
    });
    $("#clean-echo").click(clean_echo);
    $("#self-weight").change(self_weight_listener);
    $("#sku-prop").on("click", "[data-sku=\"sku-net\"]", function () {
        button = $(this);
        $("#image-net-modal").modal("show");
    });
    //sku网络图片
    $("#image-net-sku").click(function () {
        var m = $("#image-net-url-sku");
        var net_url = m.val();
        if (!net_url || net_url == "") {
            return 0;
        }
        var skupic = button.closest("tr").find("td[data-id]").attr("data-id");
        var a_dom = $("td[data-id=" + skupic + "]").closest("tr").find("a[data-pic]").html('');
        var images = $("<img/>").attr("style", "width:100%;height:100%").attr("src", net_url).appendTo(a_dom);
        var del_btn = $("<a/>").attr("href", "javascript:void(0)").attr("data-name", "del-pic")
            .attr("style", "float:right;padding:inherit").text("删除");
        a_dom.closest("a").before(del_btn);
        $("a[data-name='del-pic']").each(function (x, y) {
            var del_btn = $(y);
            del_btn.click(function () {
                $(this).closest("td").find("img")
                    .closest("a").removeAttr("href").attr("href", "javascript:void(0)");
                $(this).closest("td").find("img").remove();
                $(this).remove();
            })
        });
        m.val("");
        $("#image-net-modal").modal("hide");
    });
    //模板信息
    $("#sync-shipping").click(sync_shipping);
    $("#sync-promise").click(sync_promise);
    function sync_shipping() {
        Inform.disable();
        Inform.show("", true, "正在同步...");
        $.ajax({
            "url": "/ali/" + $("#shop-id").val() + "/api/freight",
            "type": "POST",
            "dataType": "json",
            "success": function (data) {
                Inform.enable();
                if (data.status == 1) {
                    Inform.show("同步成功");
                    render_template(data["message"]["templates"]);
                } else {
                    Inform.show(data["message"]);
                }
            }
        });
        function render_template(shipping_info) {
            var shipping_select = $("#shipping-select"),
                shipping_value = shipping_select.val();
            if (shipping_info.length > 0) {
                shipping_select.html("");
                $("<option/>").attr("value", "").text("---- 请选择运费模板 ----").appendTo(shipping_select);
                for (var i = 0; i < shipping_info.length; i++) {
                    $("<option/>").attr("value", shipping_info[i]["template_id"]).text(shipping_info[i]["template_name"])
                        .appendTo(shipping_select);
                    if (shipping_info[i]["template_id"] == shipping_value) {
                        $("[value='" + shipping_info[i]["template_id"] + "']").attr("selected", "selected");
                    }
                }
            }
        }
    }

    function sync_promise() {
        Inform.disable();
        Inform.show("", true, "正在同步...");
        $.ajax({
            "url": "/ali/" + $("#shop-id").val() + "/api/promise",
            "type": "POST",
            "dataType": "json",
            "success": function (data) {
                Inform.enable();
                if (data.status == 1) {
                    Inform.show("同步成功");
                    render_template(data["message"]["templates"]);
                } else {
                    Inform.show(data["message"]);
                }
            }
        });
        function render_template(promise_info) {
            var promise_select = $("#promise-select"),
                promise_value = promise_select.val()
            if (promise_info.length > 0) {
                promise_select.html("");
                $("<option/>").attr("value", "").text("---- 请选择运费模板 ----").appendTo(promise_select);
                for (var i = 0; i < promise_info.length; i++) {
                    $("<option/>").attr("value", promise_info[i]["template_id"]).text(promise_info[i]["template_name"])
                        .appendTo(promise_select);
                    if (promise_info[i]["template_id"] == promise_value) {
                        $("[value='" + promise_info[i]["template_id"] + "']").attr("selected", "selected");
                    }
                }
            }
        }
    }

    function options_ul_change() {
        var $dom = $("#options-area");
        var li_length = $dom.find("li").length;
        var $btn = $dom.closest(".panel").find(".glyphicon");
        var is_extend = $btn.attr("class") == "glyphicon glyphicon-minus";
        ((li_length && !is_extend) || (!li_length && is_extend)) && $btn.trigger("click");
    }
    function group_show() {
        $('#group-tree').modal('show');
    }
    function group_check(){
        var $this = $(this);
        $("#choose-group").prop("disabled", !$this.prop("checked"))
    }
    function choose_group(){
        var $this = $(this),
            $group = $this.closest(".modal").find("input[type=\"checkbox\"]:checked").closest(".gf-category"),
            group_div = $("[data-group]").closest("div").find(".full-category-name"),
            g_id = $group.attr("data-id");
        group_name = [$group.attr("data-name").trim()];
        get_group_name($this.closest(".modal"), $group.attr("data-pid"));
        $this.closest(".modal").modal("hide");
        group_div.attr("data-id", g_id).text(group_name.join(">"));
    }
    function get_group_name(area, p_id){
        if(p_id != "0"){
            var p_group = area.find(".gf-category[data-id=\""+p_id+"\"]");
            group_name.unshift(p_group.attr("data-name"));
            get_group_name(area, p_group.attr("data-pid"));
        }
    }
    function change_li_status($dom) {
        var $status = !($dom.attr("data-status") == 'true');
        $dom.attr("data-status", $status);
        return $status
    }

    function check_tags() {
        var $this = $(this),
            data_name = $this.attr("data-name");
        var tag_status = change_li_status($this);
        var options_area = $("#options-area");
        if (tag_status) {
            var option_li = '<li class="title-tags-li" data-status=false data-name="' + data_name + '">' + data_name +
                '<a href="javascript:void(0)" class="tag-del">×</a>' +
                '</li>';
            options_area.append(option_li);
            check_words_in_options();
        } else {
            options_area.find("li[data-name=\"" + data_name + "\"]").remove();
        }
        options_ul_change();
    }

    function check_options() {
        var $this = $(this);
        var $input = $("#title-final-input");
        var opt_status = !($this.attr("data-status") == 'true');
        final_title_text = $input.val().trim(); // 想一想 怎么处理比较好
        if (opt_status) {
            final_title_text += " " + $this.attr("data-name");
            $input.val(final_title_text);
            $input.focus();
            $("#clean-echo").attr("data-status", false);
            $this.attr("data-status", true);
//            $("#Title").val(final_title_text);
            check_str_length();
        }
    }

    function del_option(e) {
        e.stopPropagation();
        var $dom = $(this).closest("li");
        $dom.fadeOut("fast", function () {
            $(".tags-area").find("li[data-name=\"" + $dom.attr("data-name") + "\"]").trigger("click");
            $dom.remove();
            options_ul_change();
        });
    }

    function clean_echo() {
        var $this = $(this),
            $status = $this.attr("data-status") == "true";
        var $input = $("#title-final-input");
        if ($status) {
            $input.val(final_title_text);
            $this.attr("data-status", false);
            $this.find("span").removeClass("glyphicon-ok-sign").addClass("glyphicon-remove-sign");
        } else if (!$status && $input.val().trim()) {
            final_title_text = $input.val().trim();
            $input.val(arrayReplace(final_title_text, " "));
            $this.attr("data-status", true);
            $this.find("span").removeClass("glyphicon-remove-sign").addClass("glyphicon-ok-sign");
        }
        check_str_length();
    }

    // 去重
    function arrayReplace(str, chart) {
        var dict = {}, array = str.split(chart);
        for (var i = 0; i < array.length; i++) {
            dict[array[i]] = array[i];
        }
        array = [];
        for (var el in dict) {
            array.push(dict[el]);
        }
        return array.join(" ");
    }

    function check_words_in_options() {
        var match_str = $("#title-final-input").val().trim();
        $("#options-area").find(".title-tags-li").each(function (k, v) {
            var kv = $(v),
                $name = kv.attr("data-name");
//            var re = new RegExp("[\\s]*"+$name+"[\\s]*");
            var re = new RegExp(" " + $name + "$" + "|" + "^" + $name + "$" + "|" + "^" + $name + " " + "|" + " " + $name + " ");
            re.test(match_str) ? kv.attr("data-status", true) : kv.attr("data-status", false);
        });
//        $("#Title").val(match_str);
    }

    function check_str_length() {
        var str_len_tip = $("#str-len");
        title_length = $("#title-final-input").val().trim().length;
        str_len_tip.text(128 - title_length);
        if (title_length > 128) {
            str_len_tip.css("color", "red");
        } else {
            str_len_tip.css("color", "#8e8e8e");
        }
    }

    function request_title_tags(tag_option) {
        $.ajax({
            url: "/create/" + $("#shop-id").val() + "/keywords",
            type: "POST",
            data: {
                "word_type": tag_option,
                "cate_id": $("#CategoryID").attr("data-id")
            },
            success: function (data) {
                if (data.status == 1) {
                    var area_id = "";
                    var no_prop_tip = $("#no-prop-tip");
                    if (tag_option == "hot") {
                        area_id = "tags-area-3";
                        no_prop_tip.text("此分类下暂无热词");
                    } else {
                        area_id = "tags-area-4";
                        no_prop_tip.text("此分类下暂无飙升词");
                    }
                    if (data["words_list"].length == 0) {
                        no_prop_tip.show();
                    } else {
                        no_prop_tip.hide();
                        insert_tags(data["words_list"], area_id)
                    }
                } else {

                }
            },
            error: function () {

            }
        })
    }

    function insert_tags(tags_arr, area_id) {
        var insert_area = $("#" + area_id);
        for (var i = 0; i < tags_arr.length; i++) {
            var tag_str = '<li class="title-tags-li" data-status=false data-name="' + tags_arr[i] + '">' + tags_arr[i] + '</li>';
            insert_area.append(tag_str)
        }
    }

    function get_query_string(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)return unescape(r[2]);
        return null;
    }

    function check_self_attr() {
        var this_row = $(this).closest(".row"),
            this_text = this_row.find("span");
        if ($(this).hasClass("self-name")) {
            this_row.siblings(".row").each(function (i, v) {
                if ($(this).find(".self-name").val().trim() == this_row.find(".self-name").val().trim()) {
                    this_text.text("自定义属性名不能重复");
                    return false
                } else if (this_row.find(".self-name").val().trim().length > 40) {
                    this_text.text("自定义属性名长度不能超过40个字符");
                } else if (i == this_row.siblings(".row").length - 1) {
                    this_text.text("");
                }
            })
        } else {
            this_row.find(".self-value").val().trim().length > 70 && this_text.text("自定义属性值长度不能超过70个字符");
        }
    }

    function set_progress(now, max) {
        var rate = parseInt(now / max * 100);
        $(".progress-bar").attr({
            "aria-valuenow": now,
            "aria-valuemax": max,
            "style": "width: " + rate + "%;"
        }).text((rate > 100 ? 100 : rate) + "%")
    }

    function self_weight_listener() {
        var status = $("#self-weight").prop("checked");
        if (status) {
            $("#support-self-weight").collapse("show");
        } else {
            $("#support-self-weight").collapse("hide");
        }
    }

    function error_control(dom) {
        var style = dom.attr("class");
        dom.attr("class", style + " has-error");
        dom.focus(function () {
            $(this).attr("class", style);
        })
    }

    $("#submit-btn").click(submit_feed);
    $("#trans-sub-btn").click(function () {
        need_trans = true;
        submit_feed();
    });
    $("#save-publish-btn").click(sub_publish_feed);
    $("#online-edit-btn").click(online_edit_feed);
    $("#save-btn").click(save_feed);
    $("#check-forbid").click(check_forbid);
    function check_forbid(){
        var str = editor.html(),
            mem = $("<div/>");
        mem.html(str);
        mem.find('.actneed-temp').each(function (k, v) {
            var widget = '<kse:widget data-widget-type="{0}" id="{1}" title="{2}" type="{3}"></kse:widget>';
            var src_str = $(v).attr("src");
            var src = decodeURI(src_str);
            var params_str = src.split("?")[1];
            var groups = params_str.split("&");
            widget = widget.format(
                groups[2].split("=")[1] == "relation" ? "relatedProduct" : "customText",
                groups[0].split("=")[1], groups[1].split("=")[1], groups[2].split("=")[1]);
            var origin_str = "<img src=\"" + src_str + "\" class=\"actneed-temp\" />";
            str = str.replace(origin_str, widget);
            console.log(str);
        });
        var title = $("#Title").val().trim(),
            description = str,
            specifics = [],
            category_uid = $("#CategoryID").attr("data-id") || "";
        $("#self-define-attr").find(".row").each(function (k, v) {
            var inputs = $(v).find("input");
            var name = inputs.eq(0).val(),
                value = inputs.eq(1).val();
            if(name != "" && value != ""){
                specifics.push(name);
                specifics.push(value);
            }
        });
        if(category_uid == ""){
            Inform.enable();
            Inform.show("请先选择产品目录");
            return
        }
        if(title || description || specifics.length > 0){
            Inform.disable();
            Inform.show("", true, "正在检测中...");
            $.ajax({
                url: "/ali/" + $("#shop-id").val() + "/api/forbid",
                type: "POST",
                data: {
                    "category_uid": category_uid,
                    "title": title,
                    "description": description,
                    "specifics": JSON.stringify(specifics)
                },
                success: function(data){
                    if(data.status == 1){
                        var dt = data["title"],
                            dd = data["description"],
                            ds = data["specifics"];
                        if(dt.length == 0 && dd.length == 0 && ds.length == 0){
                            Inform.enable();
                            Inform.show("检测完成，本产品中未发现违禁词");
                        }else{
                            Inform.enable();
                            var inform_str = '<div>在产品中检测到以下违禁词:</div>';
                            if(dt.length > 0){
                                inform_str += '<div class="row">' +
                                    '<label class="control-label col-md-3">产品标题:' +
                                    '</label>' +
                                    '<div class="col-md-8">'+ dt.join(", ") +'</div>' +
                                    '</div>'
                            }
                            if(dd.length > 0){
                                inform_str += '<div class="row">' +
                                    '<label class="control-label col-md-3">产品描述:' +
                                    '</label>' +
                                    '<div class="col-md-8">'+ dd.join(", ") +'</div>' +
                                    '</div>'
                            }
                            if(ds.length > 0){
                                inform_str += '<div class="row">' +
                                    '<label class="control-label col-md-3">自定义属性:' +
                                    '</label>' +
                                    '<div class="col-md-8">'+ ds.join(", ") +'</div>' +
                                    '</div>'
                            }
                            Inform.show(inform_str);
                        }
                    }else{
                        Inform.enable();
                        Inform.show(data.message);
                    }
                },
                error: function(){
                    Inform.enable();
                    Inform.show("检测失败，请稍后重试")
                }
            })
        }else{
            Inform.enable();
            Inform.show("没有需要检测的内容，请填写标题、描述或自定义属性。");
        }
    }
    function pack_int() {
        var v = $(this).val().trim().replace(/[^0-9]/g, '');
        this.id != "LotNum" && this.id != "BaseUnit" && this.id != "AddUnit" && (v = v > 700 ? 700 : v);
        $(this).val(v ? parseInt(v) : "");
    }

    function pack_float() {
        var v = $(this).val().trim().replace(/[^0-9.]/g, '');
        $(this).val(v ? v : "");
    }

    function check_title() {
        var v = $(this).val().trim();
        if (v.length > 128) {
            $(this).val(v.substring(0, 128));
        }
    }

    function check_price() {
        var v = $(this).val().trim().replace(/[^0-9.]/g, '');
        var nums = v.split(".");
        var int = nums[0];
        if (int.length > 18) {
            nums[0] = int.substring(0, 18);
        }
        $(this).val(nums.join("."));
    }

    function check_quantity() {
        var v = $(this).val().trim().replace(/[^0-9]/g, '');
        $(this).val(v ? parseInt(v) : "");
    }
    function check_url(str_url){
        var strRegex = '^((https|http|ftp|rtsp|mms)?://)'
            + '?(([0-9a-z_!~*\'().&=+$%-]+: )?[0-9a-z_!~*\'().&=+$%-]+@)?' //ftp的user@
            + '(([0-9]{1,3}.){3}[0-9]{1,3}' // IP形式的URL- 199.194.52.184
            + '|' // 允许IP和DOMAIN（域名）
            + '([0-9a-z_!~*\'()-]+.)*' // 域名- www.
            + '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].' // 二级域名
            + '[a-z]{2,6})' // first level domain- .com or .museum
            + '(:[0-9]{1,4})?' // 端口- :80
            + '((/?)|' // a slash isn't required if there is no file name
            + '(/[0-9a-z_!~*\'().;?:@&=+$,%#-]+)+/?)$';
        var re = new RegExp(strRegex);
        return re.test(str_url)
    }
    function online_edit_feed() {
        if (!check_required()) {
            return 0;
        }
        var product_info = get_info(),
            shop_id = $("#shop-id").val(),
            product_id = $("#product-id").val(),
            self = $(this);
        Inform.disable();
        Inform.show("", true, "正在修改商品信息...");
        $.ajax({
            type: "POST",
            url: "/online/" + shop_id + "/single/republish",
            dataType: "json",
            data: {
                body: JSON.stringify(product_info),
                pid: product_id
            },
            timeout: 10000,
            success: function (data) {
                if (data.status == 0) {
                    Inform.enable();
                    Inform.show("商品信息修改失败");
                } else {
                    Inform.enable("/online/" + shop_id + "/selling");
                    Inform.show(data.message);
                }
            },
            complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
                console.log(status);
                if (status == 'timeout') {//超时,status还有success,error等值的情况
                    Inform.enable();
                    Inform.show("请求超时, 请重试");
                }
            }
        });
    }

    function save_feed() {
        var product_id = option_type == "use" ? "" : $("#product-id").val(),
            self = $(this);
        get_info();
        var url = "/create/" + $("#shop-id").val() + "/product/draft";
        Inform.disable();
        Inform.show("", true, "正在保存...");
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: {
                product: JSON.stringify(product),
                product_id: product_id
            },
            timeout: 10000,
            success: function (data) {
                if (data.status == 0) {
                    Inform.enable();
                    Inform.show("商品提交失败");
                } else {
                    if (need_trans) {
                        Inform.hide();
                        $("#trans-pid").val(data["pid"]);
                        $("#trans-control-modal").modal("show");
                    } else {
                        Inform.enable("/product/" + $("#shop-id").val() + "/draft");
                        Inform.show("商品信息已经保存到草稿箱");
                    }
                }
                need_trans = false;
            },
            complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
                if (status == 'timeout') {//超时,status还有success,error等值的情况
                    Inform.enable();
                    Inform.show("请求超时, 请重试");
                }
            }
        });

    }

    function submit_feed() {
        var product_id = option_type == "use" ? "" : $("#product-id").val(),
            self = $(this);
        if (!check_required()) {
            return 0;
        } else {
            Inform.disable();
            Inform.show("", true, "正在保存商品...");
            var url = "";
            if (product_id == "") {
                url = "/create/" + $("#shop-id").val() + "/product/save"
            } else {
                url = "/create/" + $("#shop-id").val() + "/product/update"
            }
            get_info();
            Inform.disable();
            Inform.show("", true, "正在保存...");
            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: {
                    product: JSON.stringify(product),
                    product_id: product_id
                },
                timeout: 10000,
                success: function (data) {
                    if (data.status == 0) {
                        Inform.enable();
                        Inform.show("商品提交失败");
                    } else {
                        if (need_trans) {
                            Inform.hide();
                            $("#trans-pid").val(data["pid"]);
                            $("#trans-control-modal").modal("show");
                        } else {
                            Inform.enable("/product/" + $("#shop-id").val() + "/waiting");
                            Inform.show("商品提交成功");
                        }
                    }
                    need_trans = false;
                },
                complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
                    console.log(status);
                    if (status == 'timeout') {//超时,status还有success,error等值的情况
                        Inform.enable();
                        Inform.show("请求超时, 请重试");
                    }
                }
            });
        }
    }
    function sub_publish_feed(){
        var product_id = option_type == "use" ? "" : $("#product-id").val();
        if (!check_required()) {
            return 0;
        }
        Inform.disable();
            Inform.show("", true, "正在保存商品...");
            var url = "";
            if (product_id == "") {
                url = "/create/" + $("#shop-id").val() + "/product/save"
            } else {
                url = "/create/" + $("#shop-id").val() + "/product/update"
            }
            get_info();
            Inform.disable();
            Inform.show("", true, "正在保存...");
            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: {
                    product: JSON.stringify(product),
                    product_id: product_id
                },
                timeout: 10000,
                success: function (data) {
                    if (data.status == 0) {
                        Inform.enable();
                        Inform.show("商品提交失败");
                    } else {
                        upload_feed("/product/"+shop_id+"/upload", data["pid"])
                    }
                    need_trans = false;
                },
                complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
                    console.log(status);
                    if (status == 'timeout') {//超时,status还有success,error等值的情况
                        Inform.enable();
                        Inform.show("请求超时, 请重试");
                    }
                }
            })
    }
    function upload_feed(url, id){
        var condition = {};
        condition["Ids"] = [id];
        Inform.disable();
        Inform.show("", true, "正在检测并上传商品...");
        $.ajax({
            url:url,
            type:"post",
            dataType:"json",
            data:{
                "condition" : JSON.stringify(condition),
                "shipping_id": "",
                "promise_id": "",
                "group_id": "",
                "group_name": "",
                "group2_id": "",
                "group2_name": ""
            },
            success:function(data){
                if(data.status==1){
                    var error_n = data["error_pid"].length;
                    var error_str = error_n == 0 ? "" : "<br/>商品未通过上传检测，请修改";
                    var success_n = data["success_pid"].length;
                    var success_str = success_n == 0 ? "": "<br/>已成功将商品加入上传队列";
                    Inform.enable("/product/"+shop_id+"/waiting");
                    Inform.show(error_str == "" ? success_str : error_str);
                }else{
                    Inform.enable("/product/"+shop_id+"/waiting");
                    Inform.show(data.message);
                }
            }
        })
    }
    function onekey_trans(){

    }
    function get_info() {
        var category = $("#CategoryID"),
            group = $("#GroupID"),
            bulk = $(".bulk"),
            self_define_weight = $("#self-weight"),
            pack_sell = $("#pack-sell"),
            pro_specifics = [],
            pro_skus = [],
            integrity = true,
            sku_variation_label = [];
        product["PictureURLs"] = [];

        if($("#Title").val()){
            product["Title"] = $("#Title").val();
        }else{
            product["Title"] = "待编辑";
        }
        //处理描述
        var str = editor.html(),
            mem = $("<div/>");
        mem.html(str);
        mem.find('.actneed-temp').each(function (k, v) {
            var widget = '<kse:widget data-widget-type="{0}" id="{1}" title="{2}" type="{3}"></kse:widget>';
            var src_str = $(v).attr("src");
            var src = decodeURI(src_str);
            var params_str = src.split("?")[1];
            var groups = params_str.split("&");
            widget = widget.format(
                groups[2].split("=")[1] == "relation" ? "relatedProduct" : "customText",
                groups[0].split("=")[1], groups[1].split("=")[1], groups[2].split("=")[1]);
            var origin_str = "<img src=\"" + src_str + "\" class=\"actneed-temp\" />";
            str = str.replace(origin_str, widget);
            console.log(str);
        });
        product["ParentSKU"] = $("#ParentSKU").val().trim();
        product["Category"]["ID"] = category.attr("data-id") || "";
        product["Category"]["Name"] = category.text().split(" > ") || [];
        product["Description"] = str;
        product["DispatchTimeMax"] = $("#DispatchTimeMax").val();
        product["ListingDuration"] = $('input:radio[name="ListingDuration"]:checked').val();
        product["StartPrice"] = $("#StartPrice").val();
        product["ProductUnit"] = $("#ProductUnit").find("option:selected").attr("data-id") || "";
        product["GrossWeight"] = $("#GrossWeight").val();
        product["PackageLength"] = $("#pac-length").val();
        product["PackageWidth"] = $("#pac-width").val();
        product["PackageHeight"] = $("#pac-height").val();
        product["FreightTemplateID"] = $("#shipping-select").val().trim();
        product["PromiseTemplateID"] = $("#promise-select").val().trim();
        product["SizeChartTemplateID"] = "";
        product["Group"]["ID"] = product["Group"]["ID"] = group.attr("data-id") != "" ? parseInt(group.attr("data-id")) : 0;
        product["Group"]["Name"] = group.text().trim() == "未设置分组" ? "" : group.text().trim().split(">");
        product["SupplyLink"] = $("#supply-link").val().trim();
        product["SizeChartTemplateID"] = $("#size-temp").val() || "";

        $("#feed_img").find(".image").each(function (k, v) {
            var cur = $(v);
            if (cur.attr("src") != "/static/image/add.png") {
                product["PictureURLs"].push($(v).attr("src"));
            }
        });
        if (bulk.prop("checked")) {
            product["BulkSell"] = {};
            product["BulkSell"]["BulkOrder"] = $("#BulkOrder").val();
            product["BulkSell"]["BulkDiscount"] = $("#BulkDiscount").val();
        } else {
            product["BulkSell"] = false;
        }
        if (self_define_weight.prop("checked")) {
            product["SelfDefineWeight"] = {};
            product["SelfDefineWeight"]["BaseUnit"] = $("#BaseUnit").val();
            product["SelfDefineWeight"]["AddUnit"] = $("#AddUnit").val();
            product["SelfDefineWeight"]["AddWeight"] = $("#AddWeight").val();
        } else {
            product["SelfDefineWeight"] = false;
        }
        if (pack_sell.prop("checked")) {
            product["PackageType"] = true;
            product["LotNum"] = $("#LotNum").val();
        } else {
            product["PackageType"] = false;
            product["LotNum"] = 1;
        }

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
                if (option_name != "请选择" && option_id) {
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
        $("#self-define-attr").find(".row").each(function (k, v) {
            var inputs = $(v).find("input");
            pro_specifics.push({
                "Name": inputs.eq(0).val(),
                "Value": inputs.eq(1).val(),
                "NameID": "",
                "ValueID": ""
            });
        });
        $("#sku-variation").find(".variation-row").each(function (k, v) {
            var cur = $(v);
            console.log(k);
            if (k == 0) {
                cur.find(".variation-name").each(function (p, q) {
                    var cur2 = $(q);
                    sku_variation_label.push({
                        "name_id": parseInt(cur2.attr("data-id")),
                        "name": cur2.attr("data-en")
                    })
                });
                console.log(sku_variation_label);
            } else {
                console.log("variation");
                var vs = [];
                cur.find(".variation-attr").each(function (m, n) {
                    var cur2 = $(n);
                    var name_id = parseInt(sku_variation_label[m]["name_id"]);
                    var sku_pro_tab = $(".form-group[data-id = \"" + name_id + "\"]");
                    var img_url = [];
                    if (sku_pro_tab.length > 0) {
                        var spec_tr = sku_pro_tab.find("td[data-id='" + cur2.attr("data-id") + "']").closest("tr");
                        var img_url_a = spec_tr.find("img").attr("src");
                        var v_value = spec_tr.find(":text").val() || "";
                        if (img_url_a) {
                            img_url.push(img_url_a);
                        }
                    }
                    vs.push({
                        "NameID": name_id,
                        "Name": name,
                        "ValueID": parseInt(cur2.attr("data-id")),
                        "Value": v_value || cur2.attr("data-en").trim(),
                        "Image": img_url
                    })
                });
                var stock = cur.find(".v-stock");
                var price = cur.find(".v-price");
                var sku = cur.find(".v-sku");
                if (!stock.val()) {
                    Inform.show("库存不能为空");
                    error_control(stock);
                    integrity = false;
                    return 0;
                }
                if (!price.val()) {
                    Inform.show("价格不能为空");
                    error_control(stock);
                    integrity = false;
                    return 0;
                }
                pro_skus.push({
                    "VariationSpecifics": vs,
                    "Stock": stock.val().trim(),
                    "Price": price.val().trim(),
                    "SKU": sku.val().trim()
                })
            }
        });
        if ( pro_skus.length == 0) {
            pro_skus.push({
                "Stock": $("#quantity").val().trim(),
                "Price": $("#StartPrice").val().trim(),
                "VariationSpecifics": [],
                "SKU": $("#ParentSKU").val().trim()
            });
        }
        if (!integrity) {
            return 0;
        }
        product["ProductSKUs"] = pro_skus;
        product["ProductSpecifics"] = pro_specifics;
        return product
    }

    function check_required() {
        var tip_html_str = "<p style='color:red'>*此项为必输项</p>",
            check_input = $(":required").filter(":visible"),
            product_unit = $("#ProductUnit :selected"),
            flag = true,
            bulk_status = $(".bulk").prop("checked"),
            weight_status = $("#self-weight").prop("checked"),
            scroll_height = 10000;
        var spec_div = $(".required").filter(":visible");
        spec_div.each(function (i, v) {
            var check_form = $(v).closest(".form-group");
            if (check_form.find(":checkbox").length > 0 || check_form.attr("data-type") == "CheckBox") {
                var checked_box = check_form.find("input:checked");
                $(v).closest("label").find(".blank").remove();
                if (checked_box.length == 0) {
                    $("<p/>").addClass("blank").html("*此项为必输项").appendTo($(v).closest("label"));
                    $(v).closest(".form-group").find("input:checkbox");
                    flag = false;
                    var height_t = $(v).offset().top;
                    scroll_height = scroll_height < height_t ? scroll_height : height_t;
                    check_form.find(":checkbox").click(check_click);
                }
            }
            if (check_form.find("select").length > 0) {
                var check_select = check_form.find("select");
                check_select.next(".blank").remove();
                if (!check_select.find(":selected").attr("value") && !check_select.find(":selected").attr("data-id") && !check_select.find(":selected").attr("data-name")) {
                    check_select.after(tip_html_str).css("border-color", "red");
                    flag = false;
                    var height_t = $(v).offset().top;
                    scroll_height = scroll_height < height_t ? scroll_height : height_t;
                }
            }
            if (check_form.find(".col-md-10").eq(0).find(":text").filter(":visible").length > 0) {
                var this_str = check_form.find(".col-md-10").eq(0).find(":text").filter(":visible");
                $(v).next(".blank").remove();
                this_str.each(function (i, v) {
                    if ($(v).val().trim() == '') {
                        $(v).after(tip_html_str).css("border-color", "red");
                        flag = false;
                        var height_t = $(v).offset().top;
                        scroll_height = scroll_height < height_t ? scroll_height : height_t;
                    }
                })
            }
        });
        for (var i = 0; i < check_input.length; i++) {
            if (check_input.eq(i).val().trim() == "") {
                check_input.eq(i).next().remove();
                check_input.eq(i).after(tip_html_str).css("border-color", "red").keyup(must_input_keyup);
                flag = false;
                var height_t = check_input.eq(i).offset().top;
                scroll_height = scroll_height < height_t ? scroll_height : height_t;
            }
        }
        if (!product_unit.attr("data-id")) {
            var p_unit = $("#ProductUnit");
            p_unit.next().remove();
            p_unit.after(tip_html_str).css("border-color", "red").change(must_input_keyup);
            var height_t = p_unit.offset().top;
            flag = false;
            scroll_height = scroll_height < height_t ? scroll_height : height_t;
        }
        if (!$("#CategoryID").attr("data-id")) {
            Inform.show("请选择商品分类");
            flag = false;
        } else if ($("#feed_img").find("img").attr("src") == "/static/image/add.png") {
            Inform.show("请上传产品图片");
            flag = false;
        }
        if (bulk_status) {
            var bulk_order = $("#BulkOrder");
            var bulk_discount = $("#BulkDiscount");
            bulk_order.closest(".form-inline").next().remove();
            if (!bulk_order.val().trim() || !bulk_discount.val().trim()) {
                tip_html_str = "<p class='col-md-10'  style='color:red'>*信息输入不完整</p>";
                bulk_order.closest(".form-inline").after(tip_html_str).find("input").keyup(bulk_keyup);
                flag = false;
                var height_t = bulk_order.offset().top;
                scroll_height = scroll_height < height_t ? scroll_height : height_t;
            }
        }
        if (weight_status) {
            var base_unit = $("#BaseUnit");
            var add_unit = $("#AddUnit");
            var add_weight = $("#AddWeight");
            base_unit.closest(".form-inline").next().remove();
            if (!base_unit.val().trim() || !add_unit.val().trim() || !add_weight.val().trim()) {
                tip_html_str = "<p class='col-md-10'  style='color:red'>*信息输入不完整</p>";
                base_unit.closest(".form-inline").after(tip_html_str).find("input").keyup(weight_keyup);
                flag = false;
                var height_t = base_unit.offset().top;
                scroll_height = scroll_height < height_t ? scroll_height : height_t;
            }
        }
        if (!flag) {
            $("html,body").animate({scrollTop: scroll_height}, 300);
        }
        return flag
    }

    function must_input_keyup() {
        var product_unit = $("#ProductUnit :selected");
        if (product_unit.attr("data-id") && $(this) == $("#ProductUnit")) {
            $(this).css("border-color", "");
            $(this).next().remove();
        } else if ($(this).val().trim() != "") {
            $(this).css("border-color", "");
            $(this).next().remove();
        }
    }

    function check_click() {
        var $dom = $(this).closest(".form-group");
        var checked_box = $dom.find("input:checked");
        $dom.children("label").find(".blank").remove();
        if (checked_box.length == 0) {
            $("<p/>").addClass("blank").html("*此项为必输项").appendTo($dom.children("label"));
        }
    }

    function bulk_keyup() {
        var bulk_order = $("#BulkOrder");
        var bulk_discount = $("#BulkDiscount");
        if (bulk_order.val().trim() && bulk_discount.val().trim()) {
            $(this).closest(".form-inline").next().remove();
        }
    }

    function weight_keyup() {
        var base_unit = $("#BaseUnit");
        var add_unit = $("#AddUnit");
        var add_weight = $("#AddWeight");
        if (base_unit.val().trim() && add_unit.val().trim() && add_weight.val().trim()) {
            $(this).closest(".form-inline").next().remove();
        }
    }

    function group_click() {
        Inform.disable();
        Inform.show("", true, "正在同步...");
        $.ajax({
            "url": "/ali/" + $("#shop-id").val() + "/api/group",
            "type": "POST",
            "dataType": "json",
            "success": function (data) {
                Inform.enable();
                if (data.status == 1) {
                    Inform.show("同步成功");
                } else {
                    Inform.show(data["message"]);
                }
            }
        });
    }

    var RenderPage = {
        init: function () {
            var product_id = $("#product-id").val();
            if (product_id == "") {
                console.log("product_id is null");
                return 0;
            }
            console.log("product_id is not null");
            $.ajax({
                "url": "/create/" + $("#shop-id").val() + "/product/sync",
                "type": "POST",
                "dataType": "json",
                "data": {"product_id": product_id},
                "success": function (data) {
                    product["ProductSpecifics"] = data["pro"];
                    product["ProductSKUs"] = data["sku"];
                    product["SourceInfo"] = data["source"];
                    if (data["fs"] == "on") {
                        $("#save-btn,#submit-btn,#trans-sub-btn").hide();
                        $("#online-edit-btn").show();
                        $("#save-publish-btn").hide();
                    }
                    RenderPage.render_pro();
                    RenderPage.render_sku();
                },
                "error": function () {
                    console.log("there is some error happened");
                }
            })
        },
        render_size_temp: function(){
            var cate_id = $("#CategoryID").attr("data-id");
            if(cate_id){
                AliEdit.get_size_temp(cate_id);
            }
        },
        render_pro: function () {
            for (var i = 0; i < product["ProductSpecifics"].length; i++) {
                var writing_specifics = product["ProductSpecifics"][i];
                if (writing_specifics["NameID"]) {
                    var v_id = writing_specifics["ValueID"];
                    var this_dom = $("label[data-id=\"" + writing_specifics["NameID"] + "\"]").siblings(".form-inline")
                        .find("[data-id=\"" + v_id + "\"]");
                    if (v_id && (v_id != 4)) {
                        this_dom.prop("checked", true).prop("selected", true);
                    } else if (v_id == 4) {
                        this_dom.prop("selected", true).closest("select")
                            .trigger("change").next(":text").val(writing_specifics["Value"]);
                    } else {
                        var this_line = $("label[data-id=\"" + writing_specifics["NameID"] + "\"]")
                            .siblings(".form-inline"),
                            this_select = this_line.find("select");
                        if (this_select.length > 0) {
                            var v_list = writing_specifics["Value"].split(" ");
                            this_select.val(v_list.pop());
                            var this_value = v_list.join(" ");
                            this_line.find("input").val(this_value);
                        } else {
                            this_line.find("input").val(writing_specifics["Value"]);
                        }
                    }
                } else {
                    var str = '<div class="row" name="spec">' +
                        '<div class="col-md-4">' +
                        '<input type="text"  value="' +
                        writing_specifics["Name"] +
                        '" class="form-control self-name" style="margin-bottom:15px">' +
                        '</div>' +
                        '<div class="col-md-4">' +
                        '<input type="text" value="' +
                        writing_specifics["Value"] +
                        '" class="form-control self-value" style="margin-bottom:15px">' +
                        '</div>' + '<div class="col-md-4"><div class="form-control-static"><a href="javascript:void(0)" class="rem-spec">' +
                        '<i class="glyphicon glyphicon-remove"></i></a><span style="margin-left: 10px; color: red;"></span></div></div></div>';
                    $("#addSpecifics").before(str);
                    $(".rem-spec").click(AliEdit.remove_property);
                }
            }
        },
        render_sku: function () {
            for (var i = 0; i < product["ProductSKUs"].length; i++) {
                var writing_specifics = product["ProductSKUs"][i];
                var variation_specifics_list = writing_specifics["VariationSpecifics"];
                for (var j = 0; j < variation_specifics_list.length; j++) {
                    $("label[data-id=\"" + variation_specifics_list[j]["NameID"] + "\"]").siblings(".form-inline").find("[data-id=\"" + variation_specifics_list[j]["ValueID"] + "\"]").prop("checked", true);
                }
            }
            GenerateVariation.choose_variation();
        },
        render_sku_img: function () {
            var num = product["ProductSKUs"].length;
            for (var i = 0; i < num; i++) {
                var sku_spec = product["ProductSKUs"][i]["VariationSpecifics"];
                var len = sku_spec.length;
                for (var j = 0; j < len; j++) {
                    var $td = $(".form-group[data-id='" + sku_spec[j]["NameID"] + "']")
                        .find("td[data-id='" + sku_spec[j]["ValueID"] + "']");
                    sku_spec[j].Value == $td.attr("data-en") || ($td.closest("tr").find(":text").val(sku_spec[j].Value));
                    if (sku_spec[j].Image[0] && (has_render.indexOf(sku_spec[j].Image[0]) == -1)) {
                        //var a_dom = $td.closest("tr").find("a").eq(0);
                        var a_dom = $td.closest("tr").find("a[data-pic]").eq(0).html('');
                        if (sku_spec[j].Image.length > 0) {
                            $("<img/>").attr("style", "width:100%;height:100%").attr("src", sku_spec[j].Image[0]).appendTo(a_dom);
                            var del_btn = $("<a/>").attr("href", "javascript:void(0)").attr("data-name", "del-pic")
                                .attr("style", "float:right;padding:inherit").text("删除");
                            a_dom.closest("a").before(del_btn);
                        }
                        $("a[data-name='del-pic']").each(function (x, y) {
                            var del_btn = $(y);
                            del_btn.click(function () {
                                $(this).closest("td").find("img")
                                    .closest("a").removeAttr("href").attr("href", "javascript:void(0)");
                                $(this).closest("td").find("img").remove();
                                $(this).remove();
                            })
                        });
                        has_render.push(sku_spec[j].Image[0]);
                    }
                }
            }
        },
        render_sku_value: function () {
            $("#sku-variation").find(".variation-row").each(function (k, v) {
                var row = $(v);
                if (k > 0) {
                    var attrs = [];
                    row.find(".variation-attr").each(function (m, n) {
                        attrs.push($(n).attr("data-id"));
                    });
                    var pu = RenderPage.get_variation_content(attrs);
                    if (pu) {
                        var price = pu["Price"];
                        var stock = pu["Stock"];
                        var sku = pu["SKU"];
                        row.find(".v-price").val(price);
                        row.find(".v-stock").val(stock);
                        row.find(".v-sku").val(sku);
                    }
                }
            });
        },
        get_variation_content: function (attrs) {
            console.log(attrs);
            var product_sku, flag;
            for (var i = 0; i < product["ProductSKUs"].length; i++) {
                product_sku = product["ProductSKUs"][i];
                flag = true;
                for (var j = 0; j < attrs.length; j++) {
                    if (!RenderPage.contains(product_sku["VariationSpecifics"], attrs[j])) {
                        flag = false;
                        break;
                    }
                }
                if (flag) {
                    return product_sku;
                }
            }
            return false;
        },
        contains: function (parent, child) {
            console.log(parent);
            console.log(child);
            for (var m = 0; m < parent.length; m++) {
                if (child == parent[m]["ValueID"]) {
                    return true;
                }
            }
            return false;
        }
    };
    RenderPage.init();
});