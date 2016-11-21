/**
 * Created by Administrator on 2014/12/2.
 */
var VerifyCode = {
    status: 0,
    
    init: function(){
        VerifyCode.button = $("#verify-code-btn");
        VerifyCode.input = $("#verify-code-input");
        VerifyCode.input.attr("disabled", true);
        VerifyCode.button.click(VerifyCode.send);
    },
    send: function(){
        if(VerifyCode.status==1)
            return 0;
        var mobile = $("#r-mobile").val();
        if(!(/^1[3|4|5|7|8|9][0-9]\d{4,8}$/.test(mobile))){
            Register.write_error("输入正确的手机号码");
            return 0;
        }else if(Register.number ==0|| Register.number ==-1){
            return 0;
        }else if(Register.password.val().length < 6){
            return 0;
        }
        VerifyCode.button.attr("class", "btn btn-warning");
        VerifyCode.button.attr("disabled", true);
        VerifyCode.button.html("正在发送. . .");
        $.ajax({
            url: "?r=site/get-code&mobile="+mobile,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if(data.success==true) {
                    VerifyCode.status = 1;
                    VerifyCode.input.attr("disabled", false);
                    VerifyCode.input.bind("keyup", VerifyCode.verify);

                    var interval_id = setInterval(function () {
                        var second = 60;
                        function run() {
                            if (second <= 0) {
                                clearInterval(interval_id);
                                VerifyCode.button.attr("class", "btn btn-warning");
                                VerifyCode.input.attr("disabled", false);
                                VerifyCode.button.attr("disabled", false);
                                VerifyCode.button.html("再发一次");
                                VerifyCode.status = 0;
                            } else {
                                VerifyCode.button.html("已发送（" + second + "）");
                                second -= 1;
                            }
                        }
                        return run;
                    }(), 1000);

                }else {
                    alert("验证码发送失败，请刷新后再次尝试！");
                }
            }
        });
    },
    verify: function(){
        var mobile = $("#r-mobile").val();
        var verify_code = VerifyCode.input.val();
        Register.write_error("");
        if(verify_code.length == 6){
            $.ajax({
                url: "?r=verify-code/check&code="+verify_code + "&mobile="+mobile,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if(data.success == true){
                        Register.code = 1;
                        Register.write_error("验证码输入正确");
                    }
                    else{
                        Register.write_error("请重新确认短信并输入正确的验证码");
                        Register.code = -1;
                    }
                }
            });
        }
    }
};
//重置密码
var ResetVerifyCode = {
    status: 0,
    code: "",
    init: function(){
        ResetVerifyCode.button = $("#fp-verify-code-btn");
        ResetVerifyCode.input = $("#fp-verify-code-input");
        ResetVerifyCode.input.attr("disabled", true);
        ResetVerifyCode.button.click(ResetVerifyCode.send);
    },
    send: function(){
        if(ResetVerifyCode.status==1)
            return 0;
        var mobile = $("#fp-mobile").val();
        if(!(/^1[3|4|5|7|8|9][0-9]\d{4,8}$/.test(mobile))){
            ResetPW.write_error("输入正确的手机号码");
            return 0;
        }else if(ResetPW.number ==0|| ResetPW.number ==-1){
            return 0;
        }else if(ResetPW.password.val().length < 6){
            return 0;
        }
        ResetVerifyCode.button.attr("class", "btn btn-warning");
        ResetVerifyCode.button.attr("disabled", true);
        ResetVerifyCode.button.html("正在发送. . .");
        $.ajax({
            url: "?r=site/get-code&mobile="+mobile,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if(data.success==true) {
                    ResetVerifyCode.status = 1;
                    ResetVerifyCode.input.attr("disabled", false);
                    ResetVerifyCode.input.bind("keyup", ResetVerifyCode.verify);

                    var interval_id = setInterval(function () {
                        var second = 60;
                        function run() {
                            if (second <= 0) {
                                clearInterval(interval_id);
                                ResetVerifyCode.button.attr("class", "btn btn-warning");
                                ResetVerifyCode.input.attr("disabled", false);
                                ResetVerifyCode.button.attr("disabled", false);
                                ResetVerifyCode.button.html("再发一次");
                                ResetVerifyCode.status = 0;
                            } else {
                                ResetVerifyCode.button.html("已发送（" + second + "）");
                                second -= 1;
                            }
                        }
                        return run;
                    }(), 1000);

                }else {
                    /**
                    alert("验证码发送失败，请刷新后再次尝试！");
                    **/
                    alert("验证码发送失败，请刷新后再次尝试！");
                }
            }
        });
    },
    verify: function(){
        var verify_code = ResetVerifyCode.input.val();
        var mobile = $("#fp-mobile").val();
        ResetPW.write_error("");
        if(verify_code.length == 6){
            $.ajax({
                url: "?r=verify-code/check&code="+verify_code + "&mobile="+mobile,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if(data.success == true){
                        ResetPW.code = 1;
                    }
                    else{
                        ResetPW.write_error("请重新确认短信并输入正确的验证码");
                        ResetPW.code = -1;
                    }
                }
            });
        }
    }
};
