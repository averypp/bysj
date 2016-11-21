/**
 * Created by yangguoli on 15/5/27.
 */

$(function(){
    var shop_id = $("#shop-id").val(),
        sku_tem = $("#sku-tem"),
        pro_type = $("#ProductType"),
        big_cate_id = "",
        type_flag = true,
        cate_id = "",
        sku_prop = $("#sku-prop"),
        pro_prop = $("#pro-prop"),
        sku_variation = $("#sku-variation"),
        spec_attr = $("#spec-attr"),
        need_trans = false,
        map = {},
        spec_map = [],
        next = 0,
        pic_src = [],
        url_input = "",
        crawl_pic = [],
        $brand_seller = $("input[name=\"is-brand\"]"),
        special_upc = $("#special-upc"),
        brand_seller = "0",
        url_list_length = 0,
        pic_warning = $("#pic-warning");
    var Amazon = {
        init: function(){
            Inform.init();
            $("#product-title").on("input",Amazon.check_title);
            $("#parent-sku").on("input",Amazon.check_parent);
            $("#Quantity").on("input",Amazon.check_quantity);
            $("#choose-category").click(Amazon.choose_cate);
            $("#Description").on("input",Amazon.check_des);
            $("#max-time").on("input",Amazon.check_max);
            $("#StartPrice,#SalePrice").on("input",Amazon.check_price);
            $("#key").find("input").on("input",Amazon.check_keyword);
            $("#bullet-point").find("input").on("input",Amazon.check_bullet);
            $("#pack-info").find("input").on("input",Amazon.check_pack);
            $("#submit-btn").click(Amazon.submit_feed);
            $("#save-btn").click(Amazon.save_feed);
            $("#trans-sub-btn").click(Amazon.trans_feed);
            pro_type.change(Amazon.choose_pro_type);
            sku_tem.change(Amazon.choose_temp);
            $brand_seller.change(Amazon.check_brand);
            sku_prop.on("click",":checkbox",Amazon.checkbox_click);
            sku_prop.on("click","button[name='addSpec']",Amazon.add_spec_click);
            sku_variation.on("click",".batch-set-price",Amazon.batch_sku_price);
            sku_variation.on("click",".batch-set-stock",Amazon.batch_sku_stock);
            $("html").on("click",".bulk-special-upc",Amazon.batch_sku_special_upc);
            $("html").on("click","#commit-set-sale",Amazon.batch_set_sale);
            sku_variation.on("click",".one-btn-sku", Amazon.onekey_sku);
            sku_variation.on("change",".sku-effect",Amazon.effect_sku);
            sku_variation.on("keyup",":text",Amazon.sku_value_change);
            spec_attr.on("change","select",Amazon.sku_map_change);
            $(".form-horizontal").on("change","input,select,textarea",Amazon.must_input_keyup);
            $(".ke-content").keyup(Amazon.des_keyup);
            if($("#product-id").val()){
                big_cate_id = $("#root-id").val();
                Amazon.render_page();
            }
        },
        check_brand: function(){
            var brand_obj = $brand_seller.filter(":checked"),
                $id_type = $(".brand-specifics, .common-upc");
            $("#special-upc, #product-id-type").val("");
            $id_type.toggle(brand_obj.val().trim());
        },
        batch_sku_special_upc: function () {
            var sku_table_tr = $(".table").find("tr");
            sku_table_tr.each(function(i,v){
                var sku = $(v).find(".v-sku").val()?$(v).find(".v-sku").val().trim():""
                $(v).find(".v-upc").val(sku);
            })
        },
        batch_set_sale: function(){
            var flag = true;
            var sale_price = $("#bulk-sale-price");
            var sale_begin = $("#bulk-sale-from");
            var sale_end = $("#bulk-sale-to");
            if(!((/^([\d\.])+$/g).test(sale_price.val().trim()) && parseFloat(sale_price.val().trim()) > 0)){
                flag = false;
                Inform.show("价格不合法!")
            }
            else if(sale_price.val()&&!sale_begin.val()||!sale_end.val()){
                flag = false;
                Inform.show("请将促销信息中的起止日期填写完整!")
            }
            if(!flag){
                return 0
            }else{
                sku_variation.find(".v-sale-price").each(function(k, v){
                    $(v).val(sale_price.val().trim());
                });
                sku_variation.find(".v-sale-begin").each(function(k, v){
                    $(v).val(sale_begin.val().trim());
                });
                sku_variation.find(".v-sale-end").each(function(k, v){
                    $(v).val(sale_end.val().trim());
                });
                $("#bulk-set-sale").modal("hide");
            }
        },
        get_query_string: function(name)
        {
             var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
             var r = window.location.search.substr(1).match(reg);
             if(r!=null)return  unescape(r[2]); return null;
        },
        check_quantity: function(){
            var v = $(this).val().trim().replace(/[^0-9]/g,'');
            $(this).val(v? parseInt(v):"");
        },
        check_des: function(){
            var v = $(this).val().trim();
            if (v.length>2000){
               $(this).val(v.substring(0,2000));
            }
        },
        check_title: function(){
            var v = $(this).val().trim();
            if (v.length>500){
               $(this).val(v.substring(0,500));
            }
        },
        check_parent: function(){
            var v = $(this).val().trim();
            if (v.length>40){
               $(this).val(v.substring(0,40));
            }
        },
        check_max: function(){
            var v = $(this).val().trim().replace(/[^0-9]/g,'');
            v = v? parseInt(v):v;
            $(this).val(v>30? 30 : v);
        },
        check_keyword: function(){
            $(this).val($(this).val().substring(0,50));
        },
        check_bullet: function(){
            $(this).val($(this).val().substring(0,500));
        },
        check_price: function(){
            var v = $(this).val().trim().replace(/[^0-9.]/g,'');
            var nums = v.split(".");
            var int = nums[0];
            if(int.length>18){
                nums[0] = int.substring(0,18);
            }
            $(this).val(nums.join("."));

        },
        check_url: function(str_url){
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
        },
        check_pack: function(){
            var v = $(this).val().trim();
            if(v){
                v = this.id == "lot-mum"? v.replace(/[^0-9]/g,''):v.replace(/^(0)([0-9]+(.[0-9]*)?)/g,function($1,$2,$3){ return $3});
                $(this).css("border-color","").next(".wei-error").remove();
                if(!v.match(/^[0-9]{1,10}(.[0-9]{1,2})?$/gm)){
                    $(this).after("<p class=\"wei-error\">*请输入0.01-1000000000.00之间的数字</p>").css("border-color","red")
                }
                $(this).val(v? v:"");
            }
        },
        choose_cate: function(){
            if($("#use-exist").is(":hidden")){
                big_cate_id = $(".category-area").find("li[class='chosen'][data-level='1']").attr("data-id");
                cate_id = $(".category-area").find("li[class='chosen'][data-leaf='1']").attr("data-id");
                level = $(".category-area").find("li[class='chosen'][data-leaf='1']").attr("data-level");
            }else{
                big_cate_id = $(".on").parent().attr("data-root");
                cate_id = $(".on").parent().attr("data-id");
                level = $(".on").parent().attr("data-level");
            }
            var tpl_id = $('#tpl_id').val();
            $("#category-tree").modal("hide");
            $.ajax({
                url: "/?r=product/get_producttype",//"/amazon/"+shop_id+"/api/product_type",
                type: "GET",
                data:{
                    shopId :shop_id,
                    child_id: cate_id,
                    levelId: level,
                    tpl_id: tpl_id
                },
                dataType: "json",
                success: function(data){
                    if(data.status){
                        sku_prop.empty();
                        pro_prop.empty();
                        sku_variation.empty();
                        special_upc.empty();
                        type_flag = data.flag;
                        pro_type.empty();
                        $("#other-value").val(data.other_value);
                        // $("#item-type").val(data.item_type);
                        var special_upc_option = "<option value=''>请选择</option>";
                        if(data.special_upc){
                            for(var i in data.special_upc){
                                special_upc_option += "<option data-name=\""+data.special_upc[i]+"\">"
                                         +data.special_upc[i]+"</option>";
                            }
                        }
                        console.log(special_upc_option);
                        special_upc.append(special_upc_option);
                        pro_type.append("<option>请选择</option>");

                        // $("#tpl_id").val(data.tpl_id);
                        $("#site_id").val(data.site_id);
                        if (data.product_type){

                            for(var i in data.product_type){
                                var add_html = "<option data-name=\""+data.product_type[i]+"\">"
                                         +data.product_type[i]+"</option>";
                                pro_type.append(add_html);
                            }
                            sku_tem.closest(".form-group").hide();
                            pro_type.closest(".form-group").show();
                        }else{
                            pro_type.closest(".form-group").hide();
                            sku_tem.closest(".form-group").hide();
                            var data = {};
                            //data["root_id"] = big_cate_id;
                            data["tpl_id"] = $("#tpl_id").val();
                            data["site_id"] = $("#site_id").val();
                            data["product_type"] = '';

                            Amazon.get_specifics(data);
                        }
                    }
                }
            })
        },
        choose_pro_type: function(){
            var product_type = $(this).find("option:selected").attr("data-name");
            if(product_type){
                var data = {};
                //data["root_id"] = big_cate_id;
                data["tpl_id"] = $("#tpl_id").val();
                data["site_id"] = $("#site_id").val();
                data["product_type"] = product_type;//type_flag? product_type:"";
                Amazon.get_specifics(data);
            }else{
                sku_tem.closest(".form-group").hide();
            }
        },
        choose_temp: function(){
            var skus = sku_tem.find("option:selected").attr("data-name");
            sku_variation.empty();
            spec_attr.find(".form-group").show();
            spec_map = [];
            sku_prop.find(":checkbox").prop("checked",false);
            sku_prop.find(".form-group").attr("data-select","single").each(function(i,v){
                if($(v).attr("data-need")!="true"){
                    $(v).children("label").find("span").remove();
                }
            });
            if(skus){
                $("#single-attr").hide();
                $("#parent-image").remove("span").text("父产品图片:");
                var sku_list = skus.split(";");//kjdfkdjfksjd
                for(var i in sku_list){
                    for(var j in map){
                        if(map[j]["relation"]==sku_list[i]){
                            spec_map.push(map[j]);
                            spec_attr.find("label[data-name=\""+j+"\"]").closest(".form-group").hide();
                            break;
                        }
                    }
                    var area = sku_prop.find("label[data-name=\""+sku_list[i]+"\"]").closest(".form-group");
                    if(area.attr("data-need")!="true"){
                        area.children("label").prepend("<span class=\"required\">*</span>");
                    }
                    area.attr("data-select","multi");
                }
            }else{
                $("#single-attr").show();
                $("#parent-image").text("").append("<span class=\"required\">*</span>产品图片:");
            }
        },
        get_specifics: function(data){
            //console.log(data);
            $.ajax({
                url: "/?r=product/get_specifics&shopId="+shop_id,//"/amazon/"+shop_id+"/api/get_specifics",
                type: "POST",
                data: data,
                dataType: "json",
                success: function(data){
                    if(data.success == true){
                        sku_prop.empty();
                        pro_prop.empty();
                        sku_variation.empty();
                        spec_attr.empty();
                        map = {};
                        for(var i in data.content){
                            if(data.content[i]["name"] == "VariationTheme"){
                                var temps = data.content[i]["values"];
                                // console.log(temps);
                                sku_tem.empty();
                                sku_tem.append("<option>无</option>");
                                // console.log(temps.length);
                                if(temps.length>0){
                                    for(var j in temps){
                                        var add_html = "<option data-name=\""+temps[j]["relation"].join(";")+"\" data-value = \""+temps[j]["v_name"]+"\">"
                                                 +temps[j]["v_name"]+"</option>";
                                        sku_tem.append(add_html);
                                    }
                                    sku_tem.closest(".form-group").show();
                                }else{
                                    sku_tem.closest(".form-group").hide();
                                    $("#single-attr").show();
                                }//决定参数那里显示  sku false 商品参数   sku true变体信息
                            }else if(data.content[i]["sku"]){
                                var obj  = data.content[i];
                                Amazon.add_pro(obj,"sku-prop");
                            }else{
                                var obj  = data.content[i];
                                Amazon.add_pro(obj,"pro-prop");
                            }
                        }
                        $.each(data.specifics,function(i,v){
                            map[v.name] = v;
                            var form = $("<div/>").attr("class","form-group");
                            var label = $("<label/>").attr("class","col-md-2 control-label ")
                                        .attr("data-name",v.name).text(v.name+":");
                            var select = $("<select/>").attr("class","form-control");
                            if(v.required){
                                label.prepend("<span class='required'>*</span>");
                                select.attr("required",true);
                            }
                            var content = $("<div/>").attr("class","col-md-10 form-inline");
                            var f_option = "<option data-name=\"\">请选择</option>";
                            select.append(f_option);
                            for(var i in v.values){
                                var option = "<option data-name='"+v.values[i]+"'>"+v.values[i]+"</option>";
                                select.append(option);
                            }
                            select.appendTo(content);
                            form.append(label).append(content).appendTo(spec_attr);
                        })
                    }
                }
            })
        },
        add_pro: function(obj,str){
            var attr_area = $("#"+str);
            var area = $("<div/>").attr("class","form-group").attr("data-select","single")
                        .attr("data-need",obj.required).attr("data-type",obj["ShowType"]);
            var label = $("<label/>").attr("class","col-md-3 control-label").attr("data-name",obj.name)
                        .text((obj.display_name||obj.name)+":");
            if(obj.required){
                label.prepend("<span class=\"required\">*</span>");
            }
            var check_area = $("<div/>").attr("class","row");
            var check_content= $("<div/>").attr("class","col-md-9 form-inline");
            if(obj["ShowType"]=="CheckBox"){
                check_area.css("padding-left","15px");
                for(var i in obj["values"]){
                    var add_checkbox ="<div class=\"checkbox\" style=\"width: 210px\"><label><input type=\"checkbox\" "
                            +"class=\"kcb\" name="+obj.name+" data-name=\""+obj["values"][i]+"\">"+obj["values"][i]+"</label></div>";
                    check_area.append(add_checkbox);
                }
                var add_diy = '<div class="col-md-10 col-md-offset-3 form-inline">'
                          +'<input type="text" class="form-control" style="margin-right:8px" placeholder="输入自定义属性"/>'
                          +'<button type="button" class="btn btn-primary"  name="addSpec">添加自定义属性</button></div>';
                area.attr("data-type","CheckBox");
                check_area.appendTo(check_content);
                area.append(label).append(check_content).append(add_diy).appendTo(attr_area);
            }else if(obj["ShowType"]=="List"){
                var add_select = $("<select/>").attr("class","form-control").append("<option >请选择</option>");
                for(var i in obj["values"]){
                    var add_options ="<option data-name=\""+obj["values"][i]+"\">"+obj["values"][i]+"</option>";
                    add_select.append(add_options);
                }
                check_area.append(add_select).appendTo(check_content);
                area.append(label).append(check_content).appendTo(attr_area);
            }else if(obj["ShowType"]=="String"){
                var add_string = "<div class=\"col-md-3\" style=\"padding-left:0\"><input type=\"text\" class=\"form-control\" style=\"width:100%\"/></div>";
                check_area.append(add_string);
                if (obj["unit"]){
                    var add_select = $("<select/>").attr("class","form-control").append("<option>请选择</option>");
                    for( var i in obj["unit"]){
                        var add_options ="<option data-name=\""+obj["unit"][i]+"\">"+obj["unit"][i]+"</option>";
                        add_select.append(add_options);
                    }
                    var add_area = $("<div/>").attr("class","col-md-2").append(add_select);
                    check_area.append(add_area);
                }
                check_area.appendTo(check_content);
                area.attr("data-type","String");
                area.append(label).append(check_content).appendTo(attr_area);
            }
        },
        checkbox_click: function(){
            var self_area = $(this).closest(".form-group");
            var reg = self_area.attr("data-select");
            var need_reg = self_area.attr("data-need");
            var len = self_area.find(":checked").length;
            var name  = self_area.children("label").attr("data-name");
            for( var i in map){
                if (map[i].relation == name){
                    var re_label = spec_attr.find("[data-name="+i+"]");
                    var re_form = re_label.closest(".form-group");
                    if(need_reg!="true"){
                        re_label.find("span").remove();
                        len==0 ? re_form.find("option").eq(0).prop("selected",true).closest("select").change() : re_label.prepend("<span class=\"required\">*</span>");
                    }
                }
            }
            if(reg == "single"){
                $(this).closest(".checkbox").siblings(".checkbox")
                                .find(":checkbox").prop("checked",false);
            }else{
                Amazon.get_table_value();
            }
        },
        add_spec_click: function(){
            var value = $(this).prev().val().trim();
            if(value){
                var name = $(this).closest(".form-group").children("label").attr("data-name");
                var add_checkbox = "<div class=\"checkbox\" style=\"width: 210px\"><label><input type=\"checkbox\" "
                        +"class=\"kcb\" name="+name+" data-name=\""+value+"\">"+value+"</label></div>";
                $(this).closest(".col-md-10").prev().find(".row")
                        .append(add_checkbox).find(":checkbox").eq(-1).click();
            }
        },
        get_table_value: function(){
            var sku_values=[];
            $("#sku-prop").find(".form-group[data-select='multi']").each(function(i,v){
                var this_sku = $(v);
                var checkboxs = this_sku.find(":checkbox:checked");
                var values = [];
                var name = "";
                if(checkboxs.length>0){
                    name  = this_sku.children("label").attr("data-name");
                    for(var i=0;i<checkboxs.length;i++){
                        var value = checkboxs.eq(i).attr("data-name");
                        values.push(value);
                    }
                    sku_values.push({
                        "name": name,
                        "values": values
                    });
                }
            });
            if (sku_values.length>0){
                var variation_html=Amazon.create_table(sku_values);
                sku_variation.html(variation_html);
                $(".date-choose").datetimepicker({
                    format: 'YYYY-MM-DD'
                });
            }else{
                sku_variation.empty();
            }
        },
        create_table: function(sku_values){
            var frame = '<div class="form-group">'
                +'<div class="col-md-12"><div class="form-inline">'
                +'<span style="margin:8px">批量设置价格：<input type="text" class="form-control" id="batch-price"/>'
                +'</span><a class="btn btn-primary batch-set-price" href="javascript:void(0)">确定</a>'
                +'<span style="margin:8px">批量设置库存：<input type="text" class="form-control" id="batch-stock"/>'
                +'</span><a class="btn btn-primary batch-set-stock" href="javascript:void(0)">确定</a>'
                +'<span style="margin:8px">批量设置促销信息:'
                +'</span><a class="btn btn-success" href="javascript:void(0)" data-toggle="modal" data-target="#bulk-set-sale">一键设置</a>'
                +'<span style="margin:8px">批量设置商品编码(只适用于品牌入驻卖家将商品编码与SKU设置一致):'
                +'</span><a class="btn btn-info bulk-special-upc" href="javascript:void(0)">一键设置</a>'
                +'</div></div>'
                +'<div class="col-md-12">{0}</div></div>';
            var sku_attr_str = '<tr class="variation-row"><th style="width: 100px">是否生效</th>{0}{1}<th class="variation-th">价格('+$("#currency").val()+')</th>' +
                '<th class="variation-th">促销价格('+$("#currency").val()+')</th>' +
                '<th>促销开始日期</th><th>促销结束日期</th>' + '<th class="variation-th">库存(件/个)</th><th style="width: 150px">商品编码(UPC/EAN)</th><th>图片URL</th>' +
                '<th style="width: 200px">SKU编码'
                +'<a class="one-btn-sku" id = "onekey-SKU" href="javascript:void(0)">(一键生成SKU)</a></th></tr>';
            var sku_attr = "";
            for(var i=0;i<sku_values.length;i++){
                sku_attr += '<th class="variation-name" style="width: 150px" data-name="{0}">{1}</th>'
                .format(sku_values[i].name, sku_values[i].name);
            }
            var spec_attr = "";
            $.each(spec_map,function(i,v){
                spec_attr += '<th class="spec-name variation-th"data-name="{0}">{1}</th>'
                .format(v.name, v.name);
            });
            sku_attr_str = sku_attr_str.format(sku_attr,spec_attr);
            rows = sku_attr_str;
            Amazon.generate_row([], 0, sku_values.length-1, sku_values);
            var table = '<table class="table table-striped table-bordered">{0}</table>'.format(rows);
            return frame.format(table);
        },
        generate_row: function (record, level, max_level, struc){
            for(var i=0; i<struc[level].values.length;i++){
                record[level] = {
                    name: struc[level]["name"],
                    value: struc[level].values[i]
                };
                if(level==max_level){
                    // console.log("!!!!!!!");
                    var row = '<tr class="variation-row">'
                            +'<td><label><input type="checkbox" class="sku-effect" checked/></label></td>'
                            +'{0}';
                    var spec_td ="";
                    $.each(spec_map,function(i,v){
                        if(v.required){
                            var add_str = '<td><select  data-name = \"'+v.name+'\" class="form-control v-'+v.name+' spec-attr"><option>请选择</option>{0}</select></td>';
                        }else{
                            var add_str = '<td><select  data-name = \"'+v.name+'\" class="form-control v-'+v.name+' spec-attr"><option>请选择</option>{0}</select></td>';
                        }
                        var values="";
                        $.each(v.values,function(m,n){
                            values += "<option data-name=\"" + n + "\">" + n + "</option>";
                        });
                        add_str = add_str.format(values);
                        spec_td += add_str;
                    });
                    row += spec_td;
                    row +=  '<td><input type="text" class="form-control v-price"></td>'
                            +'<td><input type="text" class="form-control v-sale-price sale"></td>'
                            +'<td style="width:160px !important;position:relative"><input type="text" class="form-control v-sale-begin date-choose sale"></td>'
                            +'<td style="width:160px !important;position:relative"><input type="text" class="form-control v-sale-end date-choose sale"></td>'
                            +'<td><input type="text" class="form-control v-stock"></td>'
                           +'<td><input type="text" class="form-control v-upc"></td>'
                            +'<td style="width:268px !important;"><div class="input-group"><input type="text" class="form-control v-pic" readonly="readonly" "><div class="input-group-addon btn btn-primary display-button" data-toggle="modal" data-target="#upModal">图片列表</div></div></td>'
                            +'<td><input type="text" class="form-control v-sku" style="word-break: break-all"></td>'
                            +'</tr>';
                    var sku_attr_str = "";
                    for(var j=0;j<record.length;j++){
                        sku_attr_str += '<td><span data-name="{0}" data-value ="{1}"class="variation-attr" style="word-break: break-all">{2}</span></td>'
                            .format(record[j].name,record[j].value, record[j].value);
                    }
                    rows += row.format(sku_attr_str);
                }else{
                    Amazon.generate_row(record, level+1, max_level, struc)
                }
            }
        },
        batch_sku_price: function(){
            var price = $("#batch-price").val();
            if((/^([\d\.])+$/g).test(price) && parseFloat(price) > 0){
                sku_variation.find(".v-price").each(function(k, v){
                    $(v).val(price);
                });
            }else{
                Inform.show("价格不合法");
            }
        },
        batch_sku_stock: function(){
            var stock = $("#batch-stock").val();
            if((/^(\d)+$/g).test(stock) && parseInt(stock) > 0){
                sku_variation.find(".v-stock").each(function(k, v){
                    $(v).val(stock);
                });
            }else{
                Inform.show("库存不合法");
            }
        },
        onekey_sku : function(){
            var parent_sku = $("#parent-sku").val().trim();
            if(!parent_sku){
                Inform.show("请先将商品的ParentSku填写完整！")
            }else{
                var sku_table_tr = $(".table").find("tr");
                sku_table_tr.each(function(i,v){
                    var v_sku = parent_sku;
                    $(v).find(".variation-attr").each(function(m,n){
                        v_sku += "-"+$(n).text();
                    });
                    $(v).find(".v-sku").val(v_sku);
                })
            }
        },
        submit_feed: function(){
            var reg = Amazon.check_required()&&Amazon.check_sku(),
                option_type = Amazon.get_query_string("type");
            if(!reg){
                return;
            }
            if($("#product-title").val() == ""){
                Inform.show("请填写产品标题");
                return;
            }
            var product_info = Amazon.get_info();
            var product_id = option_type=="use"?"":$("#product-id").val();
            var url = "";
            if(!product_id){
                url = "/?r=product/create-product-to-pub&shopId="+shop_id;//"/create/"+shop_id+"/product/save";
            }else{
                url = "/?r=product/edit-product&shopId="+shop_id;//"/create/"+shop_id+"/product/update";
            }
            Inform.disable();
            Inform.show("", true, "正在保存商品...");
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    "product": JSON.stringify(product),
                    "product_id": product_id,
                    "shopId" : shop_id,
                    "pub_status" : 1
                },
                timeout: 10000,
                success: function(data){
                    data = eval('(' + data + ')');
                    //console.log(data);
                   // console.log(data.success);
                    if(!data.success){
                        Inform.enable();
                        Inform.show(data.message);
                    }else{
                        if(need_trans){
                            Inform.hide();
                            $("#trans-pid").val(data["pid"]);
                            $("#trans-control-modal").modal("show");
                        }else{
                            Inform.enable("/?r=public-product&shopId="+shop_id+"&status=waiting");
                            Inform.show("商品提交成功");
                        }
                        need_trans = false;
                    }
                },
                complete : function(XMLHttpRequest,status){ //请求完成后最终执行参数
                    if(status=='timeout'){//超时,status还有success,error等值的情况
                        Inform.enable();
                        Inform.show("请求超时, 请重试");
                    }
                }
            })
        },
        save_feed: function(){
            var option_type = Amazon.get_query_string("type"),
                product_id = option_type=="use"?"":$("#product-id").val(),
                self = $(this);
            var product_info = Amazon.get_info();
            if($("#product-title").val() == ""){
                Inform.show("请填写产品标题");
                return false;
            }
            if(product_id){
                var url = "/?r=product/edit-product&shopId="+shop_id;//"/create/"+shop_id+"/product/draft";
            } else {
                var url = "/?r=product/create-product&shopId="+shop_id;//"/create/"+shop_id+"/product/draft";
            }
            Inform.disable();
            Inform.show("", true, "正在保存...");
            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: {
                    product: JSON.stringify(product_info),
                    product_id: product_id,
                    shopId : shop_id,
                    pub_status : 0
                },
                success: function(data){
                    if(!data.success){
                        Inform.enable();
                        Inform.show(data.message);
                    }else{
                        if(need_trans){
                            Inform.hide();
                            $("#trans-pid").val(data["pid"]);
                            $("#trans-control-modal").modal("show");
                        }else{
                            //Inform.enable("/product/"+shop_id+"/draft");
                            Inform.enable("/?r=public-product&shopId="+shop_id+"&status=draft");
                            Inform.show("商品信息已经保存到草稿箱");
                        }
                    }
                    need_trans = false;
                },
                complete : function(XMLHttpRequest,status){ //请求完成后最终执行参数
                    if(status=='timeout'){//超时,status还有success,error等值的情况
                        Inform.enable();
                        Inform.show("请求超时, 请重试");
                    }
                }
            });
        },
        trans_feed: function(){
            need_trans = true;
            Amazon.submit_feed();
        },
        get_info: function(){
            var keywords = $("input[name='keyword']"),
                keywords_list = [],
                bulletpoints = $("input[name='bullet_point']"),
                bullet_points = [],
                img_div = $("#feed_img").find("img"),
                is_brand = $brand_seller.filter(":checked").val().trim(),
                picture_urls = [],
                pro_specifics = [],
                pro_skus = [],
                CategoryObj = $("#CategoryID"),
                des = editor.html().replace(/<span/gm,"<p").replace(/<\/span><br \/>/gm,"<\/p>")
                    .replace(/<\/span>/gm,"<\/p>").replace(/<h1|<h2|<h3|<h4/gm,"<b")
                    .replace(/<\/h1>|<\/h2>|<\/h3>|<\/h4>/gm,"<\/b>").replace(/^font-family:.*?;/gm,"")
                    .replace(/style=\".*?\"|class=\".*?\"|id=\".*?\"|<div.*?>|<\/div>/gm, "")
                    .replace(/<p>\s*?<br\s+\/>\s*?<\/p>/gm, "")
                    .replace(/<p \S+?>/gm, "<p>")
                    .replace(/<o:p><\/o:p>/gm, "")
                    .replace(/<ul>|<\/ul>|<li>|<\/li>|<u>|<\/u>/gm, "");
            //获取基本信息
            product["SupplyLink"] = $("#supply-link").val().trim();
            product["Title"] = $("#product-title").val().trim();
            product["ParentSKU"] = $("#parent-sku").val().trim();
            product["Description"] = des;
            product["DispatchTimeMax"] = $("#max-time").val().trim();
            product["SpecialValue"] = $("#other-value").val().trim()?$("#other-value").val().trim():'';
            product["ItemType"] = $("#item-type").val().trim()?$("#item-type").val().trim():'';
            product["Brand"] = $("#brand").val().trim();
            product["Manufacture"] = $("#Manufacture").val().trim();
            if(sku_tem.find("option:selected").attr("data-value")){
                product["VariationTheme"] = sku_tem.find("option:selected").attr("data-value");
            }
            if(pro_type.find("option:selected").attr("data-name")){
                product["ProductType"] = pro_type.find("option:selected").attr("data-name");
            }
            product["CategoryRoot"] = big_cate_id;
            //获取分类信息
            product["Category"]["Name"] = CategoryObj.text().split(">")||[];
            product["Category"]["ID"] = CategoryObj.attr("data-id")||0;
            product["ShippingWeight"] = $("#shipping-weight").val();
            product["WeightUnit"] = $("#WeightUnit").val();
            product["Sale"]["SalePrice"] = $("#SalePrice").val();
            product["Sale"]["SaleDateFrom"] = $("#SaleDateFrom").val();
            product["Sale"]["SaleDateTo"] = $("#SaleDateTo").val();
            product["Condition"]["Name"] = $("#Condition").val();
            product["MSRP"] = $("#MSRP").val().trim();
            //单体信息
            product["UPC"] = $("#UPC").val().trim();
            product["StartPrice"] = $("#StartPrice").val().trim();
            product["Quantity"] = $("#Quantity").val().trim();
            product["tpl_id"] = $("#tpl_id").val();
            // product["level_id"] = $("#leveli_d").val();
            //单体图片
            for(var i=0;i<img_div.length;i++){
                if (img_div.eq(i).attr("src")!="/image/add.png"){
                    picture_urls.push(img_div.eq(i).attr("src"));
                }
            }
            product["PictureURLs"]=picture_urls;
            //获得是否品牌入驻卖家信息
            product["BrandSeller"] = $brand_seller.filter(":checked").val().trim();
            product["ProductIdType"] = is_brand == "1" ? $("#special-upc").val().trim() : $("#product-id-type").val().trim();
            //单体的sizemap信息作为商品属性
            spec_attr.find(".form-group").filter(":visible").each(function(i,v){
                var name = $(v).children("label").attr("data-name");
                var val = $(v).find("option:selected").attr("data-name");
                var value = val? val:"";
                pro_specifics.push({
                    "NameID": "",
                    "Name": name||"",
                    "Value": value||"",
                    "ValueID": ""
                });
            });
            //获取关键词
            for(i=0; i<keywords.length; i++){
                keywords_list.push($(keywords[i]).val());
            }
            product["KeyWords"] = keywords_list;
            //获取BulletPoints
            for(j=0; j<bulletpoints.length; j++){
                bullet_points.push($(bulletpoints[j]).val());
            }
            product["BulletPoints"] = bullet_points;
            //获取商品参数
            $("#pro-prop,#sku-prop").find(".form-group").each(function(i,v){
                var prop = $(v);
                var value = "";
                var name = ""
                if(prop.attr("data-select") != "multi"){
                    name  =  prop.children("label").attr("data-name");
                    if(prop.attr("data-type") =="CheckBox"){
                        value = prop.find(":checkbox:checked").attr("data-name");
                        if(!value){
                            value = "";
                        }
                    }else if(prop.attr("data-type") == "List"){
                        value = prop.find("option:selected").attr("data-name");
                        if(!value){
                            value = "";
                        }
                    }else if(prop.attr("data-type") =="String"){
                        if(prop.find("select").length>0){
                           value = prop.find(":text").val().trim()+"+"+prop.find("option:selected").attr("data-name");
                        }else{
                            value = prop.find(":text").val().trim();
                        }
                    }
                    pro_specifics.push({
                        "NameID": "",
                        "Name": name||"",
                        "Value": value||"",
                        "ValueID": ""
                    });
                }
            });
            product["ProductSpecifics"] = pro_specifics;
            //获取商品sku
            $("table").find("tr").each(function(i,v){
                if(i!=0&&$(v).find(":checkbox").prop("checked")){
                    var var_spec = [];
                    var sku_info = {};
                    $(v).find(".variation-attr").each(function(m,n){
                        var name = $(n).attr("data-name");
                        var value = $(n).attr("data-value");
                        var_spec.push({
                            "NameID": "",
                            "Name": name,
                            "ValueID": "",
                            "Value": value,
                            "Image": []
                        });
                    });
                    sku_info = {
                        "VariationSpecifics": var_spec,
                        "Stock": $(v).find(".v-stock").val().trim(),
                        "Price": $(v).find(".v-price").val().trim(),
                        "SKU": $(v).find(".v-sku").val().trim(),
                        "Sale": {
                            "SalePrice": $(v).find(".v-sale-price").val().trim(),
                            "SaleDateFrom": $(v).find(".v-sale-begin").val().trim(),
                            "SaleDateTo": $(v).find(".v-sale-end").val().trim()
                        },
                        "UPC": $(v).find(".v-upc").val().trim(),
                        "PictureURL": $(v).find(".v-pic").val().trim().split(";")
                    };
                    $(v).find(".spec-attr").each(function(a,b){
                        var name = $(b).attr("data-name");
                        var val = $(b).find("option:selected").attr("data-name");
                        var value = val? val:"";
                        sku_info[name] = value;
                    });
                    pro_skus.push(sku_info);
                }
            });
            product["ProductSKUs"] = pro_skus;
            return product
        },
        check_required: function(){
            var flag = true;
            var tip_html_str = "<p class='blank'>*此项为必输项</p>";
            var scroll_height = 10000;
            var spec_div = $(".required").filter(":visible");
            spec_div.each(function(i,v){
                var check_form = $(v).closest(".form-group");
                if(check_form.find(":checkbox").length>0||check_form.attr("data-type")=="CheckBox"){
                    var checked_box = check_form.find("input:checked");
                    if (checked_box.length==0){
                        $(v).closest("label").find(".blank").remove();
                        $("<p/>").addClass("blank").html("*此项为必输项").appendTo($(v).closest("label"));
                        $(v).closest(".form-group").find("input:checkbox");
                        flag = false;
                        var height_t = $(v).offset().top;
                        scroll_height = scroll_height < height_t? scroll_height:height_t;
                    }
                }
                if(check_form.find("select").length>0){
                    var check_select = check_form.find("select");
                    if(!check_select.find(":selected").attr("value")&&!check_select.find(":selected").attr("data-id")&&!check_select.find(":selected").attr("data-name")){
                        check_select.next(".blank").remove();
                        check_select.after(tip_html_str).css("border-color","red");
                        flag = false;
                        var height_t = $(v).offset().top;
                        scroll_height = scroll_height < height_t? scroll_height:height_t;
                    }
                }
                if(check_form.find(".col-md-10").eq(0).find(":text").filter(":visible").length>0){
                    var this_str = check_form.find(".col-md-10").eq(0).find(":text").filter(":visible"),
                        $b = check_form.find("#bullet-point");
                    if($b.length>0){
                        this_str.css("border-color","rgb(204, 204, 204)").next(".blank").remove();
                        var reg = 0;
                        this_str.each(function(){
                            if($(this).val().trim()!=''){
                                reg = 1;
                                return 0;
                            }
                        });
                        if(!reg){
                            this_str.after(tip_html_str).css("border-color","red");
                        }
                    }else{
                        /*this_str.each(function(i,v){
                            if($(v).val().trim()==''){
                                $(v).next(".blank").remove();
                                $(v).after(tip_html_str).css("border-color","red");
                                flag = false;
                                var height_t = $(v).offset().top;
                                scroll_height = scroll_height < height_t? scroll_height:height_t;
                            }
                        })*/
                    }
                }
            });
            if(!Amazon.check_sale()){
                flag = false;
                $("#SalePrice").closest(".material").find("input").change(Amazon.check_sale);
                var height_t = $("#SalePrice").offset().top;
                scroll_height = scroll_height < height_t? scroll_height:height_t;
            }
            editor.html().trim()||(function(){
                $(".ke-container").next(".blank").remove();
                $(".ke-container").after(tip_html_str);
                flag = false;
                var height_t = $("#CategoryID").offset().top;
                scroll_height = scroll_height < height_t? scroll_height:height_t;
            })();
            if($("#shipping-weight").val().trim()&&!$("#WeightUnit").val().trim()){
                flag = false;
                $("#WeightUnit").css("border-color", "red");
            }
            if($("#CategoryID").attr("data-id") == ""){
                Inform.show("请选择商品分类");
                flag = false;
                var height_t = $("#CategoryID").offset().top;
                scroll_height = scroll_height < height_t? scroll_height:height_t;
            }

            if(!flag){
                $("html,body").animate({scrollTop:scroll_height},300);
            }
            return flag
        },
        check_sale: function(){
            var sale_input = $("#SalePrice");
            var sale_price = sale_input.val().trim();
            var tip_html_str = "<p class='blank'>*数据输入不完整</p>";
            sale_input.closest(".material").css("border-color","").next(".blank").remove();
            if(sale_price&&$("#SalePrice").closest(".material").is(":visible")){
                if($("#SaleDateFrom").val().trim()&&$("#SaleDateTo").val().trim()){
                    return true
                }else{
                    sale_input.closest(".material").after(tip_html_str).css("border-color","red");
                    return false
                }
            }else{
                return true
            }
        },
        check_sku: function(){
            var f = true;
            var table = sku_variation.find("table");
            if (table.length>0&&table.find(":checkbox:checked").length>0){
                table.find(".variation-row").each(function(a,b){
                    if(a!=0&&$(this).find(":checkbox").prop("checked")){
                        var reg = true;
                        $(b).find(":text").each(function(){
                            if(!$(this).hasClass("sale")&&!$(this).val().trim()){
                                $(this).addClass("error");
                                reg = false;
                            }
                        });

                        $(b).find(".spec-attr").each(function(){
                            if(!$(this).find("option:selected").attr("data-name")){
                                $(this).addClass("error");
                                reg = false;
                            }
                        });
                        if($(b).find(".v-sale-price").val().trim()){
                            $(b).find(".date-choose").each(function () {
                                if(!$(this).val().trim()){
                                    f = false;
                                    $(this).addClass("error");
                                }
                            });
                        }
                        if(!reg){
                            f = false;
                        }
                    }
                })
            }else if(table.length>0&&table.find(":checkbox:checked").length==0){
                f = false;
            }
            if(!f){
                Inform.show("变体信息不完整,至少应有一条变体信息")
            }
            return f
        },
        render_page: function(){
            var product_id = $("#product-id").val();
            if(product_id == ""){
                //console.log("product_id is null");
                return 0;
            }
            //console.log("product_id is not null");
            $.ajax({
                "url": "/?r=product/get-good-info&shopId="+shop_id,//"/create/" + $("#shop-id").val() + "/product/get",
                "type": "GET",
                "dataType": "json",
                "data": "goodId=" + product_id,
                "success": function(data){
                    // console.log(data);
                    product["SourceInfo"] = data["product"]["SourceInfo"];
                    product["ProductSpecifics"] = data["product"]["ProductSpecifics"];
                    product["ProductSKUs"] = data["product"]["ProductSKUs"];
                    crawl_pic = $(".collection-pic")?data["product"]["CrawlImage"]:[];
                    brand_seller = data["product"]["BrandSeller"];
                    // console.log(product["SourceInfo"]);
                    $("#other-value").val(data["product"]["SpecialValue"]);
                    $("#item-type").val(data["product"]["ItemType"]);
                    map = {};
                    type_flag = data["flag"];
                    $.each(data["specifics"],function(i,v){
                            map[v.name] = v;
                            var form = $("<div/>").attr("class","form-group");
                            var label = $("<label/>").attr("class","col-md-2 control-label ")
                                        .attr("data-name",v.name).text(v.name+":");
                            var select = $("<select/>").attr("class","form-control");
                            if(v.required){
                                label.prepend("<span class='required'>*</span>");
                                select.attr("required",true);
                            }
                            var content = $("<div/>").attr("class","col-md-10 form-inline");
                            var f_option = "<option data-name=\"\">请选择</option>";
                            select.append(f_option);
                            for( var i in v.values){
                                var option = "<option data-name='"+v.values[i]+"'>"+v.values[i]+"</option>";
                                select.append(option);
                            }
                            select.appendTo(content);
                            form.append(label).append(content).appendTo(spec_attr);
                        });
                    sku_tem.change();
                    Amazon.render_common();
                    Amazon.render_pro();
                    Amazon.render_sku();
                },
                "error": function(){
                    //console.log("there is some error happened");
                }
            })
        },
        render_common: function () {
            var $b_specfics = $(".brand-specifics"),
                $c_specifics = $(".common-upc");
            // console.log(brand_seller);
            if(brand_seller == 1){
                $b_specfics.show();
                $c_specifics.hide();
            }else{
                $b_specfics.hide();
                $c_specifics.show();
            }

        },
        render_pro: function(){
            for(var i=0;i<product["ProductSpecifics"].length;i++){
                var writing_specifics = product["ProductSpecifics"][i];
                var Name = writing_specifics["Name"];
                if($("label[data-name=\""+Name+"\"]").length>0){
                    var find_check =  $("label[data-name=\""+String(writing_specifics["Name"])+"\"]").siblings(".form-inline")
                            .find("[data-name=\""+String(writing_specifics["Value"])+"\"]");
                    if (find_check.length>0){
                         find_check.prop("selected",true).change().click();
                    }else if($("label[data-name=\""+Name+"\"]").closest(".form-group").attr("data-type")=="String"){
                        var value_content =  $("label[data-name=\""+String(writing_specifics["Name"])+"\"]").siblings(".form-inline");
                        if(value_content.find("select").length>0){
                            var val_list = writing_specifics["Value"].split("+");
                            value_content.find("select").find("option[data-name='"+val_list[1]+"']").prop("selected",true);
                            value_content.find(":text").val(val_list[0]);
                        }else{
                            value_content.find(":text").val(writing_specifics["Value"]);
                        }
                    }else{
                        if (writing_specifics['Value']){
                            var name = writing_specifics['Value'];
                            var add_div = $("<div/>").attr("class","checkbox").css("width","210px")
                                    .html("<label><input type='checkbox' class='kcb' data-name=\""+name+"\"/>"+name+"</label></div>");
                            var area = $("label[data-name=\""+writing_specifics['Name']+"\"]").closest(".form-group").find(".col-md-10 ").eq(0).find(".row");
                            add_div.appendTo(area).find(":checkbox").click();
                        }
                    }
                }
            }
        },
        render_sku: function(){
            for(var j=0;j<product["ProductSKUs"].length;j++){
                var sku = product["ProductSKUs"][j];
                for(var k=0;k<sku["VariationSpecifics"].length;k++){
                    var sku_spec = sku["VariationSpecifics"][k];
                    var find_check = $("label[data-name=\""+sku_spec['Name']+"\"]").siblings(".form-inline")
                            .find("[data-name=\""+sku_spec['Value']+"\"]");
                            // console.log(find_check);
                    if(find_check.length>0){
                        find_check.prop("checked",true).prop("selected",true);
                    }else{
                        if(sku_spec['Value']){
                            var name = sku_spec['Value'];
                            var add_div = $("<div/>").attr("class","checkbox").css("width","210px")
                                    .html("<label><input type='checkbox' class='kcb' data-name=\""+name+"\"  checked/>"+name+"</label></div>");
                            var area = $("label[data-name=\""+sku_spec['Name']+"\"]").closest(".form-group").find(".col-md-10 ").eq(0);
                            add_div.appendTo(area);
                        }
                    }
                }
            }
            Amazon.get_table_value();
            Amazon.render_sku_value();
        },
        render_sku_value: function(){
            sku_variation.find(".variation-row").each(function(k, v){
                var row = $(v);
                if(k > 0){
                    var attrs = [];
                    row.find(".variation-attr").each(function(m, n){
                        attrs.push($(n).attr("data-value"));
                    });
                    var pu = Amazon.get_variation_content(attrs);
                    if(pu.SKU){
                        row.find(".spec-attr").each(function(m, n){
                            $(n).find("option[data-name=\""+pu[$(n).attr("data-name")]+"\"]").prop("selected",true);
                        });
                        var price = pu["Price"];
                        var stock = pu["Stock"];
                        var sku = pu["SKU"];
                        var pic = pu["PictureURL"];
                        // console.log(pu);
                        var sale_price = pu["Sale"]?pu["Sale"]["SalePrice"]: "";
                        var sale_begin = pu["Sale"]?pu["Sale"]["SaleDateFrom"]: "";
                        var sale_end = pu["Sale"]?pu["Sale"]["SaleDateTo"]: "";
                        var upc = pu["UPC"];
                        row.find(".v-price").val(price);
                        row.find(".v-sale-price").val(sale_price);
                        row.find(".v-sale-begin").val(sale_begin);
                        row.find(".v-sale-end").val(sale_end);
                        row.find(".v-stock").val(stock);
                        row.find(".v-sku").val(sku);
                        row.find(".v-upc").val(upc);
                        row.find(".v-pic").val(pic.join(";"));
                    }else{
                        row.find(".sku-effect").click();
                    }
                }
            });
        },
        get_variation_content: function(attrs){
            // console.log(attrs);
            var product_sku, flag;
            for(var i=0;i<product["ProductSKUs"].length;i++){
                product_sku = product["ProductSKUs"][i];
                flag = true;
                for(var j=0;j<attrs.length;j++){
                    if(!Amazon.contains(product_sku["VariationSpecifics"], attrs[j])){
                        flag = false;
                        break;
                    }
                }
                if(flag){
                    // console.log("87879897979");
                    // console.log(product_sku);
                    return product_sku;
                }
            }
            return flag;
        },
        contains: function(parent, child){
            for(var m=0;m<parent.length;m++){
                if(child == parent[m]["Value"]){
                    return true;
                }
            }
            return false;
        },
        must_input_keyup : function(){
            if(($(this).attr("type")=="text"||this.id=="Description")&&$(this).val().trim()!=""){
                $(this).css("border-color","");
                $(this).next(".blank").remove();
            }else if($(this).find("option").length>0&&(!$(this).attr("data-id")||!$(this).attr("data-name")||!$(this).attr("value"))){
                $(this).css("border-color","");
                $(this).next(".blank").remove();
            }else if ($(this).closest(".form-group").find("input:checked").length>0){
                $(this).closest(".form-group").find(".blank").remove();
            }
        },
        des_keyup: function(){
            if($(this).html().trim()){
                $(".ke-container").next(".blank").remove();
            }
        },
        effect_sku: function(){
            if($(this).prop("checked")){
                $(this).closest("tr").removeClass("no-effect").find(":text").removeAttr("disabled");
            }else{
                $(this).closest("tr").addClass("no-effect").find(":text").attr("disabled","disabled").removeClass("error");
            }
        },
        sku_value_change: function(){
            // console.log("1211");
            if($(this).val().trim()){
                $(this).removeClass("error");
            }
        },
        sku_map_change: function(){
            var k = $(this).closest(".form-group").children("label").attr("data-name");
            var v = $(this).find("option:selected").attr("data-name");
            var re_name = map[k]["relation"];
            var re_label = sku_prop.find("[data-name = "+ re_name+"]");
            var form = re_label.closest(".form-group");
            if(form.attr("data-need")!="true"){
                re_label.find("span").remove();
                v&&re_label.prepend("<span class=\"required\">*</span>");
            }
        }

    };
    var Image = {
        init: function(){
            $("#sku-variation").on("click",".display-button",Image.pic_display);
            $(".web-pic").bind("click",Image.web_pic_choose);
            $(".web-pic").bind("click",Image.remove_div_btn);
            $("#web-pic-ensure-button").bind("click",Image.web_pic_ensure);
            $(".pic-space").bind("click",Image.get_img_info);
            $(".pic-space").bind("click",Image.remove_div_btn);
            $("#pic-space-ensure-button").bind("click",Image.pic_space_ensure);
            $(".del-pic-mod").bind("click",Image.del_pic_mod);
            $(".local-pic").bind("click",Image.tips);
            $("#apply-btn").bind("click",Image.apply_to_other);
            $(".set-main-pic-mod").bind("click",Image.set_main_pic);
            $(".show-pic").bind("click",Image.show_existing_pic);
            $(".show-pic").bind("click",Image.remove_div_btn);
            $(".collection-pic").bind("click",Image.show_collection_pic);
            $("#collction-ensure-button").bind("click",Image.collection_ensure);
            $(".collection-area-pic").on("click",".photo-checkbox",Image.check_coll_pic)
                .on("click",".picture",function(){
                    $(this).find(".photo-checkbox").trigger("click");
                });
            $(document).ready(Image.nav_change)
        },
        //切换标签active
        nav_change:function(){
            $("#upModal").find('ul.nav > li').click(function (e) {
                e.preventDefault();
                $("#upModal").find('ul.nav > li').removeClass('active');
                $(this).addClass('active');
            });
        },
        set_progress: function(now,max){
            var rate = parseInt(now/max*100);
            $(".progress-bar").attr({
                "aria-valuenow": now,
                "aria-valuemax": max,
                "style": "width: "+rate+"%;"
            }).text((rate>100?100:rate)+"%");
        },
        pic_render:function(){
             for(var i=0;i<pic_src.length;i++){
                 $(".pic-display").find("img[data-index="+i+"]").attr("src",pic_src[i]);
             }
             for(var i=pic_src.length;i<9;i++){
                 $(".pic-display").find("img[data-index="+i+"]").attr("src","/image/add.png");
             }
            $(".pic-display").show();
            $(".pic-space-area").hide();
            $(".pic-url").hide();
            $(".url-area").hide();
            $(".my-modal-footer").show();
        },
        tips:function(){
            if(url_list_length>8){
                $("#upload-tips").show();
            }else{
                $("#upload-tips").hide();
            }
            $(".pic-space-area").hide();
            $(".pic-display").show();
            $(".my-modal-footer").show();
            $(".url-area").hide();
            $(".pic-url").hide();
        },
        pic_display:function(){
            $('ul.nav > li').removeClass('active');
            $(".show-pic").parent().addClass('active');
            var add_button = $("#web-pic-ensure-button");
            if($(this).parent().find(".v-pic").val()!=""){
                pic_src = $(this).parent().find(".v-pic").val().split(";");
            }else{
                pic_src = [];
            }
            url_input = $(this).parent().find(".v-pic");
            if(pic_src[0] != ""){
                url_list_length = pic_src.length;
                for(var i=0;i<pic_src.length;i++){
                    $("img[data-index="+i+"]").attr("src",pic_src[i]);
                }
                for(var i=pic_src.length;i<9;i++){
                    $(".pic-display").find("img[data-index="+i+"]").attr("src","/image/add.png");
                }
            }
            $(".pic-display").show();
            $(".my-modal-footer").show();
            $(".pic-space-area").hide();
            $(".collection-area").hide();
            pic_warning.hide();
            $(".url-area").hide();
            $(".pic-url").hide();
            $("#upload-tips").hide();
            var sku_pro_list = $("#sku-tem").find("option:selected").attr("data-name").split(";");
            var $sku_list = $("#sku_list").empty();
            for(var m=0;m<sku_pro_list.length;m++){
                $sku_list.append($("<option/>").attr("value",sku_pro_list[m]).text(sku_pro_list[m]));
            }
            $sku_list.append($("<option/>").attr("value", "all").text("所有变体"));
        },
        show_existing_pic:function(){
            $(".pic-url").hide();
            $(".my-modal-footer").show();
            if($(".pic-display").is(":hidden")){
               Image.pic_render() ;
               $("#web-pic-ensure-button").hide();
            }
            $("#upload-tips").hide();
            $(".collection-area").hide();
            pic_warning.hide();
        },
        web_pic_choose:function(){
            $(".url-area").remove();
            $(".pic-url").find(".web-tips").remove();
            $(".pic-space-area").hide();
            $(".collection-area").hide();
            pic_warning.hide();
            var pic_url = $(".pic-url");
            var add_button = $("#web-pic-ensure-button");
            var add_url =  '<div class="input-group url-area" style="margin-left:15px;margin-bottom:10px">'+
                                '<div class="input-group-addon">http://</div>'+
                                '<input type="text" name="pic-url-input" class="form-control" style="width: 440px">'+
                           '</div>';
            var pic_num = 0;
            $(".pic-display").find("img").each(function(k,v){
                if($(v).attr("src")!="/image/add.png"){
                    pic_num++;
                }
            });
            url_list_length = pic_num;
            if(pic_num<9){
                for(var i=0;i<9-pic_num;i++){
                add_button.before(add_url);
                $("#web-pic-ensure-button").show();
                }
            }else{
                add_button.before('<div style="margin-left:320px" class="web-tips">'+
                                        '<label style="color:rgb(245, 109, 0);font-size: 15px;margin-top:20px">图片数量已达上限，请删除部分后再进行选取!</label>'+
                                  '</div>');
                $("#web-pic-ensure-button").hide();
            }

            if(pic_url.is(":hidden")){
                $(".pic-display").hide();
                pic_url.show();
                $(".my-modal-footer").hide();
            }else{
                $(".pic-display").show();
                pic_url.hide();
                $("#web-pic-ensure-button").hide();
                $(".my-modal-footer").show();
            }
            $("#upload-tips").hide();
        },
        remove_div_btn:function(){
	        $(this).closest("ul").siblings("div").find("a").removeClass("btn btn-primary");
	    },
        web_pic_ensure:function(){
            var url_list = [];
            $(".pic-url").find("input[name='pic-url-input']").each(function(k,v){
                url_list.push($(v).val());
            });
            for(var i=0;i<(9-url_list_length);i++){
                var k = 0;
                var j = k+i+url_list_length;
                if(url_list[i]!=""){
                    $("img[data-index="+j+"]").attr("src",url_list[i]);
                    pic_src.push(url_list[i]);
                }else{
                    k--;
                }
            }
            url_list_length = pic_src.length;
            url_input.val(pic_src.join(";"));
            $(".pic-display").show();
            $(".pic-url").hide();
            $(".url-area").hide();
            $(".my-modal-footer").show();
            $("#web-pic-ensure-button").hide();
            $('ul.nav > li').removeClass('active');
            $(".show-pic").parent().addClass('active');
        },
        check_select_num:function(){
            var already_select = $("#already-select-mod");
            var selected_num = already_select.text();
            var max_select = $("#max-select-mod").text();
            if (this.checked){
                if(selected_num<max_select){
                    already_select.text(Number(selected_num) + 1);
                }else{
                    $(this).removeAttr("checked")
                }
            }else{
                already_select.text(Number(selected_num) - 1);
            }
        },
        get_img_info:function(){
            var image_content_mod = $("#img-content-mod");
            $("#max-select-mod").text(9-url_list_length);
            $("#already-select-mod").text(0);
            $(".pic-space-area").find("input[type=checkbox]").prop("checked", false);
            if($(".pic-space-area").is(":hidden")){
                $(".pic-space-area").show();
                $(".pic-display").hide();
                $(".pic-url").hide();
                $("#web-pic-ensure-button").hide();
                $(".my-modal-footer").hide();
            }else{
                $(".pic-space-area").hide();
                $(".pic-display").show();
                $(".my-modal-footer").show();
            }
            $("#upload-tips").hide();
            $(".collection-area").hide();
            pic_warning.hide();
            $("#upModal").find("input[type=checkbox]").unbind("click");

        },
        set_main_pic:function(){
            var $this = $(this);
            var pic_to_set = $this.closest(".thumbnail").find("img").attr("src");
            var data_index = $this.attr("data-index");
            if(pic_to_set != "/image/add.png"){
                $this.closest(".thumbnail").find("img").attr("src",$(".pic-display").find("img[data-index=0]").attr("src"));
                pic_src[data_index] = $(".pic-display").find("img[data-index=0]").attr("src");
                $(".pic-display").find("img[data-index=0]").attr("src",pic_to_set);
                pic_src[0] = pic_to_set;
                url_input.val(pic_src.join(";"));
            }
        },
        pic_space_ensure:function(){
            var checked = $("#sortable-sku").find("img");
            var checked_num = checked.length;
            for(var i=0;i<checked_num;i++){
                var net_url = checked.eq(i).attr("src");
                var url_list = [];
                url_list.push(net_url);
                pic_src.push(net_url);
                url_input.val(pic_src.join(";"));
                url_list_length = pic_src.length;
            }
            if($(this).parent().find(".v-pic").val()){
                pic_src = $(this).parent().find(".v-pic").val().split(";");
            }
            if(pic_src[0] != ""){
                for(var i=0;i<pic_src.length;i++){
                    $("img[data-index="+i+"]").attr("src",pic_src[i]);
                }
            }
            $(".pic-display").show();
            $(".pic-space-area").hide();
            $(".my-modal-footer").show();
            $('ul.nav > li').removeClass('active');
            $(".show-pic").parent().addClass('active');
        },
        del_pic_mod:function() {
            if ($(this).closest(".thumbnail").find("img").attr("src") != "/image/add.png") {
                var cur_dom = $(this);
                cur_dom.closest(".thumbnail").find("img").attr("src", "/image/add.png");
                pic_src = [];
                url_list_length = 0;
                $(".pic-display").find("img").each(function(k,v){
                    if($(v).attr("src")!="/image/add.png"){
                        pic_src.push($(v).attr("src"));
                        url_list_length++;
                    }
                });
                url_input.val(pic_src.join(";"));
                if(pic_src[0] != ""){
                    for(var i=0;i<pic_src.length;i++){
                        $(".pic-display").find("img[data-index="+i+"]").attr("src",pic_src[i]);
                    }
                    for(var i=pic_src.length;i<9;i++){
                       $(".pic-display").find("img[data-index="+i+"]").attr("src","/image/add.png");
                    }
                }
                $("#upload-tips").hide();
            }
        },
        apply_to_other: function () {
            var $name = $("#sku_list").val(),
                $tr = url_input.closest("tr"),
                $table = $tr.closest("table"),
                $value = $tr.find("[data-name=\""+$name+"\"]").attr("data-value"),
                tar_tr_list = $("[data-name=\""+$name+"\"][data-value=\""+$value+"\"]").closest("tr");
            if($name == "all"){
                tar_tr_list = $table.find("tr");
            }
            tar_tr_list.each(function(){
                $(this).find(".v-pic").val(url_input.val())
            })
        },
        show_collection_pic:function(){
            coll_check_length = 0;
            var $area = $(".collection-area"),
                pic_area = $(".collection-area-pic");
            if($area.is(":hidden")){
                $area.show();
                pic_warning.show();
                $("#max-select-coll").text(9-url_list_length);
                $("#already-select-coll").text(0);
                $(".pic-space-area").hide();
                $(".pic-display").hide();
                $(".pic-url").hide();
                $(".my-modal-footer").hide();
            }else{
                $area.hide();
                pic_warning.hide();
                $('ul.nav > li').removeClass('active');
                $(".show-pic").parent().addClass('active');
                $(".pic-display").show();
                $(".my-modal-footer").show();
            }
            $("#upload-tips").hide();
            $("#upModal").find("input[type=checkbox]").unbind("click");
            pic_area.find(".picture").remove();
            if(crawl_pic.length==0){
                $("#no-collection-tip").show();
            }else{
                $("#no-collection-tip").hide();
                var w = "";
                var h = "";
                for(var i=0;i<crawl_pic.length;i++){
                    var img_str = '<div class="picture">'+
                                    '<div class="photo-operate">'+
                                    '<label><input type="checkbox" class="photo-checkbox"></label></div>'+
                                    '<div class="img-content">'+
                                    '<img class="image" src={0}></div>'+
                                    '</div>';
                    img_str = img_str.format(crawl_pic[i]);
                    pic_area.append(img_str);
                }
            }

        },
        check_coll_pic:function(e){
            e.stopPropagation();
            var $this = $(this),
                is_check = $this.prop("checked");
            is_check ? $this.siblings(".operate-btns").show() : $this.siblings(".operate-btns").hide();
            var check_text = $("#already-select-coll");
            if(coll_check_length<Number($("#max-select-coll").text())){
                is_check ? check_text.text(++coll_check_length) : check_text.text(--coll_check_length);
            }else{
                is_check ? $this.removeAttr("checked") : check_text.text(--coll_check_length);
            }

        },
        collection_ensure:function(){
            $(".collection-area-pic").find(":checked").each(function(k,v){
                var kv = $(v);
                var net_url = kv.closest(".picture").find(".image").attr("src");
                var url_list = [];
                url_list.push(net_url);
                pic_src.push(net_url);
                url_input.val(pic_src.join(";"));
                url_list_length = pic_src.length;
            });
            if(pic_src[0] != ""){
                for(var i=0;i<pic_src.length;i++){
                    $("img[data-index="+i+"]").attr("src",pic_src[i]);
                }
            }
            $(".pic-display").show();
            $(".collection-area").hide();
            $(".my-modal-footer").show();
            $('ul.nav > li').removeClass('active');
            $(".show-pic").parent().addClass('active');
        }
    };
    Image.init();
    Amazon.init();
    $('#local-pic').Huploadify({
        auto:true,
        fileTypeExts:'*.jpg;*.png;*.gif',
        multi:true,
        count:9,
        fileObjName:'Filedata',
        fileSizeLimit:9999,
        showUploadedPercent:true,//是否实时显示上传的百分比，如20%
        showUploadedSize:true,
        removeTimeout:2000,
        buttonText:'本地图片选取',//上传按钮上的文字
        buttonClass:'',//上传按钮上的文字
        // uploader:"/picture/upload/local",
        uploader:"/?r=product/upload-image&shopId="+shop_id,
        onUploadStart:function(){
            console.log('开始上传');

        },
        onInit:function(){
            console.log('初始化');
            },
        onUploadComplete:function(file,data,response){
           $("#s-num").text(parseInt($("#s-num").text())+1);
                data = eval("("+data+")");
                if(data["success"] == true) {
                    pic_src.push(data["url"]);
                    url_input.val(pic_src.join(";"));
                    if(pic_src[0] != ""){
                        for(var i=0;i<pic_src.length;i++){
                            $("img[data-index="+i+"]").attr("src",pic_src[i]);
                        }
                    }
                    url_list_length = pic_src.length;
                }
        },
        onDelete:function(file){
            console.log('删除的文件：'+file);
            console.log(file);
        }
    });
});


