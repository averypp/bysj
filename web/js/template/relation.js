/**
 * Created by GF on 2015/11/23.
 */
$(function(){
    var $drop = $("#drop-area"),
        spr_font_color = $("input[name=\"spr-font-colors\"]"),
        spr_back_color = $("input[name=\"spr-back-colors\"]"),
        pre_title_text = $("#pre-title-text"),
        pre_title = $("#pre-title");
    var s_option = "T";
    var Relation = {
        init:function(){
            Inform.init();
            $(function(){
                var html_str = $("#drop-html").val();
                $("#show-col").val($("#table-col").val());
                $("#show-row").val($("#table-row").val());
                if(html_str){
                    $drop.children().remove();
                    $drop.html(html_str);
                    $drop = $("#drop-area");
                    spr_font_color = $("input[name=\"spr-font-colors\"]");
                    spr_back_color = $("input[name=\"spr-back-colors\"]");
                    pre_title_text = $("#pre-title-text");
                    pre_title = $("#pre-title");
                }
            });
            $(".pick-a-color").pickAColor({
                showSpectrum: false,
                showSavedColors: true,
                saveColorsPerElement: true,
                fadeMenuToggle: true,
                showAdvanced: true,
                showBasicColors: true,
                showHexInput: true,
                allowBlank: false,
                inlineDropdown: false
            });
//            $("#hide-title").change(function(){
//                $(this).prop("checked") ? $(".spr-img-t").hide() : $(".spr-img-t").show();
//            });
            $("#search-pro").click(Relation.search_pro);
            $drop.on("mouseover",".pre-pic",function(){
                $(this).find(".delete").show();
            });
            $drop.on("mouseleave",".pre-pic",function(){
                $(this).find(".delete").hide();
            });
            $("input[type=\"color\"]").click(Relation.color_focus)
                                        .blur(Relation.color_blur);
            $(document).on("dragenter",".pro-image",Relation.drag_add_pro);
            $(document).on("dragstart",".pro-image",function(e){
                $(this).css("cursor","move");
            });
            $("#pro-list-content").on("click",".add-pro-icon",Relation.click_add_pro);
            $("#pre-content").on("click",".delete",Relation.delete_pic);
            $("#show-col").change(Relation.generate_table);
            $("#show-row").change(Relation.generate_table);
            spr_font_color.on("change",Relation.pre_title_color);
            spr_back_color.on("change",Relation.pre_title_bgd);
            $("#spr-title").keyup(Relation.pre_title_text);
            $("#spr-font-size").change(function(){
                var $value = $(this).val();
                pre_title_text.css("font-size",$value);
                if($.inArray($value,["14px","16px","18px","20px"])!=-1){
                    pre_title_text.css("line-height","30px");
                }else if($value=="24px"){
                    pre_title_text.css("line-height","40px");
                }else{
                    pre_title_text.css("line-height","50px");
                }
            });
            $("#spr-border").change(function(){
                $(this).prop("checked") ? pre_title_text.css("font-weight",700) : pre_title_text.css("font-weight",400);
            });
            $("#hide-title").change(function(){
               $(this).prop("checked") ? $(".pre-spr-title").css("display", "none") : $(".pre-spr-title").css("display", "inline");
            });
            $("input[name=pro-back-colors]").change(function(){
                $(".pre-spr-title").css("background-color","#"+$(this).val());
            });
            $("input[name=pro-font-colors]").change(function(){
                $(".pre-spr-title").css("color","#"+$(this).val());
            });
            $("#save-template").click(Relation.save_template);
            $("#search-options").find("a").click(Relation.choose_option);
            /*
            $("#title-hide").change(function(){
                $(this).prop("checked") ? pre_title.hide() : pre_title.show();
            });
                */
        },
        color_focus:function(){
            $(this).css({
                "border-color": "#66afe9",
                "outline": 0,
                "-webkit-box-shadow": "inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6)",
                "box-shadow": "inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6)"
            });
        },
        // 颜色框失去焦点效果
        color_blur:function(){
            $(this).css({
                "box-shadow": "inset 0 1px 1px rgba(0,0,0,0.075)",
                "border": "1px solid #cccccc"
            })
        },
        click_add_pro:function(){
            var $this = $(this);
            var img_src = $this.closest("td").find("img").prop("src"),
                title = $this.closest("td").find(".pro-title").text(),
                price = $this.closest("td").find(".pro-price").text(),
                pro_id = $this.closest("td").find(".pro-image").attr("data-id"),
                pro_link = $this.closest("td").find(".pro-image").attr("data-link");
            Relation.add_pro_execute(img_src,title,price,pro_id, pro_link);
        },
        drag_add_pro:function(e){
            var $this = $(this);
//            e = e||window.event;
//            if(e.clientX-$drop.offset()["left"]>=0 && e.clientY-$drop.offset()["top"]>=0){
            $this.bind("dragend",function(){
                var img_src = $this.children().prop("src"),
                    title = $this.siblings().find(".pro-title").text(),
                    price = $this.siblings().find(".pro-price").text(),
                    pro_id = $this.attr("data-id"),
                    pro_link = $this.attr("data-link");
                Relation.add_pro_execute(img_src,title,price,pro_id, pro_link);
                $this.unbind("dragend");
            });
        },
        add_pro_execute:function(img_src,title,price,pro_id, pro_link){
            var empty_td = $drop.find("td").filter(":empty");
            var is_show = $("#hide-title").prop("checked") ? "none" : "inline";
            var colors = "#"+$("input[name=pro-font-colors]").val();
            var back_colors = "#"+$("input[name=pro-back-colors]").val();
            if(empty_td.length==0){
                alert("商品数量超出当前设置显示数量，请删除部分图片或重新设置显示数量。");
            }else{
                var content_str = '<div class="pre-pic" style="position: relative;">' +
                                '<div style="min-height:230px;max-height:230px;text-align:center;padding:15px;background-color:#fff">'+
                                '<a href="'+pro_link+'"><img src="'+img_src+'" style="width:100%;height:100%;max-height:200px"></a>' +
                                '</div>'+
                                '<a class="pre-spr-title" href="'+pro_link+'" style="font-size:13px;background-color:'+back_colors+';color:'+colors+';width:80%";display:'+is_show+'">'+title+'</a>'+
                                '<div style="margin-top:2px"><span style="color:#F60;font-weight:700">USD $'+price+'</span></div>'+
                                '<a class="delete" href="#" style="position: absolute; top: 0px; right: 1px; display: none;">×</a>';
                empty_td.eq(0).html(content_str);
            }
        },
        delete_pic:function(){
            var $this = $(this);
            $this.closest(".pre-pic").empty();
            var html_list = [],
                html_num = $drop.find(".pre-pic").length;
            $drop.find(".pre-pic").each(function(k,v){
                var kv = $(v);
                kv.html()!="" && html_list.push(kv.html());
            });
            $drop.find(".pre-pic").eq(html_num-1).remove();
            var j = 0;
            $drop.find(".pre-pic").each(function(k,v) {
                var kv = $(v);
                kv.html(html_list[j]);
                j++;
            });
//            $this.siblings().prop("src","");
//            var img_list = [],
//                img_num = $drop.find("img").length;
//            $drop.find("img").each(function(k,v){
//                var kv = $(v);
//                kv.attr("src")!="" && img_list.push(kv.prop("src")); //获取src必须使用attr
//            });
//            $drop.find("img").eq(img_num-1).closest(".pre-pic").remove();
//            var j = 0;
//            $drop.find("img").each(function(k,v) {
//                var kv = $(v);
//                kv.prop("src",img_list[j]);
//                j++;
//            })
        },
        generate_table:function(){
            var col = parseInt($("#show-col").val()),
                row = parseInt($("#show-row").val()),
                row_before = $drop.find("tr").length,
                col_before = $drop.find("tr").eq(0).find("td").length,
                pre_pic_num = $drop.find(".pre-pic").length;
            var new_row = "";
            if(pre_pic_num>col*row){
                alert("显示数量小于已有商品数量，请重新选择或删除部分图片。");
                $("#show-col").val(col_before+"列");
                $("#show-row").val(row_before+"行");
            }else{
                var html_list = [];
                $drop.find(".pre-pic").each(function(k,v){
                    var kv = $(v);
                    html_list.push(kv.html());
                });
                var new_table = '<div style="border: 1px solid #e1e1e7;border-top: none !important;background-color: #f7f7f9">'+
                                '<table class="table" style="width: 100%; border: none">{0}</table>'+
                                '</div>';
                for(var i=0;i<row;i++){
                    new_row += '<tr style="border: 0">{0}</tr>';
                    var new_col = "";
                    var td_width = (1/col).toFixed(4)*100+"%";
                    for(var j=0;j<col;j++){
                        if(html_list.length>0){
                            new_col += '<td style="width: '+td_width+';border: 0;vertical-align:top">' +
                                        '<div class="pre-pic" style="position: relative">'+html_list[0]+'' +
                                        '</td>';
                            html_list.shift();
                        }else{
                            new_col += '<td style="width: '+td_width+';border: 0;vertical-align:top">' +
                                        '</td>';
                        }
                    }
                    new_row = new_row.format(new_col);
                }
                new_table = new_table.format(new_row);
                $drop.find(".table").parent().remove();
                $drop.append(new_table);
            }
        },
        pre_title_text:function() {
            var $value = $(this).val();
            if ($value){
                pre_title_text.html($value);
            }else{
                pre_title_text.html("推广标题区域");
            }
        },
        pre_title_color:function(){
            pre_title_text.css("color","#"+$(this).val());
        },
        pre_title_bgd:function(){
            pre_title.css("background-color","#"+$(this).val());
        },
        choose_option: function(){
            $(".search-key").text($(this).text());
            s_option = $(this).attr("data-key")[0];
        },
        search_pro:function(){
            var $this = $(this);
            $this.button("loading");
//            var group_id = $("#group-id").val()=="无" ? "" : parseInt($("#group-id").val());
//            var offline_time = $("#offline-time").val()=="请选择" ? "" : parseInt($("#offline-time").val().replace(/[^0-9]/ig,""));
//            if(group_id!=""||offline_time!=""){
//                var search_info = {
//                    "groupId": group_id,
//                    "option":  s_option,
//                    "offLineTime": offline_time
//                };
//            }else{
//                var search_info = {
//                    "s_value": $("#search-input").val().trim(),
//                    "option":  s_option
//                };
//            }
//            var search_info = {
//                "groupId": group_id,
//                "offLineTime": offline_time,
//                "s_value": $("#search-input").val().trim(),
//                "option":  s_option
//            };
            $.ajax({
                "url": "/online/" + $("#shop-id").val() + "/search/feed",
                "type": "POST",
                "data": {
                    "pattern": s_option,
                    "value": $("#search-input").val().trim()
                },
                "dataType": "json",
                "success": function(data){
                    if(data.status==1){
                        var pro_list = data["json"];
                        var table_str = '';
                        var generate_table = '<table class="table">{0}</table>';
                        if(pro_list.length!=0){
                            for(var i=0;i<pro_list.length;i++){
                                table_str += '<tr><td>'+
                                            '<div class="pro-image" data-id='+pro_list[i]["Id"]+' data-link='+pro_list[i]["Link"]+' draggable="true">'+
                                            '<img src='+(pro_list[i]["GalleryURL"].split(";"))[0]+' />'+
                                            '</div>'+
                                            '<div class="pro-desc">'+
                                            '<div class="pro-title">'+pro_list[i]["Title"]+'</div>'+
                                            '<div class="pro-price">'+pro_list[i]["Price"]+'<span class="glyphicon glyphicon-plus-sign add-pro-icon"></span></div>'+
                                            '</div>'+
                                            '</td></tr>'
                            }
                        }else{
                            table_str = '<label class="control-label col-md-offset-1" style="color:red">未找到你搜索的商品信息</label>'
                        }
                        $("#pro-list-content").empty().append(generate_table.format(table_str));
                    }else{
                        Inform.show(data.message);
                        Inform.enable();
                    }
                    $this.button("reset");
                }
            })
        },
        save_template:function(){
            var spr_border = "";
            $("#spr-border").prop("checked") ? spr_border = "true" : spr_border = "false";
            var is_hide = $("#hide-title").prop("checked") ? "true" : "false";
            var template_name = $("#template-name").val().trim();
            if(template_name!=""){
                var relation = {
                "template_name": template_name,
                "template_html": $("#drop-area").html(),
                "details": {
                    "table_row": $("#show-row").val(),
                    "table_col": $("#show-col").val(),
                    "spr_title": $("#spr-title").val().trim(),
                    "spr_back_colors": $("[name=\"spr-back-colors\"]").val(),
                    "spr_font_colors": $("[name=\"spr-font-colors\"]").val(),
                    "spr_font_size": $("#spr-font-size").val(),
                    "spr_border": spr_border,
                    "pro_back_color": $("[name=\"pro-back-colors\"]").val(),
                    "pro_font_color": $("[name=\"pro-font-colors\"]").val(),
                    "is_hide": is_hide
                    }
                };
                Inform.disable();
                Inform.show("", true, "正在保存...");
                $.ajax({
                    "url": "/template/" + $("#shop-id").val() + "/save/relation",
                    "type":"POST",
                    "data":{
                        "content": JSON.stringify(relation),
                        "id": $("#relation-id").val(),
                        "name": template_name
                    },
                    "dataType": "json",
                    "success": function(data){
                        $(this).button("reset");
                        Inform.enable();
                        if(data.status == 1){
                            Inform.show("保存成功");
                            Inform.enable("/template/" + $("#shop-id").val() + "/display");
                            //alert("保存成功");
                        }else{
                            Inform.show("保存失败");
                            Inform.enable();
                            //alert("保存失败");
                        }
                    }
                });
            }else{
                Inform.show("请输入模板名称");
                Inform.enable();
            }
        }
    };
    Relation.init();
});