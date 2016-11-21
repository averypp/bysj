/**
 * Created by SHB on 11/11/2016.
 */

$(function(){
    var shopId = $("#shopId").val().trim();
    Checkbox.init(".all-select", ".sel-pro");
    var is_all = false,
        count = 0,
        s_condition = "s-title";

    Inform.init();
    var Bidding = {
        init: function(){
            $("#all-open").click(Bidding.all_start_bid);//
            $("#all-close").click(Bidding.all_suspend_bid);//
            $("#all-del-rule").click(Bidding.all_del_rule);//评论星级 排序
            $("#all-delete").click(Bidding.all_delete);//评论日期 排序
            $(".single-set").click(Bidding.single_set);
            $(".single-close").click(Bidding.single_close);
            $(".single-delete").click(Bidding.single_delete);
            $("#del-monitor").click(Bidding.single_delete_ajax);
            $(".price").click(Bidding.single_set_ajax);

            $("html").on("click", ".all-select, .sel-pro",Bidding.detect_count);
            $("html").on("click", "#all-select", Bidding.all_click);
            $("#search-options").find("a").click(Bidding.choose_option);
            $("#search-btn").click(Bidding.search_pro);
            $("#asin-search-btn").click(Bidding.searchByAsin);

            $(".rulelist-single-set").click(Bidding.rulelist_single_set);
            $(".rulelist-single-delete").click(Bidding.rulelist_single_delete);
            $("#rulelist-del-monitor").click(Bidding.rulelist_single_delete_ajax);
            Bidding.show_search_info();
        },
        all_start_bid: function () {
            var select_count = $('#product-count').html().trim();
            if(select_count <= 0){
                Inform.show("请选择调价商品");
                return false;
            }
            var ids = Bidding.get_ids();
            if(ids){
                $.ajax({
                    url: "/?r=bidding/batch-edit&shopId="+shopId,
                    type: "post",
                    data: {"ids": ids, "status":1},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });

            }
        },
        all_suspend_bid: function () {
            var select_count = $('#product-count').html().trim();
            if(select_count <= 0){
                Inform.show("请选择调价商品");
                return false;
            }
            var ids = Bidding.get_ids();
            if(ids){
                $.ajax({
                    url: "/?r=bidding/batch-edit&shopId="+shopId,
                    type: "post",
                    data: {"ids": ids, "status":0},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });
            }
        },
        all_del_rule: function () {
            var select_count = $('#product-count').html().trim();
            if(select_count <= 0){
                Inform.show("请选择调价商品");
                return false;
            }
            var ids = Bidding.get_ids();
            if(ids){
                $.ajax({
                    url: "/?r=bidding/batch-clean-rule&shopId="+shopId,
                    type: "post",
                    data: {"ids": ids},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });
            }
        },
        all_delete: function () {
            var select_count = $('#product-count').html().trim();
            if(select_count <= 0){
                Inform.show("请选择调价商品");
                return false;
            }
            var ids = Bidding.get_ids();
            if(ids){
                $.ajax({
                    url: "/?r=bidding/batch-remove-goods&shopId="+shopId,
                    type: "post",
                    data: {"ids": ids},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });
            }
        },
        single_set: function ($this) {
            var id = $(this).parent().parent('.single-edit-menu').attr('data-id');
            $("#bidding_id").val(id);

            var my_price = $(".my_price-"+id).val();
            $("#my_price").val(my_price);

            var sku = $(".sku-"+id).html();
            $("#var-sku").val(sku);

            var ori_price = $(".cost-"+id).html();
            $("#var-cost").val(ori_price);

            var mix_price = $(".mix_price-"+id).html();
            $("#var-mix").val(mix_price);

            var max_price = $(".max_price-"+id).html();
            $("#var-max").val(max_price);

            var rules_id = $(".rules_id-"+id).val();
            $('option [value='+id+']').attr('selected', 'selected');
            $("#var-rule").val(rules_id);
        },
        single_set_ajax: function(){
            var id = $("#bidding_id").val();
            var ori_price = $("#var-cost").val().trim();
            var min_price = $("#var-mix").val().trim();
            var max_price = $("#var-max").val().trim();
            var my_price = $("#my_price").val().trim();
            var rule_id = $("#var-rule").val();
            //console.log(id,ori_price,min_price,max_price,rule_id);
            if(id && ori_price && min_price && max_price){
                if(my_price < min_price || my_price > max_price){
                    Inform.show('商品价格必须在最小价格和最大价格之间!');
                }else{
                    $.ajax({
                        url: "/?r=bidding/edit-bidding&shopId="+shopId,
                        type: "post",
                        data: {"id": id,"ori_price": ori_price, "min_price": min_price, "max_price": max_price, "my_price": my_price, "rule_id": rule_id},
                        dataType: "json",
                        success: function(data){
                            if(data.status){
                                Inform.enable(window.location.href);
                                Inform.show(data.message);
                            }else {
                                Inform.enable();
                                Inform.show(data.message);
                            }
                        },
                        "error": function(){
                        }
                    });
                }
            } else {
                Inform.show('数据请填写完整');
            }
        },
        single_close: function ($this) {
            var id = $(this).parent().parent('.single-edit-menu').attr('data-id');
            var status = $(this).attr('data-status');
            if(status == 0){
                new_status = 1;
            } else {
                new_status = 0;
            }
            if(id){
                $.ajax({
                    url: "/?r=bidding/batch-edit&shopId="+shopId,
                    type: "post",
                    data: {"ids": id, 'status':new_status},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });
            }
        },
        single_delete: function () {
            var id = $(this).parent().parent('.single-edit-menu').attr('data-id');
            $("#del-monitor").attr("data-id", id);
            $("#delConfirm").modal("show");
        },
        single_delete_ajax: function(){
            $("#delConfirm").modal("hide");
            var id = $('#del-monitor').attr("data-id");
            if(id){
                $.ajax({
                    url: "/?r=bidding/batch-remove-goods&shopId="+shopId,
                    type: "post",
                    data: {"ids": id},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });
            }
        },
        rulelist_single_set: function ($this) {
            var id = $(this).parent().parent('.single-edit-menu').attr('data-id');
            var url = location.search;
            var urlArray = url.split('&');
            location.href = "/?r=bidding/edit-rule" + "&"+ urlArray[1] +"&rid="+ id;
        },
        rulelist_single_delete: function () {
            var id = $(this).parent().parent('.single-edit-menu').attr('data-id');
            $("#rulelist-del-monitor").attr("data-id", id);
            $("#delConfirm").modal("show");
        },
        rulelist_single_delete_ajax: function(){
            $("#delConfirm").modal("hide");
            var rule_id = $('#rulelist-del-monitor').attr("data-id");
            if(rule_id){
                $.ajax({
                    url: "/?r=bidding/remove-rule&shopId="+shopId,
                    type: "post",
                    data: {"rule_id": rule_id},
                    dataType: "json",
                    success: function(data){
                        if(data.status){
                            Inform.enable(window.location.href);
                            Inform.show(data.message);
                        }else {
                            Inform.enable();
                            Inform.show(data.message);
                        }
                    },
                    "error": function(){
                    }
                });
            }
        },
        get_ids: function(){
            var ids = '';
            Checkbox.child.each(function(k, v){
                if($(v).is(":checked")){
                    ids = ids + ','+ $(v).attr("data-feed-id");
                }
            });
            return ids.slice(1, ids.length);
        },
        show_search_info: function(){
            var $search = location.search;
            var re = /^[ktp]\=[0-9a-zA-Z\%\_-]+/g,
                // re2 = /(?![hktp])[a-zA-Z]+\=[0-9a-zA-Z\%\_-]*/g;
                re2 = /([a-zA-Z_]{2,}|(?![hktp])[a-z]+)\=[0-9a-zA-Z\%\_-]*/g;
            if($search != ""){

                var search_arr = $search.split('&'),
                    search_text;
                for (x in search_arr) {
                    search_text = search_arr[x].match(re);
                    if (search_text) {
                        break;
                    }
                }

                if(search_text){
                    search_text = search_text.join("");
                    var str = '<div>{0}: {1}</div>',
                        $option = search_text[0],
                        $value = unescape(search_text.split("=")[1]);
                    if($option == "t"){
                        s_condition = "s-title";
                        $option = "标题";
                    }else if($option == "k"){
                         s_condition = "s-sku";
                        $option = "Seller-SKU";
                    }else if($option == "p"){
                         s_condition = "s-asin";
                        $option = "ASIN";
                    }
                    str = str.format($option, $value);
                    var $href = location.pathname;
                    if($search.match(re2)){
                        $href = "?" + $search.match(re2).join("&");
                    }
                    $(".search-key").text($option);
                    $(".search-input").val($value);
                    $("#search-btn").after(' <a href="'+$href+'" class="btn btn-link" style="color: #eb3c00">去除搜索条件</a>');
                }
            }
        },
        // 选择搜索条件
        choose_option: function(){
            var $this = $(this);
            $(".search-key").text($this.text());
            s_condition = $this.attr("data-key");
        },
        // 搜索产品
        search_pro: function(){
            var s_value = $(".search-input").val().trim();
            //if(s_value){
                var param_str = "";
                var re = /[a-zA-Z_]+\=[0-9a-zA-Z\%\_-]*/g; // 非搜索条件和当前页的其他条件
                s_value = escape(s_value);
                if(s_condition=="s-title"){
                    param_str = "t=" + s_value;
                }else if(s_condition=="s-sku"){
                    param_str = "k="+s_value;
                }else if(s_condition=="s-asin"){
                    param_str = "p="+s_value;
                }
                var search_str = location.search;
                if(search_str == ""){
                    location.href = "?" + param_str;
                }else{
                    var new_str = search_str.match(re);
                    if (new_str) {
                        var add_filter = '';
                        $.each(new_str,function(key,value) {
                            if(value.indexOf('filter') != -1){
                                add_filter = value;
                            }
                        });
                        if(add_filter){
                            location.href = "?" + new_str[0]+ "&"+ new_str[1]+ "&" + add_filter +"&"+ param_str;
                        } else {
                            location.href = "?" + new_str[0]+ "&"+ new_str[1]+ "&" + param_str;
                        }
                    } else {
                        location.href = "?" + param_str;
                    }
                }
            //}
            /*else{
                Inform.show("请输入搜索内容")
            }*/
        },
        searchByAsin: function(){
            var s_value = $("#asin-input").val().trim();
            //if(s_value){
                var param_str = "";
                s_value = escape(s_value);
                param_str = "asin="+s_value;
                var searchUrl = location.search;
                if(searchUrl == ""){
                    location.href = "?" + param_str;
                }else{
                    var new_str = searchUrl.split('&');
                    console.log(new_str);
                    location.href = new_str[0]+ "&"+ new_str[1] +"&"+ param_str;
                }
            /*}else{
                Inform.show("请输入搜索内容")
            }*/
        },

        detect_count: function(e){
            var count = Checkbox.get_length();
            is_all = false;
            $("#product-count").text(count);
            e.stopPropagation();
        },
        all_click: function(){
            $(".sel-pro").prop("checked", true);
            var count = Checkbox.get_length();
            $("#product-count").text($(this).attr("data-count"));
            is_all = true;
        }

    };
    Bidding.init();
});