/**
 * Created by GF on 2015/11/21.
 */
$(function(){
    var size_refer_div = $("#size-refer");
    var size_chart_html = '',
        size_guide_html = '';
    var temp_pro_id = "";
    var Size = {
        init:function(){
            Inform.init();
            $(function(){
//                console.log(typeof $("#tem-return").val());
//                eval("var size_tem_return ="+$("#tem-return").val());
//                var size_tem_return = $.parseJSON($("#tem-return").val());
//                console.log(size_tem_return);
                if(size_tem_return["template_id"] !== undefined && size_tem_return["template_id"] !== ""){
                    var details = size_tem_return["details"];
                    var size_chart_div = $("#size-chart");
                    $("#template-name").val(size_tem_return["template_name"]);
                    $(".nav-tabs").find("#"+details["category"]).trigger("click");
                    $("#size-category").find("input[data-id="+details["radio_id"]+"]").trigger("click");
                    details["is_inch"] && $("#chart-in-inch").trigger("click");
                    details["is_guide"] && $("#edit-size-refer").trigger("click");
                    size_chart_div.find(".table").find("input[type=checkbox]").each(function(k,v){
                        var kv = $(v);
                        details["chart_check"][k] && kv.trigger("click");
                    });
                    size_chart_div.find(".table").find("input[type=text]").each(function(k,v){
                        var kv = $(v);
                        kv.val(details["chart"][k]);
                    });
                    size_refer_div.find(".table").find("input[type=text]").each(function(k,v){
                        var kv = $(v);
                        kv.val(details["guide"][k]);
                    });
                    if($.inArray(size_tem_return["details"]["radio_id"],["26", "27", "20001", "21", "20002", "49", "46", "47", "28", "16002", "75", "16001", "72", "48", "22001", "81", "18001", "79", "78"])==-1){
                        $("#edit-size-refer").prop("checked",false).parent().hide();
                        $("#size-refer").empty().hide();
                    }
//                    details["chart"]!="" && $("#size-chart").empty().append(details["chart"]);
//                    details["guide"]!="" && $("#size-refer").empty().append(details["guide"]);
                }
            });
            $("a[data-toggle=\"tab\"]").click(Size.choose_size);
            $("#size-refer").on("click","#go-top",Size.scroll_top);
            $("#size-category").on("click","input[type=radio]",Size.choose_category);
            $("#size-chart").on("change", "input[type=checkbox]", Size.check_size_table);
            $("#edit-size-refer").change(function(){
                $(this).prop("checked") ? size_refer_div.show() : size_refer_div.hide();
            });
            $("#save-all-temp").click(Size.save_size_chart);
            $("#data-edit-area").on("click","#pre-size-chart",Size.preview_size_chart)
                                .on("click","#pre-size-guide",Size.preview_size_guide)
                                .on("click","#save-chart",Size.save_size_chart)
                                .on("click","#save-guide",Size.save_size_chart)
                                .on("click",".remarks",function(){
                                    $(this).closest("tr").hide().next().show();
                                })
                                .on("click","input[type=text]",Size.input_enable);
        },
        translator:function(word){
            var dic = {
                "尺码": "Size",
                "肩宽": "Shoulder",
                "胸围": "Bust" ,
                "上胸围": "Upper Chest",
                "下胸围": "Under Bust",
                "腰围": "Waist",
                "臀围": "Hips",
                "适合身高区间": "Body Height",
                "适合体重区间": "Body Weight",
                "锁骨到地": "Hollow To Hem",
                "衣长": "Body Length",
                "袖长": "Sleeve",
                "袖围": "Cuff",
                "领围": "Collar Width",
                "裤长": "Pant Length",
                "裤裆": "Fly Length",
                "裙长": "Length",
                "大腿围": "Thigh CIR",
                "裤脚围": "Opening CIR",
                "下摆围": "Hem",
                "美国码": "US Size",
                "欧洲码": "Europe Size",
                "英国码": "UK Size",
                "巴西码": "Brazil Size",
                "澳大利亚码": "Australia Size",
                "脚长": "Heel To Toe",
                "美国和加拿大": "US & CAN",
                "中国码": "CN Size",
                "中国": "Chinese",
                "香港": "HongKong",
                "瑞士": "Swiss",
                "周长": "Ring CIR",
                "英国": "UK",
                "欧洲和澳大利亚": "Europe & Australia",
                "新加坡": "Singapore",
                "日本": "Japanese",
                "毫米": "mm",
                "脚跟到脚趾长": "Heel To Toe"
            };
            var key_word = word.match(/[\u4e00-\u9fa5]+/g);
            var new_word = word;
            for(var i=0;i<key_word.length;i++){
                new_word = new_word.replace(/[\u4e00-\u9fa5]+/,dic[key_word[i]])
            }
            return new_word.replace("[cm]"," (cm)").replace("[","(").replace("]",")");
        },
        choose_size:function(){
            var $this = $(this);
            var $id = $this.prop("id");
            $("#"+$id+"-category").show().siblings().hide();
            if($id=="cloth"){
                $("#edit-size-refer").parent().show();
            }else{
                $("#edit-size-refer").prop("checked",false).parent().hide();
            }
        },
        choose_category:function(){
            var $this = $(this),
                model_id = $this.attr("data-id"),
                size = size_model[model_id]["SizeInfoModel"]["size"],
                measure = size_model[model_id]["SizeInfoModel"]["measure"],
                size_weight = size_model[model_id]["SizeReferModel"]["weight"],
                size_height = size_model[model_id]["SizeReferModel"]["height"];
            if($.inArray(model_id,["26", "27", "20001", "21", "20002", "49", "46", "47", "28", "16002", "75", "16001", "72", "48", "22001", "81", "18001", "79", "78"])!=-1){
                $("#edit-size-refer").parent().show();
            }else{
                $("#edit-size-refer").prop("checked",false).parent().hide();
                $("#size-refer").empty().hide();
            }
            var standard_size = "";
            var size_type = $(".nav-tabs").find(".active").text();
            if(size_type=="服装"){
                standard_size="尺码";
            }else if(size_type=="鞋子"){
                standard_size="美国码";
            }else{
                standard_size="美国和加拿大";
            }
            var info_table = '<h3 class="theme-t">尺码表</h3>'+
                             '<table class="table table-bordered table-hover table-striped " style="text-align: center">'+
                             '<thead>'+
                             '<tr style=""><th></th>'+
                             '<th class="table-heading" data-name="Size"><div><p>'+standard_size+'</p></div></th>{0}</tr>'+
                             '</thead>'+
                             '<tbody>{1}</tbody>'+
                             '<tfoot>'+
                             '<tr style="display:none">'+
                             '<td colspan="99" style="background-color: #d9edf7"><a href="javascript:void(0)" class="remarks" style="color: #3a87ad">备注信息，点击编辑</a></td>'+
                             '</tr>'+
                             '<tr style="display:none">'+
                             '<td colspan="99">' +
                             '<input class="form-control" type="text" data-col=true data-row=true style="border: none">'+
                             '</tr>'+
                             '</tfoot>'+
                             '</table>'+
                             '<button type="button" class="btn btn-success" id="pre-size-chart" data-toggle="modal" ' +
                             'data-target="#previewModal" style="margin-right:10px">预览</button>'+
                             '<button type="button" class="btn btn-primary" id="save-chart">保存</button>';
            var table_head = '',
                table_row  = '',
                row_str = '';
            for(var i=0;i<measure.length;i++){
                if(measure[i][0]=="no"){
                    table_head += '<th class="table-heading">'+
                                  '<div class="checkbox" style="width: auto">'+
                                  '<label class=""><input class="col-check" type="checkbox">'+measure[i][1]+'</label>'+
                                  '</div>'+
                                  '</th>';
                    if(measure[i][1]=="适合身高区间[cm]"||measure[i][1]=="适合体重区间[kg]"||measure[i][1]=="脚长：cm"
                        ||(measure[i][1]=="脚跟到脚趾长[in cm]"&&model_id=="24001")){
                        row_str += '<td style="width:12em">' +
                                   '<input class="form-control" data-name="second-in" type="text" readonly=true data-col=false data-row=false style="float:right;width:47%">' +
                                   '<span style="float:right;line-height:27px">-</span>'+
                                   '<input class="form-control" data-name="first-in" type="text" readonly=true data-col=false data-row=false style="float:right;width:47%">' +
                                   '</td>'
                    }else{
                        row_str += '<td><input class="form-control" type="text" readonly=true data-col=false data-row=false></td>';
                    }
                }else{
                    table_head += '<th class="table-heading">'+
                                  '<div class="checkbox"><label class="col-check" data-col=true style="font-weight:400;margin-left: -17px">'+measure[i][1]+'</label></div>'+
                                  '</th>';
                    if(measure[i][1]=="脚长：cm"||(measure[i][1]=="脚跟到脚趾长[in cm]"&&model_id=="24001")){
                        row_str += '<td style="width:12em">' +
                                   '<input class="form-control" data-name="second-in" type="text" readonly=true data-col=true data-row=false style="float:right;width:47%">' +
                                   '<span style="float:right;line-height:27px">-</span>'+
                                   '<input class="form-control" data-name="first-in" type="text" readonly=true data-col=true data-row=false style="float:right;width:47%">' +
                                   '</td>'
                    }else{
                        row_str += '<td><input class="form-control" type="text" readonly=true data-col=true data-row=false></td>';
                    }
                }
            }
            for(var j=0;j<size.length;j++){
                table_row += '<tr>'+
                                '<td style="text-align: center">'+
                                '<label class="">'+
                                '<input class="row-check" type="checkbox" style="margin: 8px">'+
                                '</label>'+
                                '</td>'+
                                '<td style="text-align: center"><p style="margin-top: 5px">'+size[j]+'</p></td>{0}'+
                                '</tr>';
            }
            table_row = table_row.format(row_str);
            info_table = info_table.format(table_head, table_row);
            $("#size-chart").empty().append(info_table);
            var refer_table  = '',
                table_height = '',
                table_weight = '',
                height_str = '';
            if(size_weight.length>0){
                refer_table =  '<h3 class="">尺码对照建议表</h3>'+
                               '<table class="table table-bordered table-hover table-striped" style="max-height: 300px">'+
                               '<thead>'+
                               '<tr>'+
                               '<td style="text-align:center;line-height: 2.5em;white-space:nowrap">身高[cm]/体重[kg]</td>{0}'+
                               '</tr>'+
                               '</thead>'+
                               '<tbody>{1}</tbody>'+
                               '<tfoot>'+
                               '<tr style="display:none">'+
                               '<td colspan="99" style="background-color: #d9edf7;text-align: center">' +
                               '<a href="javascript:void(0)" class="remarks" style="color: #3a87ad">备注信息，点击编辑</a></td>'+
                               '</tr>'+
                               '<tr style="display:none">'+
                               '<td colspan="99">' +
                               '<input class="form-control" type="text" data-col=true data-row=true style="border: none" readonly=false>'+
                               '</tr>'+
                               '</tfoot>'+
                               '</table>'+
                               '<button type="button" class="btn btn-success" id="pre-size-guide" data-toggle="modal"' +
                               'data-target="#previewModal" style="margin-right:10px">预览</button>'+
                               '<button type="button" class="btn btn-primary" id="save-guide" style="margin-right:10px">保存</button>'+
                               '<button type="button" class="btn btn-default" id="go-top" >返回顶部</button>';
                for(var m=0;m<size_weight.length;m++){
                    table_weight += '<td style="text-align:center;line-height: 2.5em">'+size_weight[m]+'</td>';
                    height_str +='<td><input type="text" class="form-control"></td>';
                }
                for(var n=0;n<size_height.length;n++){
                    table_height += '<tr style="max-height: 36px"><td style="text-align:center;line-height: 2.5em">'+size_height[n]+'</td>{0}</tr>';
                }
                table_height = table_height.format(height_str);
                refer_table = refer_table.format(table_weight, table_height);
                size_refer_div.empty().append(refer_table);
            }
        },
        scroll_top:function(){
            $(document.body).animate({scrollTop:0}, 350);
        },
        check_size_table:function(){
            var $this = $(this);
            if($this.attr("class")=="col-check"){
                var index = $this.closest("th").index();
                $this.closest(".table").find("tr").each(function(k,v){
                    var kv = $(v);
                    kv.find("td").eq(index).children("input").attr("data-col",$this.prop("checked"));
                })
            }else{
                $this.closest("tr").find("input").attr("data-row",$this.prop("checked"));
            }
            $this.closest(".table").find("input[type=text]").each(function(m,n){
                var mn = $(n);
                if(mn.attr("data-row")=="true" && mn.attr("data-col")=="true"){
                    mn.prop("readonly",false);
                }else{
                    mn.val("").prop("readonly",true);
                }
            })
        },
        input_enable:function(){
            var $this = $(this),
                $table = $this.closest("table");
            $this.attr("data-col", true).attr("data-row", true);
            var $index = $this.closest("td").index();
            var col_check = $table.find("tr:eq(0)").find("th:eq("+$index+")").find("input[type=checkbox]"),
                row_check = $this.closest("tr").find("input[type=checkbox]");
            col_check.prop("checked") || col_check.trigger("click");
            row_check.prop("checked") || row_check.trigger("click");
        },
        generate_size_chart_pre:function(element){
            var $table = $(element).siblings(".table");
            var pre_table = '<p style="margin:4px 0;font-weight: 700">' +
                            '<span style="margin-top: -2px;font-size: 14px;vertical-align: middle;line-height: 20px;' +
                            'margin-right: 10px;color: #fff;display: inline-block;width: 20px;height: 20px;text-align: center;' +
                            'background-color: #333;">1</span>Measurement In CM</p>'+
                            '<table style="width:100%;border:1px solid #e0e0e0;border-collapse:collapse;text-align:center;" border="1" bordercolor="#e0e0e0">' +
                            '<tr><th style="text-align:center;padding:6px 0;border:1px solid #e0e0e0;height:40px;font-size:12px;color:#333;background-color:#e0e0e0;">'+Size.translator($("[data-name=Size]").text())+'</th>{0}</tr>{1}</table>';
            var table_head = '',
                table_row = '',
                table_row_inch = '',
                pre_table_inch = '';
            $table.find(".col-check").each(function(k,v){
                var kv = $(v);
                if(kv.prop("checked") || kv.attr("data-col")=="true"){
                    table_head += '<th style="text-align:center;padding:6px 0;border:1px solid #e0e0e0;height:40px;font-size:12px;color:#333;background-color:#e0e0e0;">'+Size.translator(kv.parent().text())+'</th>';
                }
            });
            $table.find("tr:gt(0)").each(function(m,n){
                var mn = $(n),
                    row_values = '',
                    row_values_inch = '';
                var re = /^[0-9]+.?[0-9]*$/;
                if(mn.find("input[type=checkbox]").prop("checked")){
                    table_row +='<tr><td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">'+mn.find("td:eq(1)").text()+'</td>{0}</tr>';
                    table_row_inch += '<tr><td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">'+mn.find("td:eq(1)").text()+'</td>{0}</tr>';
                }
                mn.find("input[type=text]").not("[readonly]").each(function(x,y){
                    var xy = $(y);
                    var value_unit = $table.find("tr:eq(0)").find("th:eq("+xy.closest("td").index()+")").text();
                    if(xy.attr("data-name")=="first-in"){
                        var first_value = re.test(xy.val()) ? xy.val() : "",
                            second_value = re.test(xy.siblings("input").val()) ? xy.siblings("input").val() : "";
                        row_values += '<td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">'+first_value+'-'+second_value+'</td>';
                        var fir_in = "",
                            sec_in = "";
                        if(value_unit.indexOf("cm")!=-1){
                            fir_in = (first_value/2.54).toFixed(1);
                            sec_in = (second_value/2.54).toFixed(1);
                        }else if(value_unit.indexOf("kg")!=-1){
                            fir_in = (first_value*2.2046).toFixed(2);
                            sec_in = (second_value*2.2046).toFixed(2);
                        }
                        row_values_inch += '<td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">'+fir_in+'-'+sec_in+'</td>';
                    }else if(xy.attr("data-name")=="second-in"){

                    }else{
                        row_values += xy.val() ? '<td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">'+xy.val()+'</td>'
                                                : '<td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">--</td>';
                        var inch_val = "";
                        // 判断单位并转换
                        if(value_unit.indexOf("cm")!=-1){
                            inch_val = re.test(xy.val()) ? (xy.val()/2.54).toFixed(1) : "--";
                        }else if(value_unit.indexOf("kg")!=-1){
                            inch_val = re.test(xy.val()) ? (xy.val()*2.2046).toFixed(2) : "--";
                        }else if(value_unit.indexOf("毫米")!=-1||value_unit.indexOf("mm")!=-1){
                            inch_val = re.test(xy.val()) ? (xy.val()/25.4).toFixed(2) : "--";
                        }else{
                            inch_val = xy.val();
                        }
                        row_values_inch += '<td style="padding:6px 0;border:1px solid #e0e0e0;font-size:12px;color:#333;">'+inch_val+'</td>';
                    }
                });
                table_row_inch = table_row_inch.format(row_values_inch);
                table_row = table_row.format(row_values);
            });
            pre_table_inch = pre_table.format(table_head, table_row_inch).replace(/mm/g,"in.").replace(/cm/g,"in.").replace(/\(kg\)/g,"(lb)").replace("1</span>Measurement In CM","2</span>Measurement In Inch");
            pre_table = pre_table.format(table_head, table_row);
            if($("#chart-in-inch").prop("checked")){
                $("#preview").html(pre_table+'<div style="margin-top:15px"></div>'+pre_table_inch);
            }else{
                $("#preview").html(pre_table);
            }
            return [pre_table,pre_table_inch]
        },
        preview_size_chart:function(){
            var pre_table = Size.generate_size_chart_pre(this);
            if($("#chart-in-inch").prop("checked")){
                $("#preview").html(pre_table[0]+'<div style="margin-top:15px"></div>'+pre_table[1]);
            }else{
                $("#preview").html(pre_table[0]);
            }
        },
        generate_size_guide_pre:function(element){
            var $table = $(element).siblings(".table");
            var pre_table = '<p style="margin:4px 0;font-weight: 700">' +
                            '<span style="padding: 2px 8px; background: #000;color: #fff;margin-right: 8px">1</span>Size Guide</p>'+
                            '<table style="max-width:100%;width:100%;border:1px solid #e0e0e0;border-collapse:collapse;text-align:center;" border="1" bordercolor="#e0e0e0">' +
                            '{0}</table>';
            var row_list = [];
            var head_list = [],
                new_list = [];

            $table.find("tr:eq(0)").find("td").each(function(k,v){
                var kv = $(v);
                var $text  =kv.text();
                if(k==0){
                    $text = "Height(cm)/Weight(kg)";
                }
                head_list.push($text);
            });
//            console.log(head_list);
            new_list.push(head_list);
            $table.find("tr:gt(0)").each(function(k,v){
                var kv = $(v);
                var row_value = [];
                row_value.push(kv.find("td:eq(0)").text());
                kv.find("input").each(function(m,n){
                    var mn = $(n);
                    row_value.push(mn.val());
                });
                row_list.push(row_value);
            });
            for(var i=0;i<row_list.length;i++){
                var v_status = false;
                for(var j=1;j<row_list[i].length;j++){
                    if(row_list[i][j]!=""){
                        v_status = true;
                    }
                }
                v_status && new_list.push(row_list[i]);
            }
            var col_status = {};
            for(var r=1;r<new_list.length;r++){
                for(var t=1;t<new_list[r].length;t++){
                    if(new_list[r][t]=="" && !col_status[t]){
                        col_status[t] = false
                    }else{
                        col_status[t] = true
                    }
                }
            }
//            console.log(new_list)
            col_status[0] = true;
            var final_content = '';
            for(var row=0;row<new_list.length;row++){
                var final_td = '';
                for(var col=0;col<new_list[row].length;col++){
                    if(col_status[col]){
                        if(row==0){
                            if(col==0){
                                final_td += '<td style="border:1px solid #e0e0e0;height:40px;font-size:12px;color:#333;background-color:#e0e0e0;width:13em !important;white-space:nowrap">'+new_list[row][col]+'</td>'
                            }else{
                                final_td += '<td style="border:1px solid #e0e0e0;height:40px;font-size:12px;color:#333;background-color:#e0e0e0;width:auto;white-space:nowrap">'+new_list[row][col]+'</td>'
                            }
                        }else{
                            final_td += '<td style="border:1px solid #e0e0e0;font-size:12px;color:#333;">'+new_list[row][col]+'</td>'
                        }
                    }
                }
                final_content += '<tr>'+final_td+'</tr>'
            }
            pre_table = pre_table.format(final_content);
            return pre_table
        },
        preview_size_guide:function(){
            $("#preview").html(Size.generate_size_guide_pre(this));
        },
        save_template:function(name){

        },
        save_size_chart:function(){
            var radio = $("#size-category").find("input:checked").parent().text();
            var template_name = $("#template-name").val().trim();
            var size_chart_div = $("#size-chart");
            if(template_name==""){
                Inform.show("请输入模板名称后再保存");
                Inform.enable();
            }else if(!radio){
                Inform.show("请选择一个类目后再保存");
                Inform.enable();
            }else{
                var table = Size.generate_size_chart_pre("#save-chart");
                var size_chart_html = $("#chart-in-inch").prop("checked") ? table[0]+table[1] : table[0];
                var radio_id = $("#size-category").find("input:checked").attr("data-id");
                var chart_values = [];
                size_chart_div.find(".table").find("input[type=text]").each(function(k,v){
                    var kv = $(v);
                    chart_values.push(kv.val());
                });
                var checkbox_list = [];
                size_chart_div.find(".table").find("input[type=checkbox]").each(function(k,v){
                    var kv = $(v);
                    checkbox_list.push(kv.prop("checked"));
                });
                var size_guide_html = Size.generate_size_guide_pre("#save-guide");
                var guide_values = [];
                size_refer_div.find(".table").find("input[type=text]").each(function(k,v){
                    var kv = $(v);
                    guide_values.push(kv.val());
                });
                var size = {
                    "template_name": template_name,
                    "size_chart_html": size_chart_html,
                    "size_guide_html": size_guide_html,
                    "details": {
                        "category": $(".nav-tabs").find(".active").children("a").attr("id"),
                        "radio_id": radio_id,
                        "radio": radio,
                        "is_inch": $("#chart-in-inch").prop("checked"),
                        "is_guide": $("#edit-size-refer").prop("checked"),
                        "chart":chart_values,
                        "chart_check": checkbox_list,
                        "guide": guide_values
                    }
                };
                var template_id = "";
                if(size_tem_return["template_id"]){
                    template_id = size_tem_return["template_id"]
                }else{
                    template_id = temp_pro_id;
                }
                $.ajax({
                    "url": "/template/" + $("#shop-id").val() + "/save/size",
                    "type":"POST",
                    "data":{
                        "content": JSON.stringify(size),
                        "id": template_id,
                        "name": template_name
                    },
                    "dataType": "json",
                    "success": function(data){
                        $(this).button("reset");
                        Inform.enable();
                        if(data.status == 1){
                            Inform.show("保存成功");
                            Inform.enable("/template/" + $("#shop-id").val() + "/display");
                            temp_pro_id = data["temp_pro_id"];
                            //alert("保存成功");
                        }else{
                            Inform.show("保存失败");
                            Inform.enable();
                            //alert("保存失败");
                        }
                    }
                });
            }
        }
    };
    Size.init();
});
