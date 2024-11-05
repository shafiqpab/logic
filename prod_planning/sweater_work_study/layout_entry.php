<form name="gsdentry_5" id="gsdentry_5"  autocomplete="off"  >
    <fieldset style="width:870px; margin-top:5px">
    	<legend>Layout Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
             <tr>
                <td width="100" class="must_entry_caption">Style Ref.</td>                                              
                <td width="180">
                     <input type="text" id="txt_style_ref_lo" name="txt_style_ref_lo" class="text_boxes" style="width:148px" placeholder="Display" disabled="disabled"/>
                     <input type="hidden" name="breakdown_id4" id="breakdown_id4" value=""/>
                     <input type="hidden" name="lo_update_id" id="lo_update_id" value=""/>
                     <input type="hidden" name="balanceId" id="balanceId" value=""/>
                </td>
                <td width="100">Buyer Name</td>                                              
                <td>
                    <? echo create_drop_down( "cbo_buyer_lo", 140, "select id,buyer_name from lib_buyer", "id,buyer_name", 1, " Display ", 0, "", 1); ?>
                </td>
                <td width="100">Garments Item</td>
                <td><? echo create_drop_down( "cbo_gmt_item_lo", 160, $garments_item, "", 1, " Display ", 0, "", 1); ?></td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Line Shape</td>
                <td><? echo create_drop_down( "cbo_line_shape", 160, $line_shape_arr, "", 1, " -- Select -- ", 0, "load_data();", 0); ?></td>
                <td class="must_entry_caption">No Of Work Station</td>
                <td><input type="text" name="txt_no_of_work_st" id="txt_no_of_work_st" placeholder="Write" class="text_boxes_numeric" style="width:128px;" onblur="load_data();"/></td>
                <td>Layout Date</td>
                <td><input type="text" name="txt_layout_date" id="txt_layout_date" placeholder="Select" class="datepicker" style="width:148px;" readonly="readonly" /></td>
            </tr>
        </table>
    </fieldset>
    <fieldset style="width:870px; margin-top:10px">
        <div id="layout_list_view"></div>
        <div style="margin-top:10px" id="summary_list_view"></div>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td colspan="5" align="center" class="button_container">
                    <? echo load_submit_buttons($permission,"fnc_layout_entry",0,1,"resetLayout();",5); ?>
                </td>
            </tr>
        </table>
    </fieldset>
</form>