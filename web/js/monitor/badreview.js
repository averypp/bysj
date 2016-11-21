/**
 * Created by SHB on 21/7/12.
 */

$(function(){
    var shop_id = $("#shop-id").val().trim(),
        products = [];
    Inform.init();
    var Monitor = {
        init: function(){
            $("#asin-add-var").click(Monitor.add_var);//新增ASIN
            $(".del-confirm").click(Monitor.del_confirm);//删除监控
            $(".orderby-star").click(Monitor.orderBy_Column);//评论星级 排序
            $(".orderby-date").click(Monitor.orderBy_Column);//评论日期 排序
            $("#asin-add-btn").click(Monitor.asin_save);
            $("#asin-search-btn").click(Monitor.asin_search);
            $("#del-monitor").click(Monitor.del_monitor);
        },
        //添加监控ASIN
        asin_save: function(){
            var asins = $("#add-asin").val().trim();
            if(!asins){
                Inform.show("ASIN内容为空,请补充完整!");
            }else{
                $("#result").text("正在加载数据!");
                $.ajax({
                    url: "/?r=bad-review/add-asin&shopId="+shop_id,
                    type: "post",
                    data: {"asins": asins},
                    dataType: "json",
                    success: function(data){
                        if(data.success){
                            Inform.enable(window.location.href);
                            Inform.show("添加监控成功!");
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                        Inform.enable(window.location.href);
                        nform.show("添加监控成功!");
                    }
                });
            }
        },
        del_confirm: function(){
            var id = $(this).attr("data-id").trim();
            $("#del-monitor").attr("data-id", id);
            $("#delConfirm").modal("show");
        },
        //逻辑删除监控
        del_monitor: function(){
            $("#delConfirm").modal("hide");
            var id = $(this).attr("data-id").trim();
            if(!id){
                Inform.show("id内容为空,请联系管理员!");
            }else{
                $.ajax({
                    url: "/?r=bad-review/delete-asin&shopId="+shop_id,
                    type: "post",
                    data: {id:id},
                    dataType: "json",
                    success: function(data){
                        //console.log(data);
                        if(data.success){
                            Inform.enable(window.location.href);
                            Inform.show("处理成功!");
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    }
                });
            }
        },
        orderBy_Column: function(){
            var sort = $(this).attr("data-value");
            var column = $(this).attr("data-id");
            if(!sort){
                sort = " desc";
            }else if(sort == "desc"){
                sort = " asc";
            }else{
                sort = " desc";
            }
            var orderBy = column + sort;
            var requestUrl = window.location.href;
            
            var num = requestUrl.indexOf("&orderBy");
            if(num < 0 ){
                var url = requestUrl+"&orderBy="+orderBy;
            }else{
                var newUrl  =requestUrl.substr(0,num);
                var url = newUrl+"&orderBy="+orderBy;
            }
            window.location.replace(url);
        },
        add_var: function () {
            var tr_obj = $(this).closest("tr");
            $("#editModal").modal("show");
        },
        asin_search: function () {
            var requestUrl = window.location.href;
            var asin_content = $("#asin-input").val().trim();
            var num = requestUrl.indexOf("&asin");
            if(num < 0 ){
                var url = requestUrl+"&asin="+asin_content;
            }else{
                var newUrl  =requestUrl.substr(0,num);
                var url = newUrl+"&asin="+asin_content;
            }
            if(!asin_content){
                var url = "/?r=bad-review/list&shopId="+shop_id;
            }
            window.location.replace(url);
        }
    };
    Monitor.init();
});