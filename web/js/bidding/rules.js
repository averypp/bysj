/**
 * Created by SHB on 12/11/2016.
 */
 $(function(){
 	var shop_id = $("#shopId").val(),
        symbol_change = $(".symbol-change"),
 		rule_form = $("#rule-form"),
 		save_btn = $("#save-btn"),
        buybox_set = $("#buybox-set"),
        buybox = $(".buybox"),
        buybox_lower = $(".buybox-lower"),
        buybox_raise = $(".buybox-raise"),
        buybox_item = $(".buybox-item"),

        basic_lt = $("#basic-lt"),
        basic_lt_temp = $(".basic-lt-temp"),
        basic_eq = $("#basic-eq"),
        basic_eq_temp = $(".basic-eq-temp"),
        basic_none = $("#basic-none"),
        basic_none_temp = $(".basic-none-temp"),
        basic_both = $("#basic-both"),
        basic_both_temp = $(".basic-both-temp"),
        protected = $("#protected"),
        protected_temp = $(".protected-temp"),

        fba_vs_fba = $(".fba_vs_fba"),
        fba_vs_fba_temp = $(".fba_vs_fba_temp"),
        fba_vs_fbm = $(".fba_vs_fbm"),
        fba_vs_fbm_temp = $(".fba_vs_fbm_temp"),
        fbm_vs_fba = $(".fbm_vs_fba"),
        fbm_vs_fba_temp = $(".fbm_vs_fba_temp"),
        fbm_vs_fbm = $(".fbm_vs_fbm"),
        fbm_vs_fbm_temp = $(".fbm_vs_fbm_temp"),

        fba_vs_fba_lt = $("#fba_vs_fba_lt"),
        fba_vs_fba_lt_temp = $(".fba_vs_fba_lt_temp"),
        fba_vs_fba_eq = $("#fba_vs_fba_eq"),
        fba_vs_fba_eq_temp = $(".fba_vs_fba_eq_temp"),
        fba_vs_fba_after_le = $("#fba_vs_fba_after_le"),
        fba_vs_fba_after_le_temp = $(".fba_vs_fba_after_le_temp"),

        fba_vs_fbm_lt = $("#fba_vs_fbm_lt"),
        fba_vs_fbm_lt_temp = $(".fba_vs_fbm_lt_temp"),
        fba_vs_fbm_eq = $("#fba_vs_fbm_eq"),
        fba_vs_fbm_eq_temp = $(".fba_vs_fbm_eq_temp"),
        fba_vs_fbm_after_le = $("#fba_vs_fbm_after_le"),
        fba_vs_fbm_after_le_temp = $(".fba_vs_fbm_after_le_temp"),

        fbm_vs_fba_lt = $("#fbm_vs_fba_lt"),
        fbm_vs_fba_lt_temp = $(".fbm_vs_fba_lt_temp"),
        fbm_vs_fba_eq = $("#fbm_vs_fba_eq"),
        fbm_vs_fba_eq_temp = $(".fbm_vs_fba_eq_temp"),
        fbm_vs_fba_after_le = $("#fbm_vs_fba_after_le"),
        fbm_vs_fba_after_le_temp = $(".fbm_vs_fba_after_le_temp"),

        fbm_vs_fbm_lt = $("#fbm_vs_fbm_lt"),
        fbm_vs_fbm_lt_temp = $(".fbm_vs_fbm_lt_temp"),
        fbm_vs_fbm_eq = $("#fbm_vs_fbm_eq"),
        fbm_vs_fbm_eq_temp = $(".fbm_vs_fbm_eq_temp"),
        fbm_vs_fbm_after_le = $("#fbm_vs_fbm_after_le"),
        fbm_vs_fbm_after_le_temp = $(".fbm_vs_fbm_after_le_temp");

    var Rules = {
     	init: function(){
            Inform.init();
            symbol_change.change(Rules.change_symbol);
            
            buybox_set.change(Rules.change_buybox_temp);

            basic_lt.change(Rules.change_basic_lt_temp);
            basic_eq.change(Rules.change_basic_eq_temp);
            basic_none.change(Rules.change_basic_none_temp);
            basic_both.change(Rules.change_basic_both_temp);
            protected.change(Rules.change_protected_temp);

            fba_vs_fba.click(Rules.prop_fba_vs_fba);
            fba_vs_fbm.click(Rules.prop_fba_vs_fbm);
            fbm_vs_fba.click(Rules.prop_fbm_vs_fba);
            fbm_vs_fbm.click(Rules.prop_fbm_vs_fbm);

            fba_vs_fba_lt.change(Rules.change_fba_vs_fba_lt_temp);
            fba_vs_fba_eq.change(Rules.change_fba_vs_fba_eq_temp);
            fba_vs_fba_after_le.change(Rules.change_fba_vs_fba_after_le_temp);

            fba_vs_fbm_lt.change(Rules.change_fba_vs_fbm_lt_temp);
            fba_vs_fbm_eq.change(Rules.change_fba_vs_fbm_eq_temp);
            fba_vs_fbm_after_le.change(Rules.change_fba_vs_fbm_after_le_temp);

            fbm_vs_fba_lt.change(Rules.change_fbm_vs_fba_lt_temp);
            fbm_vs_fba_eq.change(Rules.change_fbm_vs_fba_eq_temp);
            fbm_vs_fba_after_le.change(Rules.change_fbm_vs_fba_after_le_temp);

            fbm_vs_fbm_lt.change(Rules.change_fbm_vs_fbm_lt_temp);
            fbm_vs_fbm_eq.change(Rules.change_fbm_vs_fbm_eq_temp);
            fbm_vs_fbm_after_le.change(Rules.change_fbm_vs_fbm_after_le_temp);

            Rules.change_buybox_temp();

            Rules.change_basic_lt_temp();
            Rules.change_basic_eq_temp();
            Rules.change_basic_none_temp();
            Rules.change_basic_both_temp();
            Rules.change_protected_temp();

            Rules.prop_fba_vs_fba();
            Rules.prop_fba_vs_fbm();
            Rules.prop_fbm_vs_fba();
            Rules.prop_fbm_vs_fbm();

            Rules.change_fba_vs_fba_lt_temp();
            Rules.change_fba_vs_fba_eq_temp();
            Rules.change_fba_vs_fba_after_le_temp();

            Rules.change_fba_vs_fbm_lt_temp();
            Rules.change_fba_vs_fbm_eq_temp();
            Rules.change_fba_vs_fbm_after_le_temp();

            Rules.change_fbm_vs_fba_lt_temp();
            Rules.change_fbm_vs_fba_eq_temp();
            Rules.change_fbm_vs_fba_after_le_temp();

            Rules.change_fbm_vs_fbm_lt_temp();
            Rules.change_fbm_vs_fbm_eq_temp();
            Rules.change_fbm_vs_fbm_after_le_temp();
            save_btn.click(Rules.uploadData);
            if($("input[name=rule-id]").val() > 0){
                Rules.render_data();
            }
        },
        change_symbol: function(){
            var option = $(this).val();
            var select = $(this).parent().next().find("select");
            var text = "<option value='-'>-</option><option value='+'>+</option>";
            if(option == "min"){
                text = "<option value='+'>+</option>";
            }else if (option == "max"){
                text = "<option value='-'>-</option>";
            }
            select.html(text);
        },
        render_data: function(){
            var rule_id = $("input[name=rule-id]").val();
            $.ajax({
                "url": "/?r=bidding/render-rule-info&shopId="+$("#shopId").val(),//"/create/" + $("#shop-id").val() + "/product/get",
                "type": "GET",
                "dataType": "json",
                "data": "rid=" + rule_id,
                "success": function(data){
                    console.log(data.message);
                    if(data.message){
                        var main_rule = data.message;
                        $("#buybox-set option[value="+main_rule.buybox_set+"]").attr('selected', 'selected');
                        $("#buybox_set_value1").val(main_rule.buybox_set_value1);
                        $("#buybox_set_value2").val(main_rule.buybox_set_value2);
                        $("#buybox_set_math1 option[value="+'"'+main_rule.buybox_set_math1+'"'+"]").attr('selected', 'selected');
                        $("#buybox_set_math2 option[value="+'"'+main_rule.buybox_set_math2+'"'+"]").attr('selected', 'selected');
                        $("#buybox_item option[value="+main_rule.buybox_item+"]").attr('selected', 'selected');
                        if(main_rule.competitors){
                            $.each(main_rule.competitors.split(","),function(i,v){
                                console.log(v);
                                $("#"+v).attr('checked', 'checked');
                            });
                        }
                        if(main_rule.types){
                            $.each(main_rule.types,function(i,v){
                                if(v.type == 'basic'){
                                    $.each(v.items, function(it,vt){
                                        if(vt.compare == 'gt'){
                                            $("#basic_gt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#basic_gt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#basic_gt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#basic_gt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#basic_gt_value").val(vt.value);
                                            $("#basic_gt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');
                                        }
                                        if(vt.compare == 'lt'){
                                            if(vt.options == 'customize'){
                                                basic_lt_temp.show();
                                            }
                                            $("#basic-lt option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#basic_lt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#basic_lt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#basic_lt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#basic_lt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#basic_lt_value").val(vt.value);
                                            $("#basic_lt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');
                                        }
                                        if(vt.compare == 'eq'){
                                            if(vt.options == 'customize'){
                                                basic_eq_temp.show();
                                            }
                                            $("#basic-eq option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#basic_eq_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#basic_eq_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#basic_eq_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#basic_eq_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#basic_eq_value").val(vt.value);
                                            $("#basic_eq_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');
                                        }
                                        if(vt.compare == 'none'){
                                            if(vt.options == 'customize'){
                                                basic_none_temp.show();
                                            }
                                            $("#basic-none option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#basic_none_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#basic_none_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#basic_none_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#basic_none_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#basic_none_value").val(vt.value);
                                            $("#basic_none_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');
                                        }

                                        if(vt.compare == 'both'){
                                            if(vt.options != "stop"){
                                                vt.options = vt.item;
                                                basic_both_temp.show();
                                            }
                                            $("#basic-both option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            if(vt.options == "min"){
                                                $("#basic_both_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.options == "max"){
                                                $("#basic_both_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#basic_both_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#basic_both_value").val(vt.value);
                                            $("#basic_both_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');
                                        }
                                    });
                                }

                                if(v.type == 'protected'){
                                    $.each(v.items,function(it,vt){
                                        if(vt.compare == 'after_le'){
                                            if(vt.options == 'customize'){
                                                protected_temp.show();
                                            }
                                            $("#protected option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#protected_after_le_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#protected_after_le_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#protected_after_le_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#protected_after_le_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#protected_after_le_value").val(vt.value);
                                            $("#protected_after_le_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }
                                    });
                                }

                                if(v.type == 'fba_vs_fba'){
                                    if(v.is_open == 1){
                                         $(".fba_vs_fba[value='on']").attr('checked','checked');
                                         fba_vs_fba_temp.show();
                                    } else {
                                         $(".fba_vs_fba[value='off']").attr('checked','checked');
                                    }
                                    $(".fba_vs_fba_is_open").val(v.is_open);
                                    $.each(v.items,function(it,vt){
                                        if(vt.compare == 'gt'){
                                            $("#fba_vs_fba_gt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fba_gt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fba_gt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fba_gt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fba_gt_value").val(vt.value);
                                            $("#fba_vs_fba_gt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'lt'){
                                            if(vt.options == 'customize'){
                                                fba_vs_fba_lt_temp.show();
                                            }
                                            $("#fba_vs_fba_lt option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fba_vs_fba_lt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fba_lt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fba_lt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fba_lt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fba_lt_value").val(vt.value);
                                            $("#fba_vs_fba_lt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'eq'){
                                            if(vt.options == 'customize'){
                                                fba_vs_fba_eq_temp.show();
                                            }
                                            $("#fba_vs_fba_eq option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fba_vs_fba_eq_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fba_eq_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fba_eq_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fba_eq_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fba_eq_value").val(vt.value);
                                            $("#fba_vs_fba_eq_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'after_le'){
                                            if(vt.options == 'customize'){
                                                fba_vs_fba_after_le_temp.show();
                                            }
                                            $("#fba_vs_fba_after_le option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fba_vs_fba_after_le_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fba_after_le_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fba_after_le_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fba_after_le_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fba_after_le_value").val(vt.value);
                                            $("#fba_vs_fba_after_le_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }
                                    });
                                }


                                if(v.type == 'fba_vs_fbm'){
                                    if(v.is_open == 1){
                                         $(".fba_vs_fbm[value='on']").attr('checked','checked');
                                         fba_vs_fbm_temp.show();
                                    } else {
                                         $(".fba_vs_fbm[value='off']").attr('checked','checked');
                                    }
                                    $(".fba_vs_fbm_is_open").val(v.is_open);
                                    $.each(v.items,function(it,vt){
                                        if(vt.compare == 'gt'){
                                            $("#fba_vs_fbm_gt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fbm_gt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fbm_gt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fbm_gt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fbm_gt_value").val(vt.value);
                                            $("#fba_vs_fbm_gt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'lt'){
                                            if(vt.options == 'customize'){
                                               fba_vs_fbm_lt_temp.show();
                                            }
                                            $("#fba_vs_fbm_lt option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fba_vs_fbm_lt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fbm_lt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fbm_lt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fbm_lt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fbm_lt_value").val(vt.value);
                                            $("#fba_vs_fbm_lt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'eq'){
                                            if(vt.options == 'customize'){
                                                fba_vs_fbm_eq_temp.show();
                                            }
                                            $("#fba_vs_fbm_eq option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fba_vs_fbm_eq_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fbm_eq_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fbm_eq_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fbm_eq_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fbm_eq_value").val(vt.value);
                                            $("#fba_vs_fbm_eq_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'after_le'){
                                            if(vt.options == 'customize'){
                                                fba_vs_fbm_after_le_temp.show();
                                            }
                                            $("#fba_vs_fbm_after_le option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fba_vs_fbm_after_le_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fba_vs_fbm_after_le_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fba_vs_fbm_after_le_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fba_vs_fbm_after_le_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fba_vs_fbm_after_le_value").val(vt.value);
                                            $("#fba_vs_fbm_after_le_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }
                                    });
                                }


                                if(v.type == 'fbm_vs_fba'){
                                    if(v.is_open == 1){
                                         $(".fbm_vs_fba[value='on']").attr('checked','checked');
                                         fbm_vs_fba_temp.show();
                                    } else {
                                         $(".fbm_vs_fba[value='off']").attr('checked','checked');
                                    }
                                    $(".fbm_vs_fba_is_open").val(v.is_open);
                                    $.each(v.items,function(it,vt){
                                        if(vt.compare == 'gt'){
                                            $("#fbm_vs_fba_gt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fba_gt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fba_gt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fba_gt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fba_gt_value").val(vt.value);
                                            $("#fbm_vs_fba_gt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'lt'){
                                            if(vt.options == 'customize'){
                                                fbm_vs_fba_lt_temp.show();
                                            }
                                            $("#fbm_vs_fba_lt option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fbm_vs_fba_lt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fba_lt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fba_lt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fba_lt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fba_lt_value").val(vt.value);
                                            $("#fbm_vs_fba_lt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'eq'){
                                            if(vt.options == 'customize'){
                                                fbm_vs_fba_eq_temp.show();
                                            }
                                            $("#fbm_vs_fba_eq option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fbm_vs_fba_eq_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fba_eq_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fba_eq_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fba_eq_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fba_eq_value").val(vt.value);
                                            $("#fbm_vs_fba_eq_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'after_le'){
                                            if(vt.options == 'customize'){
                                                fbm_vs_fba_after_le_temp.show();
                                            }
                                            $("#fbm_vs_fba_after_le option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fbm_vs_fba_after_le_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fba_after_le_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fba_after_le_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fba_after_le_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fba_after_le_value").val(vt.value);
                                            $("#fbm_vs_fba_after_le_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }
                                    });
                                }


                                if(v.type == 'fbm_vs_fbm'){
                                    if(v.is_open == 1){
                                         $(".fbm_vs_fbm[value='on']").attr('checked','checked');
                                         fbm_vs_fbm_temp.show();
                                    } else {
                                         $(".fbm_vs_fbm[value='off']").attr('checked','checked');
                                    }
                                    $(".fbm_vs_fbm_is_open").val(v.is_open);
                                    $.each(v.items,function(it,vt){
                                        if(vt.compare == 'gt'){
                                            $("#fbm_vs_fbm_gt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fbm_gt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fbm_gt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fbm_gt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fbm_gt_value").val(vt.value);
                                            $("#fbm_vs_fbm_gt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'lt'){
                                            if(vt.options == 'customize'){
                                                fbm_vs_fbm_lt_temp.show();
                                            }
                                            $("#fbm_vs_fbm_lt option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fbm_vs_fbm_lt_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fbm_lt_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fbm_lt_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fbm_lt_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fbm_lt_value").val(vt.value);
                                            $("#fbm_vs_fbm_lt_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'eq'){
                                            if(vt.options == 'customize'){
                                                fbm_vs_fbm_eq_temp.show();
                                            }
                                            $("#fbm_vs_fbm_eq option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fbm_vs_fbm_eq_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fbm_eq_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fbm_eq_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fbm_eq_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fbm_eq_value").val(vt.value);
                                            $("#fbm_vs_fbm_eq_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }

                                        if(vt.compare == 'after_le'){
                                            if(vt.options == 'customize'){
                                                fbm_vs_fbm_after_le_temp.show();
                                            }
                                            $("#fbm_vs_fbm_after_le option[value="+'"'+vt.options+'"'+"]").attr('selected', 'selected');
                                            $("#fbm_vs_fbm_after_le_item option[value="+'"'+vt.item+'"'+"]").attr('selected', 'selected');
                                            if(vt.item == "min"){
                                                $("#fbm_vs_fbm_after_le_symbol").html("<option value='+' selected='selected'>+</option>");
                                            }else if(vt.item == "max"){
                                                $("#fbm_vs_fbm_after_le_symbol").html("<option value='-' selected='selected'>-</option>");
                                            }else{
                                                $("#fbm_vs_fbm_after_le_symbol option[value="+'"'+vt.symbol+'"'+"]").attr('selected', 'selected');
                                            }
                                            $("#fbm_vs_fbm_after_le_value").val(vt.value);
                                            $("#fbm_vs_fbm_after_le_math option[value="+'"'+vt.math+'"'+"]").attr('selected', 'selected');

                                        }
                                    });
                                }


                            });

                        }

                    } else {
                        Inform.show("no data");
                    }
                },
                "error": function(){
                }
            })
        },
        uploadData: function () {
            var rule_name = $("input[name=rule-name]").val().trim();
            if(rule_name == ""){
                Inform.show("规则名称为空，请补充完整！");
                return false;
            }
            // rule_form.submit();
            $.ajax({
                type: "POST",
                url:'/?r=bidding/edit-rule&shopId='+shop_id,
                data: $('#rule-form').serialize(),
                dataType: "json",
                success: function(data) {
                	// alert(data.status);
                    if(data.status){
                        //Inform.enable(window.location.href);
                        Inform.enable('/?r=bidding/rulelist&shopId='+shop_id);
                        Inform.show(data.message);
                    }else {
                        Inform.enable();
                        Inform.show(data.message);
                    }
                },
                error: function() {
                }
            });
        },
        change_buybox_temp: function(){
            var buybox_setting = buybox_set.val();

            buybox.hide();
            if(buybox_setting == 1){
            	buybox_lower.show();
            	buybox_raise.show();
            	buybox_item.show();
            }else if(buybox_setting == 2){
            	buybox_raise.show();
            	buybox_item.show();
            }else if(buybox_setting == 3){
            	buybox_lower.show();
            }
        },
        change_basic_lt_temp: function(){
        	var basic_lt_option = basic_lt.val();
        	basic_lt_temp.hide();
        	if(basic_lt_option == "customize"){
            	basic_lt_temp.show();
            }
        },
        change_basic_eq_temp: function(){
        	var basic_eq_option = basic_eq.val();
        	basic_eq_temp.hide();
        	if(basic_eq_option == "customize"){
            	basic_eq_temp.show();
            }
        },
        change_basic_none_temp: function(){
        	var basic_none_option = basic_none.val();
        	basic_none_temp.hide();
        	if(basic_none_option == "customize"){
            	basic_none_temp.show();
            }
        },
        change_basic_both_temp: function(){
        	var basic_both_option = basic_both.val();
        	basic_both_temp.hide();
        	if(basic_both_option != "stop"){
            	basic_both_temp.show();
            }
        },
        change_protected_temp: function(){
        	var protected_option = protected.val();
        	protected_temp.hide();
        	if(protected_option == "customize"){
            	protected_temp.show();
            }
        },
        prop_fba_vs_fba: function(){
        	var fba_vs_fba_check = $("input[name=fba_vs_fba]:checked").val();
        	fba_vs_fba_temp.hide();
        	if(fba_vs_fba_check == "on"){
                $(".fba_vs_fba_is_open").val(1);
            	fba_vs_fba_temp.show();
            } else {
                $(".fba_vs_fba_is_open").val(0);
                fba_vs_fba_temp.hide();
            }
        },
        prop_fba_vs_fbm: function(){
        	var fba_vs_fbm_check = $("input[name=fba_vs_fbm]:checked").val();
        	fba_vs_fbm_temp.hide();
        	if(fba_vs_fbm_check == "on"){
                $(".fba_vs_fbm_is_open").val(1);
            	fba_vs_fbm_temp.show();
            } else {
                $(".fba_vs_fbm_is_open").val(0);
                fba_vs_fbm_temp.hide();
            }
        },
        prop_fbm_vs_fba: function(){
        	var fbm_vs_fba_check = $("input[name=fbm_vs_fba]:checked").val();
        	fbm_vs_fba_temp.hide();
        	if(fbm_vs_fba_check == "on"){
                $(".fbm_vs_fba_is_open").val(1);
            	fbm_vs_fba_temp.show();
            } else {
                $(".fbm_vs_fba_is_open").val(0);
                fbm_vs_fba_temp.hide();
            }
        },
        prop_fbm_vs_fbm: function(){
        	var fbm_vs_fbm_check = $("input[name=fbm_vs_fbm]:checked").val();
        	fbm_vs_fbm_temp.hide();
        	if(fbm_vs_fbm_check == "on"){
                $(".fbm_vs_fbm_is_open").val(1);
            	fbm_vs_fbm_temp.show();
            } else {
                $(".fbm_vs_fbm_is_open").val(0);
                fbm_vs_fbm_temp.hide();
            }
        },
        change_fba_vs_fba_lt_temp: function(){
        	var fba_vs_fba_lt_option = fba_vs_fba_lt.val();
        	fba_vs_fba_lt_temp.hide();
        	if(fba_vs_fba_lt_option == "customize"){
            	fba_vs_fba_lt_temp.show();
            }
        },
        change_fba_vs_fba_eq_temp: function(){
        	var fba_vs_fba_eq_option = fba_vs_fba_eq.val();
        	fba_vs_fba_eq_temp.hide();
        	if(fba_vs_fba_eq_option == "customize"){
            	fba_vs_fba_eq_temp.show();
            }
        },
        change_fba_vs_fba_after_le_temp: function(){
        	var fba_vs_fba_after_le_option = fba_vs_fba_after_le.val();
        	fba_vs_fba_after_le_temp.hide();
        	if(fba_vs_fba_after_le_option == "customize"){
            	fba_vs_fba_after_le_temp.show();
            }
        },
        change_fba_vs_fbm_lt_temp: function(){
        	var fba_vs_fbm_lt_option = fba_vs_fbm_lt.val();
        	fba_vs_fbm_lt_temp.hide();
        	if(fba_vs_fbm_lt_option == "customize"){
            	fba_vs_fbm_lt_temp.show();
            }
        },
        change_fba_vs_fbm_eq_temp: function(){
        	var fba_vs_fbm_eq_option = fba_vs_fbm_eq.val();
        	fba_vs_fbm_eq_temp.hide();
        	if(fba_vs_fbm_eq_option == "customize"){
            	fba_vs_fbm_eq_temp.show();
            }
        },
        change_fba_vs_fbm_after_le_temp: function(){
        	var fba_vs_fbm_after_le_option = fba_vs_fbm_after_le.val();
        	fba_vs_fbm_after_le_temp.hide();
        	if(fba_vs_fbm_after_le_option == "customize"){
            	fba_vs_fbm_after_le_temp.show();
            }
        },
        change_fbm_vs_fba_lt_temp: function(){
        	var fbm_vs_fba_lt_option = fbm_vs_fba_lt.val();
        	fbm_vs_fba_lt_temp.hide();
        	if(fbm_vs_fba_lt_option == "customize"){
            	fbm_vs_fba_lt_temp.show();
            }
        },
        change_fbm_vs_fba_eq_temp: function(){
        	var fbm_vs_fba_eq_option = fbm_vs_fba_eq.val();
        	fbm_vs_fba_eq_temp.hide();
        	if(fbm_vs_fba_eq_option == "customize"){
            	fbm_vs_fba_eq_temp.show();
            }
        },
        change_fbm_vs_fba_after_le_temp: function(){
        	var fbm_vs_fba_after_le_option = fbm_vs_fba_after_le.val();
        	fbm_vs_fba_after_le_temp.hide();
        	if(fbm_vs_fba_after_le_option == "customize"){
            	fbm_vs_fba_after_le_temp.show();
            }
        },
        change_fbm_vs_fbm_lt_temp: function(){
        	var fbm_vs_fbm_lt_option = fbm_vs_fbm_lt.val();
        	fbm_vs_fbm_lt_temp.hide();
        	if(fbm_vs_fbm_lt_option == "customize"){
            	fbm_vs_fbm_lt_temp.show();
            }
        },
        change_fbm_vs_fbm_eq_temp: function(){
        	var fbm_vs_fbm_eq_option = fbm_vs_fbm_eq.val();
        	fbm_vs_fbm_eq_temp.hide();
        	if(fbm_vs_fbm_eq_option == "customize"){
            	fbm_vs_fbm_eq_temp.show();
            }
        },
        change_fbm_vs_fbm_after_le_temp: function(){
        	var fbm_vs_fbm_after_le_option = fbm_vs_fbm_after_le.val();
        	fbm_vs_fbm_after_le_temp.hide();
        	if(fbm_vs_fbm_after_le_option == "customize"){
            	fbm_vs_fbm_after_le_temp.show();
            }
        }
     };
     Rules.init();
 });