/**
 * Created by GF on 16/3/4
 */
$(function(){
    var $modal = $(".notice-modal"),
        $checkbox = $modal.find("input");
    var Notice = {
        init: function(){
            $(".no-md-close").click(Notice.close_modal);
//            $(".anchor-btn").click(function(){
//                document.cookie = "checked=" + $checkbox.val();
//                $("#notice-list").find("a[data-id=\""+$checkbox.val()+"\"]").trigger("click");
//                setTimeout(function(){$modal.removeClass("no-md-show");}, 500);
//            });
            Notice.show_modal();
        },
        show_modal: function(){
            $modal.length && setTimeout(function(){$modal.addClass("no-md-show")}, 500);
        },
        close_modal: function(){
            $modal.removeClass("no-md-show");
            if($checkbox.prop("checked")){
                document.cookie = "IN_READ=" + $checkbox.val() + "-t";
            }
        }
    };
    Notice.init();
});