/**
 * Created by GF on 2016/05/31.
 */
$(function(){
    var cache = $('#detailHtml'),
        text_html = $("#text-html").val(),
        font_color = $("input[name=\"font-color\"]"),
        back_color = $("input[name=\"back-color\"]"),
        pre_title_text = $("#pre-title-text"),
        pre_title = $("#pre-title");
    var Description = {
        init:function(){
            Inform.init();
            $(function(){
                Description.pre_title_bgd();
                Description.pre_title_color();
                Description.pre_title_text();
                Description.pre_title_size();
                $("#title-border").prop("checked") && pre_title_text.css("font-weight",700);
                $("#title-hide").prop("checked") && pre_title.hide();
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
             //加载富文本编辑器
            KindEditor.ready(function(K) {
                window.editor = K.create('#desc-editor', options);
                var detail = cache.text();
                cache.html(detail);
                cache.find("[data-widget-type]").each(function (k, v) {
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
                if(text_html){
                    editor.html(text_html);
                }
                cache.empty();
            });
            font_color.on("change",Description.pre_title_color);
            back_color.on("change",Description.pre_title_bgd);
            $("#model-title").keyup(Description.pre_title_text);
            $("#title-size").change(Description.pre_title_size);
            $("#title-border").change(function(){
                $(this).prop("checked") ? pre_title_text.css("font-weight",700) : pre_title_text.css("font-weight",400);
            });
            $("#title-hide").change(function(){
                $(this).prop("checked") ? pre_title.hide() : pre_title.show();
            });
            $("#preview-btn").click(Description.preview_result);
            $("#save-template").click(Description.save_template);
        },
        pre_title_size:function() {
            var $value = $("#title-size").val();
            pre_title_text.css("font-size", $value);
            if ($.inArray($value, ["14px", "16px", "18px", "20px"]) != -1) {
                pre_title_text.css("line-height", "30px");
            } else if ($value == "24px") {
                pre_title_text.css("line-height", "40px");
            } else {
                pre_title_text.css("line-height", "50px");
            }
        },
        pre_title_text:function() {
            var text = pre_title_text;
            var $value = $("#model-title").val();
            if ($value){
                text.html($value);
            }else{
                text.html("模板标题区域");
            }
        },
        pre_title_color:function(){
            pre_title_text.css("color","#"+font_color.val());
        },
        pre_title_bgd:function(){
            pre_title.css("background-color","#"+back_color.val());
        },
        preview_result:function(){
            var content = editor.html();
            $("#pre-content").html(content);
        },
        save_template:function(){
            var content = editor.html();
            var $this = $(this);
            $("#pre-content").html(content);
            $this.button("loading");

            var title_hide = $("#title-hide").prop("checked") ? "true" : "false";
            var border = $("#title-border").prop("checked") ? "true" :  "false";
            var template_name = $("#template-name").val().trim();
            var no_title_html = '<div>{0}</div>';
            if(template_name!=""){
                var description = {
                    "template_name": template_name,
                    "template_html": $("#preview").html(),
                    "template_html_no_title": no_title_html.format($("#pre-content").html()),
                    "details": {
                        "title": $("#model-title").val().trim(),
                        "title_hide": title_hide,
                        "title_color":$("[name=\"font-color\"]").val(),
                        "title_size": $("#title-size").val(),
                        "back_color": $("[name=\"back-color\"]").val(),
                        "border": border,
                        "text": editor.html()
                    }
                };
                Inform.disable();
                Inform.show("", true, "正在保存...");
                $.ajax({
                    "url": "/template/" + $("#shop-id").val() + "/save/description",
                    "type": "POST",
                    "data": {
                        "content": JSON.stringify(description),
                        "id": $("#description-id").val(),
                        "name": template_name
                    },
                    "dataType": "json",
                    "success": function(data){
                        $(this).button("reset");
                        Inform.enable();
                        if(data.status == 1){
                            Inform.show("保存成功");
                            Inform.enable("/template/" + $("#shop-id").val() + "/display");
    //                        alert("保存成功");
                        }else{
                            Inform.show("保存失败");
                            Inform.enable();
                            $this.button("reset");
    //                        alert("保存失败");
                        }
                    }
                });
            }else{
                Inform.show("请输入模板名称");
                Inform.enable();
                $this.button("reset");
            }

        }

    };
    Description.init();
});
