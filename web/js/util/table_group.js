/**
 * Created by GF on 2016/6/1.
 */
(function ($) {
    $.fn.gfGroup = function(options){
        var $element = $(this);
        var condition ={},
            tool_str = '<span class="operate-btns">',
            add_str = '<a class="gf-group-add" href="javascript:void(0)" title="添加分组">'+
                        '<span class="glyphicon glyphicon-plus"></span></a>',
            edit_str = '<a class="gf-group-edit" href="javascript:void(0)" title="编辑分组">'+
                        '<span class="glyphicon glyphicon-edit"></span></a>',
            delete_str = '<a class="gf-group-delete" href="javascript:void(0)" title="删除分组">'+
                          '<span class="glyphicon glyphicon-remove"></span></a></span>',
            checkbox_str = '<input type="checkbox" class="check-gf-group">';
        var level = 1; // 分组要相对所有分组偏移1，所以不从0开始
        var defaults = {
            is_checkbox: false, // 是否在前面显示复选框
            is_trigger: true, // 是否点击触发
            is_all_group_href: true, // 所有分组是否可以点击
            is_leaf_href: true, // 是否只有叶子节点可以点击
            show_no_group: true, // 是否显示未分组
            is_link: true, // 是否生成链接
            is_add: true, // 是否可新增分组
            is_edit: true, // 是否可编辑
            is_delete: true, // 是否可删除
            limit: 999, // 最大目录数
            max_level: 3, // 最大层从0开始
            is_request: true, // 是否通过请求来初始化分组
            is_sync: true, // 是否可从线上同步
            out_time: 5000, // 请求超时时间
            sync_url: "",
            init_url: "group/init",
            add_url: "group/insert",
            edit_url: "group/update",
            delete_url: "group/delete",
            insert_target: $(".group-detail"), // 渲染分组的容器div
            group_modal: $("#group-modal"), // 没有设为null
            loading_text: "正在加载分组...",
            no_group_tip: "您的商品未设置分组",
            fail_tip: "获取分组失败，请稍后重试",
            group_data: [ // 如不发送请求初始化 需提供分组数据 没有设为null
                { // 测试数据
                    "groupName": "1",
                    "groupId": 111111,
                    "childGroup": [
                        {"groupName": "11", "groupId": 222222},
                        {
                            "groupName": "12",
                            "groupId": 333333,
                            "childGroup":[
                                {
                                    "groupName": "121",
                                    "groupId": 444444,
                                    "childGroup":[
                                        {"groupName": "1211", "groupId": 5555555}
                                    ]
                                },
                                {"groupName": "122", "groupId": 6666666}
                            ]
                        }
                    ]
                },
                {
                    "groupName": "2",
                    "groupId": 7777777,
                    "childGroup":[
                        {"groupName": "21", "groupId": 8888888}
                    ]
                },
                {
                    "groupName": "3",
                    "groupId": 999999,
                    "childGroup": [
                        {"groupName": "31", "groupId": 101010100},
                        {"groupName": "32", "groupId": 121212121, "childGroup":[{"groupName": "321", "groupId": 14141441411}]},
                        {"groupName": "33", "groupId": 181811818, "childGroup":[{"groupName": "331", "groupId": 161616161}]}
                    ]
                }
            ]
        };
        var ops = $.extend(defaults, options);
        var _tool = tool_str,
            _tool_leaf = tool_str;
        ops.is_add && (_tool += add_str);
        if(ops.is_edit){
            _tool += edit_str;
            _tool_leaf += edit_str;
        }
        if(ops.is_delete){
            _tool += delete_str;
            _tool_leaf += delete_str;
        }
        _tool += '</span>';
        _tool_leaf += '</span>';
        var Group = {
            group_selectable: true,
            init:function(){
                ops.insert_target.css("position", "relative");
                if(ops.is_trigger){
                    $element.click(Group.select_group); // 触发选择分组的按钮
                }else{
                    Group.select_group();
                }
                if(ops.is_checkbox){
                    ops.insert_target.on("change", ".check-gf-group", Group.group_checkbox_change);
                }
                ops.insert_target.on("click",".gf-group-delete",Group.del_group)
                    .on("click",".gf-group-edit",Group.edit_group)
                    .on("click",".gf-group-add",Group.add_group)
                    .on("click",".gf-group-ensure",Group.ensure_group)
                    .on("click",".gf-group-cancel",Group.ensure_group)
                    .on("click",".gf-tip-close",function(){$(".gf-tip-text").filter(":visible").css("color", "");$(".gf-global-tip").hide();});
                if(ops.is_sync){
                    ops.insert_target.on("click",".sync-group-btn",Group.sync_group);
                }
            },
            // 分组前的checkbox事件
            group_checkbox_change: function(){
                var $this = $(this),
                    $area = ops.insert_target;
                if($this.prop("checked")){
                    $area.find(".check-gf-group").not($this).each(function(k, v){
                        $(v).prop("checked", false);
                    })
                }
            },
            // 获取选中的分组
            get_checked_group: function(){

            },
            // 选择分组
            select_group:function(){
                console.log(Group.select_group.caller);
                console.log(event);
                if(ops.group_modal != null){
                    ops.group_modal.modal("show");
                }
                if($element.length != 0 && $element.attr("class").indexOf("sync-group-btn") != -1){
                    Inform.disable();
                    Inform.show('<div style="width: 16px; height: 16px; float:left; margin-left: 5px">'+
                             '<img src="/static/image/spinner.gif" style="width: 100%; height: 100%"/>'+
                             '</div>'+
                             '<span style="line-height: 32px; margin-left: 10px">'+ops.loading_text+'</span>');
                }else{
                    ops.insert_target.html('<div style="width: 16px; height: 16px; float:left; margin-left: 5px">'+
                                     '<img src="/static/image/spinner.gif" style="width: 100%; height: 100%"/>'+
                                     '</div>'+
                                     '<span style="line-height: 32px; margin-left: 10px">'+ops.loading_text+'</span>');
                }
                var no_group = "<div class='gf-category' data-id='-1'><a class='group_a' href='"
                                    + Group.change_url("g", 0)+"'>未分组</a></div>";
                var all_group = ops.is_all_group_href ?  ("<div class='gf-category' data-id='0'><a class='group_a' href='" + Group.change_url("g", -1)+"'>所有分组</a>{0}</div>") :
                            ("<div class='gf-category' data-id='0'><span class='group_a'>所有分组</div>{0}</span>");
                if(ops.is_add){
                    all_group = all_group.format(tool_str+add_str+'</span>');
                }else{
                    all_group = all_group.format("");
                }
                var inher_group = "";
                if(ops.show_no_group){
                    inher_group = no_group + all_group;
                }else{
                    inher_group = all_group;
                }
                // 需要增加获取当前函数的调用者，如果是sync调用，直接渲染 不请求init_url
                if(ops.is_request){
                    $.ajax({
                        "timeout": ops.out_time,
                        "url": ops.init_url,
                        "type": "POST",
                        "dataType": "json",
                        "success": function(data){
                            if(data.status == 1){
                                if($element.length != 0 && $element.attr("class").indexOf("sync-group-btn") != -1){
                                    Inform.enable();
                                    Inform.show("获取分组成功");
                                }
                                if(!data["json"]){
                                    if(ops.is_sync){
                                        ops.insert_target.html(ops.no_group_tip);
//                                        ops.insert_target.append('<button type="button" class="btn btn-info sync-group-btn" ' +
//                                            'data-loading-text="正在同步" style="margin-left: 15px">同步分组</button>');
                                    }else{
                                        ops.insert_target.empty().append(inher_group);
                                    }
                                }else{
                                    ops.insert_target.empty().append(inher_group);
                                    for(var i=0;i<data["json"].length;i++){
                                        Group.render_group([data["json"][i]], "0");
                                        if(i < data["json"].length -1){
                                            ops.insert_target.append('<div class="gf-divider"></div>')
                                        }
                                    }
                                }
                            }else{
                                if(ops.group_modal){
                                    ops.group_modal.modal("hide");
                                }
                                if(data.message != "距离上次同步时间小于5分钟，请稍后再试"){
                                    ops.insert_target.empty().append(ops.fail_tip); // 之后加个判断 同步失败和初始化失败分开
                                }
                                Inform.enable();
                                Inform.show(data.message);
                            }

                        },
                        "error": function(){
                        },
                        "complete": function(status){
                            ops.insert_target.append('<div class="gf-global-tip"><div class="gf-tip-text"></div>' +
                                            '<a href="javascript:void(0)" class="gf-tip-close">&times;</a></div>');
                            Group.timeout_tip(status);
                        }
                    });
                }else if(ops.group_data){
                    ops.insert_target.empty().append(inher_group);
                    ops.insert_target.append('<div class="gf-global-tip"><div class="gf-tip-text"></div>' +
                                        '<a href="javascript:void(0)" class="gf-tip-close">&times;</a></div>');
                    for(var i=0;i<ops.group_data.length;i++){
                        Group.render_group([ops.group_data[i]], "0");
                        if(i < ops.group_data.length -1){
                            ops.insert_target.append('<div class="gf-divider"></div>')
                        }
                    }
                }else{
                    Inform.show("配置错误，请联系管理员")
                }
            },
            render_group:function(group_list, pid){ // 根分组pid = 0
                var gl = group_list,
                    html = "";
                var $tool;
                if(ops.is_add || ops.is_edit || ops.is_delete){
                    if(level >= ops.max_level+1){
                        $tool = _tool_leaf;
                    }else{
                        $tool = _tool;
                    }
                }else{
                    $tool = "";
                }
                var $pid = pid;
                for (var i=0; i<gl.length; i++){
                    var child = gl[i]["childGroup"],
                        $check_str = ops.is_checkbox ? checkbox_str : "";
                    if(!child || child.length == 0){
                        html = "<div class='gf-category'"+
                            "style='margin-left: "+26*level+"px' data-pid='"+$pid+"' data-id='"+gl[i]["groupId"]+ "'data-name='"+ gl[i]["groupName"] +
                            " '>{0}<a class='group_a' href='"+Group.change_url("g", gl[i]["groupId"])+"'>"+gl[i]["groupName"]+"</a>"+ $tool+ "</div>";
                        ops.insert_target.append(html.format($check_str));
                    }else{
                        html = ops.is_leaf_href ? ("<div class='gf-category'"+
                            "style='margin-left: "+26*level+"px' data-pid='"+$pid+"' data-id='"+gl[i]["groupId"]+ "'data-name='"+ gl[i]["groupName"] +
                            " '><span class='group_a'>"+gl[i]["groupName"]+"</span>"+ $tool+ "</div>")
                        :("<div class='gf-category'"+
                            "style='margin-left: "+26*level+"px' data-pid='"+$pid+"' data-id='"+ gl[i]["groupId"]+ "' data-name='"+ gl[i]["groupName"] +
                             "'>{0}<a class='group_a' href='"+Group.change_url("g", gl[i]["groupId"])+"'>"+gl[i]["groupName"]+"</a>" + $tool + "</div>").format($check_str);
                        ops.insert_target.append(html);
                        level += 1;
                        for(var j=0; j<gl[i]["childGroup"].length; j++) {
                            Group.render_group([gl[i]["childGroup"][j]], gl[i]["groupId"]);
                        }
                        level -= 1;
                    }
                }
            },
            // 拼接url
            change_url: function(key, value){
                var url = "javascript:void(0)";
                if(ops.is_link){
                    url = location.href;
                    var string_url = "", re = /(?![ghktp])[a-z]\=\w+/g, $search, con_list;
                    if(value != -1) {
                        condition[key] = value;
                        string_url = "?";
                        for (var s in condition) {
                            string_url += s + "=" + condition[s] + "&";
                        }
                        url = string_url.substring(0, string_url.length - 1);
                        // 保留其他condition
                        $search = location.search;
                        if ($search != "") {
                            con_list = $search.match(re);
                            if (con_list) {
                                url += "&" + con_list.join("&")
                            }
                        }
                    }else if(value == -1 && key == 'g'){
                        string_url = "?";
                        for (var j in condition) {
                            if(j != 'g'){
                                string_url += j + "=" + condition[j] + "&";
                            }
                        }
                        url = string_url.substring(0, string_url.length - 1);
                        // 保留其他condition
                        $search = location.search;
                        if ($search != "") {
                            con_list = $search.match(re);
                            if (con_list) {
                                if(url != ''){
                                    url += "&" + con_list.join("&")
                                }else{
                                    url = "?" + con_list.join("&")
                                }
                            }else{
                                if(url == ''){
                                    url = location.pathname
                                }
                            }
                        }
                    }
                }
                return url;
            },
            del_group :function(){
                var _this = $(this),
                    group = _this.closest(".gf-category"),
                    group_id = group.attr("data-id"),
                    has_child = !(group.next(".gf-category").length == 0 ||
                        parseInt(group.next(".gf-category").css("margin-left")) <= parseInt(group.css("margin-left")));
                if(has_child){
                    _this.prop("disabled", true);
                    group.append("<span class='child-tip' style='color:#eb3c00;margin-left:15px'>请先删除子分组！</span>");
                    _this.mouseleave(function(){
                        $(".child-tip").fadeOut(250, function(){
                            $(".child-tip").remove();
                            _this.prop("disabled", false);
                        })
                    });
                    return false;
                }else{
                    Group.del_execute(group_id, group, _this);
                }
            },
            del_execute: function(group_id, group, $btn){
                var del_ensure = confirm("确定删除当前分组吗");
                if(del_ensure){
                    $btn.find("span").removeClass("glyphicon glyphicon-remove").addClass("gf-oper-loading");
                    $btn.siblings("a").each(function(k, v){
                        $(v).prop("disabled", true)
                    });
                    $.ajax({
                        timeout: ops.out_time,
                        url: ops.delete_url,
                        type: "POST",
                        data: {
                            "group_id": group_id
                        },
                        dataType: "JSON",
                        success: function(data){
                            if(data.status == 1){
                                if(group.get(0) == ops.insert_target.find(".gf-category").get(1)){
                                    group.nextAll(".gf-divider").eq(0).fadeOut(300, function(){
                                        group.nextAll(".gf-divider").eq(0).remove();
                                    })
                                }else if(group.prev().attr("class") == "gf-divider"){
                                    group.prev().fadeOut(300, function(){
                                        group.prev().remove();
                                    })
                                }
                                group.slideUp(300, function(){
                                    group.remove();
                                });
                            }else{
                                $(".gf-tip-text").text("删除失败,请重试");
                                $(".gf-global-tip").show();
                                $btn.find("span").removeClass("gf-oper-loading").addClass("glyphicon glyphicon-remove");
                                $btn.siblings("a").each(function(k, v){
                                    $(v).prop("disabled", false)
                                });
                                return false
                            }
                        },
                        error: function(XMLHttpRequest){
                            Inform.show(XMLHttpRequest.status);
                            $btn.find("span").removeClass("gf-oper-loading").addClass("glyphicon glyphicon-remove");
                            $btn.siblings("a").each(function(k, v){
                                $(v).prop("disabled", false)
                            });
                        },
                        complete: function(status){
                            Group.timeout_tip(status);
                        }
                    });
                }
            },
            // 重命名
            edit_group :function(){
                var _this = $(this),
                    group = _this.closest(".gf-category"),
                    edit_name = group.find(".group_a"),
                    //$(".group_a").text().slice(0,$(".group_a").text().indexOf("("))
                    content = edit_name.text(),
                    genName = edit_name.attr("data-name");
                _this.closest(".operate-btns").hide();
                edit_name.hide();
                edit_name.before(" <input name='" + genName + "' class='form-control gf-sort' type='text' " +
                                    "value=\""+content+"\"/><a class='gf-group-ensure' href='javascript:void(0)' title='确认修改'>" +
                                    "<span class='glyphicon glyphicon-ok'></span></a>" +
                                    "<a class='gf-group-cancel' href='javascript:void(0)' title='取消修改'>" +
                                    "<span class='glyphicon glyphicon-remove'></span></a>");
                var $obj = group.find("input[type=\"text\"]");
                Group.setCursorPosition($obj[0], 0, $obj.val().trim().length);
            },
            // 新增分组
            add_group :function(){
                var _this = $(this),
                    $category = _this.closest(".gf-category"),
                    group_count = _this.closest(".modal-body").find(".gf-category").length;
                if(Number(group_count) < ops.limit-2){ // 未分组和所有分组不算在内
                    var $data,
                        pid = $category.attr("data-id"),
                        $margin = parseInt($category.css("margin-left")) + 26 +"px";
                    var html = "",
                        target_position = "",
                        $new_a, $group;
                    // 获取插入位置
                    if($category.nextAll(".gf-category").length == 0){
                        target_position = $category;
                    }else{
                        $category.nextAll(".gf-category").each(function(k, v){
                            var m = parseInt($category.css("margin-left")),
                                kv = $(v),
                                kv_m = parseInt(kv.css("margin-left"));
                            if(kv_m <= m){
                                target_position = kv.prevAll(".gf-category:first");
                                return false
                            }
                        });
                    }
                    if(target_position == ""){
                        target_position = $category.nextAll(".gf-category:last");
                    }
                    // 插入
                    if(pid == "0"){
                        html = "<div class='gf-category' style='margin-left: 26px' data-pid='"+ pid +"' data-id=''>"
                                +"<a class='group_a' href=''>新建分组</a>"
                                + _tool + "</div>";
                        ops.insert_target.append(html);
                        $group = ops.insert_target.find(".gf-category").last();
                        $new_a = $group.find(".group_a");
                    }else{
                        var $tool = ops.max_level-parseInt($margin)/26 > 1 ? _tool : _tool_leaf;
                        html = "<div class='gf-category' style='margin-left: "+$margin+"' data-pid='"+ pid +"'data-id=''>"
                                +"<a class='group_a' href=''>新建分组</a>"
                                + $tool+ "</div>";
                        target_position.after(html);
                        $group = target_position.next(".gf-category");
                        $new_a = $group.find(".group_a");
                    }
                    // 修改新分组名
                    $group.find(".operate-btns").hide();
                    $new_a.hide();
                    $new_a.before("<input class='form-control gf-sort' type='text' " +
                                    "value='新建分组'/><a class='gf-add-group-ensure' href='javascript:void(0)' title='确认'>" +
                                    "<span class='glyphicon glyphicon-ok'></span></a>" +
                                    "<a class='gf-add-group-cancel' href='javascript:void(0)' title='取消'>" +
                                    "<span class='glyphicon glyphicon-remove'></span></a>");
                    var $obj = $group.find("input[type=\"text\"]");
                    Group.setCursorPosition($obj[0], 0, $obj.val().trim().length);
                    $(".gf-add-group-cancel").click(function(){
                        $(this).closest(".gf-category").remove();
                    });
                    $(".gf-add-group-ensure").click(function(){
                        var $this = $(this);
                        var new_name = $obj.val().trim();
                        if(new_name != ""){
                            $data = {
                                "group_pid": pid,
                                "group_name": new_name
                            };
                            $this.siblings().not(".operate-btns").not(".group_a").remove();
                            $this.find("span").removeClass().addClass("gf-oper-loading");
                            $.ajax({
                                timeout: ops.out_time,
                                url: ops.add_url,
                                type: "POST",
                                data: $data,
                                success: function(data){
                                    if(data.status == 1){
                                        $new_a.attr("data-name", new_name);
                                        $group.find(".operate-btns").show();
                                        $group.attr("data-id", data.json["group_id"]);
                                        $new_a.text(new_name);
                                        $new_a.show();
                                        $this.remove();
                                        if(pid == "0" && $group.get(0) != $(".gf-category").get(1)){
                                            $group.before('<div class="gf-divider"></div>');
                                        }
                                    }else{
                                        $this.closest(".gf-category").remove();
                                        Inform.show(data.message);
                                    }
                                },
                                error: function(XMLHttpRequest){
                                    $this.closest(".gf-category").remove();
                                    Inform.show(XMLHttpRequest.status);
                                },
                                complete: function(status){
                                    Group.timeout_tip(status);
                                }
                            });
                        }
                    });
                }else{
                    $category.append("<span class='child-tip' style='color:#eb3c00;margin-left:15px'>分组数量已达到上限</span>");
                    _this.one("mouseleave", function(){
                        $(".child-tip").fadeOut(250, function(){
                            $(".child-tip").remove();
                            _this.prop("disabled", false);
                        })
                    });
                    return false;
                }
            },
            ensure_group:function(){
                var $this = $(this),
                    group = $(this).closest(".gf-category"),
                    _input = group.find("input[type=\"text\"]"),
                    _ensure = group.find(".gf-group-ensure"),
                    _cancel = group.find(".gf-group-cancel"),
                    new_name = _input.val().trim(),
                    edit_name = group.find(".group_a");
                if($this.attr("class") == "gf-group-ensure" && new_name != ""){
                    _cancel.remove();
                    _ensure.find("span").removeClass().addClass("gf-oper-loading");
                    $.ajax({
                        timeout: ops.out_time,
                        url: ops.edit_url,
                        type: "POST",
                        data: {
                            "group_id": group.attr("data-id"),
                            "group_name": new_name
                        },
                        dataType: "JSON",
                        success: function(data){
                            if(data.status == 1){
                                edit_name.attr("data-name",new_name);
                                edit_name.html(new_name);
                            }else{
                                $(".gf-tip-text").text("编辑失败,请重试");
                                $(".gf-global-tip").show();
                            }
                        },
                        complete: function(status){
                            edit_name.show();
                            _input.remove();
                            _ensure.remove();
                            Group.timeout_tip(status);
                            group.find(".operate-btns").show();
                        }
                    });
                }else{
                    edit_name.show();
                    _input.remove();
                    _cancel.remove();
                    _ensure.remove();
                    group.find(".operate-btns").show();
                }
            },
            // 设置光标位置或选中文本
            setCursorPosition: function(obj, start_index, end_index) {
                if(obj.createTextRange){ //IE浏览器
                    var range = obj.createTextRange();
                    range.moveEnd("character",end_index);
                    range.moveStart("character", start_index);
                    range.select();
                }else{ //非IE浏览器
                    obj.setSelectionRange(start_index, end_index);
                    obj.focus();
                }
            },
            // 请求超时提示
            timeout_tip: function(status){
                if(status == "timeout"){
                    $(".gf-tip-text").text("请求超时,请重试");
                    $(".gf-global-tip").show();
                }
            },
            // 同步分组
            sync_group: function(){
                var $this = $(this);
                $this.button("loading");
                $.ajax({
                    url: ops.sync_url,
                    type: "POST",
                    data: {

                    },
                    success: function(data){
                        Group.select_group()
                    }
                })
            }
        };
        Group.init();
    }
})(jQuery);