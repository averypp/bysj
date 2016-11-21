/**
 * Created by xuhe on 15/5/26.
 */
$(function(){
    var shop_id = $("#shopId").val();
    Inform.init();
    var category_group = [],
        c_level = 0,
        images = [],
        next = 0,
        pack_detail = $("#package-size").find("input"),
        pic_index = -1,
        picture_div = $("#feed_img").find(".thumbnail"),
        image_url ="",
        dirty_value = 0,
        category_uid = 0,
        root_id = 0,
        cate_names = [], this_div,target_index,target_url, btn, button;
    compute_pack_size();
    search_keyup();
    $("#t-upcase-btn").click(function(){
        var $dom = $(this).closest(".form-group").find(":text"),
            $title = $dom.val().trim();
        if($title!=""){
            var new_title = $title.replace(/\s[a-z]/g,function($1){return $1.toLocaleUpperCase()}).replace(/^[a-z]/,function($1){return $1.toLocaleUpperCase()}).replace(/\sOr[^a-zA-Z]|\sAnd[^a-zA-Z]|\sOf[^a-zA-Z]|\sAbout[^a-zA-Z]|\sFor[^a-zA-Z]|\sWith[^a-zA-Z]|\sOn[^a-zA-Z]/g,function($1){return $1.toLowerCase()});
            $dom.val(new_title);
        }
    });
    $("#st-upcase-btn").click(function(){
        var $dom = $(this).closest(".form-group").find(":text"),
            $title = $dom.val().trim();
        if($title!=""){
            var new_title = $title.replace(/\s[a-z]/g,function($1){return $1.toLocaleUpperCase()}).replace(/^[a-z]/,function($1){return $1.toLocaleUpperCase()}).replace(/\sOr[^a-zA-Z]|\sAnd[^a-zA-Z]|\sOf[^a-zA-Z]|\sAbout[^a-zA-Z]|\sFor[^a-zA-Z]|\sWith[^a-zA-Z]|\sOn[^a-zA-Z]/g,function($1){return $1.toLowerCase()});
            $dom.val(new_title);
        }
    });
    $("#trigger-goto").click(goto_source);
    $(".form-horizontal input,select").change(function(){
        dirty_value=1;
    });
    $(window).bind("beforeunload",function(e){
        if(dirty_value){
            return "提示:未保存的信息将会丢失"
        }
    });
    pack_detail.bind("blur", compute_pack_size);
    $(".category").find("li").click(choose_category);
    $("#image-space").click(modal_show);
    //$("#sku-prop").on("click","[data-self]", modal_show);
    $(".workspace").on("click","[data-category]",category_show);
    $("#Title, #product-title, #title, #short-des").on("keydown", check_title_limit)
        .on("change", check_title_limit)
        .on("keyup", check_title_limit)
        .on("change", check_title_limit);
    $("#sub-title").on("keydown", check_title_limit)
        .on("change", check_title_limit)
        .on("keyup", check_title_limit)
        .on("change", check_title_limit);
    $("#img-ensure-btn").click(img_ensure);
    $("#load-more").click(get_img_info);
    $(".form-horizontal").children(".row:last-child").find("button").click(function(){
        $(window).unbind("beforeunload");
    });
    $("#cate-modal").click(select_group);
    $("#sel-exit").children("li").click(function () {
        var target = $(this),
            un_target = $(this).siblings();
        target.attr("class", "active");
        un_target.attr("class", "");
        $(target.attr("data-target")).show();
        $(un_target.attr("data-target")).hide();
    });
    picture_div.bind("dragstart",function(e){
        if($(this).find("img").attr("src")!="/image/add.png"){
            $(this).css("cursor","move");
            pic_index = $(this).attr("data-index");
            image_url = $(this).find("img").attr("src");
            this_div = $(this);
        }else{
            this_div.css("cursor","default");
            image_url ="";
            pic_index = -1;
        }

    });
    picture_div.bind("dragenter",function(e){
        if($(this).find("img").attr("src")!="/image/add.png"){
            $(this).css("cursor","move");
            target_index = $(this).attr("data-index");
            target_url = $(this).find("img").attr("src");
            this_div.css("cursor","default");
            $(document).bind("dragend",function(e){
                if(pic_index!=-1){
                    picture_div.find("img").eq(target_index).attr("src",image_url);
                    picture_div.find("img").eq(pic_index).attr("src",target_url);
                    pic_index = -1;
                    $(document).unbind("dragend");
                }
            })
        }else{
            image_url ="";
            pic_index = -1;
            if(this_div){
               this_div.css("cursor","default");
            }
        }
    });
    var load_img = $("#feed_img").find(".image"),image_length;
    var platform = $(".shop-info").find("span").eq(0).text();
    var img_length_info = {
        "AliExpress": 6,
        "eBay": 6,
        "Amazon": 9,
        "Wish": 10,
        "Ensogo": 11,
        "Lazada": 8,
        "Joom": 10,
        "DHgate": 8
    };
    var title_limit_info = {
        "AliExpress": 128,
        "eBay": 80,
        "Lazada": 255,
        "Amazon": 200,
        "DHgate": 140,
        "Ensogo": 200
    };
    image_length = img_length_info[platform];
    var title_limit = title_limit_info[platform];
    for(var i=0;i<image_length;i++){
        var url=load_img.eq(i).attr("src");
        if (url!="/image/add.png"){
            images.push(url);
        }
    }
    function check_title_limit(){
        if(title_limit){
            var $this = $(this),
                al_ipt = $this.closest(".form-group").find(".already-input"),
                le_ipt = $this.closest(".form-group").find(".left-input"),
                $tl = $this.attr("id") == "sub-title" ? 55 : title_limit;
            $tl = $this.attr("id") == "short-des" ? 500 : title_limit;
            var title_length = $this.val().trim().length;
            if(title_length > $tl){
                if(platform != "Amazon"){
                    $this.val($this.val().trim().substring(0, $tl))
                }
            }
            title_length = $this.val().trim().length;
            al_ipt.text(title_length);
            le_ipt.text($tl-title_length);
        }
    }
    function category_show(){
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
    }
    function img_ensure(){
            var checked = $("#image-space-modal :checkbox:checked");
        var checked_num = checked.length;
        for(var i=0;i<checked_num;i++){
            var net_url = checked.closest(".col-md-2").find("img").eq(i).attr("src");
            var skupic = btn.closest("tr").find("td[data-id]").attr("data-id");
            if(btn.attr("data-self") == "sku"){
                var a_dom = $("td[data-id=\"" + skupic +"\"]").closest("tr").find("a[data-pic]").html('');
                var imgas = $("<img/>").attr("style","width:100%;height:100%").attr("src",net_url).appendTo(a_dom);
                var del_btn = $("<a/>").attr("href","javascript:void(0)").attr("data-name","del-pic")
                                .attr("style","float:right;padding:inherit").text("删除");
                            a_dom.closest("a").before(del_btn);
                $("a[data-name='del-pic']").each(function(x,y){
                    var del_btn = $(y);
                    del_btn.click(function(){
                        $(this).closest("td").find("img")
                            .closest("a").removeAttr("href").attr("href","javascript:void(0)");
                        $(this).closest("td").find("img").remove();
                        $(this).remove();
                    })
                });
            }else{
                $("#feed_img").find(".image").eq(images.length).attr("src", net_url).attr("draggable","false");
                images.push(net_url);
            }

        }
        $("#image-space-modal").modal("hide");
    }
    function modal_show(){
        btn = $(this);
        next = 0;
        if(platform == "Amazon"){
            $("#max-select").text(image_length-images.length);
        }else{
            $("#max-select").text(image_length-images.length);
        }
        $("#has-select").text(0);
        $("#img-content").html("");
        $("#image-space-modal").modal("show");
        get_img_info();
    }
    function check_img_num(){
        var has_select = $("#has-select");
        var selected_num = has_select.text();
        var max_select = $("#max-select").text();
        if (this.checked){
            if(selected_num<max_select){
                has_select.text(Number(selected_num) + 1);
            }else{
                $(this).removeAttr("checked");
            }
        }else{
            has_select.text(Number(selected_num) - 1);
        }
    }
    function get_img_info(){
        var image_content = $("#img-content");
        $("#image-space-modal").find("input[type=checkbox]").unbind("click");
        $.ajax({
            "type": "POST",
            "url": "/create/"+shop_id+"/picture/get",
            "dataType": "json",
            "data": "next="+next,
            "success":function(data){
                next = data.next;
                var picture_info = data["pictures"];
                var pic_num = picture_info.length;
                for (var i=0;i<pic_num;i++){
                    var div=$("<div/>").attr("class","col-md-2");
                    var img_html_str="<div class='thumbnail'>"
                                +"<div class='checkbox'><label>"
                                +"<input type='checkbox' class='pull-left select-pic'>"
                                +"选择图片</span></label></div>"
                                +"<img src='"+picture_info[i].Link+"' class='image img-responsive'>"
                                +"<div class='caption'>"
                                +"<div class='pic-name'>"+picture_info[i].Size
                                +" / " + picture_info[i].Length+"</div>"
                                +"</div></div>";
                    div.html(img_html_str).appendTo(image_content);
                }
                $("#image-space-modal").find("input[type=checkbox]").click(check_img_num);
                if(next>data.count||picture_info.length==0){
                    $("#load-more").prop("disabled", "true").html("没有更多了");
                }else{
                    $("#load-more").show("disabled", "false");
                }
            }
        })
    }
    function select_group() {
        $.ajax({
            "url": "/product/" + shop_id + "/group/select",
            "type": "POST",
            "data": {},
            "dataType": "json",
            "success": function (data) {
                console.log(data);
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
            cate_names[gl[i].level-1] = gl[i].name;
            cate_names = cate_names.slice(0, gl[i].level);
            if (gl[i]["is_leaf"]) {
                $("<div/>").attr({
                    class: "cate",
                    "data-id": gl[i]["cid"],
                    "data-root": root_id,
                    "data-names": cate_names.join(";")
                }).css({"margin-left": (gl[i]["level"] - 1) * 26 + "px"
                }).html("<a href ='javascript: void(0)'>"+gl[i]["name"] + "(" + gl[i]["count"] + ")"+"</a>").click(function () {
                    $(".on").removeClass("on");
                    $(this).find("a").addClass("on");
                    category_uid = $(this).attr("data-id");
                    $("#choose-category").attr({"disabled": false});
                    $("#second-choose-category").attr({"disabled": false});
                }).appendTo($(".group-detail"));
            } else {
                if(gl[i].level==1){
                    root_id = gl[i].cid;
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
    function choose_category(){
        var cate = $(this);
        var is_leaf = cate.attr("data-leaf") == "1";
        var level = cate.attr("data-level");
        var name = cate.find("a").text();
        var html_str = "";
        var tpl_value = cate.attr("data-tpl");
        var pop_times, temp_level;
        level = parseInt(level);
        $("#tpl_id").val(cate.attr("data-tpl"));
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
            $("#item-type").val(cate.attr("data-tag"));
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
                    url: "/?r=product/get_category",//"/api/category/get",
                    type: "GET",
                    data: {
                        shopId: shop_id,
                        tpl_id: cate.attr("data-tpl"),
                        parent_id: cate.attr("data-id")
                    },
                    dataType: "json",
                    success: function(data) {
                        if(data["categories"].length > 0){
                            html_str = render_category(data["categories"]);
                            category_dom.html(html_str).removeClass("loading-cate");
                            category_dom.find("li").click(choose_category);
                            search_keyup();
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
                            + "data-id=\"" + category["node_id"] + "\""
                            + "data-level=\"" + category["level"] + "\""
                            + "data-leaf=\"" + category["leaf"] + "\""
                            + "data-tag=\"" + category["keyword"] + "\""
                            + "data-query=\"" + category["query"] + "\""
                            + "data-cn=\""+ category["node_name"] +"\""
                            + "data-en=\""+ category["pin"] +"\">"
                            + "<a href=\"javascript: void(0)\">"
                            + category["node_name"] +"</a></li>";
                    };
                }else{
                var html_str = "<div class='form-group search-div'><input type='text' class='cate-search form-control'"
                        +" placeholder='搜索.....'><span class='glyphicon glyphicon-search form-control-feedback'></span></div>";
                for(var i=0;i<categories.length;i++){
                    var category = categories[i];
                    var class_name = category["leaf"] == 0 ? "has-leaf" : "no-leaf";
                     html_str += "<li class=\"" + class_name +"\" "
                        + "data-id=\"" + category["node_id"] + "\""
                        + "data-level=\"" + category["level"] + "\""
                        + "data-leaf=\"" + category["leaf"] + "\""
                        + "data-tag=\"" + category["keyword"] + "\""
                        + "data-query=\"" + category["query"] + "\""
                        + "data-tpl=\"" + category["tpl_id"] + "\""
                        + "data-cn=\"\""
                        + "data-en=\""+ category["node_name"] +"\">"
                        + "<a href=\"javascript: void(0)\">"
                        + category["node_name"] +"</a></li>";
                }
            }
            return html_str;
        }
    }
    $("#image-net").click(function(){
        var m = $("#image-net-url");
        var net_url = m.val();
        if(!net_url || net_url == ""){
            return 0;
        }
        $("#feed_img").find(".image").eq(images.length).attr("src", net_url).attr("draggable","false");
        m.val("");
        $("#image-net-collapse").collapse("hide");
        images.push(net_url);
    });
    $("#trans-control").click(function(){
        var modal = $("#trans-control-modal");
        var trans_title = $(".tr-title").prop("checked");
        var trans_desc = $(".tr-desc").prop("checked");
        var trans_spec = $(".tr-spec").prop("checked") || false;
        var trans_key = $(".tr-key").prop("checked") || false;
        var trans_point = $(".tr-point").prop("checked") || false;
        var trans_pid = $("#trans-pid").val();
        var src_lang = $("#src-lang").val() || "";
        var tar_lang = $("#tar-lang").val() || "";
        if(!src_lang){
            modal.find(".tips").text("请选择源语言!");
            return
        }
        if(!tar_lang){
            modal.find(".tips").text("请选择目标语言!");
            return
        }
        $(this).attr("disabled", false);
        modal.modal("hide");
        Inform.disable();
        Inform.show("", true, "正在提交请求...");
        var request_body = {
            "Title": trans_title,
            "Description": trans_desc,
            "Specifics": trans_spec,
            "KeyWords": trans_key,
            "BulletPoints": trans_point,
            "product_id": trans_pid,
            "src_lang": src_lang,
            "tar_lang": tar_lang
        };
        $.ajax({
            "url": "/create/" + shop_id + "/product/trans",
            "type": "POST",
            "dataType": "json",
            "data": request_body,
            "success": function(data){
                $(this).attr("disabled", true);
                console.log(data);
                if(data.status == 1){
                    Inform.enable("/product/"+shop_id+"/dealing");
                    Inform.show("翻译请求已提交");
                }else{
                    Inform.enable();
                    Inform.show("翻译请求被拒绝" + data.message);
                }
            },
            "error": function(){
                console.log("there is some error happened");
            }
        })
    });
    $(".del-pic").click(function(){
        if($(this).closest(".thumbnail").find("img").attr("src")!="/image/add.png"){
            var cur_dom = $(this);
            images = [];
            for(var i=0;i<image_length;i++){
                var url=load_img.eq(i).attr("src");
                if (url!="/image/add.png"){
                    images.push(url);
                }
            }
            var images_dom = $("#feed_img").find(".image");
            var image_index = parseInt(cur_dom.attr("data-index"));
            for(var i=image_index; i<images.length-1;i++){
                images[i] = images[i+1];
                images_dom.eq(i).attr("src", images[i]).attr("draggable","false");
            }
            images_dom.eq(images.length-1).attr("src", "/image/add.png").attr("draggable","false");
            images.splice(images.length-1, 1);
            console.log(images);
        }else{
            return 0;
        }
    });
    $(".del-s-pic").click(function(){
        $("#spread-img").find(".image").attr("src", "/image/add.png")
    });
    $('#upload').Huploadify({
		auto:true,
		fileTypeExts:'*.jpg;*.png;*.gif',
		multi:true,
        fileObjName:'Filedata',
		fileSizeLimit:9999,
		showUploadedPercent:true,//是否实时显示上传的百分比，如20%
		showUploadedSize:false,
		removeTimeout:2000,
        buttonText:'本地图片选取',//上传按钮上的文字
		uploader:"/?r=product/upload-image&shopId="+shop_id,//"/picture/upload/local",
		onUploadStart:function(){
			//alert('开始上传');
			},
		onInit:function(){
			//alert('初始化');
			},
		onUploadComplete:function(file,data,response){
            data = eval("("+data+")");
            if(data["success"] == true){
                var this_img = $("#feed_img").find(".image").eq(images.length);
                if(this_img.length){
                    this_img.attr("src", data["url"]).attr("draggable","false");
                    images.push(data["url"]);
                }
            }
        },
		onDelete:function(file){
			console.log('删除的文件：'+file);
			console.log(file);
		},
        onUploadError: function () {
            Inform.enable();
            Inform.show("上传失败，请您稍后再试。");
        }
		});
    $('#upload2').Huploadify({
		auto:true,
		fileTypeExts:'*.jpg;*.png;*.gif',
		multi:true,
        fileObjName:'Filedata',
		fileSizeLimit:9999,
		showUploadedPercent:true,//是否实时显示上传的百分比，如20%
		showUploadedSize:false,
		removeTimeout:2000,
        buttonText:'本地图片选取',//上传按钮上的文字
		uploader:"/picture/upload/local",
		onUploadStart:function(){
			//alert('开始上传');
			},
		onInit:function(){
			//alert('初始化');
			},
		onUploadComplete:function(file,data,response){
            data = eval("("+data+")");
            if(data["error"] == 0){
                var this_img = $("#spread-img").find(".image");
                if(this_img.length){
                    this_img.attr("src", data.url).attr("draggable","false");
                    images.push(data.url);
                }
            }
        },
		onDelete:function(file){
			console.log('删除的文件：'+file);
			console.log(file);
		},
        onUploadError: function () {
            Inform.enable();
            Inform.show("上传失败，请您稍后再试。");
        }
		});
    function set_progress(now,max){
        var rate = parseInt(now/max*100);
        $(".progress-bar").attr({
            "aria-valuenow": now,
            "aria-valuemax": max,
            "style": "width: "+rate+"%;"
        }).text((rate>100?100:rate)+"%");
    }
    function compute_pack_size(){
        var l = parseFloat(pack_detail.eq(0).val());
        var w = parseFloat(pack_detail.eq(1).val());
        var h = parseFloat(pack_detail.eq(2).val());
        if(!l){
            pack_detail.eq(0).val(1);
            l = 1;
        }else{
            pack_detail.eq(0).val(l)
        }
        if(!w){
            pack_detail.eq(1).val(1);
            w = 1;
        }else{
            pack_detail.eq(1).val(w)
        }
        if(!h){
            pack_detail.eq(2).val(1);
            h = 1
        }else{
            pack_detail.eq(2).val(h)
        }
        $("#pac-size").text(l*w*h);
    }
    function search_keyup(){
        $(".cate-search").keyup(function(){
            $(this).closest("ul").find("li").show();
            var this_list = $(this).closest("ul");
            clear_html(this_list);
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
    }
    function check_url(str_url){
        var re = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        return re.test(str_url)
    }
    function goto_source(){
        var $this = $(this),
            goto_btn = $(".goto-source"),
            $url = $this.closest(".input-group").find("input").val().trim();
        if($url){
            if(check_url($url)){
                goto_btn.attr("href", $url);
                goto_btn.get(0).click();
            }else{
                Inform.enable();
                Inform.show("您输入的链接不合法");
            }
        }
    }
    //图片裁剪，需要模态框
    $(".edit-pic").click(function(){
        var edit_modal = $("#edit-pic-modal"),
            this_img = $(this).closest(".thumbnail").find("img");
        if(this_img.attr("src")!="/image/add.png"){
            var pic_link = $(this).closest(".thumbnail").find("img").attr("src");
            var app1 = $("#edit-img-content").html("");
            var app = $("#edit-img-content").html("<div class='img-container'>"
                        +"<img src='" + pic_link + "' class='image img-responsive'>"
                        +"</div>");
            edit_modal.modal("show");

            var $image = $('.img-container > img'),
                $dataX = $('#dataX'),
                $dataY = $('#dataY'),
                $dataHeight = $('#dataHeight'),
                $dataWidth = $('#dataWidth'),
                $dataRotate = $('#dataRotate'),
                options = {
                  aspectRatio: NaN,
                  //preview: '.img-preview',
                  zoomable: false,
                  crop: function (data) {
                    $dataX.val(Math.round(data.x));
                    $dataY.val(Math.round(data.y));
                    $dataHeight.val(Math.round(data.height));
                    $dataWidth.val(Math.round(data.width));
                    $dataRotate.val(Math.round(data.rotate));
                  }
                };
            $image.cropper(options);

            // Methods
            $("#edit-pic-modal").find('[data-method="getCroppedCanvas"]').unbind().on('click', function () {
              var $btn = $(this),
                  data = $btn.data(),
                  $target,
                  result;
              $btn.html("处理中……").attr("style","cursor:not-allowed");
              if (data.method) {
                data = $.extend({}, data); // Clone a new one
                if (typeof data.target !== 'undefined') {
                      $target = $(data.target);
                      if (typeof data.option === 'undefined') {
                            try {
                              data.option = JSON.parse($target.val());
                            } catch (e) {
                              console.log(e.message+"one");
                            }
                      }
                }
                var $new_image = $('.img-container > img');
                var result = $new_image.cropper(data.method, data.option);
                var res = result.toDataURL("image/png").split(",");
                if (data.method === 'getCroppedCanvas') {
                  $.ajax({
                    "type": "POST",
                    "url": "/picture/upload/base",
                    "dataType": "json",
                    "data": {"filedata": res[res.length-1]},
                    "success":function(data){
                        if(data.status == 1) {
                            edit_modal.modal("hide");
                            this_img.attr("src",data.url);
                            $btn.html("确认").attr("style","cursor: pointer");
                        }else{

                        }
                    },
                    "error": function(){
                        console.log("there is some error happened while editing.");
                    }
                })
                }

                if ($.isPlainObject(result) && $target) {
                  try {
                    $target.val(JSON.stringify(result));
                  } catch (e) {
                    console.log(e.message + "two");
                  }
                }
              }
            });
            $("#edit-pic-modal").on('keydown', function (e) {
                  switch (e.which) {
                    case 37:
                      e.preventDefault();
                      $image.cropper('move', -1, 0);
                      break;

                    case 38:
                      e.preventDefault();
                      $image.cropper('move', 0, -1);
                      break;

                    case 39:
                      e.preventDefault();
                      $image.cropper('move', 1, 0);
                      break;

                    case 40:
                      e.preventDefault();
                      $image.cropper('move', 0, 1);
                      break;
                  }
            });
        }else{
            return 0;
        }
    });
    function clear_html(obj){
        obj.find("a").each(function(n,o){
            o.innerHTML = o.text;
        })
    }
});