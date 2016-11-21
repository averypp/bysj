/**
 * Created by xuhe on 15/6/2.
 */
var Inform = {
    init: function(){
        Inform.inform = $("#global-inform");
        Inform.header = Inform.inform.find(".md-header");
        Inform.body = Inform.inform.find(".md-body");
        Inform.panel = Inform.inform.find(".md-panel");
        Inform.loading = Inform.inform.find(".md-loading");
        Inform.l_text = Inform.inform.find(".md-loading-text");
        Inform.footer = Inform.inform.find(".md-footer");
        Inform.close = Inform.inform.find(".close-inform");
        Inform.close.click(Inform.hide);
        Inform.header.html("系统通知");
    },
    show: function(content, loading, load_text, header){
        if(header)
            Inform.header.html(header);
        if(!loading) {
            Inform.loading.css({"display": "none"});
            Inform.panel.css({"display": "block"});
            Inform.panel.html(content);
        }else{
            Inform.loading.css({"display": "block"});
            Inform.panel.css({"display": "none"});
            Inform.l_text.html(load_text);
        }
        Inform.inform.attr({"class": "md-modal md-effect-1 md-show"});
    },
    hide: function(){
        Inform.inform.removeClass("md-show");
        if(Inform.location_url){
            setTimeout(function(){
                location.href = Inform.location_url;
            }, 500);
        }
    },
    enable: function(url, reload){
        if(reload){
            Inform.close.click(function(){
                location.reload();
            });
        }
        Inform.location_url = url;
        Inform.footer.css({"display": "block"});
        Inform.close.prop("disabled", false).click(Inform.hide);
    },
    disable: function(){
        Inform.footer.css({"display": "none"});
        Inform.close.unbind().prop("disabled", true);
    },
    empty: function(){
        Inform.header.html("");
        Inform.body.html("");
    }
};
String.prototype.format= function(){
    var args = arguments;
    return this.replace(/\{(\d+)\}/g,function(s,i){
        return args[i];
    });
};