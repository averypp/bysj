/**
 * Created by xuhe on 15/1/8.
 */
var Loading = {
    init: function(){
        Loading.pannel = $("#loading");
        Loading.icon = $("#loading-icon");
        Loading.tip = $("#tip");
    },
    show: function(){
        Loading.pannel.css({"opacity": 1});
        Loading.pannel.css({"display": "block"});
    },
    hidden: function(){
        Loading.pannel.css({"opacity": 0});
        setTimeout(function(){
            Loading.pannel.css({"display": "none"});
        }, 500);
    },
    disappear: function(message){
        Loading.icon.css({"display": "none"});
        Loading.tip.html(message).css({"opacity": 1});
        setTimeout(function(){
            Loading.tip.css({"opacity": 0});
        }, 500);
        setTimeout(function(){
            Loading.icon.css({"display": "block"});
            Loading.pannel.css({"display": "none"});
        }, 1000);
    }
};
$(function(){
    Loading.init();
});