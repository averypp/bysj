/**
 * Created by xuhe on 15/5/23.
 */
var Checkbox = {
    is_all: false,
    init: function(parent, child, tag){
        console.log(parent);
        Checkbox.parent = $(parent);
        Checkbox.child = $(child);
        Checkbox.tag = tag;
        Checkbox.parent.click(Checkbox.all_select);
    },
    get_data: function(callback){
        var data_list = [];
        Checkbox.child.each(function(k, v){
            if($(v).is(":checked")){
                if(Checkbox.tag == ""){
                    data_list.push($(v).val());
                }
                else{
                    data_list.push($(v).attr("data-" + Checkbox.tag));
                }
            }
        });
        console.log(data_list);
        callback(data_list);
    },
    all_select: function(){
        Checkbox.is_all = !Checkbox.is_all
        Checkbox.child.prop("checked", Checkbox.is_all);
    },
    get_length: function(){
        var count = 0;
        Checkbox.child.each(function(k, v){
            if($(v).is(":checked")){
                count += 1;
            }
        });
        return count;
    },
    get_list: function(){
        var data_list = [];
        Checkbox.child.each(function(k, v){
            if($(v).is(":checked")){
                if(Checkbox.tag == ""){
                    data_list.push($(v).val());
                }
                else{
                    data_list.push($(v).attr("data-" + Checkbox.tag));
                }
            }
        });
        return data_list
    },
    is_full: function(){
        var flag = true;
        Checkbox.child.each(function(k, v){
            if(!$(v).is(":checked")){
                flag = false;
            }
        });
        return flag;
    }
};