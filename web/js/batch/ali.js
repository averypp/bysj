/**
 * Created by Administrator on 2016/1/13.
 */
$(function () {
    Inform.init();
    var shop_id = $("#shop-id").val(),
        c_level = 0,
        category_group = [],
        cate_id = 0,
        category_uid = 0,
        cate_names = [],
        root_id = 0,
        category_name = "",
        tem_id = "",
        position = "front",
        category_change = false,
        old_category_id = $("#category_id").val(),
        status = $("#status").val(),
        group_name = [],
        is_first_clicked = {
            "description": 0,
            "relation": 0,
            "size": 0
        },
        product = {
            "DispatchTimeMax": "",
            "ListingDuration": "",
            "ProductUnit": "",
            "BulkSell": "",
            "SelfDefineWeight": "",
            "PackageType": "",
            "LotNum": 1,
            "Quantity": "",
            "Condition": {},
            "PackageLength": "",
            "PackageHeight": "",
            "PackageWidth": "",
            "Description": "",
            "FreightTemplateID": "",
            "PromiseTemplateID": ""
        };

    var Batch = {
        Init: function () {
            var price_content = $("#price-content");
            price_content.find(".input-group-addon").click(Batch.price_icon_click);
            price_content.find(":text").keyup(Batch.price_text_keyup);
            $("input[name=\"price\"]").change(Batch.price_radio_click);
            $("#sku-prop").find(":checkbox").click(Batch.render_self_name);
            $("input[name=\"bulk\"]").change(Batch.bulk_listener);
            $("#addSpec").click(Batch.add_specifics);
            $("input[name=\"weight\"]").change(Batch.self_weight_listener);
            $("input[name=\"pack\"]").change(Batch.pack_sell_click);
            $("#add-key-content").on("click", ".input-group-addon", Batch.del_key);
            $("#sku-len,#sku-pre,#sku-next").change(Batch.sku_change);
            $("#t-pre,#t-next,#t-before,#t-after").change(Batch.title_change);
            $("#sub-edit-info").click(Batch.sub_info);
            $("#ali-basic").show();//.siblings("div").hide()
            $("#ali-pack").show();
            $("#sync-shipping").click(Batch.sync_shipping);
            $("#sync-promise").click(Batch.sync_promise);
            $("#add_template_modal").click(Batch.temp_modal_show);
            $("#template-modal").on("click", "a[data-toggle=\"tab\"]", Batch.choose_temp);
            $("#ali-sync-temp").click(Batch.sync_info_temp);
            $("#add-temp").click(Batch.add_template);
            //速卖通其他text
            $("#pro-prop").on("change", "select", function () {
                var text = $(this).closest(".form-group").find(":text");
                $(this).find(":selected").attr("data-id") == 4 ? text.css("display", "inline") : text.css("display", "none");
            });
            // 产品分组
            Batch.get_group(); //初始化分组数据
            $(".workspace").on("click", "[data-group]", Batch.group_show);
            $("#sync-group").click(Batch.sync_group);
            $("#group-select").on("click", "input[type=checkbox]", Batch.group_check);
            $("#group-tree").on("click", "#choose-group", Batch.choose_group);
        },
        del_key: function () {
            $(this).closest(".col-md-4").remove();
            $("#add-key").show();
        },
        add_key: function () {
            var add_html = "<div class=\"col-md-4\">"
                + "<div class=\"input-group\">"
                + "<input type=\"text\" class=\"form-control\" placeholder=\"关键词\">"
                + "<div class=\"input-group-addon\">"
                + "<i class=\"glyphicon glyphicon-remove\"></i>"
                + "</div></div></div>";
            var area = $("#add-key-content").find(".tip-tab");
            area.append(add_html);
            if (area.find(".col-md-4").length == 3) {
                $("#add-key").hide();
            }
            return false;
        },
        render_self_name: function () {
            var $this = $(this),
                this_line = $this.closest(".form-group"),
                table = this_line.find("table"),
                del_other = this_line.find(".del-other");
            if ($this.prop("checked")) {
                var tr = "<tr data-id=" + $this.attr("data-id") + " data-name=\"" + $this.attr("data-name") + "\">" +
                    "<td>" + $this.attr("data-name") + "</td><td><input type=\"text\" class=\"form-control\"></td></tr>";
                table.append(tr);
                table.show();
                del_other.show();
            } else {
                table.find("[data-id=" + $this.attr("data-id") + "]").remove();
                this_line.find(":checked").length == 0 && table.hide() && del_other.hide();
            }
        },
        bulk_listener: function () {
            var status = $("#bulk").prop("checked");
            if (status) {
                $("#support-bulk-sell").collapse("show");
            } else {
                $("#support-bulk-sell").collapse("hide");
            }
        },
        self_weight_listener: function () {
            var status = $("#self-weight").prop("checked");
            if (status) {
                $("#support-self-weight").collapse("show");
            } else {
                $("#support-self-weight").collapse("hide");
            }
        },
        get_group: function () {
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
        },
        group_show: function() {
            $('#group-tree').modal('show');
        },
        group_check: function(){
            var $this = $(this);
            $("#choose-group").prop("disabled", !$this.prop("checked"))
        },
        choose_group: function(){
            var $this = $(this),
                $group = $this.closest(".modal").find("input[type=\"checkbox\"]:checked").closest(".gf-category"),
                group_div = $("[data-group]").closest("div").find(".full-category-name"),
                g_id = $group.attr("data-id");
            group_name = [$group.attr("data-name").trim()];
            Batch.get_group_name($this.closest(".modal"), $group.attr("data-pid"));
            $this.closest(".modal").modal("hide");
            group_div.attr("data-id", g_id).text(group_name.join(">"));
        },
        get_group_name: function(area, p_id){
            if(p_id != "0"){
                var p_group = area.find(".gf-category[data-id=\""+p_id+"\"]");
                group_name.unshift(p_group.attr("data-name"));
                Batch.get_group_name(area, p_group.attr("data-pid"));
            }
        },
        sync_group: function () {
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
                        //render_group(data["message"]["target"]);
                    } else {
                        Inform.show(data["message"]);
                    }
                }
            });
        },
        temp_modal_show: function(){
            var template = $("#template-modal");
            template.modal("show");
            template.find("a[data-id=\"description\"]").trigger("click");
        },
        sync_info_temp: function(){
            var $this = $(this);
            $this.closest(".modal").find("input[type=\"checkbox\"]").each(function(k, v){
                $(v).prop("checked", false);
            });
            $this.closest(".modal").find(".load-info").show().siblings().hide();
            $.ajax({
                url: "/ali/" + shop_id + "/api/information",
                type: "POST",
                success: function(){
                    is_first_clicked = { // 重置
                        "description": 0,
                        "relation": 0,
                        "size": 0
                    };
                    $this.closest(".modal").find("a[data-id=\"description\"]").trigger("click");
                }
            })
        },
        choose_temp:function(){
            var $this = $(this),
                $id = $this.attr("data-id"),
                target = $('.temp-area[data-id="'+$id+'"]'),
                err_span = target.find(".error-span");
            err_span.length > 0 && err_span.hide();
            target.show().siblings().hide();
            if(is_first_clicked[$id] == 0){
                target.empty();
                $(".load-info").show();
                err_span = target.find(".error-span");
                $.ajax({
                    "url": "/template/" + $("#shop-id").val() + "/list/" + $id,
                    "type": "POST",
                    "success": function(data){
                        target.show().siblings().hide();
                        if(data.status == 0){
                            if(err_span.length == 1){
                                err_span.show().html(data.message);
                            }else {
                                target.append("<div class=\"error-span\">" + data.message + "</div>");
                            }
                            return 0;
                        }
                        if(data["json"].length == 0){
                            if(err_span.length == 1){
                                err_span.show().html(data.message);
                            }else {
                                target.append("<div class=\"error-span\">您尚未设置模板</div>");
                            }
                            return 0;
                        }else{
                            is_first_clicked[$id] = 1;
                            Batch.render_temps(target, data["json"],$id);
                        }
                    }
                });
            }
        },
        render_temps: function(target, templates, type){
            var table_head = "<table class=\"table table-hover " +
                "table-striped\"><tr><td>模块名称</td>" +
                "<td>模块类型</td>" + "<td>是否添加模块</td><td>添加位置</td></tr>";
            var table_tail = "</table>";
            var rows = "";
            for(var i=0; i<templates.length; i++) {
                var template = templates[i],
                    position = '<form>' +
                                    '<label class="radio-inline">'+
                                    '<input type="radio" name="inlineRadioOptions"  value="front" checked>前'+
                                    '</label>'+
                                    '<label class="radio-inline">'+
                                    '<input type="radio" name="inlineRadioOptions"  value="behind">后'+
                                    '</label>' +
                                    '<label class="radio-inline">'+
                                    //'<input type="radio" name="inlineRadioOptions"  value="replace">全部替换'+
                                    //'</label>' +
                                '<form/>',
                    //front = '<input type="checkbox" data-title="' + template.name + '" data-position ="front"' +
                    //         'data-id="' + template.id + '" /> <span style=\"padding-left:3px;vertical-align: top;\">前</span>',
                    //data_custom = (template["is_custom"])?"custom":"sync",
                    check_box = '<input type="checkbox" data-type="' + type + '" data-custom="' + template["is_custom"] + '" ' +
                                'data-title="' + template["template_name"] + '" data-id="' + template["template_id"] + '" />';
                rows += "<tr><td>{0}</td><td>{1}</td><td>{2}</td><td>{3}</td>"
                        .format(template["template_name"], template["mold_name"], check_box, position);
            }
            target.html(table_head + rows + table_tail);
        },
        add_template: function(){
            $("#template-modal").modal("hide");
        },
        pack_sell_click: function () {
            var status = $("#pack-sell").prop("checked");
            if (status) {
                $("#LotNum").val("1").prop("disabled", false);
            } else {
                $("#LotNum").prop("disabled", true);
            }
        },
        price_radio_click: function () {
            var this_radio = $(this);
            if (this_radio.is(":checked")) {
                var this_text = this_radio.closest(".row").find(":text");
                this_text.removeAttr("disabled").closest(".row")
                    .siblings().find(":text").val("").attr("disabled", "disabled");
            }
        },
        price_text_keyup: function () {
            var v = $(this).val();
            $(this).val(v.replace(/[^0-9.]/g, ''));
        },
        add_key_click: function () {
            var content = $("#add-key-content");
            if (content.hasClass("in")) {
                content.collapse("hide");
            } else {
                $(this).closest("p").next().removeAttr("hidden");
                content.collapse("show").siblings(".collapse").collapse("hide");
            }
            return false
        },
        edit_key_click: function () {
            var content = $("#edit-key-content");
            if (content.hasClass("in")) {
                content.collapse("hide");
            } else {
                $(this).closest("p").next().removeAttr("hidden");
                content.collapse("show").siblings(".collapse").collapse("hide");
            }
            return false
        },
        price_icon_click: function () {
            var this_icon = $(this).find("span"),
                this_text = this_icon.closest(".input-group").find(":text");
            if (this_text.is(":disabled")) {
                return
            }
            console.log(this_icon.attr(("data-id")));
            if (this_icon.attr("data-id") == "plus") {
                this_icon.attr("data-id", "minus").text("-");
                return
            }
            if (this_icon.attr("data-id") == "multi") {
                this_icon.attr("data-id", "div").text("÷");
                return
            }
            if (this_icon.attr("data-id") == "minus") {
                this_icon.attr("data-id", "plus").text("＋");
                return
            }
            if (this_icon.attr("data-id") == "div") {
                this_icon.attr("data-id", "multi").text("×");
            }
        },
        sku_change: function () {
            var len = $("#sku-len").val(),
                pre = $("#sku-pre").val(),
                next = $("#sku-next").val(),
                Num = "",
                ex = pre + Num + next;
            for (var i = 0; i < len; i++) {
                Num += Math.floor(Math.random() * 10);
            }
            $("#sku-ex").text(ex);
        },
        title_change: function () {
            var t_pre = $("#t-pre").val().trim();
            var t_next = $("#t-next").val().trim();
            var t_before = $("#t-before").val().trim();
            var t_after = $("#t-after").val().trim();
            var r_title = $("#r-title").val().trim();
            var temp_title = t_pre + " " + r_title + " " + t_next;
            var n_title = temp_title.replace(new RegExp(t_before, 'gm'), t_after);
            $("#n-title").val(n_title);
        },
        add_specifics: function () {
            var name = $(this).prev().val().trim();
            if (name) {
                var add_div = $("<div/>").attr("class", "checkbox").css("width", "210px")
                    .html("<label><input type='checkbox' class='kcb' data-name=\"" + name + "\" />" + name + "</label></div>");
                var area = $(this).closest(".form-group").find(".col-md-10 ").eq(0);
                add_div.appendTo(area).find(":checkbox").trigger("click");
            }
        },
        //模板信息
        sync_shipping: function () {
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
        },
        sync_promise: function () {
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
                    promise_value = promise_select.val();
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
        },
        sub_info: function () {
            var title = {},
                s_title = {},
                sku = {},
                key = {},
                price = {},
                price_sle = $("input[name=\"price\"]:checked"),
                productIds = $("#productIds").val(),
                category_id = "0",
                price_value = price_sle.closest(".row").find(":text").val(),
                pro_specifics = [],
                del_othersku = "false",
                sub_btn = $("#sub-edit-info");

            var js = {
                PackageLength: "pac-length",
                PackageWidth: "pac-width",
                PackageHeight: "pac-height",
                Description: "",
                Quantity: "Quantity",
                "FreightTemplateID": "FreightTemplateID",
                "PromiseTemplateID": "PromiseTemplateID"
            };

            Batch.get_value(js);
            //获取分类UID
            if (cate_id == 0 || old_category_id == cate_id) {
                category_id = old_category_id;
            } else {
                category_id = cate_id;
                category_change = true;
            }
            //获取标题操作信息
            title["prev"] = $("#t-pre").val().trim();
            title["next"] = $("#t-next").val().trim();
            title["before"] = $("#t-before").val().trim();
            title["after"] = $("#t-after").val().trim();
            //获取sku操作信息
            sku["prev"] = "";
            sku["next"] = "";
            sku["len"] = "0";
            //获取价格操作信息
            if (price_value) {
                price["type"] = price_sle.attr("data-id");
                price["option"] = price_sle.closest(".row").find(".price-ctrl-btn").attr("data-id");
                price["value"] = parseFloat(price_sle.closest(".row").find(":text").val().trim());
            } else {
                price["type"] = "value";
                price["option"] = "plus";
                price["value"] = 0;
            }
            //获取变体自定义名称信息
            var sku_props = $("#sku-prop").find(".form-group");
            var product_skus = [];
            sku_props.each(function () {
                var name_id = $(this).children("label").attr("data-id"),
                    name = $(this).children("label").attr("data-en"),
                    trs = $(this).find("tr");
                trs.each(function () {
                    if ($(this).attr("data-id")) {
                        var value_id = $(this).attr("data-id"),
                            value = $(this).find(":text").val().trim();
                        product_skus.push({
                            Name: name,
                            NameID: name_id,
                            Value: value,
                            ValueID: value_id
                        })
                    }
                });
            });
            if (product_skus.length) product["ProductSKUs"] = product_skus;
            //是否删除未被自定义的变体信息
            if ($("#del-true").prop("checked")) {
                del_othersku = "true";
            }
            var data = {
                DispatchTimeMax: "DispatchTimeMax-ali",
                ListingDuration: "ListingDuration-ali",
                ProductUnit: "ProductUnit",
                GrossWeight: "GrossWeight"
            };
            Batch.get_value(data);
            delete product["Condition"];
            //批发政策
            if ($("#bulk").prop("checked")) {
                product["BulkSell"] = {};
                product["BulkSell"]["BulkOrder"] = $("#BulkOrder").val();
                product["BulkSell"]["BulkDiscount"] = $("#BulkDiscount").val();
            } else if ($("#no-bulk").prop("checked")) {
                product["BulkSell"] = false;
            } else {
                delete product["BulkSell"];
            }
            //自定义计重
            if ($("#self-weight").prop("checked")) {
                product["SelfDefineWeight"] = {};
                product["SelfDefineWeight"]["BaseUnit"] = $("#BaseUnit").val();
                product["SelfDefineWeight"]["AddUnit"] = $("#AddUnit").val();
                product["SelfDefineWeight"]["AddWeight"] = $("#AddWeight").val();
            } else if ($("#no-weight").prop("checked")) {
                product["SelfDefineWeight"] = false;
            } else {
                delete product["SelfDefineWeight"];
            }
            if ($("#pack-sell").prop("checked")) {
                product["PackageType"] = true;
                product["LotNum"] = $("#LotNum").val().trim();
            } else if ($("#no-pack").prop("checked")) {
                product["PackageType"] = false;
                product["LotNum"] = 1;
            } else {
                delete product["PackageType"];
                delete product["LotNum"];
            }
            //读取商品参数中的内容
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
                    var input = cur.find("input[class=form-control]");
                    if (input.val()) {
                        pro_specifics.push({
                            "NameID": label_id,
                            "Name": label_name,
                            "Value": input.val(),
                            "ValueID": ""
                        });
                    }
                }
            });
            if (pro_specifics.length) product["ProductSpecifics"] = pro_specifics;
            //获取产品分组
            var group = $("#GroupID");
            if (group.attr("data-id")) {
                product["Group"] = {};
                product["Group"]["ID"] = group.attr("data-id");
                product["Group"]["Name"] = group.text().trim().split(">");
            }
            var target = $(".temp-content"),
                molds = [];
            target.find("input[type=checkbox]").each(function (k, v) {
                var vv = $(v);
                if (vv.prop("checked")) {
                    var temp_type = vv.attr("data-custom");
                    tem_id = vv.attr("data-id");
                    position = vv.closest("tr").find("input[type=radio]").filter(":checked").val();
                    if (temp_type == "false") {
                        molds.push({
                            temp_id: tem_id,
                            temp_type: "sync",
                            position: position
                        });
                    } else {
                        molds.push({
                            temp_id: tem_id,
                            temp_type: "custom",
                            position: position
                        });
                    }
                }
            });
            sub_btn.button("loading");
            Inform.disable();
            Inform.show("", true, "正在批量编辑...");
            $.ajax({
                "url": "/batch/" + $("#shop-id").val() + "/edit",
                "type": "POST",
                "dataType": "json",
                "data": {
                    "product": JSON.stringify(product),
                    "title": JSON.stringify(title),
                    "s_title": JSON.stringify(s_title),
                    "sku": JSON.stringify(sku),
                    "key": JSON.stringify(key),
                    "price": JSON.stringify(price),
                    "molds": JSON.stringify(molds),
                    "del_othersku": del_othersku,
                    "old_category_id": old_category_id,
                    "categoryId": category_id,
                    "category_change": category_change,
                    "category_name": JSON.stringify(category_name),
                    "productIds": productIds,
                    "status": status
                },
                "success": function (data) {
                    console.log(category_id);
                    var url = "/product/" + $("#shop-id").val() + "/waiting?category_id=" + category_id;
                    Inform.enable(url);
                    Inform.show(data.msg);
                },
                "error": function () {
                }
            })
        },
        get_value: function (obj) {
            for (var key in obj) {
                if (!obj.hasOwnProperty(key)) continue;
                var value = "";
                if (key != "ProductUnit" && obj[key] != "ListingDuration-ali") {
                    value = $("#" + obj[key]).val() || undefined;
                } else if (obj[key] == "ListingDuration-ali") {
                    value = $("[name=" + obj[key] + "]:checked").val();
                } else if (key == "ProductUnit") {
                    value = $("#" + key + " :selected").val();
                }
                if (value) {
                    product[key] = value.trim();
                } else {
                    delete product[key];
                }
            }
        }
    };
    //产品类别选择
    $(".category").find("li").click(choose_category);
    $("#cate-modal").click(select_group(shop_id));
    $("#sel-exit").children("li").click(function () {
        var target = $(this),
            un_target = $(this).siblings();
        target.attr("class", "active");
        un_target.attr("class", "");
        $(target.attr("data-target")).show();
        $(un_target.attr("data-target")).hide();
    });
    var choose_cate = $("#choose-category");
    choose_cate.click(function () {
        if ($("#use-exist").is(":hidden")) {
            cate_id = $(".category-area").find("li[class='chosen'][data-leaf='1']").attr("data-id");
        } else {
            cate_id = $(".on").parent().attr("data-id");
        }
        GenerateVariation.get_specifics(cate_id);
        $("#sku-variation").find(".form-group").remove();
        $("#category-tree").modal("hide");
        $(".quantity-area").show();
    });
    choose_cate.click(function () {
        category_group = [];
        if ($("#use-exist").is(":hidden")) {
            $(".chosen").each(function () {
                category_group.push($(this).text());
            })
        } else {
            category_group = $(".on").parent().attr("data-names").split(";");
        }
        var str = $("#CategoryID").attr("data-id", category_uid)
            .html(category_group.join(" &gt; "));
        category_name = str.text().split(">");
    });

    function choose_category() {
        var cate = $(this);
        var is_leaf = cate.attr("data-leaf") == "1";
        var level = cate.attr("data-level");
        var name = cate.find("a").text();
        var html_str = "";
        var pop_times, temp_level;
        level = parseInt(level);
        if (is_leaf) {
            category_uid = cate.attr("data-id");
            if (c_level < level) {
                category_group.push(name);
            } else {
                pop_times = c_level - level + 1;
                temp_level = level;
                while (pop_times > 0) {
                    category_group.pop();
                    temp_level += 1;
                    $(".category[data-level=" + temp_level + "]").remove();
                    pop_times -= 1;
                }
                category_group.push(name);
            }
            c_level = level;
            cate.attr("class", "chosen");
            cate.siblings("li").attr("class", "");
            $("#choose-category").removeAttr("disabled");
        } else {
            $("#choose-category").attr("disabled", "disabled");
            if (!cate.hasClass("chosen")) {
                if (c_level < level) {
                    category_group.push(name);
                } else {
                    pop_times = c_level - level + 1;
                    temp_level = level;
                    while (pop_times > 0) {
                        category_group.pop();
                        temp_level += 1;
                        $(".category[data-level=" + temp_level + "]").remove();
                        pop_times -= 1;
                    }
                    category_group.push(name);
                }
                c_level = level;
                var category_dom = $("<ul/>").attr({
                    "class": "category loading-cate",
                    "data-level": level + 1
                }).appendTo(".category-area");
                cate.attr("class", "chosen");
                cate.siblings("li").attr("class", "");
                $.ajax({
                    url: "/api/category/get",
                    type: "GET",
                    data: {
                        shop_id: shop_id,
                        parent_id: cate.attr("data-id")
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data["categories"].length > 0) {
                            html_str = render_category(data["categories"]);
                            category_dom.html(html_str).removeClass("loading-cate");
                            category_dom.find("li").click(choose_category);
                            search_keyup();
                        }
                    },
                    error: function () {
                        /**
                         alert("请求出错");
                         **/
                        Inform.show("请求出错");
                    }
                });
            }
        }
        function search_keyup() {
            $(".cate-search").keyup(function () {
                $(this).closest("ul").find("li").show();
                var this_list = $(this).closest("ul");
                clear_html(this_list);
                var en_cn = "en",
                    search_str_A = $(this).val().trim().toUpperCase(),
                    str_len = search_str_A.length;
                for (var j = 0; j < str_len; j++) {
                    if (/[\u4e00-\u9fa5A-Za-z]/.test(search_str_A[j])) {
                        if (/[\u4e00-\u9fa5]/.test(search_str_A[j])) {
                            en_cn = "cn";
                            break
                        }
                    }
                }
                if (str_len) {
                    this_list.find("a").each(function (n, ob) {
                        var obj = $(ob);
                        var sear_tag = obj.closest("li").attr("data-" + en_cn).toUpperCase();
                        var index = sear_tag.indexOf(search_str_A);
                        if (index == -1) {
                            obj.closest("li").hide();
                        } else {
                            var start_html = obj.text();
                            var replace_str = start_html.substring(index, index + str_len);
                            var tar_html = start_html.replace(replace_str, "<span style='color:red'>" + replace_str + "</span>");
                            obj.html(tar_html);
                        }
                    })
                }
            })
        }

        function clear_html(obj) {
            obj.find("a").each(function (n, o) {
                o.innerHTML = o.text;
            })
        }

        function render_category(categories) {
            var shop_name = $(".shop-info").find(".text").find("span")[0].innerText,
                html_str = "",
                category = "",
                class_name = "",
                i = 0;
            if (shop_name == "AliExpress") {
                html_str = "<div class='form-group search-div'><input type='text' class='cate-search form-control'"
                    + " placeholder='请输入名称/拼音首字母'><span class='glyphicon glyphicon-search form-control-feedback'></span></div>";
                for (i = 0; i < categories.length; i++) {
                    category = categories[i];
                    class_name = category["leaf"] == 0 ? "has-leaf" : "no-leaf";
                    html_str += "<li class=\"" + class_name + "\" "
                        + "data-id=\"" + category["id"] + "\""
                        + "data-level=\"" + category["level"] + "\""
                        + "data-leaf=\"" + category["leaf"] + "\""
                        + "data-tag=\"" + category["tag"] + "\""
                        + "data-query=\"" + category["query"] + "\""
                        + "data-cn=\"" + category["name"] + "\""
                        + "data-en=\"" + category["pin"] + "\">"
                        + "<a href=\"javascript: void(0)\">"
                        + category["name"] + "</a></li>";
                }
            } else {
                html_str = "<div class='form-group search-div'><input type='text' class='cate-search form-control'"
                    + " placeholder='搜索.....'><span class='glyphicon glyphicon-search form-control-feedback'></span></div>";
                for (i = 0; i < categories.length; i++) {
                    category = categories[i];
                    class_name = category["leaf"] == 0 ? "has-leaf" : "no-leaf";
                    html_str += "<li class=\"" + class_name + "\" "
                        + "data-id=\"" + category["id"] + "\""
                        + "data-level=\"" + category["level"] + "\""
                        + "data-leaf=\"" + category["leaf"] + "\""
                        + "data-tag=\"" + category["tag"] + "\""
                        + "data-query=\"" + category["query"] + "\""
                        + "data-cn=\"\""
                        + "data-en=\"" + category["name"] + "\">"
                        + "<a href=\"javascript: void(0)\">"
                        + category["name"] + "</a></li>";
                }
            }
            return html_str;
        }
    }

    function select_group(shop_id) {
        $.ajax({
            "url": "/product/" + shop_id + "/group/select",
            "type": "POST",
            "data": {},
            "dataType": "json",
            "success": function (data) {
                if (data["group_list"].length == 0) {
                    $(".group-detail").html("您的商品未设置分组");
                } else {
                    $(".group-detail").html("");
                    render_group(data["group_list"], 0);
                }
            },
            "error": function () {

            }
        })
    }

    function render_group(group_list, p_tag) {
        var gl = search_group(group_list, p_tag);
        for (var i = 0; i < gl.length; i++) {
            cate_names[gl[i].level - 1] = gl[i].name;
            cate_names = cate_names.slice(0, gl[i].level);
            if (gl[i]["is_leaf"]) {
                $("<div/>").attr({
                    class: "cate",
                    "data-id": gl[i]["cid"],
                    "data-root": root_id,
                    "data-names": cate_names.join(";")
                }).css({
                    "margin-left": (gl[i]["level"] - 1) * 26 + "px"
                }).html("<a href ='javascript: void(0)'>" + gl[i]["name"] + "(" + gl[i]["count"] + ")" + "</a>").click(function () {
                    $(".on").removeClass("on");
                    $(this).find("a").addClass("on");
                    category_uid = $(this).attr("data-id");
                    $("#choose-category").attr({"disabled": false});
                }).appendTo($(".group-detail"));
            } else {
                if (gl[i].level == 1) {
                    root_id = gl[i]["cid"];
                }
                $("<div/>").attr({class: "cate", "data-id": gl[i]["cid"]}).css({
                    "margin-left": (gl[i]["level"] - 1) * 26 + "px"
                }).text(gl[i]["name"] + "(" + gl[i]["count"] + ")").appendTo($(".group-detail"));
                render_group(group_list, gl[i]["cid"]);
            }
        }
    }

    function search_group(group_list, p_tag) {
        var gl = [];
        for (var j = 0; j < group_list.length; j++) {
            if (group_list[j]["pid"] == p_tag) {
                gl.push(group_list[j]);
            }
        }
        return gl;
    }

    var GenerateVariation = {
        get_specifics: function (category_id) {
            $.ajax({
                url: "/ali/" + $("#shop-id").val() + "/api/attribute",
                type: "GET",
                data: "category_uid=" + category_id,
                dataType: "json",
                success: function (data) {
                    var pro_part = $("#pro-prop"),
                        element,
                        delivery_time;
                    pro_part.html("");
                    delivery_time = data["delivery_time"];
                    var pro_specifics = data["specifics"]["pro"];
                    for (var j = 0; j < pro_specifics.length; j++) {
                        element = pro_specifics[j];
                        pro_part.append(GenerateVariation.create_attribute(element));
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
            var rows = sku_attr_str;
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
                    var row = '<tr class="variation-row">{0}'
                        + '<td><input type="text" class="form-control v-price"></td>'
                        + '<td><input type="text" class="form-control v-stock"></td>'
                        + '<td><input type="text" class="form-control v-sku"></td>'
                        + '</tr>';
                    var sku_attr_str = "",
                        rows = "";
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
                var v_sku = $("#ParentSKU").val().trim();
                //o_sku = v_sku;
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

    //获取产品模板信息
    $("#info-template-modal").on("click", "a[data-toggle='tab']", function () {
        var $this = $(this);
        var $id = $this.prop("id");
        var info_content = $("#info-template-content"),
            other_content = $("#other-template-content");
        if ($id == "temp-sync") {
            //info_content.show();
            //other_content.hide();
            info_content.css("display", "block");
            other_content.css("display", "none");
        } else if ($id == "temp-custom") {
            //info_content.hide();
            //other_content.show();
            info_content.css("display", "none");
            other_content.css("display", "block");
        }
    });
    //产品模板选择
    $("#add-info-temp").click(function () {
        $("#info-template-modal").modal("hide");
    });
    Batch.Init();
});