/**
 * Created by echo/xiakai on 16/7/12.
 */

$(function(){
    var shop_id = $("#shop-id").val().trim(),
        products = [];
    Inform.init();
    var Monitor = {
        init: function(){
            $("#asin-add-btn").click(Monitor.asin_save);//新增ASIN
            $(".cancel-monitor").click(Monitor.cancel_monitor);//取消监控
            $(".edit-monitor").click(Monitor.edit_var);
            $(".detail-monitor").click(Monitor.detail_monitor);//跟卖详情
            $("#edit-control").click(Monitor.edit_monitor);//编辑监控（修改排除卖家）
            $(".open-monitor").click(Monitor.open_monitor);//开启监控
            $(".del-monitor").click(Monitor.del_monitor);//删除监控
            $(".orderby-price").click(Monitor.orderBy_Column);//商品跟卖详情 单价排序
            $(".orderby-fee").click(Monitor.orderBy_Column);//商品跟卖详情 邮费排序
            $(".orderby-follow").click(Monitor.orderBy_Column);//商品跟卖详情 跟卖时间排序
            $(".orderby-monitor").click(Monitor.orderBy_Column);//商品跟卖详情 监控时间排序
            $(".orderby-follow-end").click(Monitor.orderBy_Column);//商品跟卖详情 跟卖结束时间排序
        },
        //添加监控ASIN
        asin_save: function(){
            if(!$("#asin-input").val()){
                Inform.show("ASIN内容为空,请补充完整!");
            }
            var asin_content = $("#asin-input").val().trim();
            if(!asin_content){
                Inform.show("ASIN内容为空,请补充完整!");
            }else{
                $("#result").text("正在加载数据!");
                $.ajax({
                    url: "/?r=monitor/add-asin&shopId="+shop_id,
                    type: "post",
                    data: {"asins": asin_content},
                    dataType: "json",
                    success: function(data){
                        if(data.success){
                            Inform.enable(window.location.href);
                            Inform.show("添加监控成功!");
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    }
                });
            }
        },
        //取消监控
        cancel_monitor: function(){
            var id = $(this).attr("data-id").trim();
            if(!id){
                Inform.show("id内容为空,请联系管理员!");
            }else{
                $.ajax({
                    url: "/?r=monitor/cancel-monitor&shopId="+shop_id,
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
        //开启监控
        open_monitor: function(){
            var id = $(this).attr("data-id").trim();
            if(!id){
                Inform.show("id内容为空,请联系管理员!");
            }else{
                $.ajax({
                    url: "/?r=monitor/open-monitor&shopId="+shop_id,
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
        //逻辑删除监控
        del_monitor: function(){
            var id = $(this).attr("data-id").trim();
            if(!id){
                Inform.show("id内容为空,请联系管理员!");
            }else{
                $.ajax({
                    url: "/?r=monitor/del-monitor&shopId="+shop_id,
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
        //编辑信息赋值
        edit_var: function () {
            var v_asin = $(this).attr("data-value").trim();
            var v_id = $(this).attr("data-id").trim();
            var tr_obj = $(this).closest("tr"),
                v_seller = tr_obj.find(".v-seller").text().trim(),
                v_img = tr_obj.find("img")[0].src;
            $("#editModal").modal("show");
            $("#var-Asin").val(v_asin);
            $("#var-img").attr("src",v_img);
            $("#var-seller").val(v_seller);
            $("#var-id").val(v_id);
        },
        //编辑信息保存
        edit_monitor: function(){
            var id = $("#var-id").val().trim(),
                asin = $("#var-Asin").val().trim(),
                seller = $("#var-seller").val().trim();
            if(!id && !asin){
                Inform.show("id/asin为空,请联系管理员!");
            }else{
                $.ajax({
                    url: "/?r=monitor/edit-monitor",
                    type: "get",
                    data: {"shopId": shop_id, "id": id, "asin":asin, "seller":seller},
                    dataType: "json",
                    success: function(data){
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

    };
    Monitor.init();
});