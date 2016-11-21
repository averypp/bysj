/**
 * Created by xuhe on 15/5/24.
 */
$(function(){
    Inform.init();
    var Userinfo = {
        old_username : "",
        old_qq:"",
        init: function(){
            Userinfo.edit_btn = $("#submit-btn").children().eq(0);
            Userinfo.save_btn = $("#submit-btn").children().eq(1);
            Userinfo.exit_btn = $("#submit-btn").children().eq(2);
            Userinfo.oldpw_input = $("#modify-pw input").eq(0);
            Userinfo.newpw_input = $("#modify-pw input").eq(1);
            Userinfo.conpw_input = $("#modify-pw input").eq(2);
            Userinfo.sub_btn = $("#changepw");
            Userinfo.username = $("#username");
            Userinfo.mol_name = $(".mol-name");
            Userinfo.release_shop = $(".release-shop");
            Userinfo.add_authorize= $(".add-authorize");
            Userinfo.authorize_list = $(".authorize-list");
            Userinfo.re_auhtorize = $(".re-authorize");
            Userinfo.qq = $("#qq");
            Userinfo.save_btn.css({"display": "none"});
            Userinfo.exit_btn.css({"display": "none"});
            Userinfo.mol_name.click(Userinfo.change_shop_name);
            Userinfo.release_shop.click(Userinfo.release_shop_click);
            Userinfo.add_authorize.click(Userinfo.add_authorize_click);
            Userinfo.authorize_list.click(Userinfo.authorize_list_click);
            Userinfo.re_auhtorize.click(Userinfo.re_authorize_click);
            Userinfo.edit_btn.click(Userinfo.edit_click);
            Userinfo.exit_btn.click(Userinfo.exit_click);
            Userinfo.save_btn.click(Userinfo.save_click );
            Userinfo.sub_btn.click(Userinfo.sub_click);
            Userinfo.oldpw_input.blur(Userinfo.oldpw_blur);
            Userinfo.newpw_input.blur(Userinfo.newpw_blur);
            Userinfo.conpw_input.blur(Userinfo.conpw_blur);
            $("#authorize-list").on("click", ".del-authorize", Userinfo.del_authorize)
        },
        change_shop_name: function(){
            var new_name_input = $("#name-modal").modal("show").find("input");
            var row = $(this).parents("tr");
            var shop_id = $(this).attr("data-id");
            var shop_name_dom = row.find("td").eq(1);
            var shop_name = shop_name_dom.text().trim();
            new_name_input.val(shop_name).focus();
            $("#submit-name").unbind().bind("click", function(){
                var new_shop_name = new_name_input.val().trim();
                if(new_shop_name == shop_name){
                    $("#name-modal").modal("hide");
                    Inform.show("店铺名修改成功");
                    return 0;
                }
                if(Userinfo.check_shop_name(new_shop_name)){
                    $("#name-modal").modal("hide");
                    Inform.disable();
                    Inform.show("", true, "正在修改...");
                    $.ajax({
                        "type": "POST",
                        "url": "?r=my/change-shop-name",
                        "dataType": "json",
                        "data": {
                            shopName: new_shop_name,
                            shopId: shop_id
                        },
                        success: function(data){
                            if(data.success == true){
                                Inform.enable();
                                Inform.show("店铺名修改成功");
                                shop_name_dom.text(new_name_input.val().trim())
                            }else{
                                Inform.enable();
                                Inform.show("店铺名修改失败");
                            }
                        }
                    });
                }
            });
        },
        release_shop_click: function(){
            var row = $(this).parents("tr");
            var shop_id = $(this).attr("data-id");
            var shop_name_dom = row.find("td").eq(1);
            var shop_name = shop_name_dom.text().trim();
            var master = $(this).attr("data-master");
            $("#del-label").text(shop_name);
            $("#del-shop").unbind().bind("click", function(){
                $("#del-modal").modal("hide");
                Inform.disable();
                Inform.show("", true, "正在解除店铺授权...");
                $.ajax({
                    "type": "POST",
                    "url": "?r=my/del-shop",
                    "data":{shopId:shop_id},
                    "dataType": "json",
                    "success": function(data){
                        if(data.success == true){
                            Inform.enable();
                            Inform.show("解除店铺授权成功");
                            row.remove();
                        }else{
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    }
                });
            });
            if(master==1){
                $("#del-modal").modal("show");
            }else{
                $("#del-shop").click()
            }
        },
        add_authorize_click: function(){
            $("#authorize-modal").modal("show");
            var shop_id = $(this).attr("data-id");
            $("#add-authorize").unbind().bind("click", function(){
                var mobile = $("#authorize-mobile").val().trim();
                $("authorize-modal").modal("hide");
                Inform.disable();
                Inform.show("", true, "正在授权店铺...");
                $.ajax({
                    "type": "POST",
                    "url": "/shop/"+ shop_id+"/authorize",
                    "dataType": "json",
                    "data": {
                        mobile: mobile
                    },
                    "success": function(data){
                        if(data.status == 1){
                            Inform.enable();
                            Inform.show("店铺授权成功");
                        }else{
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    }
                });
            });
        },
        authorize_list_click: function () {
            var shop_id = $(this).attr("data-id");
            $.ajax({
                url: "/my",
                type: "post",
                data: {
                    op: "child",
                    "shop_id": shop_id
                },
                timeout: 10000,
                success: function(data){
                    if(data.status){
                        var users = data.users,
                            table = $("#authorize-list").empty(),
                            th = "<tr><th>#</th><th>用户名</th><th>用户手机号</th><th>操作</th></tr>";
                        table.append(th);
                        for(var i=0; i<users.length; i++){
                            var html = "<tr><td>{0}</td><td>{1}</td><td>{2}</td><td>{3}</td></tr>",
                                num = i+1,
                                user= users[i],
                                btn = "<a class=\"btn btn-info del-authorize\" href=\"javascript:void(0)\"" +
                                    "data-mobile=\""+user.mobile+"\" data-shop=\""+shop_id+"\">解除</a>";
                            html = html.format(num, user.name, user.mobile, btn);
                            table.append(html);
                        }
                        $("#auth-list-modal").modal("show");
                    }else{
                        Inform.show(data.message)
                    }
                }
            })
        },
        re_authorize_click: function () {
            var href = $(this).attr("data-href");
            //href += "&ts="+ new Date().getTime();
            window.open(href);
        },
        del_authorize: function(){
            var $this = $(this),
                row = $this.closest("tr"),
                mobile = $this.attr("data-mobile"),
                shop_id = $this.attr("data-shop");
            $.ajax({
                "type": "POST",
                "url": "/shop/"+ shop_id+"/release",
                "dataType": "json",
                "data":{
                    mobile: mobile
                },
                "success": function(data){
                    if(data.status){
                        Inform.enable();
                        Inform.show("解除店铺授权成功");
                        row.remove();
                    }else{
                        Inform.enable();
                        Inform.show(data.message);
                    }
                }
            })
        },
        edit_click: function(){
            Userinfo.old_username = Userinfo.username.val();
            Userinfo.old_qq = Userinfo.username.val();
            Userinfo.edit_btn.css({"display": "none"});
            Userinfo.save_btn.css({"display": "inline-block"});
            Userinfo.exit_btn.css({"display": "inline-block"});
            Userinfo.username.removeAttr("readonly");
            Userinfo.qq.removeAttr("readonly");
        },
        exit_click: function(){
            Userinfo.edit_btn.css({"display": "inline-block"});
            Userinfo.save_btn.css({"display": "none"});
            Userinfo.exit_btn.css({"display": "none"});
            Userinfo.username.attr("readonly","readonly");
            Userinfo.qq.attr("readonly","readonly");
            Userinfo.username.val(Userinfo.old_username);
            Userinfo.qq.val(Userinfo.old_qq);
        },
        save_click: function(){
             $.ajax({
                "type": "POST",
                "url": "?r=my/edit-user",
                "dataType": "json",
                "data": {
                    username: Userinfo.username.val(),
                    qq: Userinfo.qq.val()
                },
                "success":function(data){
                    if (data.success == true){
                        alert("用户信息修改成功!");
                        Userinfo.edit_btn.css({"display": "inline-block"});
                        Userinfo.save_btn.css({"display": "none"});
                        Userinfo.exit_btn.css({"display": "none"});
                        Userinfo.username.attr("readonly","readonly");
                        Userinfo.qq.attr("readonly","readonly");
                    }else{
                        alert("用户信息修改失敗!");
                    }
                }

            });
        },
        sub_click: function(){
            var tip_contents = $(".tips");
            for(var i=0;i<3;i++){
                var inputDiv = $("#modify-pw input ").eq(i);
                if (inputDiv.val()==""){
                    var text = $("#modify-pw .col-md-3 p").eq(i).text();
                    var tips = text.substring(0,text.length-2)+"不能为空";
                    tip_contents.eq(i).html(tips);
                    return
                }
            }
            var check_pw = $("#old_pw").val();
            var new_pw = $("#new_pw").val();
            var conform_pw = $("#conform_pw").val();
            if(conform_pw!=new_pw){
                return
            }
            if(new_pw.length<6||new_pw.indexOf(" ")!=-1){
                return
            }
            tip_contents.html("");
            $.ajax({
                "type": "POST",
                "url": "?r=my/change-password",
                "dataType": "json",
                "data": {
                    "check_pw": check_pw,
                    "new_pw": new_pw
                },
                "success":function(data){
                    if (data.success == true ){
                        alert(data.message);
                        $("#old_pw").val("");
                        $("#new_pw").val("");
                        $("#conform_pw").val("");
                    }else{
                        alert(data.message);
                    }
                }

            });
        },
        oldpw_blur: function(){
            var tip_contents = $(".tips");
            var old_pw=$(event.target).val();
            tip_contents.eq(0).html("");
            if (old_pw == ""){
                tip_contents.eq(0).html("旧密码不能为空");
            }
        },
        newpw_blur: function(){
            var tip_contents = $(".tips");
            var new_pw = $("#new_pw").val();
            var conform_pw = $("#conform_pw").val();
            tip_contents.eq(1).html("");
            tip_contents.eq(2).html("");
            if (new_pw == ""){
                tip_contents.eq(1).html("新密码不能为空");
            }else if(new_pw.length<6||new_pw.indexOf(" ")!=-1){
                tip_contents.eq(1).html("密码格式不正确,不小于6个字符,且不能包含空格");
            }else if(new_pw!=conform_pw&&conform_pw!=""){
                tip_contents.eq(1).html("新密码与确认密码输入不一致");
                tip_contents.eq(2).html("新密码与确认密码输入不一致");
            }

        },
        conpw_blur: function(){
            var tip_contents = $(".tips");
            var new_pw = $("#new_pw").val();
            var conform_pw = $("#conform_pw").val();
            tip_contents.eq(1).html("");
            tip_contents.eq(2).html("");
            if (conform_pw == ""){
                tip_contents.eq(2).html("确认密码不能为空");
            }else if(new_pw.length<6||new_pw.indexOf(" ")!=-1){
                tip_contents.eq(1).html("密码格式不正确,不小于6个字符,且不能包含空格");
            }else if(new_pw!=conform_pw&&new_pw!=""){
                tip_contents.eq(1).html("新密码与确认密码输入不一致");
                tip_contents.eq(2).html("新密码与确认密码输入不一致");
            }
        },
        check_shop_name:function(shop_name){
            if (shop_name.length<=16&&shop_name.length>=6){
                if(shop_name.match(/^[\w-]+$/)!=null){
                     $(".name-tip").html("");
                    return true;
                }else{
                    $(".name-tip").html("店铺名格式错误");
                    return false;
                }
            }else if(shop_name.length==0){
                    $(".name-tip").html("请输入店铺名");
                    return false;
            }else{
                $(".name-tip").html("店铺名格式错误");
                return false;
            }
        }
    };
    Userinfo.init();
});