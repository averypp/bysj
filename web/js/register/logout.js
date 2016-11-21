/**
 * Created with PyCharm.
 * User: changdongsheng
 * Date: 14/12/11
 * Time: 上午11:31
 * To change this template use File | Settings | File Templates.
 */


$(document).ready(function(){
    $("#sign-out").click(function(){
        $.ajax({
            "type": "GET",
            "url": "/?r=site/logout",
            "cache": true,
            "dataType": "json",
            "success": function(data){
                if(data.success == true){
                    location.replace(data.content);
                }
            },
            "error": function(data){
                console.log("请求失败，请查看网络")
            }
        });
    });
});