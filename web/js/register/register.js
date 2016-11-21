/**
 * Created by Administrator on 2014/12/2.
 */
var Register = {
    code: 0,
    number: 0,
    init: function(){
        Register.mobile = $("#r-mobile");
        Register.password = $("#r-password");
        Register.email = $("#r-email");
        Register.verify_code = $("#verify-code-input");
        $("#register-btn").click(Register.register);
        Register.mobile.bind("blur", Register.verify);
        Register.password.bind("blur", Register.pwd_verify);
    },
    verify: function(){
        var mobile = Register.mobile.val();
        if(/^1[3|4|5|7|8|9][0-9]\d{4,8}$/.test(mobile)){
            Register.write_error("");
            $.ajax({
                url: "?r=user/verify-mobile&mobile="+mobile,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if(data.success == true)
                        Register.number = 1;
                    else{
                        Register.number = -1;
                        Register.write_error(data.message)
                    }
                }
            });
        }else{
            Register.write_error("重新输入正确的手机号码");
        }
    },
    pwd_verify: function(){
        if(Register.password.val().length < 6){
            Register.write_error("密码必须至少6位");
        }
    },
    register: function(){
        var m = Register.mobile.val();
        if(!(/^1[3|4|5|7|8|9][0-9]\d{4,8}$/.test(m))){
            /**
            alert("重新输入手机号码");
            **/
            alert("重新输入手机号码");
            return 0;
        }
        switch(Register.number){
            case -1:{
                Register.write_error("手机号已存在");
                return 0;
            }
            case 0:{
                Register.write_error("手机号验证失败, 重新输入手机号");
                return 0;
            }
            default:
                break;
        }
        if(Register.password.val().length < 6){
            Register.write_error("密码必须至少6位");
            return 0;
        }
        if(Register.verify_code.val()==""){
            Register.write_error("验证码不能为空");
            return 0;
        }
        switch(Register.code){
            case -1:{
                Register.write_error("验证码有误");
                break;

            }
            case 1:{
                var data = {"mobile": Register.mobile.val(), "password": Register.password.val()};
                var loginDate = data;
                $.ajax({
                    "type": "POST",
                    "url": "?r=site/register",
                    "data": data,
                    "cache": true,
                    "dataType": "json",
                    "success": function(data){
                        if(data.success == false){
                            if(data.message) {
                                Register.write_error(data.message);
                            }
                        }else{
                            // var url = "/?r=site/login";
                            // location.replace(url);
                            $.ajax({
                                "type": "POST",
                                "url": "?r=site/login",
                                "data": loginDate,
                                "cache": true,
                                "dataType": "json",
                                "success": function(data){
                                    if(data.success == false){
                                        if(data.message) {
                                            Register.write_error(data.message);
                                        }
                                    }else{
                                        location.replace(data.content);
                                        // var url = "/?r=site/login";
                                        // location.replace(url);
                                    }
                                },
                                "error": function(){
                                    console.log("自动登录失败，请查看网络");
                                    Register.write_error("自动登录失败，请查看网络");
                                }
                            });
                        }
                    },
                    "error": function(){
                        console.log("请求失败，请查看网络");
                        Register.write_error("请求失败，请查看网络");
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
        var target = $("#register-con");
        var msg = target.find(".error-msg");
        msg.text(error);
    }
};

$(function(){
    Register.init();
    VerifyCode.init();
});