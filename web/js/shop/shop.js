/**
 * Created by xuhe on 15/5/21.
 */
$(function(){
    $("#change-shop").click(function(){
        $.ajax({
            "type": "POST",
            "url": "/?r=public-product/shop-change&shopId="+$("#shop-id").val(),
            "dataType": "json",
            "success": function(data){
                if(data.status==1) {
                    render_modal(data["shops"]);
                }
            },
            "error": function(){
                //location.href = "/"
            }
        });
    });
    function render_modal(shops){
        var html_str = "";
        for(var i=0;i<shops.length;i++){
            var shop = shops[i];
            html_str += "<div class=\"col-md-4\">";
            if(shop.platform != "Amazon"){
                html_str += "<a href=\"/online/" + shop.shop_id +"/selling\"";
            }else{
                html_str += "<a href=\"/?r=public-product&shopId=" + shop.shop_id +"&status=waiting\"";
            }
            html_str += " class=\"thumbnail-btn";
            html_str += " p-" + shop.platform.toLowerCase() + "\"";
            html_str += ">"
            + "<span>店铺：" + shop.name + "</span><br/>"
            + "<span>平台：" + shop.platform + "</span><br/>"
            + "<span>站点：" + shop.site_name + "</span>"
            + "</a></div>"
        }
        $("#shops").html(html_str);
    }
});