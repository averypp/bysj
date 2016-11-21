/**
 * Created by Administrator on 2016/5/4.
 */
$(function(){
    var spec_list = [
            'Parent Unique Id',
            'Unique Id',
            'Price',
            'Product Name',
            'Quantity',
            'Shipping',
            'Main Image URL',
            'Tags',
            'Description',
            'Size',
            'Color',
            'MSRP',
            'Brand',
            'Landing Page URL',
            'Extra Image URL',
            'UPC',
            'Shipping Time',
            'Extra Image URL 1',
            'Extra Image URL 2',
            'Extra Image URL 3',
            'Extra Image URL 4',
            'Extra Image URL 5',
            'Extra Image URL 6',
            'Extra Image URL 7',
            'Extra Image URL 8',
            'Extra Image URL 9',
            'Extra Image URL 10'
        ],
        required_list = [
            'Unique Id',
            'Price',
            'Product Name',
            'Quantity',
            'Shipping',
            'Main Image URL',
            'Tags',
            'Description'
        ];
    var table_head = [],
        table_list = [],
        map_dic = {};
    var category_group = [],
        c_level = 0,
        category_uid = 0;
    var example_values = [['TSHIRT1', 'TSHIRT1-S-RED','12.00', 'Men\'s T-Shirt', '100', '3.00', 'http://www.yourwebsite.com/images/dress.jpg', 'tshirt, mens', 'Cool men\'s t-shirt in size S and M', 'S', 'black', '17.99', 'Custom Brand', 'http://myapp.com/p//B008PE00DA', 'http://www.yourwebsite.com/images/7324204/3|http://www.yourwebsite.com/images/dress.jpg', '18211', '20-30', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3', 'http://www.yourwebsite.com/images/7324204/3'],
                            ['TSHIRT1', 'TSHIRT1-M-RED','12.00', 'Men\'s T-Shirt', '110', '3.00', 'http://www.yourwebsite.com/images/dress.jpg', 'tshirt, mens', 'Cool men\'s t-shirt in size S and M', 'M', 'black', '17.99', 'Custom Brand',	'http://myapp.com/p//B008PE00DA', 'http://www.yourwebsite.com/images/7324204/3|http://www.yourwebsite.com/images/dress.jpg', '18212', '20-30', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg', 'http://www.yourwebsite.com/images/dress.jpg']];
    var platform = $(".shop-info").find("span").eq(0).text();
    var Imp = {
        init: function(){
            Inform.init();
            $(".panel-toggle").click(Imp.panel_toggle);
            Imp.render_example_table();
            Imp.init_upload();
            $("#map-panel").on("change", "tr[data-re=\"required\"] select", Imp.select_listener);
            $("#submit-btn").click(Imp.submit_xls);
            $("#country-content").find(":checkbox").change(Imp.choose_country);
            $(".country-radio").change(Imp.country_radio_change);
            $(".country-tab-a").click(Imp.country_toggle);
            $(".cal-btn").click(Imp.cal_btn_click);
            $(".category").find("li").click(Imp.choose_category);
            $(".workspace").on("click","[data-category]",Imp.category_show);
            $("#sel-exit").children("li").click(Imp.cate_li_click);
            $("#choose-category").click(function () {
                $("#category-tree").modal("hide");
                $(".single-area").show();
            });
        },
        panel_toggle: function(){
           var $this = $(this).find("span"),
               help_panel = $("#imp-help");
           if($this.attr("class") == "glyphicon glyphicon-plus" && help_panel.is(":hidden")){
               $this.attr("class", "glyphicon glyphicon-minus");
           }else if($this.attr("class") == "glyphicon glyphicon-minus" && help_panel.is(":visible")){
               $this.attr("class", "glyphicon glyphicon-plus");
           }
        },
        init_upload: function(){
            var button = $('#select-file');
            var fileType = "xls";
            new AjaxUpload(button,{
                action: "/excel/" + $("#shop-id").val() + "/upload",
                data:{
                    "file_type": ""
                },
                name: 'xl_data',
                onSubmit : function(file, ext){
                    if(fileType == "xls")
                    {
                        if(ext && /^(xls|xlsx|csv)$/.test(ext)){
                            var file_type = $("#type-select").val();
                            if((/^(xls|xlsx)$/.test(ext) && file_type != "excel") || (/^(csv)$/.test(ext) && file_type == "excel")){
                                Inform.show("上传文件类型与选择不符，请重新上传");
                                return false;
                            }else{
                                this.setData({
                                    'file_type': file_type
                                });
                            }
                        }else{
                            Inform.show("非excel/csv文件，请重新上传");
                            return false;
                        }
                    }
                    Inform.disable();
                    Inform.show("", true, "文件导入中...");
                    this.disable();
                },
                onComplete: function(file, response){
                    var re = eval('(' + response + ')');
                    if(re["status"] == 1){
                        table_head = re["table_head"];
                        table_list = re["table_list"];
                        if(table_list.length == 0 || table_head.length < 9){
                            Inform.enable(location.href);
                            Inform.show("您上传的文件中数据不完整，请重新上传");
                        }else{
                            Inform.enable();
                            Inform.show("上传成功,请将列映射到属性以继续");
                            Imp.render_map_property(table_head);
                            $("#submit-panel").show();
                            if(platform == "Ensogo"){
                                $("#country-set").show();
                                $("#category-panel").show();
                            }
                        }
                    }else{
                        Inform.enable(location.href);
                        Inform.show(re["message"]);
                    }
                }
            });
        },
        country_radio_change: function(){
            var $area = $("#country-set-area");
            if($(this).attr("data-value") == "uni"){
                $area.hide();
            }else{
                $area.show();
            }
        },
        country_toggle: function(){
            var country = $(this).closest("li").attr("data-value");
            $('.country-rel-set[data-id="'+country+'"]').show().siblings(".country-rel-set").hide();
        },
        cal_btn_click: function(){
            var $this = $(this),
                $span = $this.find("span"),
                $class = $span.attr("class");
            if($class == "glyphicon glyphicon-plus"){
                $this.attr("title", "在原基础上减少");
                $span.attr("class", "glyphicon glyphicon-minus");
                $span.attr("data-id", "minus");
            }else if($class == "glyphicon glyphicon-minus"){
                $this.attr("title", "直接替换");
                $span.attr("class", "glyphicon glyphicon-refresh");
                $span.attr("data-id", "replace");
            }else if($class == "glyphicon glyphicon-refresh"){
                $this.attr("title", "在原基础上增加");
                $span.attr("class", "glyphicon glyphicon-plus");
                $span.attr("data-id", "plus");
            }
        },
        render_example_table: function(){
            var table = $("<table>").attr("class", "table table-striped table-bordered"),
                tds = ["", "", ""],
                trs = '<tr>{0}</tr><tr>{1}</tr><tr>{2}</tr>';
            for(var i=0;i<spec_list.length;i++){
                tds[0] += '<th>'+spec_list[i]+'</th>';
                tds[1] += '<td>'+example_values[0][i]+'</td>';
                tds[2] += '<td>'+example_values[1][i]+'</td>';
            }
            table.append(trs.format(tds[0], tds[1], tds[2]));
            $("#example-area").append(table);
        },
        render_map_property: function(t_head){
            var table = $("<table>").attr("class", "table table-striped").attr("id", "map-table"),
                trs = "",
                select_str = '<select class="form-control custom-th-slt">{0}</select>',
                option_str = '<option value="0">请选择</option>';
            for(var i=0;i<t_head.length;i++){
                option_str += '<option>'+t_head[i]+'</option>';
            }
            select_str = select_str.format(option_str);
            table.append('<thead><tr><th>对应属性</th><th>必需/可选</th><th>您的列名称</th></tr></thead>');
            for(var j=0;j<spec_list.length;j++){
                var is_required = $.inArray(spec_list[j], required_list) != -1 ? 'required' : 'optional',
                    req_text = is_required == 'required' ? "必需" : "可选";
                trs += '<tr data-re="'+is_required+'">' +
                    '<td>'+spec_list[j]+'</td>' +
                    '<td class="'+is_required+'">'+req_text+'</td>' +
                    '<td>'+select_str+'</td>' +
                    '</tr>';
            }
            table.append(trs);
            $(".imp-tip").hide();
            $("#map-panel").append(table);
            $("#map-table").find("option").each(function(k, v){
                var kv = $(v);
                if(kv.text().trim().replace("*","").toLowerCase() == kv.closest("tr").find("td").eq(0).text().trim().toLowerCase()){
                    kv.prop("selected", true);
                }
            });
            Imp.select_listener();
        },
        get_map: function(){
            for(var i in map_dic){
                if(map_dic[i]){
                    map_dic[i] = $.inArray(map_dic[i], table_head)
                }
            }
        },
        table_list_handler: function(table_list){
            var new_list = [];
            for(var i =0;i<table_list.length;i++){
                new_list[i] = [];
                for(var j=0;j<spec_list.length;j++){
                    var $key = spec_list[j];
                    if(map_dic[$key] !== ""){
                        new_list[i].push(table_list[i][map_dic[$key]]);
                    }else{
                        new_list[i].push("");
                    }
                }
            }
            return new_list
        },
        select_listener: function(){
            var $this = $(this),
                sub_btn = $("#submit-btn"),
                sub_tip = $(".submit-tip");
            if($this.find("option:selected").text().trim() == "请选择"){
                sub_btn.prop("disabled", true);
                sub_tip.show();
            }else{
                var flag = true;
                $("#map-panel").find("tr[data-re=\"required\"] option:selected").each(function(k, v){
                    if($(v).text().trim() == "请选择"){
                        flag = false;
                        return false
                    }
                });
                sub_btn.prop("disabled", !flag);
                flag ? sub_tip.hide() : sub_tip.show();
            }
        },
        choose_country: function(){
            var $this = $(this),
                is_check = $this.prop("checked"),
                country = $this.attr("data-value"),
                count = 0;
            $("#country-content").find("input").each(function(k, v){
                $(v).prop("checked") && (count += 1);
            });
            if(!is_check && count == 0){
                Inform.enable();
                Inform.show("至少需要选择一个国家");
                $this.prop("checked", true);
            }else{
                var $li = $('li[data-value="'+country+'"]');
                if(is_check){
                    $li.show();
                }else{
                    $li.hide();
                    if($li.attr("class") == "active"){
                        $("#country-set-area").find("li").filter(":visible").eq(0).find("a").trigger("click");
                    }
                }
            }
        },
        import_pro: function(new_list){
            var countries = [],
                country_table = {},
                c_id = "",
                c_name = [];
            if(platform == "Ensogo"){
                var category = $("#CategoryID");
                if(category.attr("data-id") == ""){
                    Inform.enable();
                    Inform.show("请选择目录");
                    return
                }
                c_id = category.attr("data-id");
                c_name = category.text().split(" > ") || [];
                $("#country-content").find(":checkbox").filter(":checked").each(function(k, v){
                    countries.push($(v).attr("data-value"));
                });
                if(countries.length == 0){
                    Inform.enable();
                    Inform.show("请至少选择一个国家");
                    return
                }
                for(var i=0;i<countries.length;i++){
                    var inputs = $('input[data-country="'+countries[i]+'"]');
                    if($(".country-radio :checked").attr("data-value") == "uni"){
                        country_table[countries[i]] = {}
                    }else{
                        var cur_msrp = inputs.eq(0).val().trim(),
                            cur_price = inputs.eq(1).val().trim(),
                            cur_ship = inputs.eq(2).val().trim();
                        var re = /^(\d+(\.\d+)?)$/;
                        if(cur_msrp !== ""){
                            if(!re.test(cur_msrp)){
                                Inform.enable();
                                Inform.show("您填写的国家" + countries[i] + "中的MSRP为无效值");
                                return
                            }
                        }
                        if(cur_price !== ""){
                            if(!re.test(cur_price)){
                                Inform.enable();
                                Inform.show("您填写的国家" + countries[i] + "中的价格为无效值");
                                return
                            }
                        }
                        if(cur_ship !== ""){
                            if(!re.test(cur_ship)){
                                Inform.enable();
                                Inform.show("您填写的国家" + countries[i] + "中的运费为无效值");
                                return
                            }
                        }
                        country_table[countries[i]] = {
                            "msrp": [cur_msrp, inputs.eq(0).closest(".input-group").find("span").attr("data-id")],
                            "price": [cur_price, inputs.eq(1).closest(".input-group").find("span").attr("data-id")],
                            "ship": [cur_ship, inputs.eq(2).closest(".input-group").find("span").attr("data-id")]
                        }
                    }
                }
            }
            Inform.disable();
            Inform.show("", true, "产品导入中...");
            $.ajax({
                url: "/excel/" + $("#shop-id").val() + "/import",
                data: {
                    "table_list": JSON.stringify(new_list),
                    "country_table": JSON.stringify(country_table),
                    "c_id": c_id,
                    "c_name": JSON.stringify(c_name)
                },
                dataType:"json",
                type:"POST",
                success: function(data){
                    if(data.status == 1){
                        Inform.enable(location.href);
                        Inform.show("已成功导入{0}条产品信息".format(data["pro_len"]));
                    }else{
                        Inform.enable(location.href);
                        Inform.show("导入失败，请稍后重试");
                    }
                }
            })
        },
        submit_xls: function(){
            $("#map-panel").find("tr:gt(0)").each(function(k, v){
                var kv = $(v),
                    $key = kv.find("td").eq(0).text().trim(),
                    $value = kv.find("option:selected").text().trim();
                map_dic[$key] = $value == "请选择" ? "" : $value;
            });
            Imp.get_map();
            var new_list = Imp.table_list_handler(table_list);
            for(var i=0;i<new_list.length;i++){
                var $pro = new_list[i];
                for(var j=1;j<9;j++){
                    if($pro[j] === ""){
                        Inform.enable(location.href);
                        Inform.show("您表格中的必要信息未填写完整，请修改后重新上传");
                        return
                    }
                }
            }
            Imp.import_pro(new_list);
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
                    category_group.push(name);
                }else{
                    pop_times = c_level - level + 1;
                    temp_level = level;
                    while(pop_times > 0){
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
                $("#second-choose-category").removeAttr("disabled");
            }else{
                $("#choose-category").attr("disabled","disabled");
                $("#second-choose-category").attr("disabled","disabled");
                if(!cate.hasClass("chosen")){
                    if(c_level < level){
                        category_group.push(name);
                    }else{
                        pop_times = c_level - level + 1;
                        temp_level = level;
                        while(pop_times > 0){
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
                            shop_id: $("#shop-id").val(),
                            parent_id: cate.attr("data-id")
                        },
                        dataType: "json",
                        success: function(data) {
                            if(data["categories"].length > 0){
                                html_str = render_category(data["categories"]);
                                category_dom.html(html_str).removeClass("loading-cate");
                                category_dom.find("li").click(Imp.choose_category);
                                Imp.search_keyup();
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
                        };
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
                Imp.clear_html(this_list);
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
        clear_html: function(obj){
            obj.find("a").each(function(n,o){
                o.innerHTML = o.text;
            })
        },
        category_show: function(){
            var btn = $(this).attr("data-this", 1),
                category_name = btn.closest("div").find(".full-category-name");
            $("#category-tree").modal("show");
            $("#choose-category").on("click.a",function(){
                category_group = [];
                if($("#use-exist").is(":hidden")){
                    $("#category-tree").find(".chosen").each(function(){
                       category_group.push($(this).text());
                    })
                }else{
                    category_group = $(".on").parent().attr("data-names").split(";");
                }
                category_name.attr("data-id", category_uid)
                    .html(category_group.join(" &gt; "));
                btn.removeAttr("data-this");
                $(this).off("click.a");
            });
        },
        cate_li_click: function(){
            var target = $(this),
                un_target = $(this).siblings();
            target.attr("class", "active");
            un_target.attr("class", "");
            $(target.attr("data-target")).show();
            $(un_target.attr("data-target")).hide();
        }
    };
    Imp.init();
});