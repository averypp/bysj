/**
 * Created with PyCharm.
 * User: changdongsheng
 * Date: 14/12/10
 * Time: 上午10:55
 * To change this template use File | Settings | File Templates.
 */

$(function(){
    $(document).keydown(function (event) {
        if(event.keyCode == 13&&($("#lg-password").is(":focus")||$("#lg-mobile").is(":focus"))){
            $("#login-btn").trigger("click");
        }
    });
    $(".btn-top").click(function(){
        $("#index-full").fullpage.moveTo(1);
    });
    $(".login-tab").find("a").eq(0).click(function(){
        $("#register-con").css("display","none");
        $("#login-con").css("display","block");
        $(this).attr("class","control-btn col-md-6");
        $(".login-tab").find("a").eq(1).attr("class","control-btn col-md-6 dis");
    });
    $(".login-tab").find("a").eq(1).click(function(){
        $("#register-con").css("display","block");
        $("#login-con").css("display","none");
        $(this).attr("class","control-btn col-md-6");
        $(".login-tab").find("a").eq(0).attr("class","control-btn col-md-6 dis");
    });

    $("#login-btn").click(function(){
        var mobile = $("#lg-mobile").val();
        var pw = $("#lg-password").val();
        var data = {"mobile": mobile, "password": pw};
        var log_btn = $("#login-btn");
        log_btn.text("登录中...").attr("disabled","disabled");
        $.ajax({
            "type": "POST",
            "url": "/?r=site/login",
            "data": data,
            "cache": true,
            "dataType": "json",
            "success": function(data){
                console.log(data);
                if(data.success == false){
                     write_error("账户有问题");
                     return false;
                    /*switch (data["error_code"]) {
                        case "0":
                            write_error("参数不全");
                            
                        case "1":
                            write_error("账户有问题，多个用户存在");
                            break;
                        case "2":
                            write_error("用户不存在");
                            break;
                        case "3":
                            write_error("密码错误");
                            break;
                        case "100":
                            write_error("未知错误");
                            break;
                    }*/
                }else{
                    location.replace(data.content);
                    log_btn.removeAttr("disabled").text("登录");
                }
            },
            "error": function(){
                log_btn.removeAttr("disabled").text("登录");
                console.log("请求失败，请查看网络");
            }
        });
    });
    function write_error(error){
        var target = $("#login-con");
        var msg = target.find(".error-msg");
        msg.text(error);
        $("#login-btn").removeAttr("disabled").text("登录");
    }
    ResetPW.init();
    ResetVerifyCode.init();
});

var ResetPW = {
    code: 0,
    number: 0,
    init: function(){
        ResetPW.mobile = $("#fp-mobile");
        ResetPW.password = $("#fp-password");
        ResetPW.verify_code = $("#fp-verify-code-input");
        $("#fpConfirm").click(ResetPW.reset);
        ResetPW.mobile.bind("blur", ResetPW.verify);
        ResetPW.password.bind("blur", ResetPW.pwd_verify);
    },
    verify: function(){
        var mobile = ResetPW.mobile.val();
        if(/^1[3|4|5|7|8|9][0-9]\d{4,8}$/.test(mobile)){
            ResetPW.write_error("");
            $.ajax({
                url: "?r=user/verify-mobile&mobile="+mobile,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if(data.success == true){
                        ResetPW.number = -1;
                        ResetPW.write_error("用户不存在");
                    }else{
                        ResetPW.number = 1;
                    }
                }
            });
        }else{
            ResetPW.write_error("重新输入正确的手机号码");
        }
    },
    pwd_verify: function(){
        ResetPW.write_error("");
        if(ResetPW.password.val().length < 6){
            ResetPW.write_error("密码必须至少6位");
        }
    },

    reset: function(){
        var m = ResetPW.mobile.val();
        if(!(/^1[3|4|5|7|8|9][0-9]\d{4,8}$/.test(m))){
            /**
            alert("重新输入手机号码");
            **/
            alert("重新输入手机号码");
            return 0;
        }
        switch(ResetPW.number){
            case -1:{
                ResetPW.write_error("用户不存在");
                return 0;
            }
            case 0:{
                ResetPW.write_error("手机号验证失败, 重新输入手机号");
                return 0;
            }
            default:
                break;
        }

        if(ResetPW.password.val().length < 6){
            ResetPW.write_error("密码必须至少6位");
            return 0;
        }
        if(ResetPW.verify_code.val()==""){
            ResetPW.write_error("验证码不能为空");
            return 0;
        }
        switch(ResetPW.code){
            case -1:{
                ResetPW.write_error("验证码有误");
                break;
            }
            case 0:{
                ResetPW.write_error("正在检查验证码");
                break;
            }
            case 1:{
                var data = {"mobile": ResetPW.mobile.val(), "password": ResetPW.password.val(), "code":ResetPW.verify_code.val()};

                $.ajax({
                    "type": "POST",
                    "url": "?r=user/reset-password",
                    "data": data,
                    "cache": true,
                    "dataType": "json",
                    "success": function(data){
                        if(data.success == false){
                            ResetPW.write_error(data.message);
                        }else{
                            console.log("success");
                            alert("重置密码成功!");
                        }
                    },
                    "error": function(){
                        console.log("请求失败，请查看网络");
                        ResetPW.write_error("请求失败，请查看网络");
                    }
                });
                break;
            }
            default:{
                break;
            }
        }
    },
    write_error: function write_error(error){
        var target = $("#fpModal");
        var msg = target.find(".error-msg");
        msg.text(error);
    }
};
