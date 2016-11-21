/**
 * Created by xirui_yang625 on 16/4/19.
 */

$(function(){
    var shop_id = $("#shop-id").val(),
        search_option = "",
        products = [];
    Inform.init();
    var Sold = {
        init: function(){
            $("#search-options").find("a").click(Sold.search_option_change);
            $("#search-btn").click(Sold.search);
            $(".amazon-sold-edit").click(Sold.amazon_sold_edit);
            $("html").on("click", ".get-variation", Sold.get_variation);
            $("html").on("click", ".sold-yours", Sold.sold_yours);
            $("html").on("click", "#sold-save", Sold.save_sold);
            $("html").on("change", ".m-required", Sold.input_change);
        },
        search_option_change: function() {
            var s_value = $(this).text();
            search_option = s_value;
            $(".search-key").text(s_value);
        },
        search: function(){
            var search_content = $(".search-input").val().trim();
            if(!search_option||!search_content){
                Inform.show("搜索条件或者搜索内容为空,请补充完整!");
            }else{
                $("#result").text("正在加载数据!");
                $.ajax({
                    url: "/sold/" + shop_id + "/search",
                    type: "post",
                    data: {"option": search_option, "content": search_content},
                    success: function(data){
                        if(data.status){
                            products = data["products"];
                            Sold.render_products(products);
                        }else {
                            $("#result").text("无搜索结果!");
                        }
                    }
                });
            }
        },
        render_products: function(){
            var th_str = "<tr class=\"table-title\">"
                         + "<th>产品</th><th style=\"width: 700px\">标题</th>"
                         + "<th>操作</th></tr>",
                tr_str = "<tr><td><div><img src=\"{0}\" style=\"max-width: 100px;max-height: 120px\"/></div></td>"
                         + "<td><div class=\"pro-title\"><a href=\"{1}\" target=\"_blank\">{2}</a>"
                         + "<div class=\"asin\" data-value=\"{3}\">Asin:{4}</div><div id=\"Brand\" data-value=\"{5}\">Brand:{6}</div></td>"
                         + "<td>{7}</td>"
                         + "</tr>",
                table = $("<table/>").addClass("table table-condensed product-table").css("table-layout", "fixed");
            for(var i=0;i<products.length;i++){
                var option = "<button class=\"btn btn-success sold-yours\" data-asin=\"{0}\">出售您的</button>".format(
                    products[i]["Asin"]);
                if(products[i]["HasChild"]){
                    option = "<button class=\"btn btn-default get-variation\" data-asin=\"{0}\" data-children=\"false\" child-sku=\"{1}\">展开变体</button>".format(
                        products[i]["Asin"], products[i]["ChildSkus"]
                    );
                }
                th_str += tr_str.format(
                    products[i]["Image"], products[i]["Url"], products[i]["Title"], products[i]["Asin"], products[i]["Asin"],
                    products[i]["Brand"], products[i]["Brand"], option
                );
            }
            console.log(th_str);
            table.append(th_str);
            $(".product-content").empty().append(table);
        },
        get_variation: function(){
            var childs = $(this).attr("child-sku").trim(),
                url = "/sold/" + shop_id + "/get/variation",
                $this = $(this);
            if($this.attr("data-children") == "false"){
                $("#spinner-modal").modal("show");
                var asin = $(".get-variation").closest("tr").find(".asin").attr("data-value").trim();
                var params = {"content": childs, "parent": asin};
                Sold.ajax_handler(url, params);
            }else{
                $(".variation").toggle("show", function(){
                    $this.text($(".variation").is(":visible") ? "收回变体" : "展开变体");
                });
            }
        },
        ajax_handler: function(url, params){
            $.ajax({
                url: url,
                type: "post",
                data: params,
                success: function(data){
                    if(data.status){
                        $("#spinner-modal").modal("hide");
                        products = data["products"];
                        Sold.render_variation(products);
                    }else if(data.status == "1"){
                        console.log(data.msg);
                        Sold.un_finish();
                    }
                    else{
                        Inform.show(data.msg);
                    }
                },
                statusCode: {504: function(){
                    console.log("ajax 请求超时!");
                    Sold.un_finish();
                }}
            });
        },
        un_finish: function(){
            var url = "/sold/" + shop_id + "/get/task",
                asin = $(".get-variation").closest("tr").find(".asin").attr("data-value").trim(),
                params = {"parent": asin};
            console.log("执行定时轮训操作!");
            setTimeout(Sold.ajax_handler(url, params), 500);
        },
        render_variation: function(){
            var th_str = "",
                tr_str = "<tr class=\"variation\"><td><div><img src=\"{0}\" style=\"max-width: 100px;max-height: 120px\"/></div></td>"
                         + "<td><div class=\"pro-title\"><a href=\"{1}\" target=\"_blank\">{2}</a>"
                         + "<div class=\"asin\" data-value=\"{3}\">Asin:{4}</div><div id=\"Brand\" data-value=\"{5}\">Brand:{6}</div></td>"
                         + "<td>{7}</td>"
                         + "</tr>";
            for(var i=0;i<products.length;i++){
                var option = "<button class=\"btn btn-success sold-yours\" data-asin=\"{0}\">出售您的</button>".format(
                    products[i]["Asin"]);
                th_str += tr_str.format(
                    products[i]["Image"], products[i]["Url"], products[i]["Title"], products[i]["Asin"], products[i]["Asin"],
                    products[i]["Brand"], products[i]["Brand"], option
                );
            }
            console.log(th_str);
            $(".product-table tbody").append(th_str);
            $(".get-variation").attr("data-children", "true").text("收起变体");
        },
        sold_yours: function(){
            $("#sold-base-modal").find("input, select").val("").css("border-color", "");
            var asin = $(this).attr("data-asin"),
                title = $(this).closest("tr").find("a").text().trim(),
                image = $(this).closest("tr").find("img").attr("src").trim(),
                brand = $(this).closest("tr").find("#Brand").attr("data-value").trim();
            $("#Asin").val(asin);
            $("#Title").val(title);
            $("#Image").val(image);
            $("#Brand").val(brand);
            $('.date-choose').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $("#sold-base-modal").modal("show");
        },
        save_sold: function(){
            var flag = Sold.check_required(),
                url = "/sold/" + shop_id + "/save",
                p_id = $("#PID").val().trim();
            if(!flag){
                Inform.show("请将红框中的内容补充完整!");
                return 0;
            }
            var sku = $("#Sku").val().trim(),
                asin = $("#Asin").val().trim(),
                title = $("#Title").val().trim(),
                condition = $("#Condition").val().trim(),
                price = $("#Price").val().trim(),
                brand = $("#Brand").val().trim(),
                sale_price = $("#sale-price").val().trim(),
                sale_date_from = $("#sale-date-from").val().trim(),
                sale_date_to = $("#sale-date-to").val().trim(),
                image = $("#Image").val().trim(),
                stock = $("#Stock").val().trim(),
                params = {"title": title, "asin": asin, "sku": sku, "condition": condition, "brand": brand,
                    "price": price, "sale_price": sale_price, "image": image, "stock": stock,
                    "sale_date": [sale_date_from, sale_date_to].join(";")};

            if(p_id){
                url = "/sold/" + shop_id + "/update";
                params["pid"] = p_id;
            }
            $.ajax({
                url: url,
                type: "post",
                data: {"params": JSON.stringify(params)},
                success: function(data){
                    $("#sold-base-modal").modal("hide");
                    if(p_id){
                        Inform.location_url = location.href;
                    }
                    Inform.show(data.msg);
                }
            });

        },
        amazon_sold_edit: function(){
            var product_id = $(this).attr("data-id").trim();
            $.ajax({
                "url": "/sold/" + shop_id + "/get/product",
                "data": {"p_id": product_id},
                "type": "POST",
                "dataType": "json",
                "success": function(data){
                    if(data.status){
                        var product = data.info;
                        console.log(product["price"]);
                        $("#Sku").val(product["sku"]);
                        $("#Asin").val(product["asin"]);
                        $("#Title").val(product["title"]);
                        $("#Condition").val(product["condition"]);
                        $("#Price").val(product["price"]);
                        $("#sale-price").val(product["sale_price"]);
                        $("#sale-date-from").val(product["sale_from"]);
                        $("#sale-date-to").val(product["sale_to"]);
                        $("#Stock").val(product["stock"]);
                        $("#PID").val(product["pid"]);
                        $('.date-choose').datetimepicker({
                            format: 'YYYY-MM-DD'
                        });
                        $("#sold-base-modal").modal("show");
                    }else{
                        Inform.show(data.msg);
                    }
                }
            })
        },
        check_required: function(){
            var sold_info = $(".m-required"),
                flag = true;
            sold_info.each(function(i, v){
                if(!$(v).val().trim()){
                    flag = false;
                    $(v).css("border-color", "red");
                }
            });
            if($("#sale-price").val().trim()){
                $(".date-choose").each(function(i, v){
                    if(!$(v).val().trim()){
                        flag = false;
                        $(v).css("border-color", "red");
                    }
                });
            }
            return flag
        },
        check_price: function(){
            var v = $(this).val().trim().replace(/[^0-9.]/g,'');
            var nums = v.split(".");
            var int = nums[0];
            if(int.length>18){
                nums[0] = int.substring(0,18);
            }
            $(this).val(nums.join("."));

        },
        input_change: function(){
            var $this = $(this);
            if($this.val().trim()){
                $this.css("border-color", "");
            }
        }

    };
    Sold.init();
});