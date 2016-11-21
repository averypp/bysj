/**
 * Created by GF on 2016/1/8.
 */

$(function(){
    var check_img_num = 0,
        max_length = 0,
        m_images = [];
    var btn_status, condition_info=[];
    var btn;
    var search_id = "";
    var platform = $(".shop-info").find("span").eq(0).text();
    var load_img = $("#feed_img").find(".image");
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
    max_length = img_length_info[platform];
    function is_default(treeId, treeNode){
        return treeNode.id!= "no-group" && treeId!="search-tree" && treeId!="move-tree"
    }
    function zTreeOnMouseUp() {
        $("#photoBankGroupList").find(".groupTitle").removeClass("activeGroup");
    }
    function zTreeOnClick(event, treeId, treeNode) {
        btn_status = "group";
        var treeObj = $.fn.zTree.getZTreeObj("tree");
        var t_id = treeNode["tId"];
        var page_n;
        if(treeId=="tree"){
            page_n = $(".footer-stat").find("button").attr("data-name") || 30;
            Image.show_pic_ajax(t_id,page_n,1);
            search_id = t_id;
        }else if(treeId=="search-tree"){
            $(".search-key").text(treeNode["name"]).attr("data-id",t_id);
        }else if(treeId=="tree-sku"){
            page_n = $(".footer-stat").eq(1).find("button").attr("data-name") || 30;
            Image.show_pic_ajax("tree_"+t_id.split("_")[1],page_n,1);
            search_id = "tree_"+t_id.split("_")[1];
        }
    }
    var zTreeObj,
        setting = {
            view: {
                selectedMulti: false,
                fontCss : {fontSize:"16px"}
            },
            drag:{
                isMove: false,
                isCopy: false
            },
            callback:{
                onMouseUp: zTreeOnMouseUp,
                onClick: zTreeOnClick
            },
            data:{
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pId",
                    rootPId: null
                }
            }
        },
        zTreeNodes = [{"name":"未分组图片", open:false, isParent:false, "id":"no-group"}];
    var Image = {
        init: function(){
            Inform.init();
            $("#end-time").datetimepicker({format: 'YYYY-MM-DD',defaultDate:{Default: true}});
            $("#start-time").datetimepicker({format: 'YYYY-MM-DD',defaultDate:{Default: true}});
            $("#sku-end-time").datetimepicker({format: 'YYYY-MM-DD',defaultDate:{Default: true}});
            $("#sku-start-time").datetimepicker({format: 'YYYY-MM-DD',defaultDate:{Default: true}});
            $("#image-space2").click(Image.show_modal);
            $("#sku-prop").on("click","[data-self]", Image.show_modal);
            $(".pic-space").click(Image.show_modal);
//            $('[data-name="imgSpace"]').click(Image.img_modal_init);
            $("#photoList").on("click",".picture",function(){
                $(this).find("input[type=checkbox]").trigger("click");
            })
                .on("click",".photo-checkbox",Image.check_pic);
            $("#sku-photoList").on("click",".picture",function(){
                $(this).find("input[type=checkbox]").trigger("click");
            })
                .on("click",".photo-checkbox",Image.check_pic);
            $("#photoArea").on("click",".goto-page",Image.goto_page)
                .on("click",".pic-every-page",Image.goto_page);
            $("#sku-photoArea").on("click",".goto-page",Image.goto_page)
                .on("click",".pic-every-page",Image.goto_page);
            $( "#sortable" ).sortable().disableSelection();
            $( "#sortable-sku" ).sortable().disableSelection();
            $(".img-temp-list").on("click",".del-temp-img",Image.del_temp_img);
            $(".groupTitle").on("click",Image.group_active).on("click",Image.show_group_pic);
            $("#search-pic").click(Image.search_pic);
            $("#pic-filter").click(Image.filter_pic);
            $("#search-pic-sku").click(Image.search_pic);
            $("#pic-filter-sku").click(Image.filter_pic);
            $("#search-in-all").click(function(){
                $(".search-key").text("所有分组").attr("data-id","all_group");
            });
            $("#image-space-modal2").on('shown.bs.modal', function () {
                Image.img_modal_init();
            });
            $("#img-ensure-btn2").click(Image.img_ensure);
            Image.check_img_length();
            $(".form-horizontal").on("click","[data-name=imgSpace]",function(){
               btn = "description_img";
            });
        },
        check_img_length:function(){
            m_images = [];
            for(var i=0;i<max_length;i++){
                var url=load_img.eq(i).attr("src");
                if (url!="/image/add.png"){
                    m_images.push(url);
                }
            }
        },
        show_modal:function(){
            btn = $(this);
            window.btn_show_modal = "";
            if(btn.attr("data-self")=="amazon-sku"){
                Image.img_modal_init();
            }else{
                $("#image-space-modal2").modal("show");
            }
        },
        img_modal_init:function(){
            $("#tree").empty();
            $("#tree-sku").empty();
            zTreeNodes = [{"name":"未分组图片", open:false, isParent:false, "id":"no-group"}];
            $.ajax({
                url: "/picture/modal",
                type: "POST",
                success: function(data){
                    var used_space = data["used_space"];
                    Image.check_used_space(1000,used_space);
                    var pic_group = $.parseJSON(data["pic_group"]);
                    if(pic_group.length!=0){
                        for(var i=0;i<pic_group.length;i++){
                            var $nodes = pic_group[i].split(";");
                            zTreeNodes.push({id:$nodes[1],pId:$nodes[0],name:$nodes[2]});
                        }
                    }
                    if(btn && btn.attr("data-self")=="amazon-sku"){
                        zTreeObj = $.fn.zTree.init($("#tree-sku"), setting, zTreeNodes);
                    }else{
                        zTreeObj = $.fn.zTree.init($("#tree"), setting, zTreeNodes);
                    }
                }
            });
            $("#btnShowAllGroup").trigger("click");
            if(window.btn_show_modal==""){
                if(btn && btn.attr("data-self")=="amazon-sku"){

                }else if(btn && btn.attr("data-self")=="product"){
                    Image.check_img_length();
                    $("#max-select2").show().prev().show();
                    $("#max-select2").next().show();
                    $("#max-select2").text(max_length-m_images.length);
                    $("#has-select2").text(0);
                }else{
                    $("#max-select2").hide().prev().hide();
                    $("#max-select2").next().hide();
                    $("#has-select2").text(0);
                }
            }else{
                $("#max-select2").text(9);
                $("#has-select2").text(0);
            }
            $(".img-temp-list").empty();
            check_img_num = 0;
        },
        check_used_space:function(max_space, used_space){
            var used_space = parseFloat(used_space);
            var percent = used_space/max_space;
            var percent_div = $(".percent-status");
            $(".percent-value").text(used_space+"/1000M");
            percent_div.css("width",percent*100+"%");
            if(percent>0){
                if(percent<0.5){
                    percent_div.css("background-color","#5fb129");
                }else if(percent>=0.5&&percent<0.8){
                    percent_div.css("background-color","#f0ad4e");
                }else{
                    percent_div.css("background-color","#d9534f");
                }
            }
        },
        group_active:function(){
            btn_status = "group";
            Image.check_all_group(); // cancel tree node selected
            var $this = $(this);
            $this.addClass("activeGroup").closest("#photoBankGroupList").find(".groupTitle").not($this).removeClass("activeGroup");
        },
        check_all_group:function(){
            var treeObj;
            if(btn){
                treeObj = btn.attr("data-self")=="amazon-sku" ? $.fn.zTree.getZTreeObj("tree-sku") : $.fn.zTree.getZTreeObj("tree");
            }
            if(treeObj){
                if(treeObj.getSelectedNodes()){
                    treeObj.cancelSelectedNode();
                }
            }
        },
        show_pic_ajax:function(group_id, page_n, c_page){
            $(".loading-tip").show().siblings().hide();
            $.ajax({
                url: "/picture/group/pic",
                type: "POST",
                data: {
                    group_id: group_id,
                    page_n: page_n,
                    c_page: c_page
                },
                success: function(data){
                    $(".loading-tip").hide();
                    if(data.status == 1){
                        if(data["pictures"].length==0){
                            var no_pic_tip = (btn && btn.attr("data-self") == "amazon-sku") ? $("#no-pic-tip-sku") : $("#no-pic-tip");
                            no_pic_tip.show().siblings().hide();
                            $(".img-temp-list").css("margin-top","");
                        }else{
                            var pictures = data["pictures"],
                                col_map = data["col_map"],
                                page_total = data["page_total"],
                                page_n = data["page_n"],
                                pages = data["pages"],
                                c_page = data["c_page"];
                            Image.render_pic(pictures,col_map,page_total,page_n,pages,c_page);
                            $(".img-temp-list").css("margin-top","21px");
                        }
                    }else{
                        Inform.show("操作失败");
                    }

                },
                error: function(){
                    Inform.enable();
                    Inform.show("操作失败");
                }
            });
        },
        render_pic:function(pictures,col_map,page_total,page_n,pages,c_page){
            Image.empty_pic_area();
            var pic_area = (btn && (btn.attr("data-self")=="amazon-sku")) ? $("#sku-photoList") : $("#photoList");
            pic_area.show().siblings().hide();
            for(var i=0; i<pictures.length; i++){
                var add_div = Image.create_pic(pictures[i]);
                pic_area.append(add_div)
            }
            var page_str = '<div class="row footer-stat">'+
                        '<ul class="nav pull-right">'+
                        '<li><ul class="pagination page-bar">{0}{1}{2}</ul></li>'+
                        '<li class="btn-group page-n-ctrl">'+
                        '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" data-name='+page_n+'>'+
                        '每页显示'+page_n+'个 <span class="caret"></span>'+
                        '</button>'+
                        '<ul class="dropdown-menu" role="menu">'+
                        '<li><a href="javascript:void(0)" class="pic-every-page">30</a></li>'+
                        '<li><a href="javascript:void(0)" class="pic-every-page">50</a></li>'+
                        '</ul>'+
                        '</li>'+
                        '<li style="line-height:36px;">共'+page_total+'页</li>'+
                        '</ul></div>';
            var p_str = "",
                n_str = "",
                m_str = "";
            if(c_page==1){
                p_str = '<li class="disabled">'+
                        '<a href="javascript:void(0)" aria-label="Previous">'+
                        '<span aria-hidden="true">&laquo;</span>'+
                        '</a>'+
                        '</li>';
            }else{
                p_str = '<li>'+
                        '<a href="javascript:void(0)" aria-label="Previous" class="goto-page" data-name='+(c_page-1)+'>'+
                        '<span aria-hidden="true">&laquo;</span>'+
                        '</a>'+
                        '</li>';
            }
            if(c_page >= page_total){
                n_str = '<li class="disabled">'+
                        '<a href="javascript:void(0)" aria-label="Previous">'+
                        '<span aria-hidden="true">&raquo;</span>'+
                        '</a>'+
                        '</li>';
            }else{
                n_str = '<li>'+
                        '<a href="javascript:void(0)" aria-label="Next" class="goto-page" data-name='+(c_page+1)+'>'+
                        '<span aria-hidden="true">&raquo;</span>'+
                        '</a>'+
                        '</li>';
            }
            for(var page=0;page<pages.length;page++){
                if(pages[page]==c_page){
                    m_str += '<li class="active" data-name='+pages[page]+'>'+
                            '<a href="javascript:void(0)">'+pages[page]+''+
                            '</a>'+
                            '</li>';
                }else{
                    m_str += '<li>'+
                            '<a href="javascript:void(0)" class="goto-page" data-name='+pages[page]+'>'+pages[page]+''+
                            '</a>'+
                            '</li>';
                }
            }
            page_str = page_str.format(p_str,m_str,n_str);
            pic_area.after(page_str);
        },
        create_pic:function(picture){
            var operate_div,operate_str,img_div, img, info_div, title_div, block;
            block = $("<div/>").attr("class", "picture");
            img = $("<img/>").attr({
                "class": "image",
                "title": picture.Name,
                "src": picture.Link
            });
            operate_str = '<label><input type="checkbox" class="photo-checkbox"></label>'+
                '<span class="operate-btns" style="display: none;">'+
                '<a class="pic-delete" href="javascript:void(0)" title="删除">'+
                '<span class="glyphicon glyphicon-remove"></span></a>'+
                '<a class="pic-edit" href="javascript:void(0)" title="编辑图片">'+
                '<span class="glyphicon glyphicon-edit"></span></a>'+
                '<a class="copy_link" href="javascript:void(0)" title="复制图片链接">'+
                '<span class="glyphicon glyphicon-copy"></span></a></span>';
            img_div = $("<div/>").attr("class", "img-content");
            operate_div = $("<div/>").attr({
                "class": "photo-operate",
                "data-id": picture.Id
            }).append(operate_str);
            img_div.append(img);
            info_div = $("<div/>").attr("class", "pic-info").html("<span>尺寸：{0}</span> <span>大小：{1}</span>".format(picture.Size, picture.Length));
            title_div = $("<div/>").attr("class", "pic-name").text(picture.Name);
            block.append(operate_div,img_div,title_div,info_div);
            return block
        },
        show_group_pic:function(){
            var $this = $(this);
            var group_id = "all_group";
            var page_n = $(".footer-stat").find("button").attr("data-name") || 30;
            if($this.prop("id")=="btnShowAllGroup"){
                Image.show_pic_ajax("all_group",page_n,1)
            }else if($this.prop("id")=="recycleBox"){
                group_id = "tree_0";
                Image.show_pic_ajax(group_id,page_n,1)
            }
            search_id = group_id;
        },
        check_pic:function(e){
            e.stopPropagation();
            var $this = $(this);var is_check = $this.prop("checked");
            var pic_src = $this.closest(".picture").find("img").attr("src"),
                pic_id = $this.closest(".photo-operate").attr("data-id");
            var img_temp_list;
            is_check ? $this.siblings(".operate-btns").show() : $this.siblings(".operate-btns").hide();
            if(btn&&window.btn_show_modal==""){
                if(btn&&btn.attr("data-self")=="amazon-sku"){
                    img_temp_list = $("#sortable-sku");
                    var max_sku_img = Number($("#max-select-mod").text());
                    var $max_text = $("#already-select-mod");
                    var sku_img_length = Number($max_text.text());
                    if(max_sku_img>0&&max_sku_img-sku_img_length>0){
                        if(is_check){
                            var pic_str = '<div class="img-pre-box" data-id="'+pic_id+'"><a href="javascript:void(0)"><span class="del-temp-img"></span></a><img src='+pic_src+'></div>';
                            img_temp_list.append(pic_str);
                            $this.closest(".picture").addClass("pic-checked");
                            sku_img_length +=1;
                        }else{
                            $this.closest(".picture").removeClass("pic-checked");
                            sku_img_length -=1;
                        }
                        $max_text.text(sku_img_length);
                    }else{
                        is_check && $(this).removeAttr("checked")
                    }
                }else if(btn&&btn.attr("data-self")=="product"){
                    img_temp_list = $("#sortable");
                    if(check_img_num<(max_length-m_images.length)){
                        if(is_check){
                            check_img_num+=1;
                            var pic_str = '<div class="img-pre-box" data-id="'+pic_id+'"><a href="javascript:void(0)"><span class="del-temp-img"></span></a><img src='+pic_src+'></div>';
                            img_temp_list.append(pic_str);
                            $this.closest(".picture").addClass("pic-checked")
                        }else{
                            check_img_num-=1;
                            $this.closest(".picture").removeClass("pic-checked");
                        }
                    }else{
                        is_check ? $(this).removeAttr("checked") : check_img_num-=1;
                        is_check || $this.closest(".picture").removeClass("pic-checked");
                    }
                    $("#max-select2").text(max_length-m_images.length);
                    $("#has-select2").text(check_img_num);
                }else{
                    img_temp_list = $("#sortable");
                    if(is_check){
                        check_img_num+=1;
                        var pic_str = '<div class="img-pre-box" data-id="'+pic_id+'"><a href="javascript:void(0)"><span class="del-temp-img"></span></a><img src='+pic_src+'></div>';
                        img_temp_list.append(pic_str);
                        $this.closest(".picture").addClass("pic-checked")
                    }else{
                        check_img_num-=1;
                        $this.closest(".picture").removeClass("pic-checked");
                    }
                    $("#has-select2").text(check_img_num);
                }
            }else{
                img_temp_list = $("#sortable");
                var $text = $("#has-select2");
                var $length = Number($text.text());
                if($length<9){
                    if(is_check){
                        $length+=1;
                        var pic_str = '<div class="img-pre-box" data-id="'+pic_id+'"><a href="javascript:void(0)"><span class="del-temp-img"></span></a><img src='+pic_src+'></div>';
                        img_temp_list.append(pic_str);
                        $this.closest(".picture").addClass("pic-checked")
                    }else{
                        $length-=1;
                        $this.closest(".picture").removeClass("pic-checked");
                    }
                }else{
                    is_check ? $(this).removeAttr("checked") : $length-=1;
                    is_check || $this.closest(".picture").removeClass("pic-checked");
                }
                $text.text($length);
            }
            is_check || img_temp_list.find("div[data-id="+pic_id+"]").remove();
        },
        goto_page:function(){
            var treeObj, group_id;
            if(btn&&btn.attr("data-self")=="amazon-sku"){
                treeObj = $.fn.zTree.getZTreeObj("tree-sku");
                group_id = $(".pic-space-area").find(".activeGroup").prop("id")=="recycleBox" ? "tree_0" : "all_group";
            }else{
                treeObj =  $.fn.zTree.getZTreeObj("tree");
                group_id = $("#image-space-modal2").find(".activeGroup").prop("id")=="recycleBox" ? "tree_0" : "all_group";
            }
            var nodes = treeObj.getSelectedNodes();
            if(nodes.length==0){

            }else{
                group_id = "tree_"+(nodes[0].tId).split("_")[1];
            }
            var $this = $(this);
            var page_n, c_page;
            if($this.attr("class")=="pic-every-page"){
                page_n = $this.text();
                c_page = 1;
            }else{
                page_n = $this.closest(".footer-stat").find("button").attr("data-name");
                c_page = $(this).attr("data-name");
            }
            if(btn_status=="group"){
                Image.show_pic_ajax(group_id,page_n,c_page);
            }else if(btn_status=="group-filter"||btn_status=="search-filter"){
                Image.filter_pic(page_n,c_page);
            }else if(btn_status=="search"){
                Image.search_pic(page_n,c_page)
            }
        },
        empty_pic_area:function(){
            var pic_area = (btn && (btn.attr("data-self")=="amazon-sku")) ? $("#sku-photoList") : $("#photoList");
            pic_area.empty();
            pic_area.siblings(".footer-stat").remove();
        },
        del_temp_img:function(){
            var $this = $(this);
            var img_id = $this.closest(".img-pre-box").attr("data-id");

            $this.closest(".img-pre-box").remove();
            if(btn.attr("data-self")!="amazon-sku"){
                check_img_num-=1;
                $("#max-select2").text(max_length-m_images.length);
                $("#has-select2").text(check_img_num);
                $("#photoList").find(".photo-operate[data-id=\""+img_id+"\"]").find(".photo-checkbox").attr("checked",false);
            }else{
                var $max_text = $("#already-select-mod");
                var sku_img_length = Number($max_text.text());
                $max_text.text(sku_img_length-1);
                $("#sku-photoList").find(".photo-operate[data-id=\""+img_id+"\"]").find(".photo-checkbox").attr("checked",false);
            }
        },
        search_pic:function(){
//            var tree_index = $(".search-key").attr("data-id");
//            var group_id = tree_index == "all_group" ? "all_group" : "tree_"+tree_index.split("_")[1],
            var pic_title_input;
            if(btn&&btn.attr("data-self") == "amazon-sku"){
                pic_title_input = $("#sku-search-input");
            }else{
                pic_title_input = $("#search-input");
            }
            var pic_title = pic_title_input.val().trim();
            var start_time = "",
                end_time = "";
            if(btn_status=="filter"){
                start_time = condition_info["start_time"];
                end_time = condition_info["end_time"];
            }
            btn_status = "search";
            condition_info["group_id"] = search_id;
            condition_info["pic_title"] = pic_title;
            var page_n = Number($(".pic-every-page").text()),
                c_page = 1;
            console.log(arguments[0]);
            if(arguments&&arguments.length>1){
                page_n = arguments[0];
                c_page = arguments[1];
            }
            if(pic_title){
                pic_title_input.prop("placeholder","图片名称");
                $(".loading-tip").show().siblings().hide();
                $.ajax({
                    url: "/picture/search",
                    type: "POST",
                    data: {
                        "group_id": search_id,
                        "pic_title": pic_title,
                        "page_n": page_n,
                        "c_page": c_page
                    },
                    success:function(data){
                        $(".loading-tip").hide();
                        if(data["pictures"].length!=0){
                            var pictures = data["pictures"],
                                col_map = data["col_map"],
                                page_total = data["page_total"],
                                page_n = data["page_n"],
                                pages = data["pages"],
                                c_page = data["c_page"];
                            Image.render_pic(pictures,col_map,page_total,page_n,pages,c_page);
                        }else{
                            Image.empty_pic_area();
                            var no_search_tip = btn.attr("data-self") == "amazon-sku" ? $("#no-search-tip-sku") : $("#no-search-tip");
                            no_search_tip.show().siblings().hide();
                        }
                    }
                })
            }else{
                pic_title_input.prop("placeholder","请输入图片名称")
            }
        },
        filter_pic:function(){
            var start_time, end_time, treeObj;
            if(btn&&btn.attr("data-self") == "amazon-sku"){
                start_time = $("#sku-start-time").val();
                end_time = $("#sku-end-time").val();
                treeObj = $.fn.zTree.getZTreeObj("tree-sku");
            }else{
                start_time = $("#start-time").val();
                end_time = $("#end-time").val();
                treeObj = $.fn.zTree.getZTreeObj("tree");
            }
            var nodes = treeObj.getSelectedNodes();
            var group_id = nodes.length==0 ? "all_group" : "tree_"+(nodes[0].tId).split("_")[1];
            var pic_title = "";
            if(btn_status=="search"||btn_status=="search-filter"){
                group_id = condition_info["group_id"];
                pic_title = condition_info["pic_title"];
                btn_status = "search-filter";
            }else{
                btn_status = "group-filter";
            }
            condition_info["start_time"] = start_time;
            condition_info["end_time"] = end_time;
            var page_n = Number($(".pic-every-page").text()),
                c_page = 1;
            if(arguments&&arguments.length>1){
                page_n = arguments[0];
                c_page = arguments[1];
            }
            $(".loading-tip").show().siblings().hide();
            $.ajax({
                url: "/picture/filter",
                type: "POST",
                data: {
                    "group_id": group_id,
                    "start_time": start_time,
                    "end_time": end_time,
                    "pic_title": pic_title,
                    "page_n": page_n,
                    "c_page": c_page
                },
                success:function(data){
                    $(".loading-tip").hide();
                    if(data["pictures"].length!=0){
                        var pictures = data["pictures"],
                            col_map = data["col_map"],
                            page_total = data["page_total"],
                            page_n = data["page_n"],
                            pages = data["pages"],
                            c_page = data["c_page"];
                        Image.render_pic(pictures,col_map,page_total,page_n,pages,c_page);
                    }else{
                        Image.empty_pic_area();
                        var no_search_tip = btn.attr("data-self") == "amazon-sku" ? $("#no-search-tip-sku") : $("#no-search-tip");
                        no_search_tip.show().siblings().hide();
                    }
                }
            })
        },
        img_ensure:function(){
            var checked = $(".img-temp-list").find("img");
            var checked_num = checked.length;
            for(var i=0;i<checked_num;i++){
                var net_url = checked.eq(i).attr("src");
//                console.log("123456000000000=" + btn.attr("data-self"));
                if(btn && window.btn_show_modal==""){
                    if(btn.attr("data-self") == "ali-sku"){
                        var skupic = btn.closest("tr").find("td[data-id]").attr("data-id");
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
                    }else if(btn.attr("data-self") == "amazon-sku"){

                    }else if(btn.attr("data-self") == "sku"){
                        var skupic = btn.closest("tr").find("td").eq(0).text();
                        var str_tdpic ='<div class="pre-all" >'
                        +'<div class="pre-img" >'
                            +'<img src="'+net_url+'" style="width: 100%; height: 100%"/>'
                        +'</div>'
                        +'<div>'
                            +'<a class="pre-img-del" data-name="del-pic"  href="javascript:void(0)">删除</a>'
                            +'<a class="pre-img-edit" data-name="edit-pic" href="javascript:void(0)">编辑</a>'
                        +'</div>'
                        +'</div>';
                        $("td[pic='" + skupic +"']").append(str_tdpic);

                    }else{
                        $("#feed_img").find(".image").eq(m_images.length).attr("src", net_url).attr("draggable","false");
                        m_images.push(net_url);
                    }
                }else{
                    editor.insertHtml('<img src='+net_url+'>');
                }
            }
            $("#image-space-modal2").modal("hide");
        }
    };
    Image.init();
});